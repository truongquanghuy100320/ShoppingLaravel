<?php

namespace App\Http\Controllers;

use App\Models\Login;
use App\Models\Social;
use Illuminate\Http\Request;
Use DB;
use Socialite;
use App\Http\Requests;
Use Session;
use Mail;
use App\Models\Slider;
use App\Models\Post;
Use Illuminate\Support\Facades\Redirect;
session_start();
class HomeController extends Controller
{
    public function index(){

         //category post
        $category_post =  Post::orderBy('cate_post_id', 'desc')->get();
        $slider = Slider::orderBy('slider_id','DESC')->take(4)->get();
        $cate_product = DB::table('category')->where('categoryStatus','1')->orderby('categoryID', 'desc')->get();
        $brand_product = DB::table('brand')->where('brandStatus','1')->orderby('brandID', 'desc')->get();
        $supplier_product = DB::table('suppliers')->orderby('supplierID', 'desc')->get();

        $all_product = DB::table('product')->join('category','category.categoryID', '=', 'product.categoryID')
            ->join('brand','brand.brandID', '=', 'product.brandID')
            ->join('suppliers','suppliers.supplierID', '=', 'product.supplierID')
            ->orderby('product.productID', 'desc')->paginate(6);
        if(isset($_GET['sort_by'])) {
            $sort_by = $_GET['sort_by'];
             if($sort_by == 'tang_dan') {
                 $all_product = DB::table('product')->join('category', 'category.categoryID', '=', 'product.categoryID')
                     ->join('brand', 'brand.brandID', '=', 'product.brandID')
                     ->join('suppliers', 'suppliers.supplierID', '=', 'product.supplierID')
                     ->orderby('product.Price', 'asc')->paginate(12)->appends(Request()->query());
             }
                else if($sort_by == 'giam_dan') {
                    $all_product = DB::table('product')->join('category', 'category.categoryID', '=', 'product.categoryID')
                        ->join('brand', 'brand.brandID', '=', 'product.brandID')
                        ->join('suppliers', 'suppliers.supplierID', '=', 'product.supplierID')
                        ->orderby('product.Price', 'desc')->paginate(12)->appends(Request()->query());
                }
                else if($sort_by == 'kytu_az') {
                    $all_product = DB::table('product')->join('category', 'category.categoryID', '=', 'product.categoryID')
                        ->join('brand', 'brand.brandID', '=', 'product.brandID')
                        ->join('suppliers', 'suppliers.supplierID', '=', 'product.supplierID')
                        ->orderby('product.productName', 'asc')->paginate(12)->appends(Request()->query());
                }
                else if($sort_by == 'kytu_za') {
                    $all_product = DB::table('product')->join('category', 'category.categoryID', '=', 'product.categoryID')
                        ->join('brand', 'brand.brandID', '=', 'product.brandID')
                        ->join('suppliers', 'suppliers.supplierID', '=', 'product.supplierID')
                        ->orderby('product.productName', 'desc')->paginate(12)->appends(Request()->query());
                }
        } elseif (isset($_GET['start_price'] ) && isset($_GET['end_price'])){
            $min_price = $_GET['start_price'];
            $max_price = $_GET['end_price'];
            $all_product = DB::table('product')->join('category', 'category.categoryID', '=', 'product.categoryID')
                ->join('brand', 'brand.brandID', '=', 'product.brandID')
                ->join('suppliers', 'suppliers.supplierID', '=', 'product.supplierID')
                ->whereBetween('product.Price', [$min_price, $max_price])->paginate(12)->appends(Request()->query());
        } else {
            $all_product = DB::table('product')->join('category', 'category.categoryID', '=', 'product.categoryID')
                ->join('brand', 'brand.brandID', '=', 'product.brandID')
                ->join('suppliers', 'suppliers.supplierID', '=', 'product.supplierID')
                ->orderby('product.productID', 'desc')->paginate(6);
        }
        $min_price = DB::table('product')->min('Price');
        $max_price = DB::table('product')->max('Price');
        $min_price_range = $min_price - 500000;
        $max_price_range = $max_price + 10000000;
//        $all_product = DB::table('product')->where('Status','1')->orderby('productID', 'desc')->paginate(6); ;
        return view('pages.home')->with('category', $cate_product)->with('brand', $brand_product)->with('supplier', $supplier_product)
            ->with('all_product', $all_product)->with('slider', $slider) ->with('category_post', $category_post)
            ->with('min_price', $min_price)->with('max_price', $max_price)->with('min_price_range', $min_price_range)->with('max_price_range', $max_price_range);
    }
    public function search(Request $request){
        $keywords = $request->keywords_submit;

        $cate_product = DB::table('category')->where('categoryStatus','1')->orderby('categoryID', 'desc')->get();
        $brand_product = DB::table('brand')->where('brandStatus','1')->orderby('brandID', 'desc')->get();
        $search_product = DB::table('product')->where('productName','like','%'.$keywords.'%')->get();
        return view('pages.Product.seach')->with('category', $cate_product)->with('brand', $brand_product) ->with('search_product', $search_product);
    }
    public  function  send_mail(){
        $to_name = "Truong Quang Huy";
        $to_email = "tahuuthang9@gmail.com";//send to this email

        $data = array("name"=>"Mail from customer account","body"=>'Mail sent about the problem of goods'); //body of mail.blade.php

                Mail::send('pages.send_mail',$data,function($message) use ($to_name,$to_email){
                    $message->to($to_email)->subject('test mail nhé');//send this mail with subject
                    $message->from($to_email,$to_name);//send from this mail
                });
                //--send mail
        return redirect('/')->with('message','');

    }

    public function login_facebook(){
        return Socialite::driver('facebook')->redirect();
    }

    public function callback_facebook(){
        $provider = Socialite::driver('facebook')->user();
        $account = Social::where('provider','facebook')->where('provider_user_id',$provider->getId())->first();
        if($account){
            //login in vao trang quan tri
            $account_name = Login::where('employeeID',$account->user)->first();
            Session::put('FullName',$account_name->FullName);
            Session::put('employeeID',$account_name->employeeID);
            return redirect('/dashboard')->with('message', 'Đăng nhập Admin thành công');
        }else{

            $hieu = new Social([
                'provider_user_id' => $provider->getId(),
                'provider' => 'facebook'
            ]);

            $orang = Login::where('admin_email',$provider->getEmail())->first();

            if(!$orang){
                $orang = Login::create([
                    'FullName' => $provider->getName(),
                    'Email' => $provider->getEmail(),
                    'Password' => '',
                    'Phone' => '',
                    'admin_status' => 1

                ]);
            }
            $hieu->login()->associate($orang);
            $hieu->save();

            $account_name = Login::where('employeeID',$account->user)->first();

            Session::put('FullName',$account_name->FullName);
            Session::put('employeeID',$account_name->employeeID);
            return redirect('/dashboard')->with('message', 'Đăng nhập Admin thành công');
        }
    }
}
