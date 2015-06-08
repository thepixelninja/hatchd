/*
 * COMMON.JS
 * BY ED FRYER
 * FRYED.CO.UK
 */

/*----------SOME GLOBAL VARS----------*/

var isMobile,isAppleMob,oldIE,winWidth,winHeight,layout,layoutDetector,resizeListener;

/*-----------ON DOC READY-------------*/

$(document).ready(function(){

	//kick it all off
	init();

});

/*---------------INIT----------------*/

function init(){

	//work out if on mobile
	isMobile = navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry)/) || false;

	//work out if on apple mobile
	isAppleMob = navigator.userAgent.match(/(iPhone|iPod|iPad)/) || false;

	//work out if in old ie
	oldIE = $("html").hasClass("msie");

	//layout
	layout 			= "mobile";
	layoutDetector 	= $("#layoutDetector");

	//on window resize
	resize();
	$(window).resize(function(){
		resize();
	});

	//add lightbox class to editable content images
	$(".editableContent img").each(function(){
		var link = $(this).parents("a");
		link.addClass("lightBox");
	});

	//init lightboxes
	if(isMobile){
		$("a.lightBox").touchTouch();
	}else{
		$("a.lightBox").lightBox();
	}

	//handle form labels
	if(Modernizr.input.placeholder){
		$("label").hide();
	}

	//main nav fix active classes
	$("#mainNav").find(".children .active").parents("li").addClass("active");

	//show/hide the main nav
	mainNav();

	//handle the sidebar nav
	sidebarNav();

	//sort out the sticky pods
	stickyPods();

	//init the paralax header
	initParalax();

	//back to top button
	backToTop();

	//init loading buttons
	loadingButtons();

	//init ajax forms
	ajaxForms();
	
	//init google maps
	$("#googleMap").fryedGooglemap({
		postcode 	: "Eggville, MS 38857 USA",
		width	 	: "100%",
		height	 	: 400,
		directions 	: false,
		zoom		: 12
	});
	
	//remove loading once we are ready
	loadAssets();
	
	//the eggs easter egg
	$("#eggsplosion").on("click",function(e){
		e.preventDefault();
		eggs.init();
	});

}

/*------------LOAD ASSETS-------------*/

function loadAssets(){
	
	//grab the page
	var page = $("#mainNav, #page");
	
	//load up the google web fonts
	WebFont.load({
		active : function(){
			//check all the images have loaded
			onImagesLoaded("body",function(){
				page.css("display","block");
				setTimeout(function(){
					page.css("opacity",1);
				},500);
			});
		},
    	google : {
      		families : ["Lobster","Source Sans Pro"]
    	}
	});
	
}

/*---------------MAIN NAV-------------*/

function mainNav(){
	
	//grab the toggle
	var toggle = $("#menuIcon");
	//grab the page
	var page = $("#page");
	
	//show/hide the main nav
	toggle.on("click",function(){
		$("html").toggleClass("navOpen");
		toggle.toggleClass("glyphicon-remove").toggleClass("glyphicon-menu-hamburger");
	});
	page.on("click",function(){
		$("html").removeClass("navOpen");
		toggle.removeClass("glyphicon-remove").addClass("glyphicon-menu-hamburger");
	});
	
}

/*-------------AJAX FORMS-------------*/

function ajaxForms(){

	//grab the forms
	var forms = $(".custForm form");

	//remove errors on focus
	forms.find("input,select,textarea").on("focus",function(){
		$(this).parents("li").removeClass("error");
	});

	//on submit of forms
	forms.submit(function(e){

		e.preventDefault();

		var form 	= $(this);
		var data 	= form.serialize();
		var url		= form.attr("action");

		$.ajax({
			url 	: url,
			type 	: "post",
			data	: data,
			success	: function(html){

				var html 	= $(html);
				var success = html.find("#gforms_confirmation_message");
				if(success.length){
					var message = success.text();
					showMessage(message,"message",form);
					trackForms();
				}else{
					var required = html.find(".gfield_error");
					required.each(function(){
						var id = $(this).attr("id");
						form.find("#"+id).addClass("error");
					});
					showMessage("Please fill out the required fields.","error",form);
				}
				if(typeof(loadingButtons) === "function"){
					loadingButtons(true);
				}

			},
			error	: function(a,b,c){
				//console.log(a,b,c);
				showMessage("Sorry, there has been an error submitting the form, Please try again.","error",form);
				if(typeof(loadingButtons) === "function"){
					loadingButtons(true);
				}
			}
		});

	});

}

/*------------SHOW MESSAGE------------*/

function showMessage(msg,type,appendTo){

	if(appendTo === undefined){
		appendTo = $(".editableContent:first");
	}
	$(".custMessage").remove();
	if(type == "error"){
		type = "alert-danger";
	}else{
		type = "alert-success";
	}
	var message = $("<div class='custMessage alert "+type+"'>"+msg+"</div>");
	message.hide();
	appendTo.prepend(message);
	var to = appendTo.offset().top-100;
	$("body,html").not(":animated").animate({
		scrollTop : to
	},1000,function(){
		message.slideDown("slow");
		if(typeof(loadingButtons) === "function"){
			loadingButtons(true);
		}
	});

}

/*----------LOADING BUTTONS-----------*/

