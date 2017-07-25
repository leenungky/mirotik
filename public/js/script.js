var url = "http://getdata.popbox.asia";
$( document ).ready(function() {
	$("#btnWishSubmit").click(function() {
		var vName = $("#txtName").val();
		var vPhone = $("#txtPhone").val();
		var vProductWish = $("#txtProductWish").val();		
		$.post(url + "/wishbox", { name: vName, phone_number: vPhone,"product_wish":vProductWish}).done(function(result) {				
			console.log(result.response.code + '=' + result.response.message);
			if (result.response.code=="600"){
				$(".modal-body p").html("Terima kasih");
				$('#thankModal').modal();
			}else{
				$(".modal-body p").html("Ada kesahalan");
				$('#thankModal').modal();
			}
			
		});				

	});

	$("#myModalLabel").click(function() {
		$('#stepBuyModal').modal();
	});
	
	

	$.post(url + "/category", function( data ) {    	 	
		//getItemLandingByParameter();
		var tag = "";	    	 	
			if (data[6].id_category!=""){
		       	tag +='<div class="row icon-category"><div class="col-md-12"><img src="img/landing/must-have.jpg" width="84px"></div>';
		       	tag +='<div class="right-side-name" idval="' + data[6].id_category + '">';
		       	tag +='</div></div>';		       	
	        }
	        var nav = "";
	        nav += '<li><a href="#" class="ui-link">'+data[6].category_name+'</a></li>';
	  	 	$.each( data, function( keys, value ) { 	  	 		
	  	 		getItemLandingByParameter(value.category_name, value.id_category);
	  	 		var key = value.id_category;	
	   	 	  	htmlTag = "";	    	      
	   	 	  	if (key!="7"){
	   	 	  		nav += '<li><a href="#" id-cat="' + value.id_category + '" class="ui-link">'+value.category_name+'</a></li>';
		    	    htmlTag +='<div class="row icon-category">';
						htmlTag +='<div class="col-md-12">';
					    			    
					    if (key=="1"){
					  		htmlTag +='<img src="img/landing/bauty-n-care.jpg" width="84px">';
					  	}else if (key=="2"){
					  		htmlTag +='<img src="img/landing/cosmetic.jpg" width="84px">';
					  	}else if (key=="3"){
					  		htmlTag +='<img src="img/landing/gadget-n-accesories.jpg" width="84px">';
					  	}else if (key=="4"){
					  		htmlTag +='<img src="img/landing/household.jpg" width="84px">';
					  	}else if (key=="5"){
					  	  	htmlTag +='<img src="img/landing/snack.jpg" width="84px">';
					  	}else if (key=="6"){
					  	  	htmlTag +='<img src="img/landing/toys.jpg" width="84px">';
					  	}
					  	htmlTag +='</div>';		      						  	
				  	  	htmlTag +='<div class="right-side-name" idval="' + value.id_category+ '">' + value.category_name + '</div>';		
				  	  	  
				    htmlTag +='</div>';        	  
		    		tag +=htmlTag;	
	    		}		  
		});			
	  	 $(".navbar-nav").html(nav);
		$(tag).insertBefore(".content-side-distance");	
	});
		 	
	$(document).on('click', '.icon-category', function(){ 
		var val = $(this).find('.right-side-name').attr("idval");	
		var valname = $(this).find('.right-side-name').text();
		valname = splitWord(valname);		
		valname = valname.replace(/\s+/g, '-').toLowerCase();
		window.history.pushState("", "", '?cat=' + valname);
	  	getItemLanding(val);
	});

	$(document).on('mouseover', '.item-landing', function(){ 
		$(this).find('.box-item').addClass("box-item-hover");
		
	});

	$(document).on('mouseout', '.item-landing', function(){ 
		$(this).find('.box-item').removeClass("box-item-hover");		
	});

	$(document).on('click', '.item-landing', function(){ 
		var qr = $(this).attr("itm-qr");
		var name = $(this).attr("itm-name");		
		var prc = $(this).attr("itm-prc");

		$("#qrModal").find('img').attr("src", qr);
		$("#qrModal").find('.item-name').html(name);
		$("#qrModal").find('.item-price').html("Rp " + formatNumber(prc));		
		$("#qrModal").modal();
	});		

	$(document).on('click', '.ui-link', function(){ 
		var idcat = $(this).attr("id-cat");
		getItemLanding(idcat);		
		$('.navbar-collapse').removeClass('in');
	});
	
});

function getItemLandingByParameter(pCategoryName, id){
	var catNameByUrl = $.urlParam("cat");
	if (catNameByUrl!=null)	{		
		var categoryName = catNameByUrl.replace(/\-/g, " ")
		pCategoryName = splitWord(pCategoryName);
		console.log(categoryName + "==" + pCategoryName.toLowerCase());
		if (categoryName==pCategoryName.toLowerCase()){			
			getItemLanding(id);
		}	
	}else{
		getItemLanding(7);
	}
}

$.urlParam = function(name){
    var results = new RegExp('[\?]' + name + '=([^#]*)').exec(window.location.href);
    if (results==null){
       return null;
    }
    else{
       return results[1] || 0;
    }
}

function getItemLanding(category){	
	$.post(url + "/list", { page: "1", category: category,"pagesize":"20"}).done(function(result) {				
		var tag = "";
		var name = "";
		$.each( result.data[0], function( key, value ) { 
			var htmlTag = '';
			htmlTag += '<div class="col-md-2 col-xs-6 item-landing" itm-qr="'+ value.qrcode+'" itm-name="'+ value.name+'" itm-prc="'+value.price+'">';
				htmlTag += '<div class="thumbnail box-item">';
				htmlTag += '<img src="' + value.image + '" width="100%">';
				htmlTag +='</div>';
			htmlTag +='<div class="item-name">' + value.name + '</div>';
			htmlTag +='<div class="item-price">Rp ' + formatNumber(value.price) + '</div>';
			htmlTag +='</div>';
			tag += htmlTag;					
			name = value.name;
		});		
   		$("#content-side-content .row").html(tag);   		
	});	
}

function formatNumber(value){
	return (value/1000).toFixed(3);    	
}

function splitWord(pValue){
	if (pValue.indexOf("&") >= 0){
		return pValue.split('&')[0];
	}else{
		return pValue;
	}
}