<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
     @include('head')
     <style type="text/css" media="print">
           @media print {
            @page { margin: 0px 6px; }
                body  { margin: 0px 6px; }                        
            }
     </style>
</head>
<body >
    <?php use App\Http\Helpers\Helpdesk; ?>
 
 <div id="contents">
    <div class="container container-fluid">       
        @include('header')      
        <br/>
        <div class="row">   
            <div class="col-md-12">     
                <form method="post" action="/price/update/{{$price->id}}" class="formsubmit">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">                  
                    <div class="form-group">
                        <label for="email">price</label>
                         <input type="text" class="form-control" name="area_code" value="{{$price->area_code}}" placeholder="input nama" required>
                    </div>              
                    <div class="form-group">
                        <label for="email">City</label>
                         <input type="text" class="form-control" name="city" value="{{$price->city}}" placeholder="input pemilik" required>
                    </div>                  
                    
                    <div class="form-group">
                        <label for="pwd">Kecamatan</label>
                        <input type="text" class="form-control" name="kecamatan" value="{{$price->kecamatan}}" placeholder="input multiple email with comma" required>
                    </div>
                    <div class="form-group">
                        <label for="pwd">City Code</label>
                        <input name="city_code" class="form-control" rows="3" value="{{$price->city_code}}" placeholder="input address minimum 30 character" required>
                    </div>              
                    <div class="form-group">
                        <label for="pwd">Regular Price</label>
                        <input type="text" class="form-control" name="regular_price" value="{{$price->regular_price}}" placeholder="input Telephone" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="pwd">Estimasi regular:</label>
                        <input type="text" class="form-control" name="est_delivery" value="{{$price->est_delivery}}" placeholder="input discount" required>
                    </div>
                    <div class="form-group">
                        <label for="pwd">One day Price:</label>
                        <input type="text" class="form-control" name="oneday_price" value="{{$price->oneday_price}}" placeholder="input discount" required>
                    </div>
                    <button type="submit" class="btn">Submit</button>
                </form>
            </div>
        </div>
     </div>         
</div>
</body>
</html>
<script type="text/javascript">
    // $(document.ready(function(){
        
    // }))
</script>