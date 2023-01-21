<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perawatan extends Model
{
    use HasFactory;

    public $table = 'perawatan';

    protected $fillable = [
        'nama',
        'harga',
    ];
}
