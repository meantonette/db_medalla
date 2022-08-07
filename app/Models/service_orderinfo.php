<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class service_orderinfo extends Model
{    

    use HasFactory;
    use SoftDeletes;

    protected $dates = ["deleted_at"]; 
    
    protected $table = 'service_orderinfo';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['employee_id','schedule', 'status'];
}
