<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/Database/Conexion.php';

final class Solicitud
{
    public static function servicios(): array
    {
        return Conexion::abrir()->query(
            'SELECT id, nombre_especialidad, precio_base FROM especialidades WHERE activa = 1 ORDER BY nombre_especialidad'
        )->fetchAll();
    }

    public static function existeServicio(int $id): bool
    {
        $consulta = Conexion::abrir()->prepare('SELECT 1 FROM especialidades WHERE id = :id AND activa = 1');
        $consulta->execute(['id' => $id]);
        return $consulta->fetchColumn() !== false;
    }

    public static function crear(int $clienteId, int $servicioId, string $descripcion, string $direccion,
        string $fechaServicio, string $franja, string $telefono, string $urgencia): string
    {
        $pdo = Conexion::abrir();
        $anyoMadrid = (new DateTimeImmutable('now', new DateTimeZone('Europe/Madrid')))->format('Y');
        $localizador = 'REP-' . $anyoMadrid . '-' . strtoupper(bin2hex(random_bytes(4)));
        $pdo->beginTransaction();
        try {
            $consulta = $pdo->prepare(
                'INSERT INTO incidencias (localizador, cliente_id, especialidad_id, descripcion, direccion, fecha_servicio, franja_horaria, telefono_contacto, tipo_urgencia, estado)
                 VALUES (:localizador, :cliente, :servicio, :descripcion, :direccion, :fecha, :franja, :telefono, :urgencia, :estado)'
            );
            $consulta->execute([
                'localizador' => $localizador, 'cliente' => $clienteId, 'servicio' => $servicioId,
                'descripcion' => $descripcion, 'direccion' => $direccion, 'fecha' => $fechaServicio,
                'franja' => $franja, 'telefono' => $telefono, 'urgencia' => $urgencia, 'estado' => 'Pendiente',
            ]);
            $id = (int) $pdo->lastInsertId();
            $historial = $pdo->prepare(
                'INSERT INTO historial_estados (incidencia_id, estado_anterior, estado_nuevo, actor_id, observacion)
                 VALUES (:incidencia, NULL, :nuevo, :actor, :nota)'
            );
            $historial->execute([
                'incidencia' => $id, 'nuevo' => 'Pendiente', 'actor' => $clienteId,
                'nota' => 'Solicitud registrada por el cliente',
            ]);
            $pdo->commit();
            return $localizador;
        } catch (Throwable $ex) {
            $pdo->rollBack();
            throw $ex;
        }
    }

    public static function listarPorCliente(int $clienteId): array
    {
        $consulta = Conexion::abrir()->prepare(
            'SELECT i.id, i.localizador, i.fecha_servicio, i.tipo_urgencia, i.estado, e.nombre_especialidad
             FROM incidencias i JOIN especialidades e ON e.id = i.especialidad_id
             WHERE i.cliente_id = :cliente ORDER BY i.created_at DESC, i.id DESC'
        );
        $consulta->execute(['cliente' => $clienteId]);
        return $consulta->fetchAll();
    }

    public static function obtenerPorCliente(int $incidenciaId, int $clienteId): ?array
    {
        $consulta = Conexion::abrir()->prepare(
            'SELECT i.*, e.nombre_especialidad, e.precio_base
             FROM incidencias i JOIN especialidades e ON e.id = i.especialidad_id
             WHERE i.id = :id AND i.cliente_id = :cliente LIMIT 1'
        );
        $consulta->execute(['id' => $incidenciaId, 'cliente' => $clienteId]);
        $fila = $consulta->fetch();
        return $fila === false ? null : $fila;
    }

    public static function historial(int $incidenciaId): array
    {
        $consulta = Conexion::abrir()->prepare(
            'SELECT h.estado_anterior, h.estado_nuevo, h.fecha_cambio, h.observacion, u.nombre AS actor
             FROM historial_estados h JOIN usuarios u ON u.id = h.actor_id
             WHERE h.incidencia_id = :id ORDER BY h.fecha_cambio, h.id'
        );
        $consulta->execute(['id' => $incidenciaId]);
        return $consulta->fetchAll();
    }

