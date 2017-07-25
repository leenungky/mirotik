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
				            <li>{{str_replace("name","Nama toko",$error)}}</li>
				        @endforeach 
				    </ul>
			    </div>
		    </div>
		@endif 
		<br/>
		<div class="row">				
			<div class="col-md-4">		
				<form method="post" action="/role/update" class="formsubmit">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">					
					<input type="hidden" name="role_id" value="{{$role->id}}">					
					<div class="form-group">
					    <label for="email">Role Name</label>
						 <input type="text" class="form-control" id="nama" name="nama" placeholder="input role name" value="{{$role->name}}" required>
					</div>					
					<div class="form-group">
					    <label for="email">Role Description</label>
						 <input type="text" class="form-control" id="description" name="description" placeholder="input role description" value="{{$role->description}}" required>
					</div>					
					
					<button type="submit" class="btn">Submit</button>
				</form>
			</div>
			<div class="col-md-4">
				<div class="row">
					<div class="col-md-12">
            			<div class="status-barang">List Priviledge</strong></div>        
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
            			<table class="table">
            				<thead>
            					<th>
            						<input type="checkbox" name="all_role_master" class="form-control" style="height: 20px;width:15px;" />
            					</th>
            					<th>
            						Role Name
            					</th>
            					<th>
            						Description
            					</th>
            				</thead>
            				<tbody class="master-priviledge">
            					@foreach ($priviledges as $key => $value)            						
            					<tr class="priv_master_{{$value->id}}">
            						<td>
            							<input type="checkbox" name="ids_priviledge[]" value="{{$value->id}}" item1="{{$value->name}}" item2="{{$value->description}}" class="form-control" style="height: 20px;width:15px;" />
            						</td>
            						<td>{{$value->name}}</td>
            						<td>{{$value->description}}</td>
            					</tr>
            					@endforeach
            				<tr class="role"><td></td><td></td><td></td></tr>	
            				</tbody>
            			</table>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<input type="button" value="add >>" class="btn btn-addpriviledge" />
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="row">
					<div class="col-md-12">
            			<div class="status-barang">{{ucfirst($role->name)}} Priviledge</strong></div>        
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<form action="/role/addrolepriviledge" method="post" id="frm-save-priviledge">					
							<input type="hidden" name="_token" value="{{ csrf_token() }}">					
							<input type="hidden" name="role_id" value="{{$role->id}}">
	            			<table class="table">
	            				<thead>
	            					<th>
	            						<input type="checkbox" name="all_role_result" class="form-control" style="height: 20px;width:15px;" />
	            					</th>
	            					<th>Role Name</th><th>Description</th>
	            				</thead>
	            				<tbody class="result-priviledge">            				
	            				@foreach ($result_priviledges as $key => $value)            						
            					<tr class="priv_result_{{$value->id}}">
            						<td>
            							<input type="checkbox" name="ids_priviledge[]" value="{{$value->id}}" item1="{{$value->name}}" item2="{{$value->description}}" class="form-control" style="height: 20px;width:15px;" />
            						</td>
            						<td>{{$value->name}}</td>
            						<td>{{$value->description}}</td>
            					</tr>
            					@endforeach
	            				<tr class="role"><td></td><td></td><td></td></tr>
	            				</tbody>            			
	            			</table>
            			</form>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<input type="button" value="<< Remove" class="btn btn-removepriviledge" />
						<input type="button" value="Save" class="btn btn-save-result" />
					</div>
				</div>
			</div>
		</div>
	</div>	    	

</div>
</body>
</html>

<script type="text/javascript">
	$(document).ready(function(){
		// $(".tbody-role-result").load(base_url + "/role/rolepriviledge");			
		$( "input[name=name]" ).focus();

		$(".btn-addpriviledge").click(function(){
			var selected = [];
			$('.master-priviledge input:checked').each(function() {
			    selected.push([$(this).attr('value'),$(this).attr('item1'),$(this).attr('item2')]);
			});

			$.each(selected, function( index, value ) {
				var strValue = "<tr class='priv_result_" + value[0] + "'><td>";
            		strValue = strValue + '<input type="checkbox" name="ids_priviledge[]" value="' + value[0] + '" item1="' + value[1] + '" item2="' + value[2] + '" class="form-control" style="height: 20px" />';
            		strValue = strValue + '</td>';
            		strValue = strValue + '<td>' + value[1] + '</td>';
            		strValue = strValue + '<td>' + value[2] + '</td></tr>';
				$(strValue).insertBefore( $( ".result-priviledge .role" ) );
				$(".priv_master_" + value[0]).remove();				
			  	console.log( index + ": " + value[1] );
			});			
			$("input[name='all_role_master']").attr('checked', false);
			
		});

		$(".btn-removepriviledge").click(function(){
			var selected = [];
			$('.result-priviledge input:checked').each(function() {
			    selected.push([$(this).attr('value'),$(this).attr('item1'),$(this).attr('item2')]);
			});

			$.each(selected, function( index, value ) {
				var strValue = "<tr class='priv_master_" + value[0] + "'><td>";
            		strValue = strValue + '<input type="checkbox" name="ids_priviledge[]" value="' + value[0] + '" item1="' + value[1] + '" item2="' + value[2] + '" class="form-control" style="height: 20px" />';
            		strValue = strValue + '</td>';
            		strValue = strValue + '<td>' + value[1] + '</td>';
            		strValue = strValue + '<td>' + value[2] + '</td></tr>';
             	console.log(strValue);    			
				$(strValue).insertBefore( $( ".master-priviledge .role" ) );
				$(".priv_result_" + value[0]).remove();				
			  	console.log( index + ": " + value[1] );
			});			
			$("input[name='all_role_result']").attr('checked', false);
		
		});

		$(".btn-save-result").click(function(){
			$('.result-priviledge :checkbox').each(function() {
	            this.checked = true;                        
	        });
			$("#frm-save-priviledge").submit();
		});

		$("input[name='all_role_master']").change(function(){
			if ($(this).is(":checked")){
				$('.master-priviledge :checkbox').each(function() {
		            this.checked = true;                        
		        });
			}else{
				$('.master-priviledge :checkbox').each(function() {
		            this.checked = false;                        
		        });
			}
		})

		$("input[name='all_role_result']").change(function(){
			if ($(this).is(":checked")){
				$('.result-priviledge :checkbox').each(function() {
		            this.checked = true;                        
		        });
			}else{
				$('.result-priviledge :checkbox').each(function() {
		            this.checked = false;                        
		        });
			}
		})
	});
</script>
