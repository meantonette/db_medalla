<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes; 

class employees extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'position',
        'address',
        'phonenum',
        'email',
        'password',
    ];

    protected $table = "employees"; 

    protected $primaryKey = "id";

    protected $guarded = ["id"];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static $rules = [  
        'name' =>'required|regex:/^[a-zA-Z\s]*$/', 
        'position'=>'required|regex:/^[a-zA-Z\s]*$/',
        'address'=>'required|regex:/^[a-zA-Z\s]*$/',
        'phonenum'=>'required|numeric',
        'img_path' => 'mimes:jpeg,png,jpg,gif,svg',

];
        
public static $messages = [
'required' => 'This is a required field',
'min' => 'Text is too small',
'alpha' => 'Letters only',
'numeric' => 'Number only',

];

}
