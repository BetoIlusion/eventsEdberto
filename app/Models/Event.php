<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'hora_inicial',
        'hora_final',
        'fecha',
        'activo',
        'sala_id',
    ];

    protected $casts = [
        'hora_inicial' => 'datetime:H:i',
        'hora_final'   => 'datetime:H:i',
        'fecha'        => 'date:Y-m-d',
        'activo'       => 'boolean',
    ];

    public function existeSuperPosicion()
    {
        return Event::where('fecha', $this->fecha)
            ->where('hora_inicial', '<', $this->hora_final)
            ->where('hora_final', '>', $this->hora_inicial)
            ->where('activo', true)
            ->where('sala_id', $this->sala_id)
            ->exists();
    }
    public function eventoSuperpuesto()
    {
        return Event::where('fecha', $this->fecha)
            ->where('hora_inicial', '<', $this->hora_final)
            ->where('hora_final', '>', $this->hora_inicial)
            ->where('activo', true)
            ->where('sala_id', $this->sala_id)
            ->first();
    }
    public function crearEvento($sala_id)
    {
        self::create([
            'nombre' => $this->nombre,
            'hora_inicial' => $this->hora_inicial,
            'hora_final' => $this->hora_final,
            'fecha' => $this->fecha,
            'sala_id' => $sala_id,
        ]);
    }
    public function existeSalaDisponible()
    {
        $room = Room::where('ocupado', false)->first();
        if ($room)
            return true;
        return false;
    }
    // Relación con la sala
    public function sala()
    {
        return $this->belongsTo(Room::class, 'sala_id');
    }
    public function showXnombre($nombre)
    {
        return self::where('nombre', $nombre)->first();
    }

    public function crearOpcion2()
    {
        $salas = Room::where('existe', true)->get();

        foreach ($salas as $sala) {
            $existe = Event::where('sala_id', $sala->id)
                ->where('fecha', $this->fecha)
                ->where('hora_inicial', '<', $this->hora_final)
                ->where('hora_final', '>', $this->hora_inicial)
                ->where('activo', true)
                ->exists();

            if (!$existe) {
                Event::create([
                    'nombre' => $this->nombre,
                    'hora_inicial' => $this->hora_inicial,
                    'hora_final' => $this->hora_final,
                    'fecha' => $this->fecha,
                    'sala_id' => $sala->id,
                    'activo' => true,
                ]);
                return true;
            }
        }
        return false;
    }
    public function consultarActivosEnRango($hora_inicio, $hora_final, $fecha)
    {
        return self::with('sala')
            ->where('fecha', $fecha)
            ->where('hora_inicial', '<', $hora_final)
            ->where('hora_final', '>', $hora_inicio)
            ->where('activo', true)
            ->get();
    }
    public function generarReporteOcupacion($fecha)
    {
        $jornadaTotalMinutos = 12 * 60; // 08:00 a 20:00

        $salas = Room::where('existe', true)->get();

        $resultado = [];

        foreach ($salas as $sala) {
            $eventos = self::where('fecha', $fecha)
                ->where('sala_id', $sala->id)
                ->where('activo', true)
                ->orderBy('hora_inicial')
                ->get();

            $totalMinutos = 0;
            $franjas = [];

            foreach ($eventos as $evento) {
                $inicio = strtotime($evento->hora_inicial);
                $fin = strtotime($evento->hora_final);
                $minutos = ($fin - $inicio) / 60;
                $totalMinutos += $minutos;

                $franjas[] = [
                    'inicio' => date('H:i', $inicio),
                    'fin' => date('H:i', $fin),
                ];
            }

            $porcentaje = $jornadaTotalMinutos > 0
                ? round(($totalMinutos / $jornadaTotalMinutos) * 100, 1)
                : 0;

            $resultado[] = [
                'sala' => $sala->name,
                'eventos' => count($eventos),
                'total_horas_ocupadas' => round($totalMinutos / 60, 2),
                'porcentaje_ocupacion' => "{$porcentaje}%",
                'franjas' => $franjas
            ];
        }

        return $resultado;
    }
}
