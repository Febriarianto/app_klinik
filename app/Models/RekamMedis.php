<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    use HasFactory;

    public $table = 'rekam_medis';

    protected $fillable = [
        'pasien_id',
        'keluhan',
        'dokter_id',
        'diagnosa',
        'perawatan',
        'keterangan',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id');
    }
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }
}
