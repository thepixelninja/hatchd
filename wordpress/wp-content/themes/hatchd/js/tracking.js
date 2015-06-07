/*
 * TRACKING.JS
 * BY ED FRYER
 * FRYED.CO.UK
 */

/*-----ON DOC READY-----*/

$(document).ready(function(){
	
	//stop if no analytics
	/*if(typeof(ga) === "undefined"){
		return false;
	}*/
	
	//track goals
	trackGoals();
	
	//track events
	trackEvents();
	
	//track the forms
	trackForms();
	
});

/*-----TRACK GOALS-----*/

function trackGoals(){
	
	var goalLinks = $("[data-goal='true']");
	
	//on click of goal link
	goalLinks.on("click",function(){
		var link = $(this);
		var type  = link.attr("data-type");
		if(type === undefined){
			type = "Not set";
		}
		var desc  = link.attr("data-description");
		if(desc === undefined){
			desc = link.text();
		}
		var trackingUrl	= "?goal="+type+"&description="+desc;
		if(typeof(ga) === "undefined"){
			console.log("Track Goal",trackingUrl,desc);
		}else{
			ga("send",{
				hitType : "pageview",
				page	: trackingUrl,
				title	: desc
			});
		}
	});
	
}

/*-----TRACK EVANTS-----*/

function trackEvents(){
	
	var eventLinks = $("[data-event='true']");
	
	//on click of event link
	eventLinks.on("click",function(){
		var link = $(this);
		var type  = link.attr("data-type");
		if(type === undefined){
			type = "Not set";
		}
		var desc  = link.attr("data-description");
		if(desc === undefined){
			desc = link.text();
		}
		if(typeof(ga) === "undefined"){
			console.log("Track Event",type,desc);
		}else{
			ga("send",{
				hitType 		: "event",
				eventCategory 	: type,
				eventAction		: "click",
				eventLabel		: desc 
			});
		}
	});
	
}

/*-----TRACK FORMS-----*/

function trackForms(){
	
	var form 	= $(".custForm");
	var success = form.find("#gforms_confirmation_message, .alert-success");
	if(!form.length || !success.length){
		return false;
	}
	var type 		= "Enquiry";
	var desc 		= document.title;
	var trackingUrl	= "?goal="+type+"&description="+desc;
	if(typeof(ga) === "undefined"){
		console.log("Track Goal",trackingUrl,desc);
	}else{
		ga("send",{
			hitType : "pageview",
			page	: trackingUrl,
			title	: desc
		});
	}

}
