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
        @if (count($errors))     
            <div class="row">               
                <div class="col-md-12 alert alert-danger">      
                    <ul>
                        @foreach($errors->all() as $error)                                              
                            <li>{{$error}}</li>
                        @endforeach 
                    </ul>
                </div>
            </div>
            <br/>
        @endif 
        
        @if(Session::has('message'))
            <div class="row">               
                <div class="col-md-12 alert alert-warning">      
                    <ul>
                        <li>{!! Session::get('message') !!}</li>                      
                    </ul>
                </div>
            </div>
            <br/>
        @endif        
        <div class="row">   
            <form action="/price/list" method="get">
                <div class="col-md-2">
                    City<br/>
                    <input type="text" name="city" class="form-control" value="{{isset($filter["city"]) ? $filter["city"] : ""}}">
                </div>               
                <div class="col-md-2">
                    Kecamatan<br/>
                    <input type="text" name="kecamatan" class="form-control" value="{{isset($filter["kecamatan"]) ? $filter["kecamatan"] : ""}}">
                </div>               
                <div class="col-md-2">
                    <br/>
                    <input type="submit" value="find" class="btn">
                </div>
            </form>
        </div><br/>
        <div class="row">               
            <div class="col-md-12">
                <a href="#" data-toggle="modal" data-target="#upload_price">Upload</a>
            </div>
        </div>
        <br/>
        <div class="row">   
            <div class="col-md-12">
                <table class="table">
                    <?php 
                        $str_parameter = "";
                        if (isset($order_by)){
                            if ($order_by=="asc"){
                                $str_parameter = "&order_by=desc";
                            }
                            else if ($order_by=="desc"){
                                $str_parameter = "&order_by=asc";
                            }   
                        }
                    ?>
                    <thead>
                        <th>Code</th>
                        <th>City</th>                      
                        <th>Kecamatan</th>
                        <th>Regular</th>
                        <th>Regular estimasi</th>
                        <th>one day</th>          
                        <th>Action</th>
                    </thead>
                    <tbody>     
                        @foreach ($price as $key => $value)
                            <tr>
                                <td>{{$value->area_code}}</td>
                                <td>{{$value->city}}</td>
                                <td>{{$value->kecamatan}}</td>
                                <td>{{$value->regular_price}}</td>
                                <td>{{$value->est_delivery}}</td>
                                <td>{{$value->oneday_price}}</td>
                                <td>
                                    <a href="/price/edit/{{$value->id}}">
                                        <span class="edit"> 
                                            <span class="glyphicon glyphicon-pencil"  rel="tooltip" title="delete"></span>
                                        </span>
                                    </a> | 
                                    <a href="/price/delete/{{$value->id}}" class="confirmation">
                                        <span class="delete">
                                            <span class="glyphicon glyphicon-remove"  rel="tooltip" title="delete"></span>
                                        </span>
                                    </a>                                    
                                </td>
                            </tr>
                        @endforeach                     
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">   
            <?php 
                if (isset($filter)){
                    $price->appends($filter);
                }
            ?>
            {!! $price->render() !!}
            </div>
        </div>
     </div>         
</div>

<div id="upload_price" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Upload</h4>
      </div>
      <div class="modal-body">
        <p>Untuk contoh upload klik disini >><a href="/upload/price.xls">Download</a></p>
        <form action="/price/upload" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">  
            Select image to upload:
            <input type="file" name="fileupload" id="fileupload" class="form-control"><br/>
            <input type="submit" value="Upload" class="btn" name="submit">
        </form>
      </div>     
    </div>

  </div>
</div>

</body>
</html>