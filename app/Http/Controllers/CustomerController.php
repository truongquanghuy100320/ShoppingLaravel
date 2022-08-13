<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
Use Illuminate\Support\Facades\Redirect;
Use Session;
use App\Models\Customer;
session_start();
class CustomerController extends Controller
{
    public function AuthLogin(){
        $admin_id = Session::get('admin_id');
        if($admin_id){
            return Redirect::to('dashboard');
        }else{
            return Redirect::to('admin')->send();
        }
    }

    public  function  all_customer(){
        $this->AuthLogin();
        $data = request()->all();
        $customer =  Customer::orderBy('customerID', 'desc')->paginate(10);
        return view('Admin.customer.all_customer')->with(compact('customer'));
    }
    public function  delete_customer($customerID){
        $customer = Customer::find($customerID);
        $customer->delete();
        Session::put('message', 'Delete Customer Successfully !');
        return redirect()->back();
    }
}
