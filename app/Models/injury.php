<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class injury extends Model
{
    use HasFactory;
    protected $fillable=['title','description'];
}
