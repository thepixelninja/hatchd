$(document).ready(function(){
	
	//init cancas
	$("#canvas").canvas({
		width	: 	600,
		height 	: 	600
	});
	
});

$.fn.canvas = function(options){
	
	var defaults = {
		width 	: 100,
		height	: 800
	};
	
	var options = $.extend(defaults,options);
	
	return this.each(function(){
		
		//-----SETUP-----//
		//set canvas
		var jcanvas 	= $(this);
		var canvasOb 	= this;
		var controls 	= $(".controls");
		
		//set width and height
		jcanvas.attr("width",options.width);
		jcanvas.attr("height",options.height);
		
		//set context
		var canvas = canvasOb.getContext("2d"); 
		
		//set default options
		canvas.lineWidth 	= 5;
		options.color		= "#000";
		canvas.strokeStyle 	= options.color;//line colour
		canvas.lineCap 		= "round"; 		//round,butt,square
		canvas.lineJoin		= "round";		//round,butt,square
		
		//-----CONTROLS-----//
		//change line colour
		controls.find(".color").click(function(){
			var color 			= $(this).css("background-color");
			options.color		= color;
			canvas.strokeStyle 	= color;
		});
		
		//change line width
		controls.find(".width").click(function(){
			var width 			= parseFloat($(this).text());
			canvas.lineWidth 	= width;
		});
		
		//reset
		controls.find(".reset").click(function(){
			canvas.clearRect(0,0,options.width,options.height);
		});
		
		//fill
		controls.find(".fill").click(function(){
			if(options.fill){
				options.fill = false;
			}else{
				options.fill = true;
			}
		});
		
		//save as image
		controls.find(".save").click(function(){
			var data = canvasOb.toDataURL();
			$(".savedImg")
				.show()
				.find(".imgHolder")
				.html("<img src='"+data+"'>")
			;
		});
		
		//-----HANDLE DRAWING LINES-----//
		//start line
		$("body").mousedown(function(e){
			options.mousedown = true;
			canvas.beginPath();
			var offset = jcanvas.offset();
			canvas.moveTo(e.pageX-offset.left,e.pageY-offset.top);
		});
		
		//stop line
		$("body").mouseup(function(e){
			options.mousedown = false;
			canvas.closePath();
		});
		
		//draw the path
		jcanvas.mousemove(function(e){
			if(options.mousedown){
				var offset = jcanvas.offset();
				canvas.lineTo(e.pageX-offset.left,e.pageY-offset.top);
				if(options.fill){
					canvas.fillStyle = options.color;
					canvas.fill();
				}
				canvas.stroke();
			}
		});

	});
	
	//-----TESTING-----//
	function test(){
		
		//start path
		canvas.beginPath();
		
		//draw line
		canvas.moveTo(100,150);
		canvas.lineTo(450,50);
		canvas.lineWidth 	= 5;
		canvas.strokeStyle 	= "#424242";	//line colour
		canvas.lineCap 		= "round"; 		//round,butt,square
		canvas.lineJoin		= "round";
		
		//draw arc
		var arc 			= new Object;
		arc.x 				= 500; 
		arc.y 				= 150;
		arc.radius 			= 75;
		arc.startingAngle	= 1.1*Math.PI;
		arc.endingAngle		= 2.5*Math.PI;
		arc.antiClockwise	= false;
		canvas.arc(
			arc.x,
			arc.y,
			arc.radius,
			arc.startingAngle,
			arc.endingAngle,
			arc.antiClockwise
		);
		
		//arc to
		canvas.arcTo(50,100,100,100,50);
		
		//close path
		canvas.closePath();
		
		//fill shapes
		//canvas.fillStyle = "#ccc";
		var grad = canvas.createLinearGradient(230, 0, 370, 200);
	    grad.addColorStop(0.5,"#8ED6FF"); // light blue
	    grad.addColorStop(1,"#004CB3"); // dark blue
	    canvas.fillStyle = grad;
		canvas.fill();	
		
		//draw shapes
		canvas.stroke();
		
	}
	
}
