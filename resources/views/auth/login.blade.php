<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
     @include('head')
</head>
<body>
    <?php use App\Http\Helpers\Helpdesk; ?>

 
 <div id="contents">
        <div class="login-container container-fluid">            
            
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
        <form method="post" action="{{ url('user/signin')}}" class="form-vertical box" id="fr">
            {{ csrf_field() }}                
                <div class="row box-login">
                    <div class="sub-box-login">

                        <div class="row">
                            <div class="col-md-12">
                                <!-- <img src="{{URL::asset('img/logo-bks.png')}}" class="logo-login"> -->
                            </div>
                        </div>
                       <div class="row">                              
                            <div class="col-md-12 alileft label-email">
                                Email Address<br/>
                                <input type="text" name="email" class="form-control" id="inputEmail3" placeholder="Email Address">
                            </div>
                        </div> 
                        <div class="row">                              
                            <div class="col-md-12 alileft label-email">
                                Password<br/>
                                <input type="password" name="password" class="form-control" id="inputEmail3" placeholder="Password">
                            </div>
                        </div>                        
                        
                        
                        <div class="row top-row-login">                              
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary masuk" data-dismiss="modal">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
          
    </div>
</div>

