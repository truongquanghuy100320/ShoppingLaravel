@extends('admin_layout')
@section('admin_content')
   <h1 >Welcome to admin</h1>

    <div class="container-fluid">
        <style type="text/css">
            p.title_thongke{
                text-align: center;
                font-size: 20px;
                font-weight: bold;
            }
        </style>
        <div class="row">
           <p class="title_thongke">Thống kê đơn hàng doanh số</p>
            <form autocomplete="off">
                @csrf
                <div class="col-md-2">
                    <P>Từ</P>
                </div>
            </form>
        </div>

    </div>
@endsection
