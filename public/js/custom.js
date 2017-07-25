var domain = location.protocol+'//'+location.hostname+(location.port ? ':'+location.port: '');

$( document ).ready(function() {	
	$(".col-top-menu").click(function(){
		$(".col-top-menu").removeClass("active");
		$(this).addClass("active");
	})

	$(".col-top-menu").click(function(){		
		if ($(this).text()=="Inbound"){
			location.href="/inbound";
		}else if ($(this).text()=="Outbound"){
			location.href="/outbound";
		}else if ($(this).text()=="Undelivery"){
			location.href="/undelivery";
		}else if ($(this).text()=="History"){
			location.href="/history";
		}else if ($(this).text()=="All Order"){
			location.href="/allorder";
		}		
	});

	$("select[name='courier_company']").change(function(){		
		var url = domain + "/courier?company_id=" + $(this).val();
		console.log(url);	
		$.ajax({
			url: url,
			dataType: 'json',
			success: function(result) {			  	
			   	console.log(result.response.code);
			   	// console.log(result.data);
			   	if (result.response.code == 200){			   		
			   		$("select[name='courier']").empty();
			   		$.each(result.data, function(index,value){
			   			$("select[name='courier']").append($('<option>', {value:value.id, text:value.name}));			   						   			
			   		});
				}
			}
		})
	})

	$("input[name='rd_origin']").click(function(){
		var type = $(this).val();
		if (type=="address"){
			$(".origin-address").show();
			$(".origin-loker").hide();
		}else if (type=="loker"){
			$(".origin-address").hide();
			$(".origin-loker").show();
		}
	})

	$("input[name='rd_dest']").click(function(){
		var type = $(this).val();
		if (type=="address"){
			$(".dest-address").show();
			$(".dest-loker").hide();
		}else if (type=="loker"){
			$(".dest-address").hide();
			$(".dest-loker").show();
		}
	})

	$("input[name='panjang']").on('keyup', function() {
		var pjg = $(this).val().length		
		if (pjg ==0 ){		
			$("input[name='volume']").attr('checked', false);
			$(this).removeAttr("placeholder");				
		}else{
			$(this).removeAttr("style");	
		}
	});

	$("input[name='lebar']").on('keyup', function() {
		var pjg = $(this).val().length		
		if (pjg ==0 ){		
			$("input[name='volume']").attr('checked', false);
			$(this).removeAttr("placeholder");				
		}else{
			$(this).removeAttr("style");	
		}
	});

	$("input[name='tinggi']").on('keyup', function() {
		var pjg = $(this).val().length		
		if (pjg ==0 ){		
			$("input[name='volume']").attr('checked', false);
			$(this).removeAttr("placeholder");				
		}else{
			$(this).removeAttr("style");	
		}
	});

	$("input[name='rounded']").click(function(){
		var isValidate = false;
		isValidate = validate("tinggi", 'required', isValidate);
		isValidate = validate("lebar", 'required', isValidate);
		isValidate = validate("panjang", 'required', isValidate);			
		isValidate = validate("oweight", 'required', isValidate);			
		
		if (isValidate){
			$(this).attr('checked', false);			
			return;
		}
		rounded();	
	})

	$("input[name='oweight']").keyup(function(){		
		var arr_value = $(this).val().split(".");
		if (arr_value.length>0){
			if (arr_value[1]!=null){
				var str_decimal = arr_value[1].substring(0,1);
				if (str_decimal > 3){				
					arr_value[0] = parseInt(arr_value[0])+1;
				}
			}
			oweight = arr_value[0];
			if (arr_value[0]==0){
				oweight = 1;
			}			
		}
		$("input[name='rweight']").val(oweight);
	})

	$(".btn-save").click(function(e){			
		insertAjax(false);
	})

	$(".btn-print").click(function(e){		
		insertAjax(true);
	})

	$(".btn-rwb").click(function(e){
		var isValidate = false;		
		isValidate = validate("oweight", 'required', isValidate);	
		isValidate =  validateSelect("courier", 'required', isValidate);
		isValidate =  validateSelect("delivery_type", 'required', isValidate);
		$("textarea[name='remark']").removeAttr("style");
		
		if (isValidate){			
			return;
		}		
		$("input[name='is_generate']").val("1");
		var url = domain + "/generaterwb";
		console.log(url);
		$.ajax({
			url: url,
			dataType: 'json',
			success: function(result) {		  				
			   	if (result.response.code == 200){			   		
			   		$("input[name='order_id']").val(result.data);
			   		$(".alert-info").html("<strong>Pemberitahuan!</strong> silahkan lengkapi form dibawah dengan <strong>AWB</strong> baru!");
					$(".alert-info").show();
					$(".border-input").addClass("active");	
					empty();		    			    		
		    		$(".read").hide();
					$(".new").show();
			   	}
			}
		});
	});

	
	$(document).on("click",".btn-print-read",function() {	
		var order_id 	= $("input[name='order_id']").val();	
		printDivIcon("printableArea");
	})
	

	$(".icon-filter").click(function(){
		var val = $("input[name='filter']").val();	
		$("input[name='order_id']").val(val);
		showCell(true);
	})

	$(".close-alert").click(function(){		
		$(".alert-warning").hide();
	})


   
	$(document).on("click",".edit",function() {
		var order_no 	= $(this).attr("order-id");
		var id 			= $(this).attr("val-id");
		var status 		= $(this).attr("val-status");				
		var date 		= $(this).attr("val-date");	
		var url 	    = base_url + "/edit?order_no=" + order_no + "&id=" + id + "&status=" + status + "&date=" + encodeURIComponent(date);
		console.log(url);
		$('.modal-body').load(url);        		
		$('#myModal').modal('show');
    });

    $(document).on("click",".delete",function() {
		var order_no = $(this).attr("order-id");
		var id = $(this).attr("val-id");
		var status = $(this).attr("val-status");
		$('.modal-body').load(base_url + "/deleteconf?order_no=" + order_no + "&id=" + id + "&status=" + status);        
		$('#myModal').modal('show');
    });

     $(document).on("click",".print",function() {
		var order_no = $(this).attr("order-id");
		var id = $(this).attr("val-id");
		var status = $(this).attr("val-status");
		printDivIcon("printableArea");		
    });

    $("input[name=sender]").keyup(function( event ) {		        
    	var  url =  "/transaction/getcustomer";
    	var idelement = "#sender";    	
       	autocomplete(url, idelement); 
    });

    $("input[name=pelanggan]").keyup(function( event ) {		        
    	var  url =  "/transaction/getcustomer";
    	var idelement = "#sender";    	
       	autocomplete(url, idelement); 
    });

    $("input[name=customer]").keyup(function( event ) {		        
    	var  url =  "/transaction/getcustomer?type=laporan_pengiriman";
    	var idelement = "#sender";    	
       	autocomplete(url, idelement); 
    });
    

    $("input[name=City]").keyup(function( event ) {		        
    	var  url =  "/transaction/getkecamatan";
    	var idelement = "#city";
       	autocomplete(url, idelement);
    });

     $("#city_name").keyup(function( event ) {		        
    	var  url =  "/cities/citiesfromkec";
    	var idelement = "#city_name";
       	autocomplete(url, idelement);
    });

 
    $("input[name='receive']").bind("keydown", function(event) {
        if (event.keyCode==13){
            return false;
        }
    });

    $("#city_name").bind("keydown", function(event) {
        if (event.keyCode==13){
            return false;
        }
    });

    $("input[name='weight']").keyup(function( event ) {		      
    	var tval = $(this).val();
    	console.log(tval);
        if (event.keyCode==13){
            return false;
        }else{
        	var price = trans_price*tval;
        	$("input[name=price]").val(price);
        }
    });



    $("input[name='city']").bind("keydown", function(event) {
        if (event.keyCode==13){
            return false;
        }
    });

    $("input[name='weight']").numeric();
    $("input[name='total']").numeric();
    $("input[name='discount']").numeric();
    $("input[name=jam]" ).numeric();
    $("input[name=menit]" ).numeric();
    $("input[name=oneday_price]" ).numeric();
    $("input[name=regular_price]" ).numeric();

    $('.confirmation').on('click', function () {
        return confirm('Are you sure?');
    });


    $( ".datepicker" ).datepicker({
    	dateFormat: "yy-mm-dd"
    });

    $('.email').tokenfield();

    
	$(".formsubmit" ).validate({
	  rules: {
	    weight: {
	      required: true,
	      min: 1
	    }
	  }
	});
});

