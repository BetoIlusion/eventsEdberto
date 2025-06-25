<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'existe'
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function createRoom()
    {
        $total = self::count();
        self::create([
            'name' => 'Room ' . ($total + 1),
        ]);
    }

    public function eventos()
    {
        return $this->hasMany(Event::class, 'id_sala');
    }

    public function dentroDelTiempo($hora_inicial, $hora_final){

    }
}
