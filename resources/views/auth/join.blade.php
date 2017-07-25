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
                    Please enter your mobile number
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
            <form class="form-horizontal" method="POST" action="{{ url('/auth/verification') }}">
                                     {{ csrf_field() }}
            <div class="box-login">
                <div class="row">                              
                    <div class="col-md-12">
                        <input type="text" class="form-control" id="inputEmail3" placeholder="No Handphone" name="phone">
                    </div>
                </div>                                
                <div class="row">                              
                    <div class="col-md-12">
                            <button type="submit" class="btn btn-primary masuk" data-dismiss="modal">Join Now</button>    
                    </div>
                </div>
            </div>
        </form>        
    </div>
</div>

@include('uangku.footer')
@include('uangku.top-always')
