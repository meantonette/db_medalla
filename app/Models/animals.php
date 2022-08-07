<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class animals extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dates = ["deleted_at"]; 

    protected $fillable = ['petName', 'Age', 'Type', 'Breed', 'Color', 'img_path','customer_id'];

    protected $table = "animals"; 

    protected $primaryKey = "id";

    protected $guarded = ["id"]; 

    public static $rules = [  
        'petName' =>'required|regex:/^[a-zA-Z\s]*$/', 
        'Age'=>'required|numeric',
        'Type'=>'required|regex:/^[a-zA-Z\s]*$/',
        'Breed'=>'required|regex:/^[a-zA-Z\s]*$/',
        'Sex'=>'required|regex:/^[a-zA-Z\s]*$/',
        'Color'=>'required|regex:/^[a-zA-Z\s]*$/',
        'customers_id'=>'required|numeric',
        'img_path' => 'mimes:jpeg,png,jpg,gif,svg'
];
                      
    public static $messages = [
            'required' => 'This is a required field',
            'min' => 'Text is too small',
            'alpha' => 'Letters only',
            'numeric' => 'Number only',
           
        ];
}
