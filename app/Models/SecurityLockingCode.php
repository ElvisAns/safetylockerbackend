<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityLockingCode extends Model
{
    use HasFactory;
    protected $table =  'security_locking_code';
}
