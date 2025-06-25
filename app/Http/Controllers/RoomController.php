<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    public function index()
    {
        $Salas = Room::where('existe', true)->get();
        return response()->json($Salas);
    }
    public function store(Request $request)
    {
        $Sala = new Room();
        $Sala->createRoom();

        return response()->json('Sala creada');
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'error, ingresa valor entero.',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $sala = Room::find($id);
        if ($sala) {
            $sala->update([
            'name' => $request->input('nombre'),
            ]);
            return response()->json([
            'message' => 'Sala actualizada',
            'sala' => $sala
            ]);
        } else {
            return response()->json(['error' => 'Sala no encontrada'], 404);
        }
    }
}