    // Solo el cliente titular puede corregir sus datos antes de que se tramite la cita.
    // FOR UPDATE protege el plazo y el estado frente a modificaciones simultáneas.
    public static function actualizarDatosCliente(int $incidenciaId, int $clienteId,
        string $descripcion, string $direccion, string $telefono, DateTimeImmutable $ahora): string
    {
        $pdo = Conexion::abrir();
        $pdo->beginTransaction();
        try {
            $consulta = $pdo->prepare(
                'SELECT estado, fecha_servicio, descripcion, direccion, telefono_contacto
                 FROM incidencias WHERE id = :id AND cliente_id = :cliente FOR UPDATE'
            );
            $consulta->execute(['id' => $incidenciaId, 'cliente' => $clienteId]);
            $fila = $consulta->fetch();
            if ($fila === false) {
                $pdo->rollBack();
                return 'no_encontrada';
            }
            if ($fila['estado'] !== 'Pendiente') {
                $pdo->rollBack();
                return 'estado_no_permitido';
            }
            $cita = new DateTimeImmutable($fila['fecha_servicio'], new DateTimeZone('Europe/Madrid'));
            if ($cita->getTimestamp() - $ahora->getTimestamp() <= 48 * 3600) {
                $pdo->rollBack();
                return 'fuera_de_plazo';
            }
            if ($fila['descripcion'] === $descripcion && $fila['direccion'] === $direccion
                && $fila['telefono_contacto'] === $telefono) {
                $pdo->rollBack();
                return 'sin_cambios';
            }

            $actualiza = $pdo->prepare(
                'UPDATE incidencias SET descripcion = :descripcion, direccion = :direccion,
                 telefono_contacto = :telefono WHERE id = :id'
            );
            $actualiza->execute([
                'descripcion' => $descripcion, 'direccion' => $direccion,
                'telefono' => $telefono, 'id' => $incidenciaId,
            ]);
            // Es un evento informativo: permanece Pendiente, pero queda trazabilidad.
            $historial = $pdo->prepare(
                'INSERT INTO historial_estados (incidencia_id, estado_anterior, estado_nuevo, actor_id, observacion)
                 VALUES (:id, :anterior, :nuevo, :actor, :nota)'
            );
            $historial->execute([
                'id' => $incidenciaId, 'anterior' => 'Pendiente', 'nuevo' => 'Pendiente',
                'actor' => $clienteId, 'nota' => 'Datos de contacto y avería actualizados por el cliente',
            ]);
            $pdo->commit();
            return 'actualizada';
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $ex;
        }
    }

    // FOR UPDATE evita que dos peticiones cancelen simultáneamente la misma cita.
    public static function cancelarCliente(int $incidenciaId, int $clienteId, DateTimeImmutable $ahora): string
    {
        $pdo = Conexion::abrir();
        $pdo->beginTransaction();
        try {
            $consulta = $pdo->prepare(
                'SELECT estado, fecha_servicio FROM incidencias
                 WHERE id = :id AND cliente_id = :cliente FOR UPDATE'
            );
            $consulta->execute(['id' => $incidenciaId, 'cliente' => $clienteId]);
            $fila = $consulta->fetch();
            if ($fila === false) {
                $pdo->rollBack();
                return 'no_encontrada';
            }
            if (!in_array($fila['estado'], ['Pendiente', 'Asignada'], true)) {
                $pdo->rollBack();
                return 'estado_no_permitido';
            }
            $cita = new DateTimeImmutable($fila['fecha_servicio'], new DateTimeZone('Europe/Madrid'));
            if ($cita->getTimestamp() - $ahora->getTimestamp() <= 48 * 3600) {
                $pdo->rollBack();
                return 'fuera_de_plazo';
            }
            $actualiza = $pdo->prepare('UPDATE incidencias SET estado = :nuevo WHERE id = :id');
            $actualiza->execute(['nuevo' => 'Cancelada', 'id' => $incidenciaId]);
            $historial = $pdo->prepare(
                'INSERT INTO historial_estados (incidencia_id, estado_anterior, estado_nuevo, actor_id, observacion)
                 VALUES (:id, :anterior, :nuevo, :actor, :nota)'
            );
            $historial->execute([
                'id' => $incidenciaId, 'anterior' => $fila['estado'], 'nuevo' => 'Cancelada',
                'actor' => $clienteId, 'nota' => 'Cancelada por el cliente',
            ]);
            $pdo->commit();
            return 'cancelada';
        } catch (Throwable $ex) {
            $pdo->rollBack();
            throw $ex;
        }
    }
}