function printDivIcon(divName) {			
	var printContents = document.getElementById(divName).innerHTML;
	var originalContents = document.body.innerHTML;
	document.body.innerHTML = printContents;
 	window.print();	   			
	document.body.innerHTML = originalContents;
	location.reload();

}

function onEnter(e){
	var key=e.keyCode || e.which;
	if(key==13){
		var val = $("input[name='filter']").val();					
		if (val!=""){					
			$("input[name='order_id']").val(val);
			$(".alert-warning").hide();
			$(".border-input").removeClass("active");			
			showCell(true);			
		}
	}
}


function showCell(isInsert){	
	var val = $("input[name='order_id']").val();		
	var courier = $("select[name='courier'] option:selected").val();		
	var type = typex; 
	var remark = $("textarea[name='remark']").val();	
	var delivery_typex = $( "#delivery_type option:selected" ).text();		
	var weight = $("input[name='weight']").val();	
	var url_lastest = domain + "/find_latest_status?orderNo=" + val + "&status=" + typex + "&delivery_type=" + delivery_typex;
	console.log(url_lastest);	
	$.ajax({
		url: url_lastest,
		dataType: 'json',
		success: function(result) {		  	
			console.log("==== find_latest_status ======");
		   	console.log(result);
		   	locked();
		   	if (result.response.code == 200){
		   		if (type=="in" && result.response.description=="in"){		   			
		   			$("#txt-msg").html("order id <strong>" + val + "</strong> terakhir sudah status in di tanggal <strong>" + result.response.date + "</strong>");
		   			$(".alert-warning").show();
		   			$("input[name='filter']").val("");
					$("input[name='filter']").focus();	
					doInsert(false, val);
		   		}else if (type=="out" && result.response.description=="out"){
		   			console.log("data terakhir di input dengan status out");
		   			$("#txt-msg").html("order id <strong>" + val + "</strong> terakhir sudah status out di tanggal <strong>" + result.response.date + "</strong>");
		   			$(".alert-warning").show();
		   			$("input[name='filter']").val("");
					$("input[name='filter']").focus();						
					doInsert(false, val);
		   		}else{
		   			doInsert(isInsert, val);
		   		}
		   	}else{		   		
		   		if (result.response.description=="in_is_emtpy"){
		   			$("#txt-msg").html("order id <strong>" + val + "</strong> <strong>out</strong> tidak bisa dilakukan sebelum <strong>in</strong>");
		   			$(".alert-warning").show();		   			
		   		}else if (result.response.description=="out_is_empty"){
		   			$("#txt-msg").html("order id <strong>" + val + "</strong> type <strong>" + delivery_typex + "</strong> in tidak bisa dilakukan sebelum out");
		   			$(".alert-warning").show();		   		
		   		}else if (result.response.description=="data_is_emtpy"){
		   			$("#txt-msg").html("nomor order <strong>" + val + "</strong> baru pertama kali di input, Silahkan input di bagian inbound");
		   			$(".alert-warning").show();		   			
		   		}else{
		   			doInsert(isInsert, val);		   		
		   		}
		   	}
		 }
	});
}

