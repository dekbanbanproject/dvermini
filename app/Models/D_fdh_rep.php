<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class D_fdh_rep extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    // protected $connection = 'mysql2';
    protected $table = 'd_fdh_rep';
    protected $primaryKey = 'd_fdh_rep_id';
    protected $fillable = [  
        'A',
        'B',  
        'C', 
        'D',
        'E',
        'F',
        'G',
        'H',
        
    ];

  
}