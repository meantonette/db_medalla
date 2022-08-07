<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use App\Models\employees;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $employees = DB::table('employees')
   
        ->select('employees.id','employees.name','employees.img_path')
        ->get();

        $employees = employees::withTrashed()->orderBy('id','DESC')->paginate(5); 
        return View::make('employees.index', ['employees' => $employees]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
     
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $employees = DB::table('employees')
        ->select('employees.id', 'employees.name','employees.position', 'employees.address', 'employees.phonenum', 'employees.email', 'employees.img_path')
        ->where('id', $id)
        ->get();
      
        return view('employees.show', ['employees' => $employees]);
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $employees = employees::find($id);
	    return View::make('employees.edit',compact('employees'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
          $request->validate([
        'name' =>'required|regex:/^[a-zA-Z\s]*$/', 
        'position'=>'required|regex:/^[a-zA-Z\s]*$/',
        'address'=>'required|regex:/^[a-zA-Z\s]*$/',
        'phonenum'=>'required|numeric',
        'img_path' => 'mimes:jpeg,png,jpg,gif,svg',
        'email' => 'email| required| unique:users',
       

]);

        $employees = employees::find($id);
        $employees->name = $request->input("name");
        $employees->position = $request->input("position");
        $employees->address = $request->input("address");
        $employees->phonenum = $request->input("phonenum");
       // $employees->email = $request->input("email");
        // $employees->password = Hash::make($request->input("password"));
       // $employees->password = bcrypt($request->input('password'));
      
       if ($request->hasfile("img_path")) {
        $destination = "images/employees/" . $employees->img_path;
        if (File::exists($destination)) {
            File::delete($destination);
        }
        $file = $request->file("img_path");
        $filename = $file->getClientOriginalName();
        $file->move("images/employees/", $filename);
        $employees->img_path = $filename;

    }

       $employees->email = $request->input("email");
    //    $employees->password = bcrypt($request->input('password'));

        $employees->update();
        return Redirect::to('employees')->withSuccessMessage("New Employee Updated!");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        employees::destroy($id);
        return Redirect::to('/employees')->with('SUCCESS!','Employee deleted!');
    }

    public function restore($id)
    {
        employees::onlyTrashed()->findOrFail($id)->restore(); 
        return  Redirect::route('employees.index')->with('SUCCESS','Employee restored successfully!');
    }

    // public function forceDelete($employee_id)
    // {

    //     employees::withTrashed()
    //     ->findOrFail($employee_id)
    //     ->forceDelete(); 
    //      return Redirect::route("employees.index")->with("SUCCESS!", "Employee Permanently Deleted!");

    //     // $employees = employees::findOrFail($employee_id);
    //     // $destination = "images/employees/" . $employees->img_path;
    //     // if (File::exists($destination)) {
    //     //     File::delete($destination);
    //     // }
    //     // $employees->forceDelete();
    //     // return Redirect::route("employees.index")->with("SUCCESS!", "Employee information Permanently Deleted!");

    // }

    public function getSignup()
    {
        return view('employees.signup');
    }

    public function postSignup(Request $request)
    {
        $this->validate($request, [
            'name' =>'required|regex:/^[a-zA-Z\s]*$/', 
            'position'=>'required|regex:/^[a-zA-Z\s]*$/',
            'address'=>'required|regex:/^[a-zA-Z\s]*$/',
            'phonenum'=>'required|numeric',
            'img_path' => 'mimes:jpeg,png,jpg,gif,svg,JPEG,PNG,JPG,GIF,SVG',

            'email' => 'email| required| unique:users',
            'password' => 'required| min:4',
        ]);

        $employee = new employees();
            $employee->name = $request->input("name");
            $employee->position = $request->input("position");
            $employee->address = $request->input("address");
            $employee->phonenum = $request->input("phonenum");

            if ($request->hasfile("img_path")) {
                $file = $request->file("img_path");
                $filename = $file->getClientOriginalName();
                $file->move('images/employees/', $filename);
                $employee->img_path = $filename;
            }

            $employee->email = $request->input("email");
            $employee->password = bcrypt($request->input('password'));
          
            // 'name' =>  $request->name,
            // 'position' =>  $request->position,
            // 'address' =>  $request->address,
            // 'phonenum' =>  $request->phonenum,

            // 'email' => $request->input('email'),
            // 'password' => bcrypt($request->input('password'))
       
        $employee->save();
        Auth::login($employee);
        return redirect()->route('employees.profile');
    }

    public function getProfile()
    {
        return view('employees.profile');
    }

    public function getLogout()
    {
        Auth::logout();
        return redirect('/home');
    }

    public function getSignin()
    {
        return view('employees.signin');
    }

    public function postSignin(Request $request)
    {
        $this->validate($request, [
            'email' => 'email| required',
            'password' => 'required| min:4',
        ]);

        if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->password])) {
            return redirect()->route('employees.profile');
        } else {
            return redirect()->back();
        };
       
    }

    public function Email()
    {
        return view("password.email");
    }

    public function Reset()
    {
        return view("password.reset");
    }
}