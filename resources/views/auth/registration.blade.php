<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
     @include('uangku.head')
</head>
<body>
    <?php use App\Http\Helpers\Helpdesk; ?>

 @include("uangku.header")
 <div id="contents">
        <div class="container container-fluid">                             

            <div class="row step masuk-title">
              <div class="col-md-12">
                    Register
              </div>
            </div>
            <div class="row step">
              <div class="col-md-12">
                    <div class="desc-verify">Please complete your data to access PopBox account
                    Your phone number : {{$phone}}</div>
              </div>
            </div>
            @if (Session::has('error'))
                <div class="row">                              
                    <div class="col-md-12">
                        <div class="err-msg">{{session('error')}}</div>
                    </div>
                </div>                        
            @endif
            @if (count($errors) > 0)
                <div class="row">                              
                    <div class="col-md-12">
                        <div class="err-msg alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            <form class="form-horizontal" method="POST" action="{{ url('/auth/complete') }}">
            {{ csrf_field() }}
                <div class="box-login">
                    <div class="row">                              
                        <div class="col-md-12">
                            <input type="text" class="form-control" id="inputEmail3" placeholder="Name" name="name">
                        </div>
                    </div>
                    <div class="row">                              
                        <div class="col-md-12">
                            <input type="text" class="form-control" id="inputPassword3" placeholder="Email" name="email">
                        </div>
                    </div>
                    <div class="row">                              
                        <div class="col-md-12">
                            <input type="password" class="form-control" id="inputPassword3" placeholder="Password" name="password">
                        </div>
                    </div>
                    <div class="row">                              
                        <div class="col-md-12">
                            <input type="password" class="form-control" id="inputPassword3" placeholder="Re-type Password" name="repassword">
                        </div>
                    </div>                    
                    <div class="row">                              
                        <div class="col-md-12">
                                <button type="submit" class="btn btn-primary masuk" data-dismiss="modal">Submit</button>    
                        </div>
                    </div>
                </div>
            </form>
            @if (Session::has('err_message'))
                <div>{!! session('err_message') !!}</div>
            @endif
    </div>
</div>

@include('uangku.footer')
@include('uangku.top-always')
