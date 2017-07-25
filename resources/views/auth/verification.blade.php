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
                    Account Verification
              </div>
            </div>
            <div class="row">                              
                <div class="col-md-12">
                    <div class="desc-verify">The verification code send to {{$phone}}. Please insert your code to verify</div>
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
            <div class="validate-code"></div>
                <form class="form-horizontal" method="POST" action="{{ url('/auth/postRegister') }}" id="formSubmit">
                    {{ csrf_field() }}
                    <div class="box-login">
                        <div class="row">                              
                            <div class="col-md-12">
                                <input type="text" name="code" id="code" class="form-control inputText" value="" placeholder="Verification Code">
                            </div>
                        </div>                         
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" id="btn-submit" class="btn btn-primary masuk" data-dismiss="modal">Submit</button>    
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $("#btn-submit").click(function(){
            var inputStr = $("#code").val();
            if(inputStr.length<4){
                $(".validate-code").html("enter atleast 4 chars in the input box");                
                $(".validate-code").addClass("active");                
            }else{
                $("#formSubmit").submit();
            }
        })
    });
</script>

@include('uangku.footer')
@include('uangku.top-always')