function getWeight(order_id){
	$.get( domain + "/getweight?order_no=" + order_id, function( result ) {				
		if (result.response.code == 200){			
			$("#weight").val(result.data.weight);
		}
	});
}

function doInsert(isInsert, order_no){
	var courier = $("select[name='courier'] option:selected").val();		
	var type = $("input[name='typeinv']").val();
	var remark = $("textarea[name='remark']").val();	
	var delivery_typex = $( "#delivery_type option:selected" ).text();		
	var weight = $("input[name='weight']").val();
	var panjang = $("input[name='panjang']").val();
	var lebar = $("input[name='lebar']").val();
	var tinggi = $("input[name='tinggi']").val();	
	var isrounded = isRounded();	
	if (isInsert){
		var isValidate = false;						
		isValidate =  validateSelect("courier", 'required', isValidate);
		
		if (isValidate){
			$("input[name='filter']").val("");
			$("input[name='filter']").focus();
			return;
		}
	}
					
	var full = "";
	var params = "?orderNo=" + order_no + "&courier=" + courier + "&type=" + type + 
				"&remark=" + remark + "&del_type=" + delivery_typex + 
				"&weight=" + weight + "&panjang=" + panjang + "&lebar=" + lebar + "&tinggi=" + tinggi + "&isrounded=" + isrounded;
	if (isInsert){
		full = domain + "/find" + params;
	}else{
		full = domain + "/read" + params;
	}	
	
	
	$("#spancode").text(order_no);		
	cekUndel(isInsert, full, type);		
	$("input[name='filter']").val("");
	$("input[name='filter']").focus();	
}

