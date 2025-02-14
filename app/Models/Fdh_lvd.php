<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Fdh_lvd extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $connection = 'mysql';
    protected $table = 'fdh_lvd';
    protected $primaryKey = 'fdh_lvd_id';
    protected $fillable = [
        'SEQLVD','AN','DATEOUT','TIMEOUT' 
    ];

  
}
