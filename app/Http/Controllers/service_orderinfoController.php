<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\service_orderinfo;
use App\Models\orderline;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\View; 

use App\Models\services;
use App\Models\employees;
use App\Cart;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\animals;
use App\Models\customers;
use Illuminate\Support\Facades\Redirect;

class service_orderinfoController extends Controller
{
    //
    public function Index()
    {
        $services = services::all();
        $animals = animals::all();

        return view('shop.index', [ 'services' => $services,'animals' => $animals,]);
    }

    public function edit($id)
    {
        //
        $service_orderinfo = service_orderinfo::find($id);
        $employees = employees::pluck('name', 'id');
	    return View::make('shop.edit',compact('service_orderinfo', 'employees'));
    }

    public function update(Request $request, $id)
    {

        $success = false; //flag
        DB::beginTransaction();
        try {
           
        $service_orderinfo = service_orderinfo::find($id);
        $service_orderinfo->employee_id = $request->input("id");
        $service_orderinfo->schedule = $request->input("schedule");
        $service_orderinfo->status = $request->input("Status");

          
        $service_orderinfo->update();
    
            $success = true;
            if ($success) {
                DB::commit();
            }
    
        } catch (\Exception $e) {
            DB::rollback();
            $success = false;
            return ["error" => $e->getMessage()];
        }
    
        // return ["success" => "Data Inserted"];

         return redirect()->route('shop.orders')->with('SUCCESS!', 'Order added!');
    }

    public function show()
    {

        $service_orderinfo = DB::table('service_orderinfo')
        ->leftJoin('service_orderline','service_orderinfo.id','=','service_orderline.service_orderinfo_id')
        ->leftjoin('employees','service_orderinfo.employee_id','employees.id')
        ->leftJoin('animals','animals.id','=','service_orderline.animal_id')
        ->leftJoin('services','services.id','=','service_orderline.service_id')

        ->select('service_orderinfo.id', 'animals.petName', 'services.servname', 'employees.name','service_orderinfo.schedule', 'service_orderinfo.status', 'service_orderinfo.deleted_at')
        ->get();
     
        return view('shop.orders', ['service_orderinfo' => $service_orderinfo]);

    
    }

    // public function orders($id)
    // {

    //     $servorderinfo = DB::table('servorderinfo')
    //     ->leftjoin('employees','servorderinfo.employee_id','employees.id')
    //     ->select('servorderinfo.id', 'employees.name','servorderinfo.schedule', 'servorderinfo.status')
    //     ->where('servorderinfo.id', $id)
    //     ->get();
     
    //     return view('servorderinfo.orders', ['servorderinfo' => $servorderinfo]);

    // }

    public function destroy($id)
    {
        service_orderinfo::destroy($id);
        return Redirect::to('/show')->with('SUCCESS!','Order deleted!');
    }

    public function restore($id)
    {
        service_orderinfo::onlyTrashed()->findOrFail($id)->restore(); 
        return  Redirect::route('shop.show')->with('SUCCESS','Order restored successfully!');
    }

    public function getCart()
    {
        if (!Session::has('cart')) {
            return view('shop.availservices');
        }
        // DB::commit();
        // Session::forget('cart');
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        //dd($oldCart);
        return view('shop.availservices', ['services' => $cart->services, 'totalPrice' => $cart->totalPrice, 'pets' => $cart->pets]);
    }

    public function getAddToCart(Request $request, $id)
    {
     
        $serv = services::find($id);
        // $oldCart = Session::has('cart') ? Session::get('cart'): null;
        $oldCart = Session::has('cart') ? $request->session()->get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->add($serv, $serv->id);
        $request->session()->put('cart', $cart);
        Session::put('cart', $cart);
        $request->session()->save();
        // $request->save();
        // dd(Session::all());
    }

    public function getPet(Request $request, $id)
    {
        $pets = animals::find($id);
        $oldCart = Session::has('cart') ? $request->session()->get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->addPet($pets, $pets->id);
        $request->session()->put('cart', $cart);
        Session::put('cart', $cart);
        $request->session()->save();
        // dd(Session::all());
    }

    public function getRemoveItem($id)
    {
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);
        $cart->removeService($id); 
        if (count($cart->services) > 0) {
            Session::put('cart', $cart);
            //session::save();
        } else {
            Session::forget('cart');
        }
        return redirect()->route('shop.availservices');
    }

    public function removeService($id)
    {
        $this->totalPrice -= $this->services[$id]['price'];
        unset($this->services[$id]);
       // unset($this->pets[$id]);
    }

    public function postCheckout(Request $request)
    {
        if (!Session::has('cart')) {
            return redirect()->route('shop.index');
        }
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        try {
            DB::beginTransaction();
            $order = new service_orderinfo();
            $employ =  employees::where('id', Auth::id())->first();
            $order->employee_id = $employ->id;
            $order->schedule = now();
            $order->status = 'pending';
            $order->save();

            foreach ($cart->services as $services) {
                foreach ($cart->pets as $pets) {
                    $id = $services['services']['id'];
                    $animal_id = $pets['animals']['id'];
                    DB::table('service_orderline')->insert(
                        [
                            'service_id' => $id,
                            'animal_id' => $animal_id,
                            'service_orderinfo_id' => $order->id, 
                            ]
                    );
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('shop.availservices')->with('error', $e->getMessage());
        }
        DB::commit();
        Session::forget('cart');
        return redirect()->route('home');
    }

    public function Receipt(Request $request)
    {
       
        if (!Session::has('cart')) {
            return view('shop.availservices');
        }
        // DB::commit();
        // Session::forget('cart');
        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        //dd($oldCart);
        return view('shop.receipt', ['services' => $cart->services, 'totalPrice' => $cart->totalPrice, 'pets' => $cart->pets]);

    }

}