function loadingButtons(cancel){

	var buttons = $(".btn");

	if(cancel !== undefined){
		buttons.each(function(){
			resetButton(this);
		});
		return false;
	}

	buttons.click(function(e){
		var btn = $(this);
		if(btn.hasClass("loading")){
			return false;
		}
		var origText = btn.data("origText");
		if(!origText){
			origText = btn.text();
			btn.data("origText",origText);
		}
		buttonLoading(this);
	});

	buttons.parents("form").find("input,select,textarea").on("blur",function(){
		loadingButtons(true);
	});

	function buttonLoading(btn,text){
		var btn = $(btn);
		if(text === undefined){
			text = "Loading";
			btn.addClass("loading");
		}
		if(!btn.hasClass("loading")){
			return false;
		}
		btn.text(text);
		switch(text){
			case "Loading":
				text = "Loading.";
			break;
			case "Loading.":
				text = "Loading..";
			break;
			case "Loading..":
				text = "Loading...";
			break;
			case "Loading...":
				text = "Loading";
			break;
		}
		setTimeout(function(){
			buttonLoading(btn,text);
		},500);
	}

	function resetButton(btn){
		var btn 	 = $(btn);
		var origText = btn.data("origText");
		if(origText){
			btn.removeClass("loading").text(origText);
		}
	}

}

/*------------STICKY PODS-------------*/

function stickyPods(){

	var sticky 			= $(".sticky");
	var stickyHeight 	= sticky.outerHeight();

	if(!sticky.length){
		return false;
	}

	var container 	= $("#mainContent");
	var origTop 	= sticky.offset().top;
	var margin		= 80;
	sticky.css("position","relative");

	$(window).scroll(function(){
		if(layout == "mobile"){
			sticky.css("top","");
			return false;
		}
		var scrollTop 	= $(window).scrollTop();
		var winHeight 	= $(window).height();
		var bottomLimit = container.outerHeight()+container.offset().top;
		if((scrollTop+margin) > origTop){
			if(scrollTop+stickyHeight < bottomLimit-margin){
				sticky.css("top",(scrollTop-origTop)+margin);
			}
		}else{
			sticky.css("top","");
		}
	});

}

/*-------------BACK TO TOP-----------*/

function backToTop(){

	var toTop = $("#backToTop");
	$(window).scroll(function(){
		var scrollTop = $(window).scrollTop();
		if(scrollTop <= 0){
			toTop.css("opacity",0);
		}else{
			toTop.css("opacity",1);
		}
	});
	toTop.on("click",function(){
		$("body,html").not(":animated").animate({
			scrollTop : 0
		},1000);
	});

}

/*--------------PARALAX--------------*/

function initParalax(){

	//dont bother if on mobile. its too jerky
	if(isMobile){
		return false;
	}

	//grab the el
	var paralax = $("#featuredImage, #featureSlider");

	//set the speed and get height
	var speed 	= 2;
	var pHeight = paralax.height();

	//if no el stop
	if(!paralax){
		return false;
	}

	//on window scroll
	$(window).scroll(function(){

		var scrollTop 	= $(window).scrollTop();
		var amount 		= scrollTop/speed;

		paralax.css({
			position : "relative",
			top		 : amount
		});

	});

}

/*------------SIDEBAR NAV------------*/

function sidebarNav(){

	//grab the els
	var pod		= $("#sideBar .pod");
	var toggle 	= pod.find(".podTitle");

	//on click on menu icon
	toggle.click(function(){
		var menu = $(this).parents(".pod").find(".podContent");
		var visible = menu.is(":visible");
		if(visible){
			menu.slideUp("fast");
		}else{
			menu.slideDown("fast");
		}
	});

}

/*----------ON WINDOW RESIZE----------*/

function resize(){

	winWidth  = $(window).width();
	winHeight = $(window).height();

	if(layoutDetector.is(":visible")){
		layout = "desktop";
	}else{
		layout = "mobile";
	}

	clearTimeout(resizeListener);
	resizeListener = setTimeout(function(){
		afterResize();
	},500);

}

/*---------AFTER WINDOW RESIZE--------*/

function afterResize(){

	switch(layout){

		case "desktop":

		break;

		case "mobile":

		break;

	}

}

/*-----MAKE AN IMAGE FILL A SPACE-----*/

function imageCover(el){

	var els = $(el);

	els.each(function(){

		var el = $(this);

		onImagesLoaded(el,function(){

			var img	= el.find("img");

			//reset img
			img.css({
				"width" 		: "",
				"height" 		: "",
				"max-width" 	: "",
				"margin-left" 	: ""
			});

			//grab the bg image and work out height and width
			var iWidth 	= img.width();
			var iHeight = img.height();

			//get the container width n height
			var cWidth  = el.width();
			var cHeight = el.height();

			//work how to style the image
			if(iHeight < cHeight){
				img.css({
					"width" 	: "auto",
					"height"	: "100%",
					"max-width" : "none"
				});
				var center = (cWidth/2)-(img.width()/2);
				img.css("margin-left",center+"px");
			}

		});

	});

}

/*-----RUN ONCE IMAGES HAVE LOADED----*/

function onImagesLoaded(el,callback){
	
	var el		= $(el);
	var images  = el.find("img");
	var imgNo   = images.length;
	var	counter = 0;
	images.each(function(i,img){
		if(img.complete){
			counter++;
		}else{
			$(img).load(function(){
				counter++;
			});
		}
	});
	var checker = setInterval(function(){
		if(counter == imgNo){
			clearInterval(checker);
			callback(el);
		}
	},100);

}

/*------MAKE ELS AN EQUAL HEIGHT------*/

function equalHeight(els,callback){

	var els 	  = $(els);
	var maxHeight = 0;

	if(window.matchMedia("(max-width: 992px)").matches){
		els.css("height","");
		return false;
	}

	els.each(function(i){
		var el = $(this);
		el.css("height","");
		var height = el.height();
		if(height > maxHeight){
			maxHeight = height;
		}
		if(i == els.length-1){
			els.css("height",maxHeight+"px");
			if(callback !== undefined){
				callback();
			}
		}
	});

}