function insertAjax(isPrint){	
	var order_idx 	= $("input[name='order_id']").val();	
	var courierx 	= $("select[name='courier']").val();
	var phonex 		= $("input[name='phone']").val();	
	var rd_originx 	= $("input[name='rd_origin']:checked").val();
	var rd_destx 	= $("input[name='rd_dest']:checked").val();	
	var delivery_typex 	= $( "#delivery_type option:selected" ).text();
	var weightx 			= $("input[name='weight']").val();	
	var oweightx 			= $("input[name='oweight']").val();	
	var rweightx 			= $("input[name='rweight']").val();	
	var panjangx 		= $("input[name='panjang']").val();	
	var tinggix 			= $("input[name='lebar']").val();	
	var lebarx 			= $("input[name='tinggi']").val();	
	var origin_addressx = $("textarea[name='origin_address']").val();
	var is_generatex = $("input[name='is_generate']").val();	
	var resi_nox = $("input[name='resi_no']").val();	
	var namax = $("input[name='nama']").val();	
	var isroundedx = isRounded();

	var isValidate = false;
	if (rd_originx=="loker"){
		var origin_addressx	= $("input[name='origin_loker']").val();
		isValidate = validate("origin_loker", 'required', isValidate);
	}else{
		isValidate = validateArea("origin_address", 'required', isValidate);
	}

	var dest_addressx = $("textarea[name='dest_address']").val();
	if (rd_destx=="loker"){
		var dest_addressx 	= $("input[name='dest_loker']").val();
		isValidate = validate("dest_loker", 'required', isValidate);	
	}else{
		isValidate = validateArea("dest_address", 'required', isValidate);	
	}
	var merchantx 	= $("input[name='merchant-name']").val();
	
	isValidate = validate("merchant-name", 'required', isValidate);	
	if (isValidate){	
		return;
	}	
	var emailx 		= $("input[name='email']").val();
	var data_post = { order_id   	: order_idx ,
					 resi_no        : resi_nox,
				     is_generate    : is_generatex,
				     rd_dest        : rd_destx,
				     nama 			: namax, 
					 merchant 		: merchantx, 
					 phone 			: phonex, 
					 email 			: emailx,
					 origin_address : origin_addressx,
					 dest_address 	: dest_addressx, 
					 rd_origin 		: rd_originx, 
					 rd_dest 		: rd_destx , 
					 courier 		: courierx, 
					 delivery_type  : delivery_typex,
					 weight 		: weightx,
					 oweight 		: oweightx,
					 rweight 		: rweightx,
					 isrounded 		: isroundedx,
					 panjang 		: panjangx,
					 tinggi 		: tinggix,
					 lebar 			: lebarx,
					 type 			:typex};
	console.log('insertajax');
	$.post( "/insertajax",data_post, function(result) {			
		if (result.response.code==200){						 
			showingQrCode(order_idx);
			$("#table-div").load(base_url + "/readHistory/" + typex);
			$("#txt-msg").html(result.response.description);
			$(".alert-warning").show();						
			$("html, body").animate({ scrollTop: 0 }, "slow");
			$("input[name='phone']").val(result.data.inventory.phone);	
		    $("textarea[name='origin_address']").val(result.data.inventory.origin);	
		    $("textarea[name='dest_address']").val(result.data.inventory.dest);	
		    $("input[name='email']").val(result.data.inventory.email);	
		    $("input[name='merchant-name']").val(result.data.inventory.merchant_name);			    				    
			$("#download").attr("href","/download?order_no=" + result.data.inventory.order_no);				    	
			$("#total").text(result.data.total);
		   
		    $(".border-input").removeClass("active");
			empty();	
			locked();			
			if (isPrint){		
				setTimeout(function(){
				  printDivIcon('printableArea');
				}, 1000);
			 }
		}
		$(".alert-info").hide();			
		
	});	
}

