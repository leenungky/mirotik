<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
     
    
</head>
<body>
     
@foreach ($role_priviledges as $key => $value)
	<tr class="role">
		<td><input type="checkbox"/></td>
		<td>{{$value->name}}</td>
		<td>{{$value->description}}</td>
	</tr>
@endforeach

</body>
</html>