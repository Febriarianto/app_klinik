<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;
    public $table = 'Pasien';

    protected $fillable = [
        'nomor_rm',
        'nama',
        'no_tlp',
        'alamat',
        'jenis_kelamin',
    ];
}