function validate(input, type, isValidate){
	var inputName = $("input[name='" + input + "']").val();
	if (type=="required"){
		if (inputName==""){
			$("input[name='"+ input +"']").attr("placeholder", input + " tidak boleh kosong");
			$("input[name='"+ input +"']").css("color", "red");	
			$("input[name='"+ input +"']").css("border", "3px solid red");
			$("input[name='"+ input +"']").focus();
			isValidate = true;
		}else{
			$("input[name='"+ input +"']").removeAttr("style");	
		}
	}	
	if (isValidate!=null)
		return isValidate;
	else
		return false;
}

function validateArea(input, type, isValidate){
	var inputName = $("textarea[name='" + input + "']").val();
	if (type=="required"){
		if (inputName==""){
			$("textarea[name='"+ input +"']").attr("placeholder", input + " tidak boleh kosong");
			$("textarea[name='"+ input +"']").css("color", "red");	
			$("textarea[name='"+ input +"']").css("border", "3px solid red");
			$("textarea[name='"+ input +"']").focus();
			isValidate = true;
		}else{
			$("textarea[name='"+ input +"']").removeAttr("style");	
		}
	}	
	if (isValidate!=null)
		return isValidate;
	else
		return false;
}

function validateSelect(name, type, isValidate){
	var inputName = $("select[name='" + name + "']").val();		
	console.log("======= " + type + " ====== " + inputName)	;
	if (type=="required"){
		if (inputName==null || inputName==""){
			$("select[name='"+ name +"']").attr("placeholder", name + " tidak boleh kosong");
			$("select[name='"+ name +"']").css("color", "red");	
			$("select[name='"+ name +"']").css("border", "3px solid red");
			$("select[name='"+ name +"']").focus();
			isValidate = true;
		}else{
			$("select[name='"+ name +"']").removeAttr("style");	
		}
	}	
	if (isValidate!=null)
		return isValidate;
	else
		return false;
}


function cekUndel(isInsert, full, type){		
	var val = $("input[name='order_id']").val();	
	if (isInsert){		
		var remark = $("textarea[name='remark']").val();	
		console.log(domain + "/find_status?orderNo=" + val + "&status=" + typex);
		$.ajax({
	     	url: domain + "/find_status?orderNo=" + val + "&status=" + typex,
	     	dataType: 'json',
	     	success: function(result) {
	     		console.log(result);
	     		if (result.response.code == 200){	     			
	     			if (remark==""){	    
	     				validateArea("remark", 'required', true);		     				
	     			}else{
	     				$("textarea[name='remark']").removeAttr("style");	     				
	     				insertOrFind(isInsert, full, type, val);
	     			}
	     		}else{	     			
	     			insertOrFind(isInsert, full, type, val);

	     		}
	     	}
		});
	}else{
		insertOrFind(isInsert, full, type, val);
	}
	
}

function insertOrFind(isInsert, full, type, order_no){
	console.log(full);
	$.ajax({
		    url: full,
		    dataType: 'json',
		    success: function(result) {
		    	if (result.response.code == 200){	
		    		showingQrCode(order_no);				    		
		    		$("input[name='resi_no']").val(result.data.inventory.resi_no);
		    		$("input[name='panjang']").val(result.data.inventory.panjang);
		    		$("input[name='lebar']").val(result.data.inventory.lebar);
		    		$("input[name='tinggi']").val(result.data.inventory.tinggi);
		    		$("input[name='weight']").val(result.data.inventory.weight);
		    		$("input[name='oweight']").val(result.data.inventory.oweight);
		    		$("input[name='rweight']").val(result.data.inventory.rweight);
		    		$("input[name='nama']").val(result.data.inventory.recipient_name);	
		    		$("input[name='phone']").val(result.data.inventory.phone);	
		    		if (result.data.inventory.isrounded=="1"){
		    			$("input[name='volume']").prop('checked', true);
		    		}
		    		$("textarea[name='origin_address']").val(result.data.inventory.origin);	
		    		$("textarea[name='dest_address']").val(result.data.inventory.dest);	
		    		$("input[name='email']").val(result.data.inventory.email);			    		
		    		$("input[name='merchant-name']").val(result.data.inventory.merchant_name);			    				    					    	
			    	$('select[name="merchan"] option[value="' + result.data.inventory.merchant_name +'"]').attr('selected','selected');

			    	$('#delivery_type option[value="' + result.data.history.delivery_type + '"]').attr('selected','selected');	    				    	
		    		$("#table-div").load(base_url + "/readHistory/" + type);
					locked();
		    		$("textarea[name='remark']").val("");	
		    		if (isInsert){
			    		$("#txt-msg").html("Data berhasil " + typex);
						$(".alert-warning").show();
						$("select[name='delivery_type']").removeAttr("style");						
					}
					$(".read").show();
					
		    	}else{	
		    		var isValidate = validate("oweight", 'required', isValidate);
	     			if (isValidate){
	     				return;
	     			}
		    		$("input[name='is_generate']").val("0");
		    		var delivery_typex 	= $( "#delivery_type option:selected" ).text();
		    		if (delivery_typex=="Pilih"){								    			
						validateSelect("delivery_type", "required");								
						return;
					}
					$("select[name='delivery_type']").removeAttr("style");
		    		
		    		empty();		    			    		
		    		$(".read").hide();
					$(".new").show();
					$("#message").html("<strong>Pemberitahuan!</strong> Data tidak ditemukan silahkan lengkapi form dibawah!");
					$(".alert-info").show();
					$(".border-input").addClass("active");										
		    	}
		    }
		});	
	$("input[name='filter']").val("");
	$("input[name='filter']").focus();	
}

