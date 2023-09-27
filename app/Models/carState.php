<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class carState extends Model
{
    use HasFactory;

    protected $fillable = ['locked_state'];
}