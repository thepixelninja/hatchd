/*-----------ON DOCUMENT READY----------*/

$(document).ready(function(){
	
	//remove nojs class
	$("body").removeClass("nojs");
	
	//init paralax
	$("body").paralax({
		section			: ".page",
		element 		: ".element",
		maxOffsetY 		: 10000,
		maxOffsetX		: 10000,
		bgSpeed			: 5,
		horizontal		: false
	});
	
	$("nav a").smoothScroll({
		speed : 3000
	});

});

//----------SMOOTHSCROLL PLUGIN----------//

$.fn.smoothScroll = function(options){
	
	var defaults = {
		speed : 1000
	}
	
	var options = $.extend(defaults,options);
	
	return this.each(function(){
		
		$(this).click(function(e){
			
			//prevent default
			e.preventDefault();
			
			//define el
			var el = $(this);
			
			//get href
			var href = el.attr("href");
			
			//get el to animate to
			var to = $(href);

			//if to exists
			if(to.length > 0){

				//get position
				var top = to.position().top;
				
				//animate page
				$("body,html").animate({
					scrollTop : top
				},{
					duration 	: options.speed,
					queue 		: false,
					easing		: "easeOutCirc",
					complete	: function(){
						//set hash
						window.location.hash = href;
					}
				});
				
			}
		
		});
		
	});
	
}
/*----------PARALAX PLUGIN----------*/