function locked(){
	$("input[name='order_id']").prop('readonly', true);
	$("input[name='phone']").prop('readonly', true);
	$("textarea[name='origin_address']").prop('readonly', true);
	$("textarea[name='dest_address']").prop('readonly', true);
	$("input[name='email']").prop('readonly', true);	
	$("input[name='merchant-name']").prop("readonly", true);
	$("input[name='origin_loker']").prop("readonly", true);	
	$("input[name='dest_loker']").prop("readonly", true);	
	$("input[name='resi_no']").prop("readonly", true);
	$("input[name='nama']").prop("readonly", true);	
	$(".new").hide();
}

function empty(){
	$("input[name='phone']").val("");
	$("textarea[name='origin_address']").val("");
	$("textarea[name='dest_address']").val("");	
	$("input[name='merchant-name']").val("")
	$("input[name='email']").val("");
	$("input[name='resi_no']").val("");
	$("input[name='nama']").val("");
	$("input[name='origin_loker']").removeProp("readonly");
	$("input[name='dest_loker']").removeProp("readonly");	
	$("input[name='phone']").removeProp("readonly");		  
	$("textarea[name='origin_address']").removeProp("readonly");
	$("textarea[name='dest_address']").removeProp("readonly");
	$("input[name='email']").removeProp("readonly");		  
	$("input[name='merchant-name']").removeProp("readonly");
	$("input[name='resi_no']").removeProp("readonly");
	$("input[name='nama']").removeProp("readonly");
	$("input[name='resi_no']").focus();
}

function showingQrCode(order_id){
	$.get( domain + "/qrcode?code=" + order_id, function( result ) {				
		if (result.response.code == 200){			
			$(".spancode").text(order_id);
			$("#label-dest").text(result.data.label_dest);
			$("#qr-name").text(result.data.name);
			$("#qr-phone").text(result.data.phone);
			$("#qr-full-address").html(result.data.tujuan);
			$("#qr-weight").text(result.data.weight);
			$("#qrcode").attr("src","data:image/png;base64," + result.data.qrcode);				
		}
	});	
}


function rounded(){	
	var str_volume  = 0;
	if ($("input[name='rounded']").is(':checked')){	
		var panjang = $("input[name='panjang']").val();
		var lebar = $("input[name='lebar']").val();
		var tinggi = $("input[name='tinggi']").val();		
		var volume = (panjang * lebar * tinggi) / 6000;
		str_volume = volume.toLocaleString();
		var arr_value = str_volume.split(".");
		if (arr_value.length>0){
			if (arr_value[1]!=null){
				var str_decimal = arr_value[1].substring(0,1);
				if (str_decimal > 3){				
					arr_value[0] = parseInt(arr_value[0])+1;
				}
			}
			str_volume = arr_value[0];
			if (arr_value[0]==0){
				str_volume = 1
			}			
		}
	}
	$("input[name='weight']").val(str_volume);
	var oweight = $("input[name='oweight']").val();
	if (oweight >= str_volume){
		var arr_value = oweight.split(".");
		if (arr_value.length>0){
			if (arr_value[1]!=null){
				var str_decimal = arr_value[1].substring(0,1);
				if (str_decimal > 3){				
					arr_value[0] = parseInt(arr_value[0])+1;
				}
			}
			oweight = arr_value[0];
			if (arr_value[0]==0){
				oweight = 1
			}			
		}
		$("input[name='rweight']").val(oweight);
	}else{
		$("input[name='rweight']").val(str_volume);
	}

}

