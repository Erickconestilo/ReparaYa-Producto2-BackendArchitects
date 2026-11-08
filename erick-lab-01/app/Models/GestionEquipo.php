<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/Database/Conexion.php';

/**
 * Integracion local del flujo de administracion y tecnico.
 * Inspirada en los casos de uso entregados por Paula Gomez del Aguila.
 * Las reglas de permisos y transiciones se validan en MySQL, no solo en las vistas.
 */
final class GestionEquipo
{
    public static function incidenciasParaAdmin(): array
    {
        return Conexion::abrir()->query(
            'SELECT i.id, i.localizador, i.estado, i.tipo_urgencia, i.fecha_servicio, i.especialidad_id,
                    e.nombre_especialidad, u.nombre AS cliente,
                    t.nombre_completo AS tecnico
             FROM incidencias i
             JOIN especialidades e ON e.id = i.especialidad_id
             JOIN usuarios u ON u.id = i.cliente_id
             LEFT JOIN tecnicos t ON t.id = i.tecnico_id
             ORDER BY i.fecha_servicio DESC, i.id DESC'
        )->fetchAll();
    }

    public static function tecnicosDisponibles(): array
    {
        return Conexion::abrir()->query(
            "SELECT t.id, t.nombre_completo, t.especialidad_id, e.nombre_especialidad
             FROM tecnicos t
             JOIN usuarios u ON u.id = t.usuario_id
             JOIN especialidades e ON e.id = t.especialidad_id
             WHERE t.disponible = 1 AND u.rol = 'tecnico'
             ORDER BY t.nombre_completo, t.id"
        )->fetchAll();
    }

    public static function calendarioDesdeHasta(string $desde, string $hasta): array
    {
        $consulta = Conexion::abrir()->prepare(
            'SELECT i.id, i.localizador, i.estado, i.tipo_urgencia, i.fecha_servicio,
                    i.franja_horaria, e.nombre_especialidad,
                    u.nombre AS cliente, t.nombre_completo AS tecnico
             FROM incidencias i
             JOIN usuarios u ON u.id = i.cliente_id
             JOIN especialidades e ON e.id = i.especialidad_id
             LEFT JOIN tecnicos t ON t.id = i.tecnico_id
             WHERE i.fecha_servicio >= :desde AND i.fecha_servicio < :hasta
             ORDER BY i.fecha_servicio, i.id'
        );
        $consulta->execute(['desde' => $desde, 'hasta' => $hasta]);
        return $consulta->fetchAll();
    }

    public static function incidenciasDeTecnico(int $usuarioId): array
    {
        $consulta = Conexion::abrir()->prepare(
            'SELECT i.id, i.localizador, i.estado, i.fecha_servicio, i.tipo_urgencia,
                    e.nombre_especialidad
             FROM incidencias i
             JOIN tecnicos t ON t.id = i.tecnico_id
             JOIN especialidades e ON e.id = i.especialidad_id
             WHERE t.usuario_id = :usuario AND t.disponible = 1
             ORDER BY i.fecha_servicio, i.id'
        );
        $consulta->execute(['usuario' => $usuarioId]);
        return $consulta->fetchAll();
    }

    public static function incidenciaDeTecnico(int $incidenciaId, int $usuarioId): ?array
    {
        $consulta = Conexion::abrir()->prepare(
            'SELECT i.*, e.nombre_especialidad
             FROM incidencias i
             JOIN tecnicos t ON t.id = i.tecnico_id
             JOIN especialidades e ON e.id = i.especialidad_id
             WHERE i.id = :incidencia AND t.usuario_id = :usuario AND t.disponible = 1
             LIMIT 1'
        );
        $consulta->execute(['incidencia' => $incidenciaId, 'usuario' => $usuarioId]);
        $fila = $consulta->fetch();
        return $fila === false ? null : $fila;
    }