$.fn.paralax = function(options){
	
	var defaults = {
		section			: ".page",
		element 		: ".element",
		maxOffsetY 		: 500,
		maxOffsetX		: 100,
		listenerSpeed 	: 500,
		bgSpeed			: 5,
		horizontal		: false
	}
	
	var options = $.extend(defaults,options);
	
	return this.each(function(){
		
		//--------------------SETUP--------------------//
		
		//set vars
		var el			 		= $(this);
		options.element 		= el.find(options.element);
		options.section			= el.find(options.section);
		options.scrollDir		= "forward";
		options.scrolling		= false;
		options.scrollData		= new Object();
		options.sectionArray 	= new Array();
		
		//hide main overflow
		if(options.horizontal){
			el.css("overflow-y","hidden");
		}else{
			el.css("overflow-x","hidden");
		}
		
		//call resize
		resize();
		
		//trigger scroll
		setTimeout(function(){
			if(options.horizontal){
				$(window).scrollLeft(1);
			}else{
				$(window).scrollTop(1);
			}
			window.location.hash = "";
		},100);

		//setup sections
		options.section.each(function(){
			
			//define section
			var section = $(this);
			
			//set bg pos
			if(options.horizontal){
				options.bgPosReset = "1px center";
			}else{
				options.bgPosReset = "center 1px";
			}
			
			//set css
			section.css({
				"background-position" 	: options.bgPosReset,
				"background-attachment" : "fixed",
				"overflow"				: "hidden"
				//"background-size"		: "contain"
			});
			
			//get bg image
			var image = section.css("background-image");
			
			//check for repeat
			var repeat = section.css("background-repeat");
			
			//if no repeat load up bg image and store height
			if(repeat == "no-repeat" && image != "none"){
				
				//get bg image src
				var image = image.replace('url("','').replace('")','');
				
				//create image
				var img = new Image();
				img.src = image;
				
				//hide image
				$(img).css("display","none");
				
				//append to body
				$("body").append(img);
				
				//once loaded
				$(img).load(function(){
					//get height and width
					var imgHeight = $(this).height();
					var imgWidth  = $(this).width();
					//store
					section.data({
						"bgWidth"  : imgWidth,
						"bgHeight" : imgHeight
					});
					//remove image
					$(this).remove();
				});
		
			}
		
		});
		
		//setup elements
		options.element.each(function(){
			
			//define para
			var para = $(this);  
			
			//get para width
			var width = para.width();
			
			//get top and left vals
			var top 	= para.attr("data-top");
			var left 	= para.attr("data-left");
			
			//if no top and left generate random values
			if(top === undefined || left === undefined){                                                                                                                                                                                                                                                                                            
				//generate random amounts
				var top 	= Math.floor(Math.random()*options.maxOffsetY+1)-(options.maxOffsetY/2);
				var left 	= Math.floor(Math.random()*options.maxOffsetX+1)-(options.maxOffsetX/2);
				//var top 	= Math.floor(Math.random()*options.maxOffsetY+1);
				//var left 	= Math.floor(Math.random()*options.maxOffsetX+1);
			}else{
				//convert to numbers
				top 	= parseFloat(top);
				left 	= parseFloat(left); 
			}
			
			//get orig pos
			var origPos = para.position();
			
			//create new pos
			var startPos = {
				top 	: top,
				left 	: left
			}
			
			//work out if negative values
			var negative = {
				top : false,
				left : false
			};
			if(origPos.top < 0 || top < 0){
				negative.top = true;
			}
			if(origPos.left < 0 || left < 0){
				negative.left = true;
			}
	
			//move para by random amounts
			para.css({
				top 		: startPos.top,
				left 		: startPos.left,
				//width		: width
			});
			
			//store as data
			para.data("startPos",startPos);
			para.data("origPos",origPos);
			para.data("negative",negative);

			//show
			para.show();
			
		});
		
		//--------------------EVENTS-------------------//
		
		//when scrolling
		$(window).scroll(function(e){
			
			//set scroll dir
			scrollDir();
			
			//start listener
			scrollListener();
			
			//animate elements
			animateElements();
			
			//animate bg image
			animateBg();
			
			//log scrolling
			options.scrolling = true;
			
		});
		
		//on mousedown
		$(window).mousedown(function(){
			
			//log all the scroll data on scroll start
			//safari uses the mouse hack
			scrollStart();

		});
		
		//on mouseup
		$(window).mouseup(function(e){
			
			//handle releasing the mouse after scrolling
			//only for mozilla other browsers use the mouse hack
			if($.browser.mozilla){
				scrollFin();
			}
			
		});
		
		//on resize
		$(window).resize(function(){
			
			//get win heights etc
			resize();
			
		});
		
		//webkit hack as mouse up is not fired on scrollbar in these browsers
		if($.browser.webkit){
			mouseHack();
		}
		
		//--------------------INTERNAL FUNCTIONS--------------------//
		
		//----------called on window resize----------//
		function resize(){
			
			//get win size
			options.winHeight 	= $(window).height();
			options.winWidth	= $(window).width();
			
			//get doc size
			options.docHeight 	= $(document).height();
			options.docWidth	= $(document).width();
			
			//if horizontal resize sections
			if(options.horizontal){
				var spacerNo 	= el.find(".spacer").length;
				var spacerWidth = el.find(".spacer").width();
				var sectionNo 	= options.section.length;
				var elWidth		= options.winWidth*sectionNo+spacerWidth*spacerNo;
				el.css("width",elWidth+"px");
				options.section.css("width",options.winWidth+"px");
			}
			
			//workout end scroll
			if(options.horizontal){
				options.endScroll = options.docWidth-options.section.width();
			}else{
				options.endScroll = options.docHeight-options.section.height();
			}
			
			//trigger scroll
			$(window).trigger("scroll");
			
		}
		
		//----------function to observe scrolling----------//
		function scrollListener(){
			
			if(options.listener){
				clearTimeout(options.listener);
			}
			
			options.listener = setTimeout(function(){
				
				//set scrolling to false
				options.scrolling = false;

			},100);
			
		}
		
		//----------function to workout scroll dir----------//
		function scrollDir(){
			
			//get scroll val
			if(options.horizontal){
				var scroll = $(window).scrollLeft();
			}else{
				var scroll = $(window).scrollTop();
			}
				
			//workout scroll direction
			if(scroll > options.prevScroll){
				options.scrollDir = "forward";
			}else{
				options.scrollDir = "backward";
			}
			
			//update prev scroll top
			options.prevScroll = scroll;
			
		}
		
		//----------function to animate elements when scrolling----------//
		function animateElements(){
			
			//get scroll val
			if(options.horizontal){
				var scroll = $(window).scrollLeft();
			}else{
				var scroll = $(window).scrollTop();
			}
			
			//stop anim if at start or end
			if(scroll == 0 || scroll >= options.endScroll){
				$("body,html").stop();
			}
			
			//loop sections
			options.section.each(function(){
				
				//define section
				var section = $(this);
				
				//get section top or left
				if(options.horizontal){
					var pagePos = section.position().left;
				}else{
					var pagePos = section.position().top;
				}
				
				//define elements
				var elements = section.find(options.element);

				//loop elements within section
				elements.each(function(i){
					
					//define para
					var para = $(this);
					
					//get position
					var position = para.css("position");
					
					//work out diffs
					var diff = pagePos-scroll;

					//convert to percentage
					var percent = Math.floor(diff/pagePos*100);
					
					//convert to fraction
					var fract	= diff/pagePos;
					//catch infinate and NaN
					/**BAD FIX - NEEDS WORK**/
					if(!isFinite(fract)){
						fract = diff/1000;
					}
					
					//get start pos
					var startPos 	= para.data("startPos");
					startPos.top 	= parseFloat(startPos.top);
					startPos.left 	= parseFloat(startPos.left);
					
					//if absolute
					if(position == "absolute"){
						
						//get orig position
						var origPos 	= para.data("origPos");
						origPos.top 	= parseFloat(origPos.top);
						origPos.left 	= parseFloat(origPos.left);
						
						//work out diff top
						var diffTop = origPos.top-startPos.top;
						if(diffTop < 0){
							diffTop = diffTop*-1;
						}

						//work out diff left
						var diffLeft = origPos.left-startPos.left;
						if(diffLeft < 0){
							diffLeft = diffLeft*-1;
						}
						
						//get nagative
						var negative = para.data("negative");
						
						//handle negative values
						//if neg top val
						if(negative.top){
							var top = -(diffTop*fract)+origPos.top;
						}else{
							var top = (diffTop*fract)+origPos.top;
						}	
						//if neg left val
						if(negative.left){
							var left = -(diffLeft*fract)+origPos.left;
						}else{
							var left = (diffLeft*fract)+origPos.left;
						}

					//else reletive
					}else{
						
						//work out where to animate to
						var top 	= startPos.top*fract;
						var left 	= startPos.left*fract;
						
					}

					//animate position
					para.animate({
						top 	: top,
						left 	: left
					},10); 		
					
				});

			});
				
		}
		
		//----------function to animate background image----------//
		function animateBg(){

			//get scroll val
			if(options.horizontal){
				var scroll = $(window).scrollLeft();
			}else{
				var scroll = $(window).scrollTop();
			}
			
			//reset bg if at top
			if(scroll == 0){
				options.section.css("background-position",options.bgPosReset);
			}

			//loop sections
			options.section.each(function(){
				
				//define section
				var section = $(this);
				
				//get height or width and position
				if(options.horizontal){
					var size = section.width();
					var pos  = section.position().left;
				}else{
					var size = section.height();
					var pos  = section.position().top;
				}
				
				//check for bg image
				var image = section.css("background-image");
				
				//check if in view and has bg image
				if(pos+size >= scroll && pos <= scroll+size && image != "none"){
					
					//log active section
					var sectionNo 					= section.index();
					options.sectionArray[sectionNo] = section;
					var activeSection 				= options.sectionArray.pop();
					window.activeSection 			= activeSection;
					
					//get section bg pos
					var bgPos 	= section.css("background-position").split(" ");
					if(options.horizontal){
						bgPos = parseFloat(bgPos[0].replace(/[^0-9\-]/g,""));
					}else{
						bgPos = parseFloat(bgPos[1].replace(/[^0-9\-]/g,""));
					}
					
					//webkit you are just too fast
					if($.browser.webkit){
						bgPos = bgPos*2;
					}
					
					//work out new pos
					if(options.scrollDir == "forward"){
						var newPos 	= bgPos-options.bgSpeed;
					}else{
						var newPos 	= bgPos+options.bgSpeed;
					}
					
					//stop whiteness
					if(newPos > 0){
						newPos = 0;
					}
					
					//if no repeat stop values over bgheight or width
					if(options.horizontal){
						var bgSize = section.data("bgWidth");
					}else{
						var bgSize = section.data("bgHeight");
					}
					
					if(bgSize !== undefined){
						if(newPos > bgSize){
							newPos = bgSize;
						}
					}
					
					//webkit you are just too fast
					if($.browser.webkit){
						newPos = newPos/2;
					}

					//animate bg
					if(options.horizontal){
						var bgPos = newPos+"px center";
					}else{
						var bgPos = "center "+newPos+"px";
					}
					section.css("background-position",bgPos);
					
				}
				
			});	

		}
		
		//----------function run when scrolling starts----------//
		function scrollStart(){
			
			//stop body anim
			$("body,html").stop();
			
			//log scroll start and time
			if(options.horizontal){
				var scrollStart = $(window).scrollLeft();
			}else{
				var scrollStart = $(window).scrollTop();
			}
			
			//get date
			var date = new Date();
			var time = date.getTime();
			
			options.scrollData.startTime 	= time;
			options.scrollData.scrollStart 	= scrollStart;
			
		}
		
		//----------function run when scrolling has stopped----------//
		function scrollFin(){
			
			//log scroll stop and time
			if(options.horizontal){
				var scrollStop = $(window).scrollLeft();
			}else{
				var scrollStop = $(window).scrollTop();
			}
			
			//get date
			var date = new Date();
			var time = date.getTime();
			
			options.scrollData.stopTime 	= time;
			options.scrollData.scrollStop 	= scrollStop;

			//run scrollbounce function only if page has scrolled
			if(options.scrollData.scrollStart != scrollStop){
				scrollBounce();
			}
			
		}
		
		//----------function to add pazaz to end of scroll----------//
		function scrollBounce(){
			
			//work out speed
			var time 	= options.scrollData.stopTime - options.scrollData.startTime;
			var dist 	= options.scrollData.scrollStop - options.scrollData.scrollStart;
			var speed 	= dist/time;
		
			//calculate dist to move by
			var moveBy = speed*500;
			
			//set top and left
			if(options.horizontal){
				var left 	= options.scrollData.scrollStop+moveBy; 
				var top 	= 0;
			}else{
				var top 	= options.scrollData.scrollStop+moveBy; 
				var left 	= 0;
			}
			
			//add animation if applicable
			$("html,body").not(":animated").animate({
				scrollTop 	: top,
				scrollLeft 	: left
			},{
				easing 		: "easeOutCirc",
				duration 	: 1000,
				queue		: false
			});
			
			//listen for bounceing
			var bounceCheck = setInterval(function(){
				
				//get scroll val
				if(options.horizontal){
					var scroll = $(window).scrollLeft();
				}else{
					var scroll = $(window).scrollTop();
				}
				
				//if hit top or bottom of page
				if(scroll == 0 || scroll >= options.endScroll){
					
					//hit so stop checker
					clearInterval(bounceCheck);
					
					//workout bounce vars
					if(top < 0 || left < 0){
						top 	= (moveBy*-1)/4;
						left 	= (moveBy*-1)/4
					}else{
						top 	= options.endScroll-moveBy/4;
						left 	= options.endScroll-moveBy/4;
					}
					
					//animate bounce
					$("body,html").stop().animate({
						scrollTop 	: top,
						scrollLeft 	: left
					},{
						easing 		: "easeOutCirc",
						duration 	: 500,
						queue		: false
					});
					
				}
				
			},100);
			
			//stop checking for bounce after 1sec as anim should have stopped
			setTimeout(function(){
				clearInterval(bounceCheck);
			},1010);
			
		}
		
		//----------mouseup hack for webkit browsers----------//	
		function mouseHack(){
			
			//for safari
			if(navigator.userAgent.indexOf("Safari") > -1 && navigator.userAgent.indexOf("Chrome") == -1){
				
				//treat mouseleave as mouse down to detect mousedown on scrollbar
				$(window).mouseleave(function(){
					scrollStart();
					$(window).bind("scroll",hackScroll);
				});

			//for other webkit. ie chrome
			}else{
				
				//treat mousedown on html as mousedown no scrollbar
				$(window).mousedown(function(e){
					var target = $(e.target);
					if(target.is("html")){
						$(window).bind("scroll",hackScroll);
					}				
				});
				
			}
			
			//listen for scroll stop to simulate mouse up
			var hackScroll = function(e){
				
				if(options.scrollHack){
					clearTimeout(options.scrollHack);	
				}
				
				options.scrollHack = setTimeout(function(){
					scrollFin();
					$(window).unbind(e);
				},100);

			}

		}

	});
	
}

/*----------extend easing----------*/
jQuery.extend(jQuery.easing,{
	def: "easeOutQuad",
	easeOutCirc: function (x,t,b,c,d){
		return c * Math.sqrt(1 - (t=t/d-1)*t) + b;
	}
});