<?php

namespace App\Enums;

enum EstadoCita: string
{
    case Pendiente = 'pendiente';
    case Confirmada = 'confirmada';
    case Cancelada = 'cancelada';
    case Atendida = 'atendida';

    public static function activos(): array
    {
        return [self::Pendiente->value, self::Confirmada->value];
    }

    public function esTerminal(): bool
    {
        return in_array($this, [self::Cancelada, self::Atendida], true);
    }

    public function permite(EstadoCita $destino): bool
    {
        if ($this === $destino) {
            return true;
        }

        return match ($this) {
            self::Pendiente => in_array($destino, [self::Confirmada, self::Cancelada], true),
            self::Confirmada => in_array($destino, [self::Atendida, self::Cancelada], true),
            self::Cancelada, self::Atendida => false,
        };
    }
}
