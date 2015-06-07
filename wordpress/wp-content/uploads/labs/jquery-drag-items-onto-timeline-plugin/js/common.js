/*
 * TIMELINE PLUGIN BY ED FRYER
 * FOR STACKOVERFLOW QUESTION -> http://stackoverflow.com/questions/14627860/jquery-dragging-with-collision-detection
 */

/*----------ON DOCUMENT READY----------*/

$(document).ready(function(){
	
	$("#timeline").timeline({
		items : ".item"
	});
	
});

/*----------THE TIMELINE PLUGIN----------*/

$.fn.timeline = function(options){
	
	var defaults = {
		items : "div"
	}
	
	var options = $.extend(defaults,options)
	
	return this.each(function(){
		
		//-----SETUP-----//
		
		//define all the vars we will need later
		var el			= $(this);
		var items 		= $(options.items);
		var mousedown 	= false;
		var dragging  	= false;
		var activeItem  = false;
		var placedItems = new Array();
		
		//make everything unselectable so it dosne interfere with dragging
		$("html").find("*").css({
			"user-select" 			: "none",
			"-moz-user-select" 		: "none",
			"-webkit-user-select" 	: "none",
			"-ms-user-select" 		: "none",
			"-o-user-select" 		: "none",
		}).attr("unselectable","true").unbind("onselectstart");
		
		//-----EVENTS-----//
		
		//log when the mouse is down anywhere on the doc
		$(document).mousedown(function(){
			mousedown = true;
		});
		
		//when the mouse is released
		$(document).mouseup(function(e){
			//if was dragging an item attempt to place it
			if(mousedown && dragging){
				placeItem(e);
			}
			//log that dragging has stopped
			mousedown = false;
			dragging  = false;
		});
		
		//log when the mouse is pressed over an item
		items.mousedown(function(){
			dragging 	= true;
			//clone the active item and hide it ready for dragging
			activeItem  = $(this).clone().appendTo("body").hide();
		});
		
		//when the mouse movers over the doc
		$(document).mousemove(function(e){
			//if mouse was pressed over item attempt to drag
			if(mousedown && dragging){
				dragItem(e);
			}	
		});
		
		//-----FUNCTIONS-----//
		
		//drag the item around the screen
		function dragItem(e){
			
			//if no active item done do owt
			if(!activeItem){
				return false;
			}
			
			//work out where the drag anchor is
			var x = e.pageX-(activeItem.height()/2);
			var y = e.pageY-(activeItem.width()/2);
			
			//save the original position in case we cant place the item
			if(!activeItem.origPos){
				activeItem.origPos = {
					x : x,
					y : y
				}
			}
			
			//drag the item
			activeItem.css({
				"position"  : "absolute",
				"top" 		: y,
				"left" 		: x,
				"z-index" 	: "999",
				"opacity"	: 0.6,
				"display"	: "block"
			});
			
		}
		
		//attempt to place the item
		function placeItem(e){
			
			//if no active item dont do owt
			if(!activeItem){
				return false;
			}
			
			//define som vars needed later on
			var onTargetY = false;
			var onTargetX = false;
			var remove 	  = false;
			var collision = false;
			
			//check if item is being relesed withing the timeline bounds
			if(e.pageY > el.position().top && e.pageY < el.position().top+el.height()){
				onTargetY = true;
			}
			if(e.pageX > el.position().left && e.pageX < el.position().left+el.width()){
				onTargetX = true;
			}
			
			//if on target attempt to drop on timeline
			if(onTargetX && onTargetY){
				
				//snap to the left or right if dropped at the left or right edges
				var maxLeft  = el.position().left;
				var maxRight = el.position().left+el.width()-activeItem.width();
				x = e.pageX-(activeItem.width()/2);
				if(x < maxLeft){
					x = maxLeft;
				}else if(x > maxRight){
					x = maxRight;
				}
				
				//loop the items already on the timeline and check for collisions
				$.each(placedItems,function(i,item){
					var itemMin = item.position().left;
					var itemMax = item.position().left+item.width();
					if(x+activeItem.width() > itemMin && x < itemMax){
						collision = true; 
					}
				});
				y = el.position().top;
				
			}
			
			//if there is a collision or the item is dropped outside the timeline 
			//set x and y back to original position and set removal flag to true
			if(collision || !onTargetX || !onTargetY){
				x = activeItem.origPos.x;
				y = activeItem.origPos.y;
				remove = true;
			//if dropped inside the timeline and no collisions add item to the 
			//array of items inside the timeline
			}else{
				placedItems.push(activeItem);
			}
			
			//finally either animate the item back to where it started then remove it
			//or snap it into the timeline in the space found
			activeItem.animate({
				top 	: y,
				left 	: x
			},{
				duration : 300,
				queue 	 : false,
				complete : function(){
					
					//if remove flag set remove the item from the dom
					if(remove){
						$(this).remove();
					}
					
					//some tidying up
					activeItem.css("opacity",1);
					activeItem = false;
					
				}
			});
			
		}
		
	});
	
}