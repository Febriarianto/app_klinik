<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    use HasFactory;

    public $table = 'Dokter';

    protected $fillable = [
        'nama',
        'spesialis',
        'alamat',
        'no_tlp',
    ];
}
