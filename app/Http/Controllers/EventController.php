<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class EventController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (Event::where('nombre', $value)->where('activo', true)->exists()) {
                        $fail('Este nombre ya ha sido registrado para un evento activo, inserte otro nombre.');
                    }
                }
            ],
            'sala_id' => 'required|integer',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_final' => 'required|date_format:H:i|after:hora_inicio',
            'fecha' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:today'
            ],
        ], [
            'fecha.after_or_equal' => 'Solo puede ingresar fecha presente o futura'
        ]);

        if ($validator->fails()) {
            Log::warning('Validación fallida', $validator->errors()->toArray());

            return response()->json([
                'message' => 'Verifica los datos ingresados.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $Evento = new Event([
            'nombre' => $request->nombre,
            'hora_inicial' => $request->hora_inicio,
            'hora_final' => $request->hora_final,
            'fecha' => $request->fecha,
            'sala_id' => $request->sala_id
        ]);

        if (!$Evento->existeSuperPosicion($request->sala_id)) {

            $Evento->crearEvento($request->sala_id);

            return response()->json([
                'message' => 'Evento validado correctamente.',
            ], 201);
        } else {
            $superpuesto = $Evento->eventoSuperpuesto();
            Log::warning('Evento superpuesto detectado', [
                'nombre' => $superpuesto->nombre ?? null,
                'hora_inicial' => $superpuesto->hora_inicial ?? null,
                'hora_final' => $superpuesto->hora_final ?? null,
            ]);

            return response()->json([
                'message' => 'Rechazado por superposición del horario',
                'evento_superpuesto' => [
                    'nombre' => $superpuesto->nombre ?? null,
                    'hora_inicial' => $superpuesto->hora_inicial ?? null,
                    'hora_final' => $superpuesto->hora_final ?? null,
                ]
            ], 409);
        }
        return response()->json([
            'message' => 'Evento validado correctamente.',
        ], 201);
    }

    public function crearOpcion2(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|unique:events,nombre',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_final' => 'required|date_format:H:i|after:hora_inicio',
            'fecha' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:today'
            ],
        ], [
            'nombre.unique' => 'Este nombre ya ha sido registrado, inserte otro nombre.',
            'fecha.after_or_equal' => 'Solo puede ingresar fecha presente o futura'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Verifica los datos ingresados.',
                'errors' => $validator->errors(),
            ], 422);
        }
        $Evento = new Event([
            'nombre' => $request->nombre,
            'hora_inicial' => $request->hora_inicio,
            'hora_final' => $request->hora_final,
            'fecha' => $request->fecha,
        ]);

        $exito = $Evento->crearOpcion2();

        if ($exito) {
            return response()->json(['message' => 'Evento creado en una sala automáticamente.'], 201);
        } else {
            return response()->json(['message' => 'No hay ninguna sala disponible para ese horario.'], 409);
        }
    }
    public function eventosActivos(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date_format:Y-m-d',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_final' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Verifica los datos ingresados.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $eventos = Event::with('sala')
            ->where('fecha', $request->fecha)
            ->where('hora_inicial', '<', $request->hora_final)
            ->where('hora_final', '>', $request->hora_inicio)
            ->where('activo', true)
            ->get();

        return response()->json($eventos, 200);
    }
    public function cancelEventXnombre(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|exists:events,nombre',
        ], [
            'nombre.exists' => 'No se encontró un evento con ese nombre.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Verifica los datos ingresados.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $evento = Event::where('nombre', $request->nombre)->first();

        if (!$evento->activo) {
            return response()->json([
                'message' => 'El evento ya está cancelado.',
            ], 409);
        }

        $evento->activo = false;
        $evento->save();

        return response()->json([
            'message' => 'Evento cancelado correctamente.',
            'evento' => [
                'nombre' => $evento->nombre,
                'activo' => $evento->activo,
            ]
        ], 200);
    }
    public function eventActivos(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hora_inicio' => 'required|date_format:H:i',
            'hora_final' => 'required|date_format:H:i|after:hora_inicio',
            'fecha' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Verifica los datos ingresados.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $eventos = (new Event())->consultarActivosEnRango(
            $request->hora_inicio,
            $request->hora_final,
            $request->fecha
        );
        return response()->json($eventos, 200);
    }
    public function reporteOcupacion(Request $request)
{
    $validator = Validator::make($request->all(), [
        'fecha' => 'required|date_format:Y-m-d',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Verifica los datos ingresados.',
            'errors' => $validator->errors(),
        ], 422);
    }

    $reporte = (new Event())->generarReporteOcupacion($request->fecha);

    return response()->json([
        'fecha' => $request->fecha,
        'ocupacion' => $reporte
    ], 200);
}

}