    public static function asignar(int $incidenciaId, int $tecnicoId, int $adminUsuarioId): string
    {
        $pdo = Conexion::abrir();
        $pdo->beginTransaction();
        try {
            $admin = $pdo->prepare("SELECT id FROM usuarios WHERE id = :id AND rol = 'admin'");
            $admin->execute(['id' => $adminUsuarioId]);
            if ($admin->fetchColumn() === false) {
                $pdo->rollBack();
                return 'sin_permiso';
            }

            $consulta = $pdo->prepare(
                'SELECT estado, especialidad_id, tecnico_id FROM incidencias WHERE id = :id FOR UPDATE'
            );
            $consulta->execute(['id' => $incidenciaId]);
            $incidencia = $consulta->fetch();
            if ($incidencia === false) {
                $pdo->rollBack();
                return 'no_encontrada';
            }
            if ($incidencia['estado'] !== 'Pendiente' || $incidencia['tecnico_id'] !== null) {
                $pdo->rollBack();
                return 'estado_no_permitido';
            }

            $tecnico = $pdo->prepare(
                "SELECT t.id FROM tecnicos t JOIN usuarios u ON u.id = t.usuario_id
                 WHERE t.id = :tecnico AND t.especialidad_id = :especialidad
                   AND t.disponible = 1 AND u.rol = 'tecnico' LIMIT 1"
            );
            $tecnico->execute([
                'tecnico' => $tecnicoId,
                'especialidad' => (int) $incidencia['especialidad_id'],
            ]);
            if ($tecnico->fetchColumn() === false) {
                $pdo->rollBack();
                return 'tecnico_no_valido';
            }

            $actualiza = $pdo->prepare(
                "UPDATE incidencias SET tecnico_id = :tecnico, estado = 'Asignada' WHERE id = :id"
            );
            $actualiza->execute(['tecnico' => $tecnicoId, 'id' => $incidenciaId]);
            $historial = $pdo->prepare(
                'INSERT INTO historial_estados
                 (incidencia_id, estado_anterior, estado_nuevo, actor_id, observacion)
                 VALUES (:id, :anterior, :nuevo, :actor, :nota)'
            );
            $historial->execute([
                'id' => $incidenciaId, 'anterior' => 'Pendiente', 'nuevo' => 'Asignada',
                'actor' => $adminUsuarioId, 'nota' => 'Tecnico asignado por administracion',
            ]);
            $pdo->commit();
            return 'asignada';
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            throw $error;
        }
    }

    public static function avanzarEstado(int $incidenciaId, int $tecnicoUsuarioId, string $estadoNuevo): string
    {
        $permitidas = ['Asignada' => 'En curso', 'En curso' => 'Finalizada'];
        if (!in_array($estadoNuevo, array_values($permitidas), true)) {
            return 'estado_no_permitido';
        }

        $pdo = Conexion::abrir();
        $pdo->beginTransaction();
        try {
            $consulta = $pdo->prepare(
                "SELECT i.estado FROM incidencias i
                 JOIN tecnicos t ON t.id = i.tecnico_id
                 JOIN usuarios u ON u.id = t.usuario_id
                 WHERE i.id = :incidencia AND t.usuario_id = :usuario
                   AND t.disponible = 1 AND u.rol = 'tecnico' FOR UPDATE"
            );
            $consulta->execute(['incidencia' => $incidenciaId, 'usuario' => $tecnicoUsuarioId]);
            $incidencia = $consulta->fetch();
            if ($incidencia === false) {
                $pdo->rollBack();
                return 'no_encontrada';
            }
            $estadoAnterior = $incidencia['estado'];
            if (($permitidas[$estadoAnterior] ?? null) !== $estadoNuevo) {
                $pdo->rollBack();
                return 'estado_no_permitido';
            }

            $actualiza = $pdo->prepare('UPDATE incidencias SET estado = :estado WHERE id = :id');
            $actualiza->execute(['estado' => $estadoNuevo, 'id' => $incidenciaId]);
            $historial = $pdo->prepare(
                'INSERT INTO historial_estados
                 (incidencia_id, estado_anterior, estado_nuevo, actor_id, observacion)
                 VALUES (:id, :anterior, :nuevo, :actor, :nota)'
            );
            $historial->execute([
                'id' => $incidenciaId, 'anterior' => $estadoAnterior, 'nuevo' => $estadoNuevo,
                'actor' => $tecnicoUsuarioId, 'nota' => 'Cambio de estado por el tecnico asignado',
            ]);
            $pdo->commit();
            return 'actualizada';
        } catch (Throwable $error) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            throw $error;
        }
    }
}
