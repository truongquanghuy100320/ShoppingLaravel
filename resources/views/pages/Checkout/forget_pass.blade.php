@extends('layout')
@section('content')
    <section id="form"><!--form-->
        <div class="container">
            <div class="row">
                <div class="col-sm-4 col-sm-offset-1">
                    <div class="login-form"><!--login form-->
                        <h2>Login to your account</h2>
                        <form action="{{URL::to('/login-customer')}}" method="POST" name="login">
                            {{csrf_field()}}
                            <input type="text" name="email_account" placeholder="Account" />
                            <input type="password" name="password_account" placeholder="Email Address" />
                            <span>
								<input type="checkbox" class="checkbox">
								Keep me signed in
							</span>
                            <span>
								<a href="{{url('/quen-mat-khau')}}">Forgot password</a>
							</span>
                            <button type="submit" class="btn btn-default">Login</button>
                        </form>
                        <style type="text/css">
                            ul.list-login{
                                margin: 10px;
                                padding: 0;
                            }
                            ul.list-login li{
                                display: inline;
                                margin: 5px;
                            }

                        </style>
                        <ul class="list-login">
                            <li>
                                <a href="{{URL::to('/login-google')}}"><img width="10%" alt="login Google" src="{{asset('public/Fontend/images/gg.png')}}"></a>
                            </li>
                        </ul>
                    </div><!--/login form-->
                </div>
            </div>
        </div>
    </section><!--/form-->
@endsection
