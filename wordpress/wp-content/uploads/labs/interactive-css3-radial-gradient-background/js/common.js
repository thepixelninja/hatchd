//-----------ON DOCUMENT READY----------//

$(document).ready(function(){
	
	//--just for testing--//
	//add ie6 class
	var version = parseInt($.browser.version);
	if($.browser.msie && version < 7){
		$("body").addClass("ie6");
	}
	//--end--//
				
	$("body").crazyBG({
		bgColor 	: "#000",
		colorRange 	: 255
	});
	
});

//----------CRAZY BG PLUGIN----------//

/* 
 * creates a radial gradient that follows the mouse and changes
 * size and color depending on mouse position and speed.
 * NB. as the lovely ie doesnt support radial gradients and 
 * probably never will the plugin falls back to a linear gradient
 * in ie where supported.
 */

$.fn.crazyBG = function(options){
	
	var defaults = {
		bgColor 	: "#000", 	//the background color
		minSize		: 500,		//the min size of the radial gradient - not ie
		maxSize 	: 2000,		//the max size of the radial gradient - not ie
		colorRange 	: false,	//the color range - either false or a number between 0 and 255
		fade		: true		//wether to fade in and out the gradient when the mouse is static
	}
	
	var options  = $.extend(defaults,options);
	var data	 = new Object();
	
	return this.each(function(){

		//----------setup---------//
		
		//define el
		var el = $(this);
		
		//set el css
		el.css({
			"background-color"  : options.bgColor,
			"position" 			: "relative",
			"z-index"			: "1",
			"overflow"			: "hidden"
		});
		
		//add in gradient holder
		el.append("<div id='gradient'></div>");
	
		//define gradient
		var grad = el.find("#gradient");
		
		//set grad css
		grad.css({
			"position"  			: "absolute",
			"-moz-transition"		: "width 0s, height 0s",
			"-webkit-transition"	: "width 0s, height 0s",
			"display"				: "none"
		});
		
		//call resize
		resize();
		
		//log mouse speed
		data.mouseSpeed = getSpeed();
		setInterval(function(){
			data.mouseSpeed = getSpeed();
		},100);
		
		//trigger mouse move if no fade
		if(!options.fade){
			var e = {
				pageX : 0,
				pageY : 0
			}
			drawGradient(e);
			grad.show();
			
		//check for static mouse and fade out grad if fade
		}else{
			var checker = setInterval(function(){
				if(data.moving){
					grad.fadeIn("slow");
				}else{
					grad.fadeOut("slow");
				}
			},1000);
		}
		
		//----------events----------//
		
		//-----on mouse move-----//
		el.mousemove(function(e){
			drawGradient(e);	
		});
		
		//-----on window resize-----//
		$(window).resize(function(){
			resize();
		});
		
		//----------functions----------//
		
		//-----window resize------//
		function resize(){
			data.elWidth 	= el.width();
			data.elHeight 	= el.height();
		}
		
		//-----draw gradient-----//
		function drawGradient(e){
			
			//log e for mouse speed
			data.mouseX = e.pageX;
			data.mouseY = e.pageY;
			
			//work out scales
			var scaleX = data.elWidth/255;
			var scaleY = data.elHeight/255;
			
			//create rgb color vals based on position
			var col1 = Math.floor(e.pageX/scaleX);
			var col2 = Math.floor(e.pageY/scaleY);
			
			//create rgb color vals based on speed if no color range set
			if(!options.colorRange){
				var col3 = Math.floor(200*data.mouseSpeed);
			}else{
				var col3 = options.colorRange;
			}
			if(col3 > 255){
				col3 = 255;
			}
			
			//build rgb
			var rgb  = new Array(col1,col2,col3);
			
			//get hex val
			var hex = convertHex(rgb);
			
			//set gradient size based on speed
			var size = Math.floor(800*data.mouseSpeed);
			if(size < options.minSize){
				size = options.minSize;
			}else if(size > options.maxSize){
				size = options.maxSize;
			}
			
			//get browser version
			var version = parseInt($.browser.version);
			
			//set gradient var based on browser
			var gradient = "-moz-radial-gradient(50% 50%,circle,"+hex+" 10%,"+options.bgColor+" 70%)";
			if($.browser.webkit){
				gradient = "-webkit-gradient(radial,50% 50%,"+size/4+",50% 50%,"+size/2+",from("+hex+"),to("+options.bgColor+"))";
			}else if($.browser.opera){
				gradient = "-o-radial-gradient(50% 50%,50% 50%,"+hex+","+options.bgColor+")";
			}
			
			//ie8 and below fix
			if($.browser.msie && version < 10){
				
				gradient = "progid:DXImageTransform.Microsoft.gradient(startColorstr='"+hex+"',endColorstr='"+options.bgColor+"')";
				
				//apply gradient and css
				grad.css({
					"filter" 	 : gradient,
					"top"		 : 0,
					"left"		 : 0,
					"width" 	 : data.elWidth,
					"height" 	 : data.elHeight,
					"zoom"		 : 1
				});
			
			//all other browsers	
			}else{
				
				//apply gradient and css
				grad.css({
					"background" : gradient,
					"top"		 : e.pageY-size/2,
					"left"		 : e.pageX-size/2,
					"width" 	 : size,
					"height" 	 : size
				});
				
			}	
			
		}
		
		//-----get mouse speed-----//
		function getSpeed(){
			
			//define mouse pos vars
			if(data.mouseX === undefined || data.mouseY === undefined){
				data.mouseX 	= 0;
				data.mouseY 	= 0;
				data.lastMouseX = 0;
				data.lastMouseY = 0;
			}
			
			//calculate distance traveled
			var dist = Math.max(Math.abs(data.mouseX-data.lastMouseX),Math.abs(data.mouseY-data.lastMouseY));
			
			//calculate speed
			var speed = dist/500;
			
			//log if movement
			if(data.mouseX == data.lastMouseX && data.mouseY == data.lastMouseY){
				data.moving = false;
			}else{
				data.moving = true;
			}
			
			//log last mouse pos
			data.lastMouseX = data.mouseX;
			data.lastMouseY = data.mouseY;
			
			return speed;
			
		}
		
		//-----convert rgb array to hex-----//
		function convertHex(rgb){
			
			var hex  = new Array();
			//get hex from rgb
			for(var i = 0; i <= 2; ++i){
				hex[i] = parseInt(rgb[i]).toString(16);
				if(hex[i].length == 1){
					hex[i] = "0"+hex[i];
				}
			}
			var hex = "#"+hex.join("");
			return hex;
			
		}
		
	});
	
}