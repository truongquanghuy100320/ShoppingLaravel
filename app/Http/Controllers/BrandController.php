<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Slider;
use Illuminate\Http\Request;
Use DB;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
Use Session;
use App\Models\Brand;
Use Illuminate\Support\Facades\Redirect;
session_start();
class BrandController extends Controller
{
    public function AuthLogin(){

//        $admin_id = Session::get('admin_id');
//        if($admin_id){
//            return Redirect::to('dashboard');
//        }else{
//            return Redirect::to('admin')->send();
//        }

//        $admin_id = Auth::id();
//        if($admin_id){
//            return Redirect::to('dashboard');
//        }else{
//            return Redirect::to('admin')->send();
//        }
        $admin_id = Session::get('admin_id');
        if($admin_id){
            return Redirect::to('dashboard');
        }else{
            return Redirect::to('admin')->send();
        }
    }
    public  function valudation(Request $request){
        return $this->validate($request,[
            'brandName' => 'required|min:3|max:100',
            'brandDescription' => 'required|min:3|max:100',
        ]);
    }
    public function add_brand_product()
    {  $this->AuthLogin();
        return view('Admin.Brand.add_brand_product');
    }
    public function all_brand_product()
    {
        $this->AuthLogin();
//        $all_brand_product = DB::table('brand')->get();
        $all_brand_product = Brand::orderBy('brandID', 'desc')->paginate(10);
        $manage_brand_product = view('Admin.Brand.all_brand_product')->with('all_brand_product', $all_brand_product);
        return view('admin_layout')->with('Admin.Brand.all_brand_product', $manage_brand_product);
    }
    public function save_brand_product(Request $requesty, BrandController $brandController )
    {
        $this->AuthLogin();
        $this -> valudation($requesty);
         $data= $requesty->all();
         $brand = new Brand;
         $brand->brandName = $data['brandName'];
         $brand->brandDescription = $data['brandDescription'];
         $brand->brandStatus = $data['brandStatus'];
            $brand->save();
//        $data = array();
//        $data['brandName'] = $requesty->brandName;
//        $data['brandDescription'] = $requesty->brandDescription;
//        $data['brandStatus'] = $requesty->brandStatus;
//        echo '<pre>';
//        print_r($data);
//        echo '</pre>';
//        DB::table('brand')->insert($data);

        Session::put('message', '  Brand added successfully');
        return redirect::to('/add-brand-product');
    }
    public function unactive_brand_product($brandID)
    {
        $this->AuthLogin();
        DB::table('brand')->where('brandID', $brandID)->update(['brandStatus' => 0]);
        Session::put('message', 'Brand unactive successfully');
        return redirect::to('/all-brand-product');
    }
    public function active_brand_product($brandID)
    {
        $this->AuthLogin();
        DB::table('brand')->where('brandID', $brandID)->update(['brandStatus' => 1]);
        Session::put('message', 'Brand active successfully');
        return redirect::to('/all-brand-product');
    }
    public function edit_brand_product($brandID)
    {
        $this->AuthLogin();
//        $edit_brand_product = DB::table('brand')->where('brandID', $brandID)->get();
        $edit_brand_product = Brand::where('brandID', $brandID)->get();
        $manage_brand_product = view('Admin.Brand.edit_brand_product')->with('edit_brand_product',   $edit_brand_product);
        return view('admin_layout')->with('Admin.Brand.edit_brand_product', $manage_brand_product);
    }
    public function delete_brand_product($brandID)
    {
        $this->AuthLogin();
        DB::table('brand')->where('brandID', $brandID)->delete();
        Session::put('message', 'brand delete successfully');
        return redirect::to('/all-brand-product');
    }
    public  function  update_brand_product(Request $requesty , BrandController $brandID )
    {
        $this->AuthLogin();
//        $data = array();
//        $data['brandName'] = $requesty->brandName;
//        $data['brandDescription'] = $requesty->brandDescription;
//        DB::table('brand')->where('brandID', $requesty->brandID)->update($data);
        $brand = Brand::find($requesty->brandID);
        $data = $requesty->all();
        $brand->brandName = $data['brandName'];
        $brand->brandDescription = $data['brandDescription'];
        $brand->save();
        Session::put('message', 'Brand updated successfully');
        return redirect::to('/all-brand-product');
    }

