/*
 * EGGS.JS
 * BY ED FRYER
 * ED@THEPIXEL.NINJA
 */

var eggs = {
	
	canvas : $("<canvas></canvas>"),
	
	trigger : $("#eggsplosion"),
	
	init : function(){
		
		var body = $("body");
		body.append(eggs.canvas);
		
		eggs.canvas.css({
			"position" 	: "absolute",
			"z-index" 	: 999,
			"top"		: 0,
			"left"		: 0	
		});
		
		eggs.resize();
		eggs.ctx = eggs.canvas.get(0).getContext("2d");
		eggs.objects = new Array();
		
		var start = eggs.trigger.offset();
		console.log(start);
		eggs.center = {
			//x : eggs.winWidth/2,
			//y : eggs.winHeight/2
			x : start.left,
			y : start.top
		};
		
		$(window).resize(function(){
			eggs.resize();
		});
		
		body.on("mousemove",function(e){
			eggs.center = {
				x : e.pageX,
				y : e.pageY
			};
		});
		
		for(var i=0; i<500; i++){
			eggs.objects.push(eggs.create());
		}
		
		eggs.playing = true;
		
		eggs.animate();
		
		eggs.canvas.on("click",function(){
			eggs.destroy();
		});
		
	},
	
	create : function(){
		
		var e = {
			x 		: eggs.center.x+eggs.randomBetween(-20,20),
			y 		: eggs.center.y+eggs.randomBetween(-20,20),
			radius 	: eggs.randomBetween(20,50),
			vx		: eggs.randomBetween(-5,5),
			vy		: eggs.randomBetween(-5,5),
			opacity : 1
		};
		
		var rand = eggs.randomBetween(1,3);
		if(rand == 3){
			e.color = [65,92,100];
		}else if(rand == 2){
			e.color = [253,233,214];
		}else{
			e.color = [221,108,92];
		}
		
		return e;
		
	},
	
	resize : function(){
		
		eggs.winWidth  = $(window).width();
		eggs.winHeight = $(document).height();
		eggs.canvas
			.attr("width",eggs.winWidth)
			.attr("height",eggs.winHeight)
			.css({
				width  : eggs.winWidth,
				height : eggs.winHeight
			});
		;
		
	},
	
	randomBetween : function(min,max){
		return Math.floor(Math.random()*(max-min+1)+min);
	},
	
	draw : function(){
		
	    eggs.ctx.globalCompositeOperation 	= "source-over";
	    //eggs.ctx.fillStyle 					= "rgba(0,0,0,0.15)";
	    //eggs.ctx.fillRect(0,0,eggs.winWidth,eggs.winHeight);
	    eggs.ctx.clearRect(0,0,eggs.winWidth,eggs.winHeight);
		
		for(var j=0; j<eggs.objects.length; j++){
			
			var e = eggs.objects[j];
			
			eggs.ctx.beginPath();
			//eggs.ctx.arc(e.x,e.y,e.radius,0,Math.PI*2,false);
			eggs.drawEgg(e.x,e.y,e.radius,e.radius+(e.radius/2));
	        eggs.ctx.fillStyle = "rgba("+e.color[0]+","+e.color[1]+","+e.color[2]+","+e.opacity+")";
			eggs.ctx.fill();
			
			e.x += e.vx;
			e.y += e.vy;
			e.radius  -= 0.5;
			e.opacity -= 0.01;
			
			if(e.radius < 0 || e.opacity < 0){
				eggs.objects[j] = eggs.create();
			}
			
		}
		
		eggs.drawEgg(100,100,100,150);
		
	},
	
	animate : function(){
		if(eggs.playing){
			requestAnimationFrame(eggs.animate);
			eggs.draw();
		}
	},
	
	drawEgg : function(x,y,w,h){
		
		var north = {
			x : x,
			y : y-(h/2)
		};
		var south = {
			x : x,
			y : y+(h/2)
		};
		var east = {
			x : x-(w/2),
			y : y+(h/6)
		};
		var west = {
			x : x+(w/2),
			y : y+(h/6)
		};
		var nw 		= eggs.findHalfWayPoint(north,west);
		var nnw 	= eggs.findHalfWayPoint(nw,north);
		var wnw 	= eggs.findHalfWayPoint(nw,west);
		var ne 		= eggs.findHalfWayPoint(north,east);
		var nne 	= eggs.findHalfWayPoint(ne,north);
		var ene 	= eggs.findHalfWayPoint(ne,east);
		var sw 		= eggs.findHalfWayPoint(south,west);
		var ssw 	= eggs.findHalfWayPoint(sw,south);
		var wsw 	= eggs.findHalfWayPoint(sw,west);
		var se 		= eggs.findHalfWayPoint(south,east);
		var sse 	= eggs.findHalfWayPoint(se,south);
		var ese 	= eggs.findHalfWayPoint(se,east);
		
		eggs.ctx.beginPath();
		eggs.ctx.moveTo(nw.x, nw.y);
		eggs.ctx.bezierCurveTo(nnw.x, nnw.y, nne.x, nne.y, ne.x, ne.y);
		eggs.ctx.bezierCurveTo(ene.x, ene.y, ese.x, ese.y, se.x, se.y);
		eggs.ctx.bezierCurveTo(sse.x, sse.y, ssw.x, ssw.y, sw.x, sw.y);
		eggs.ctx.bezierCurveTo(wsw.x, wsw.y, wnw.x, wnw.y, nw.x, nw.y);

		//eggs.ctx.strokeStyle = "red";
		//eggs.ctx.stroke();
		
	},
	
	findHalfWayPoint : function(pt1,pt2){
		return {
	        x : eggs.findHalfWay(pt1.x, pt2.x),
	        y : eggs.findHalfWay(pt1.y, pt2.y)
	    };   
	},
	
	findHalfWay : function(a,b){
		var l = Math.max(a,b);
    	var s = Math.min(a,b);
    	return s+((l-s)/2);
	},
	
	destroy : function(){
		eggs.playing = false;
		eggs.canvas.fadeOut("slow",function(){
			eggs.canvas.remove();
		});
	}
	
};