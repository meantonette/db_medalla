<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use App\Models\customers;

class CustomerController extends Controller
{
    //
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = DB::table('customers')
        ->leftJoin('animals','customers.id','=','animals.customer_id')
        ->select('customers.id','customers.title', 'customers.lastName','customers.img_path', 'animals.petName')
        ->get();
        $customers = customers::withTrashed()->orderBy('id','DESC')->paginate(5); 
      
        return View::make('customers.index',compact('customers'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return View::make('customers.create');
        
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
            'title' =>'required|regex:/^[a-zA-Z\s]*$/', 
            'firstName'=>'required|regex:/^[a-zA-Z\s]*$/',
            'lastName'=>'required|regex:/^[a-zA-Z\s]*$/',
            'age'=>'required|numeric',
            'address'=>'required|regex:/^[a-zA-Z\s]*$/',
            'sex'=>'required|regex:/^[a-zA-Z\s]*$/',
            'phoneNumber'=>'required|numeric',
            'img_path' => 'mimes:jpeg,png,jpg,gif,svg'
]);

        $customer = new customers();
        $customer->title = $request->input("title");
        $customer->firstName = $request->input("firstName");
        $customer->lastName = $request->input("lastName");
        $customer->age = $request->input("age");
        $customer->address = $request->input("address");
        $customer->sex = $request->input("sex");
        $customer->phoneNumber = $request->input("phoneNumber");

        if ($request->hasfile("img_path")) {
            $file = $request->file("img_path");
            $filename = $file->getClientOriginalName();
            $file->move('images/customers/', $filename);
            $customer->img_path = $filename;
        }

        $customer->save();
        // return Redirect::to("customer")->withSuccessMessage("New customer Added!");
        // $request->validate([
        //     'img_path' => 'mimes:jpeg,png,jpg,gif,svg',
        // ]);

        // if($file = $request->hasFile('img_path')) {
            
        //     $file = $request->file('img_path') ;
        //     $fileName = uniqid().'_'.$file->getClientOriginalName();
        //     // $fileName = $file->getClientOriginalName();
        //     // dd($fileName);
        //     $request->img_path->storeAs('customersimg', $fileName, 'public');
        //     //

        //     $input['img_path'] = 'customersimg/'.$fileName;
        //     $customer = customers::create($input);
        //     // $file->move($destinationPath,$fileName);
        // }

        // $customer->save();
        return Redirect::to('customers')->with('SUCCESS!','New customer added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

        $customers = DB::table('customers')
        ->leftJoin('animals','animals.customer_id','customers.id')
        ->select('customers.id', 'customers.title','customers.firstName', 'customers.lastName', 'customers.age', 'customers.address','customers.sex', 'customers.img_path', 'animals.petName','customers.phoneNumber')
        ->where('customers.id', $id)
        ->get();
   
        return view('customers.show', ['customers' => $customers]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $customers = customers::find($id);
        
	    return View::make('customers.edit',compact('customers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' =>'required|alpha_num|min:2', 
            'firstName'=>'required|alpha',
            'lastName'=>'required|alpha',
            'age'=>'required|numeric',
            'address'=>'required|alpha_num',
            'sex'=>'required|alpha',
            'phoneNumber'=>'required|numeric',
            'img_path' => 'mimes:jpeg,png,jpg,gif,svg'
]);

        $customer = customers::find($id);
        $customer->title = $request->input("title");
        $customer->firstName = $request->input("firstName");
        $customer->lastName = $request->input("lastName");
        $customer->age = $request->input("age");
        $customer->address = $request->input("address");
        $customer->sex = $request->input("sex");
        $customer->phoneNumber = $request->input("phoneNumber");

        if ($request->hasfile("img_path")) {
            $destination = "images/customers/" . $customer->img_path;
            if (File::exists($destination)) {
                File::delete($destination);
            }
            $file = $request->file("img_path");
            $filename = $file->getClientOriginalName();
            $file->move("images/customers/", $filename);
            $customer->img_path = $filename;

        }

        $customer->update();
        return Redirect::to('customers')->withSuccessMessage("New customer Updated!");
  
        //$customers = customers::find($id);
        //$customers->update($request->all());
        //return Redirect::to('/customers')->with('SUCCESS!','customer updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\customers  $customers
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        customers::destroy($id);
        return Redirect::to('/customers')->with('SUCCESS!','Customer deleted!');
    }

    public function restore($id)
    {
        customers::onlyTrashed()->findOrFail($id)->restore(); 
        return  Redirect::route('customers.index')->with('SUCCESS','Customer restored successfully!');
    }

    public function forceDelete($customer_id)
    {

        customers::withTrashed()
        ->findOrFail($customer_id)
        ->forceDelete(); 
         return Redirect::route("customers.index")->with("SUCCESS!", "Customer Permanently Deleted!");


        // $customers = customers::findOrFail($customer_id);
        // $destination = "images/customers/" . $customers->img_path;
        // if (File::exists($destination)) 
        // {
        //     File::delete($destination);
        // }

        // $customers->forceDelete();
        // return Redirect::route("customers.index")->with("SUCCESS!", "Customer information Permanently Deleted!");

        // customers::withTrashed()
        // ->findOrFail($customer_id)
        // ->forceDelete(); 
        //  return Redirect::route("customers.index")->with("success","customer Permanently Deleted!");
    }

    public function search(Request $request){
        $search_text = $request->get('query');
      
        $customers = DB::table('customers')
        ->rightJoin("animals","animals.customer_id", "=","customers.id")
        ->rightjoin("servorderline", "servorderline.animal_id", "=", "animals.id")
        ->leftjoin("services", "services.id","=", "servorderline.service_id")
        ->leftjoin("servorderinfo","servorderinfo.id","=", "servorderline.servorderinfo_id")
        ->select("customers.firstName", "customers.lastName", "animals.petName","services.servname", "services.price", "servorderinfo.id", "servorderinfo.schedule", "customers.deleted_at")
        ->orderBy("servorderinfo.id", "DESC")
        ->where('lastName', 'LIKE', '%' .$search_text.'%') 
        ->get();

        return View::make('customers.search',compact('customers'));
      }



}
