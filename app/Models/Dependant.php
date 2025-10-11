<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dependant extends Model
{
    /** @use HasFactory<\Database\Factories\DependantFactory> */
    use HasFactory;

    protected $table = 'dependant';
}
