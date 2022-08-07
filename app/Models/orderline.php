<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orderline extends Model
{
    use HasFactory;
    protected $table = 'servorderline';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['service_id','animal_id','id'];
}
