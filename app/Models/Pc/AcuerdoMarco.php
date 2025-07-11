<?php

namespace App\Models\Pc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcuerdoMarco extends Model
{
    /** @use HasFactory<\Database\Factories\Pc\AcuerdoMarcoFactory> */
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'isActive',
    ];
}
