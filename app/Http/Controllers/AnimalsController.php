<?php

namespace App\Http\Controllers;

use App\Models\animals;
use App\Models\customers;
use App\Models\employees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class AnimalsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //       
   
        $animals = DB::table('animals')
        ->leftjoin('customers','animals.customer_id','customers.id')
        ->select('animals.id','animals.petName', 'animals.img_path')
        ->get();
        $animals = animals::withTrashed()->orderBy('id','DESC')->paginate(5); 
        return view('animals.index', ['animals' => $animals]);

        // $animals = animals::all();
        // $customers = customers::pluck('firstName', 'customer_id');
	    // return View::make('animals.index',compact('animals', 'customers'));
        // $animals = DB::table('animals')
        // ->join('customers','animals.customers_id','customers.customer_id')
        // // ->select('animals.animal_id','animals.Name', 'animals.Age', 'animals.Type', 'animals.Breed', 'animals.Sex','animals.Color', 'animals.img_path', 'customers.customer_id', 'customers.LName', 'customers.FName')
        // ->get();
        // $animals = animals::withTrashed()->orderBy('animal_id','ASC')->paginate(5); 
    
        // return view('animals.index', ['animals' => $animals]);

      
      
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    
        $customers = customers::pluck('firstName', 'id');
        return View::make('animals.create',['customers' => $customers]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'petName' =>'required|regex:/^[a-zA-Z\s]*$/', 
            'Age'=>'required|numeric',
            'Type'=>'required|regex:/^[a-zA-Z\s]*$/',
            'Breed'=>'required|regex:/^[a-zA-Z\s]*$/',
            'Sex'=>'required|regex:/^[a-zA-Z\s]*$/',
            'Color'=>'required|regex:/^[a-zA-Z\s]*$/',
            'id'=>'required|numeric',
            'img_path' => 'mimes:jpeg,png,jpg,gif,svg'
]);

        $animal = new animals();
        $animal->petName = $request->input("petName");
        $animal->Age = $request->input("Age");
        $animal->Type = $request->input("Type");
        $animal->Breed = $request->input("Breed");
        $animal->Sex = $request->input("Sex");
        $animal->Color = $request->input("Color");
      
        if ($request->hasfile("img_path")) {
            $file = $request->file("img_path");
                $file = $request->file("img_path");
                $filename = $file->getClientOriginalName();
                $file->move('images/animals/', $filename);
                $animal->img_path = $filename;

        }

        $animal->customer_id = $request->input("id");

        $animal->save();
       
        return Redirect::to('animals')->with('SUCCESS!','New animal added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\animals  $animals
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        // $animals = animals::find($id);
        // $animals = DB::table('animals')
        // ->leftJoin('customers','animals.customers_id','=','customers.customer_id')
        // ->select('animals.animal_id','animals.petName', 'animals.Age', 'animals.Type', 'animals.Breed', 'animals.Sex','animals.Color', 'animals.img_path', 'animals.customers_id','animals.created_at', 'animals.updated_at', 'animals.deleted_at', 'customers.customer_id', 'customers.firstName')
        // ->get();
        // return View::make('animals.show', ['animals' => $animals]);

        // $animals = animals::all();
        // return View::make('animals.index',compact('animals'));

        $animals = DB::table('animals')
        ->leftjoin('customers','animals.customer_id','customers.id')
        ->select('animals.id', 'animals.petName','animals.Age', 'animals.Type', 'animals.Breed', 'animals.Sex','animals.Color', 'animals.img_path', 'customers.id', 'customers.lastName', 'customers.firstName')
        ->where('animals.id', $id)
        ->get();
        // $animals = animals::withTrashed()->orderBy('animal_id','ASC')->paginate(5); 
    
        return view('animals.show', ['animals' => $animals]);

        // $animals = animals::all();
        // $customers = customers::pluck('firstName', 'customer_id');
	    // return View::make('animals.show',compact('animals', 'customers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\animals  $animals
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $animals = animals::find($id);
        $customers = customers::pluck('firstName', 'id');
	    return View::make('animals.edit',compact('animals', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\animals  $animals
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'petName' =>'required|regex:/^[a-zA-Z\s]*$/', 
            'Age'=>'required|numeric',
            'Type'=>'required|regex:/^[a-zA-Z\s]*$/',
            'Breed'=>'required|regex:/^[a-zA-Z\s]*$/',
            'Sex'=>'required|regex:/^[a-zA-Z\s]*$/',
            'Color'=>'required|regex:/^[a-zA-Z\s]*$/',
            'id'=>'required|numeric',
            'img_path' => 'mimes:jpeg,png,jpg,gif,svg'
]);

        $animal = animals::find($id);
        $animal->petName = $request->input("petName");
        $animal->Age = $request->input("Age");
        $animal->Type = $request->input("Type");
        $animal->Breed = $request->input("Breed");
        $animal->Sex = $request->input("Sex");
        $animal->Color = $request->input("Color");
        $animal->customer_id = $request->input("id");
      
        if ($request->hasfile("img_path")) {
            $file = $request->file("img_path");
                $file = $request->file("img_path");
                $filename = $file->getClientOriginalName();
                $file->move('images/animals/', $filename);
                $animal->img_path = $filename;

        }

        $animal->update();
       
        return Redirect::to('animals')->with('SUCCESS!','New animal added!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\animals  $animals
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        animals::destroy($id);
        return Redirect::to('/animals')->with('SUCCESS!','Animal deleted!');
    }

    public function restore($id)
    {
        animals::onlyTrashed()->findOrFail($id)->restore(); 
        return  Redirect::route('animals.index')->with('SUCCESS','Animal restored successfully!');
    }

    public function forceDelete($animal_id)
    {

        animals::withTrashed()
        ->findOrFail($animal_id)
        ->forceDelete(); 
         return Redirect::route("animals.index")->with("SUCCESS!", "Pet Permanently Deleted!");

        // $animals = animals::findOrFail($animal_id);
        // $destination = "images/animals/" . $animals->img_path;
        // if (File::exists($destination)) {
        //     File::delete($destination);
        // }
        // $animals->forceDelete();
        // return Redirect::route("animals.index")->with("SUCCESS!", "Animal information Permanently Deleted!");
    }

}   
