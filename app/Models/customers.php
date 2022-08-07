<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class customers extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $dates = ["deleted_at"]; 

    protected $fillable = ['title', 'firstName', 'lastName', 'age', 'address', 'sex', 'phoneNumber', 'img_path', 'created_at', 'updated_at','deleted_at'];

    protected $table = "customers"; 

    protected $primaryKey = "id";

    protected $guarded = ["id"]; 

    public static $rules = [  
                    'title' =>'required|regex:/^[a-zA-Z\s]*$/', //LOCAL RULES AND MESSAGE ONLY GOOD FOR ADOPTERS MODEL AND CONTROLLER
                    'firstName'=>'required|regex:/^[a-zA-Z\s]*$/',
                    'lastName'=>'required|regex:/^[a-zA-Z\s]*$/',
                    'age'=>'required|numeric',
                    'address'=>'required|regex:/^[a-zA-Z\s]*$/',
                    'sex'=>'required|regex:/^[a-zA-Z\s]*$/',
                    'phoneNumber'=>'required|numeric',
                    'img_path' => 'mimes:jpeg,png,jpg,gif,svg'
];
                    
    public static $messages = [
            'required' => 'This is a required field',
            'min' => 'Text is too small',
            'alpha' => 'Letters only',
            'numeric' => 'Number only',
           
        ];
}