function isRounded(){	
	if ($("input[name='rounded']").is(':checked')){	
		return 1;
	}
	return 0;
}

function autocomplete(url, idelement){
	if (idelement=="#sender"){
		$(idelement).autocomplete({
	            source: function( request, response) {            	
	                if (request.term != "") {
	                    $.ajax({
	                        url: domain + url,
	                        dataType: "json",
	                        method: "get",
	                        data: {
	                            nama: request.term
	                        },
	                        success: function (result) {                                                         
	                            if (result.response.code == "200"){
	                                response($.map(result.data, function (item) {                                	
	                                    var name = item.name;
	                                    return {
	                                        label: name,
	                                        value: name,
	                                        data: item
	                                    }
	                                }));
	                            }
	                        }
	                    });
	                }
	            },
	            autoFocus: true,
	            minLength: 3,
	            select: function( event, ui ) {
	                if (ui.item == null){                    
	                    $(idelement).val("");
	                    return false;
	                }else{
	                    var data = ui.item.data.name;                    
	                    $("input[name=sender_id]").val(ui.item.data.id);   
	                    $(idelement).val(data);                       
	                }
	            },
	            change: function(event,ui){
	                if (ui.item == null){                    
	                    $(idelement).val("");
	                    return false;
	                }
	            }
	        });
	} else if (idelement =="#city")  {
		$(idelement).autocomplete({
	            source: function( request, response) {            	
	                if (request.term != "") {
	                    $.ajax({
	                        url: domain + url,
	                        dataType: "json",
	                        method: "get",
	                        data: {
	                            nama: request.term
	                        },
	                        success: function (result) {                                                         
	                            if (result.response.code == "200"){
	                                response($.map(result.data, function (item) {                                	
	                                    var name = item.city + " , " + item.kecamatan;
	                                    return {
	                                        label: name,
	                                        value: name,
	                                        data: item
	                                    }
	                                }));
	                            }
	                        }
	                    });
	                }
	            },
	            autoFocus: true,
	            minLength: 3,
	            select: function( event, ui ) {
	                if (ui.item == null){                    
	                    $(idelement).val("");
	                    return false;
	                }else{
	                	var weight = $("input[name=weight]").val();
	                	trans_price = ui.item.data.regular_price; 
	                    var price  = trans_price*weight;
	                    $("input[name='price']").val(price);	
	                	$("input[name='city_id']").val(ui.item.data.id);	
	                               	                                       
	                }
	            },
	            change: function(event,ui){
	                if (ui.item == null){                    
	                    $(idelement).val("");
	                    $("input[name='price']").val("");	
	                    return false;
	                }
	            }
	    });
   	
   	}else if (idelement=="#city_name"){   		   	
	    $(idelement).autocomplete({
		    source: function( request, response) {            	
		        if (request.term != "") {
		             $.ajax({
		            	url: domain + url,
		                dataType: "json",
		                method: "get",
		                data: {
		                	nama: request.term
		                },
		                success: function (result) {                                                         
		                	if (result.response.code == "200"){
		                    	response($.map(result.data, function (item) {                                	
		                        var name = item.city;
			                        return {
			                        	label: name,
			                            value: name,
			                            data: item
			                        }
		                         }));
		                    }
		                }
		            });
		        }
		    },
		    autoFocus: true,
		    minLength: 3,
		    select: function( event, ui ) {
		    	if (ui.item == null){                    
		        	$(this).val("");
		            return false;
		        }else{
		        	var data = ui.item.data.name;                    	                    
		            $(this).val(data);                       
		        }
		    },
		    change: function(event,ui){
		    	if (ui.item == null){                    
		        	$(this).val("");
		            return false;
		        }
		    }
		});	    
   	}
}
