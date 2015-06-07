/*-----RUN WHEN DOCUMENT IS READY-----*/

$(document).ready(function(){

	//fire once once document is ready
	responsiveDiv();
	
	//fire every time window is resized
	$(window).resize(function(){
		responsiveDiv();
	});

});

/*-----THE RESPONSIVE DIV FUNCTION-----*/

function responsiveDiv(){
	
	//if the window width is less than 1024px
	if($(window).width() < 1024){
	
		//fade out the div
		$("#floatDiv").fadeOut("slow");
	
	//else the window is wider than 1024px
	}else{
		
		//fade in the div
		$("#floatDiv").fadeIn("slow");
		
	}

}

