/*-------------------------------//
 * 
 * FRYED.GOOGLEMAP.JS
 * JQUERY GOOGLE MAP PLUGIN
 * WRITTEN BY ED FRYER
 * WWW.FRYED.CO.UK
 * 
 -------------------------------*/

/*----------GOOGLEMAP PLUGIN----------*/

$.fn.fryedGooglemap = function(options){
	
	var defaults = {
		width 				: 800,
		height				: 400,
		lat					: 52.76018729906565,
		lng					: -1.5990419921874945,
		postcode			: null,
		zoom				: 8,
		icon				: window.themePath+"/images/map-icon.png",
		iconTitle			: "We are here",
		directions			: true,
		debug				: false,
		country			 	: "United Kingdom",
		directionsTarget 	: false
	};
	
	var options = $.extend(defaults,options);
	
	return this.each(function(){
		
		//----------SETUP-----------//
		
		//define el
		var el 	= $(this);
		var _el = this;
	
		//wrap in holder
		el.wrap("<div class='fryedMap'></div>");
		
		//add class
		el.addClass("fryedMapHolder");
		
		//define holder
		var holder = el.parent(".fryedMap");
		
		//set width of holder
		holder.css({
			width 	: options.width
		});
		
		//if options.height is an el
		if(typeof(options.height) == "object"){
			options.mirrorHeight = options.height;
			options.height = options.height.outerHeight();
		}
		
		//set width and height of map
		el.css({
			width 	: options.width,
			height 	: options.height
		});
		
		//get google maps script
		$.getScript("http://maps.googleapis.com/maps/api/js?sensor=false&callback=init");
		
		//----------EVENTS-----------//
		
		//assign events
		function assignEvents(){
			
			//click of marker
			google.maps.event.addListener(options.marker,"click",function(){
				this.setAnimation(google.maps.Animation.BOUNCE);
				setTimeout(function(){
					options.marker.setAnimation(null);
				},1400);
			});
			
			//debug - get lat lng on click
			if(options.debug){
				google.maps.event.addListener(options.map,"click",function(e){
					console.log(e.latLng.toString());
				});
			}
			
			//get directions on submit of directions form
			$(".fryedDirectionsHolder form").submit(function(e){
				
				//stop form submission
				e.preventDefault();
				
				//defnine form
				var form = $(this);
				
				//get start point
				var start = form.find("input[name='start']").val();
				start = start+" "+options.country;
				
				//define request
				var request = {
					origin 		: start,
					destination : options.center,
					travelMode 	: google.maps.TravelMode.DRIVING 
				};
				
				//get directions
				options.directionService.route(request,function(result,status){
					if(status == google.maps.DirectionsStatus.OK) {
						options.directionRenderer.setDirections(result);
					}else{
						alert("Error: There has been an error requesting your directions.");
					}
				});

			});
			
			//on resize of window
			if(options.mirrorHeight){
				$(window).resize(function(){
					el.css("height",options.mirrorHeight.outerHeight());
				});
			}
			
		}

		//-----------FUNCTIONS-----------//
		
		//-----init the map-----//
		window.init = function(){
			
			//define geocoder
			var geocoder = new google.maps.Geocoder();
			
			//if no postcode use lng and lat
			if(!options.postcode){
				options.center = new google.maps.LatLng(options.lat,options.lng);
				drawMap();
			//else geocode postcode
			}else{
				geocoder.geocode({"address":options.postcode},function(results,status){
					if(status == google.maps.GeocoderStatus.OK){
						options.center = results[0].geometry.location;
						drawMap();
					}else{
						alert("Error: Geocode was not successful for the following reason: " + status);
					}
				});
			}
			
			//function to draw the map
			function drawMap(){
				
				//set options
				var mapOptions = {
					center 			: options.center,
					zoom			: options.zoom,
					mapTypeId		: google.maps.MapTypeId.ROADMAP,
					backgroundColor	: "#efefef",
					styles			: [{ "stylers":[{"saturation":-100}]}]
				};
				
				//set map
				options.map = new google.maps.Map(_el,mapOptions);
				
				//add marker
				options.marker = new google.maps.Marker({
				    position : options.center,
				    title	 : options.iconTitle,
				    icon	 : options.icon
				});
				
				//set marker animation to drop
				options.marker.setAnimation(google.maps.Animation.DROP);
				
				//add marker to map
				setTimeout(function(){
					options.marker.setMap(options.map);
				},1000);
				
				//if directions
				if(options.directions){
					
					//define directions service
					options.directionService = new google.maps.DirectionsService();
					
					//define directions renderer
					options.directionRenderer = new google.maps.DirectionsRenderer();
					
					//set directions to map
					options.directionRenderer.setMap(options.map);
					
					if(options.directionsTarget){
						var dirDiv = "";
					}else{
						var dirDiv = "<div id='fryedDirections'></div>";
					}
					
					//add form to end of map
					holder.append("\
						<div class='fryedDirectionsHolder'>\
							<form method='post' action=''>\
								<input type='text' name='start' class='start' required='required' placeholder='Starting address'/>\
								<button type='submit' class='btn btn-default' name='get_directions'>Get directions</button>\
							</form>\
							"+dirDiv+"\
						</div>\
					");
					
					//define dir holder
					if(options.directionsTarget){
						$(options.directionsTarget).append("<div id='fryedDirections'></div>");
					}
					var dirHolder = document.getElementById("fryedDirections");
					
					//set text directions to holder
					options.directionRenderer.setPanel(dirHolder);
					
				}
			
				//add event listners
				assignEvents();
			
			}
			
		};
		
	});
	
};