    // brand product end
    public function show_brand_home($brandID){
        $category_post =  Post::orderBy('cate_post_id', 'desc')->where('cate_post_status', '1')->get();
        $slider = Slider::orderBy('slider_id', 'DESC')->where('slider_status', '1')->take(4)->get();
        $cate_product = DB::table('category')->where('categoryStatus','1')->orderby('categoryID', 'desc')->get();
        $brand_product = DB::table('brand')->where('brandStatus','1')->orderby('brandID', 'desc')->get();
        $brand_by_id = DB::table('product')->join('brand','product.brandID', '=', 'brand.brandID')
            ->where('brand.brandID', $brandID)->get();

        if(isset($_GET['sort_by'])) {
            $sort_by = $_GET['sort_by'];
            if($sort_by == 'tang_dan'){
                $brand_by_id = DB::table('product')->join('brand','product.brandID', '=', 'brand.brandID')
                    ->where('brand.brandID', $brandID)->orderBy('Price', 'asc')->paginate(12)->appends(Request()->query());
            }
            elseif($sort_by == 'giam_dan'){
                $brand_by_id = DB::table('product')->join('brand','product.brandID', '=', 'brand.brandID')
                    ->where('brand.brandID', $brandID)->orderBy('Price', 'desc')->paginate(12)->appends(Request()->query());
            } elseif($sort_by == 'kytu_az'){
                $brand_by_id = DB::table('product')->join('brand','product.brandID', '=', 'brand.brandID')
                    ->where('brand.brandID', $brandID)->orderBy('productName', 'asc')->paginate(12)->appends(Request()->query());
            } elseif($sort_by == 'kytu_za'){
                $brand_by_id = DB::table('product')->join('brand','product.brandID', '=', 'brand.brandID')
                    ->where('brand.brandID', $brandID)->orderBy('productName', 'desc')->paginate(12)->appends(Request()->query());
            }

            }elseif (isset($_GET['start_price'] ) && isset($_GET['end_price'])){
            $min_price = $_GET['start_price'];
            $max_price = $_GET['end_price'];
            $brand_by_id = DB::table('product')->join('brand','product.brandID', '=', 'brand.brandID')
                ->where('brand.brandID', $brandID)->whereBetween('Price', [$min_price, $max_price])->paginate(12)->appends(Request()->query());
           } else{
            $brand_by_id = DB::table('product')->join('brand','product.brandID', '=', 'brand.brandID')
                ->where('brand.brandID', $brandID)->paginate(12)->appends(Request()->query());
             }
        $min_price = DB::table('product')->join('brand','product.brandID', '=', 'brand.brandID')
            ->where('brand.brandID', $brandID)->min('Price');
        $max_price = DB::table('product')->join('brand','product.brandID', '=', 'brand.brandID')
            ->where('brand.brandID', $brandID)->max('Price');
        $max_price_range = $max_price + 10000000;
        $min_price_range = $min_price - 500000;
//
//        $min_price = DB::table('product')->join('category','product.categoryID', '=', 'category.categoryID')
//            ->where('category.categoryID', $brandID)->min('Price');
//        $max_price = DB::table('product')->join('category','product.categoryID', '=', 'category.categoryID')
//            ->where('category.$brandID', $brandID)->max('Price');
//        $max_price_range = $max_price + 50000;;
//        $min_price_range = $min_price - 10000000;;


        $brandName = DB::table('brand')->where('brand.brandID',$brandID)->limit(1)->get();
        return view('pages.Brand.show_brand')->with('category', $cate_product)->with('brand', $brand_product)
            ->with('brand_by_id', $brand_by_id)->with('brandName', $brandName) ->with('category_post', $category_post) ->with('slider', $slider)
            ->with('min_price', $min_price)->with('max_price', $max_price)->with('max_price_range', $max_price_range)->with('min_price_range', $min_price_range);
    }

}
