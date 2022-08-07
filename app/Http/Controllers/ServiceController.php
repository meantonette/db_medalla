<?php

namespace App\Http\Controllers;

use App\Models\services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View; 
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        //
      
        $services = DB::table('services')
   
        ->select('services.id','services.servname', 'services.img_path')
        ->get();

        $services = services::withTrashed()->orderBy('id','DESC')->paginate(5); 
        return view("services.index", ["services" => $services]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return View::make('services.create');
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
            'servname'=>'required|regex:/^[a-zA-Z\s]*$/',
            'description'=>'required|regex:/^[a-zA-Z\s]*$/',
            'price'=>'required|numeric',
            'img_path' => 'mimes:jpeg,png,jpg,gif,svg'
]);

        $service = new services();
        $service->servname = $request->input("servname");
        $service->description = $request->input("description");
        $service->price = $request->input("price");
      
        if ($request->hasfile("img_path")) {
            $file = $request->file("img_path");
                $file = $request->file("img_path");
                $filename = $file->getClientOriginalName();
                $file->move('images/services/', $filename);
                $service->img_path = $filename;

        }

        $service->save();
       
        return Redirect::to('services')->with('SUCCESS!','New service added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\services  $services
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
      
        $services = DB::table('services')
        ->select('services.id', 'services.servname','services.description','services.price', 'services.img_path')
        ->where('services.id', $id)
        ->get();
      
        return view('services.show', ['services' => $services]);
    }

    public function viewComment($id)
    {
        //
        $services = DB::table('services')
        ->rightJoin('comments','comments.service_id','services.id')
        ->select('comments.id', 'comments.service_id','services.servname', 'comments.guestName', 'comments.gEmail', 'comments.cellnum','comments.gcomment')
        ->where('services.id', $id)
        ->get();
   
        return view('comments.index', ['services' => $services]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\services  $services
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $services = services::find($id); 
        return view('services.edit',compact('services'));  

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\services  $services
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $service = services::find($id);
        $service->servname = $request->input("servname");
        $service->description = $request->input("description");
        $service->price = $request->input("price");

        if ($request->hasfile("img_path")) {
            $destination = "images/services/" . $service->img_path;
            if (File::exists($destination)) {
                File::delete($destination);
            }
            
                $file = $request->file("img_path");
                $filename = $file->getClientOriginalName();
                $file->move("images/services/", $filename);
                $service->img_path = $filename;
        }

        $service->update();
        return Redirect::to('services')->with('SUCCESS!','Current service updated!');
     
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\services  $services
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        services::destroy($id);
        return Redirect::to('/services')->with('SUCCESS!','service deleted!');

    }

    public function restore($id) {

        services::onlyTrashed()->findOrFail($id)->restore(); 
        return  Redirect::route('services.index')->with('SUCCESS','service restored successfully!');
    
    }
        //OnlyTrashed - dinelete lang makikita 
        //WithTrashed = Kasama dinelete at di dineelte makikita 
        //WithoutTrashed = Yung di dinelete lang makikita

    public function forceDelete($id)
    { 
       
        services::withTrashed()
            ->findOrFail($id)
            ->forceDelete(); 
        return Redirect::route("services.index")->with("SUCCESS!", "Service Permanently Deleted!");
        
    }

}
