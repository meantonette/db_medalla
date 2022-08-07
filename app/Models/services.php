<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class services extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $dates = ["deleted_at"]; 

    protected $table = "services"; 

    protected $primaryKey = "id";

    protected $guarded = ["id"]; 

    public static $rules = [  
                    'name'=>'required|regex:/^[a-zA-Z\s]*$/',
                    'description'=>'required|regex:/^[a-zA-Z\s]*$/',
                    'price'=>'required|numeric',
                    'img_path' => 'mimes:jpeg,png,jpg,gif,svg'
             
];
                    
    public static $messages = [
            'required' => 'This is a required field',
            'min' => 'Text is too small',
            'alpha' => 'Letters only',
           
        ];
}
