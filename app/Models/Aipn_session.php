<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Aipn_session extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $connection = 'mysql';
    protected $table = 'aipn_session';
    protected $primaryKey = 'aipn_session_id';
    protected $fillable = [  
        'aipn_session_no',  
        'aipn_session_date',  
    ];

  
}
