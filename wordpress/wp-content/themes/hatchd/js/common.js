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
	
	//check for full screen mode support
	if(document.fullscreenEnabled !== undefined){
		$("html").addClass("hasFullscreenMode");
	}else{
		$("html").addClass("noFullscreenMode");
	}
	
	//on click of full screen for labs
	$("#labs .fullScreen").click(function(){
		if(document.fullscreenEnabled){
			document.exitFullscreen();
		}else{
			var parent = $(this).parents(".labItem").get(0);
			parent.requestFullscreen();
		}
	});
	$("#labs .labItem").dblclick(function(){
		document.exitFullscreen();
	});
	
	//main nav fix active classes
	$("#mainNav").find(".children .active").parents("li").addClass("active");
	
	//show/hide the main nav
	$("#menuIcon").on("click",function(){
		$("html").toggleClass("navOpen");
	});
	$("#page").on("click",function(){
		$("html").removeClass("navOpen");
	});
	
	//init portfolio mixitup
	initPortfolioFilter();
	
	//init isotope on folio gal
	initIsotope("#folioGal",".imageHolder");
	
	//handle google comments
	googleComments();
	
	//the social tooltip
	socialToolTips();
	
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

/*--------INIT PORTFOLIO FILTER--------*/

function initPortfolioFilter(){
	
	//grab items
	var gallery = $("#projects");
	var filter  = $("#sideBar .filter");
	var items	= ".mix";
	
	//if no els stop
	if(!gallery.length || !filter.length){
		return false;
	}
	
	//init isotope on portfolio
	initIsotope(gallery,items);
	
	//check for hash
	var hash = window.location.hash.replace("#","");
	if(hash !== ""){
		var show = "."+hash;
		gallery.isotope({ 
			filter : show
		});
		filter.removeClass("active");
		filter.filter("[data-filter='"+show+"']").addClass("active");
	}
	
	//on click of filter
	filter.on("click",function(){
		var el = $(this);
		filter.removeClass("active");
		el.addClass("active");
		var show = el.attr("data-filter");
		gallery.isotope({ 
			filter : show
		});
	});
	
}

/*-----------INIT ISOTOPE------------*/

function initIsotope(el,selector){
	
	var iso = $(el);
	if(!iso.length){
		return false;
	}
	onImagesLoaded(iso,function(){
		iso.isotope({
			itemSelector : selector,
			layout		 : "masonry"
		});
	});
	
}

/*-------------MORE INFO--------------*/

function moreInfo(){
	
	var folio = $("#projects");
	
	if(!folio.length){
		return false;
	}
	
	var items = folio.find(".portfolioItem");
	items.each(function(){
		var item = $(this);
		onImagesLoaded(item,function(){
			var titleHeight = item.find("h3").outerHeight(true)+20;
			var height		= item.find("img").height();
			var info		= item.find(".info");
			info.css("top",height-titleHeight);
		});
	});
	
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
	var paralax = $("#paralax");
	
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
	
		/*if(amount < 0){
			amount = 0;
		}else if(amount > -pHeight){
			amount = -pHeight;
		}*/
		
		//transform dont work due to a webkit bug regarding fixed bg images
		/*if(Modernizr.csstransforms){
			paralax.css({
				"transform" 		: "translateY("+amount+"px)",
				"-webkit-transform" : "translateY("+amount+"px)",
				"-moz-transform" 	: "translateY("+amount+"px)",
			});
		}else{
			paralax.css({
				position : "relative",
				top		 : amount
			});
		}*/
		
	});
	
}

/*----------GOOGLE COMMENTS----------*/

function googleComments(loadNow){
	
	//grab the comments
	var comments = $("#comments");
	
	//check if comments are present
	if(!comments.length){
		return false;
	}
	
	//check if already loaded
	if(comments.find("iframe").length){
		return false;
	}
	
	//if load now just show comments
	if(loadNow !== undefined){
		
		loadComments();
		
	//show the comments but
	}else{
		
		var commentsBut = $("<button class='btn center-block btn-large btn-full'>Show Comments</button>");
		comments.append(commentsBut);
		
		//on click of show comments but
		commentsBut.on("click",function(){
			loadComments();
		});
		
	}
	
	//load the actual comments
	function loadComments(){
		var width = comments.width();
		if(typeof(gapi) === "undefined"){
			loadGoogleScripts(function(){
				setTimeout(function(){
					loadComments();
				},1000);
			});
		}else{
			gapi.comments.render(comments.get(0),{
				href					: window.location.href,
				width					: width,
				first_party_property 	: "BLOGGER",
				view_type				: "FILTERED_POSTMOD"
			});
			comments.attr("data-loaded","true");
		}
	}
	
}

/*-----------SOCIAL POPUPS-----------*/

function socialToolTips(){
	
	//grab the els we need
	var links = $("a[data-share]");
	var modal = $("#socialModal");
	
	//on click
	links.on("click",function(e){
		e.preventDefault();
		$(this).focus();
	});
	
	//setup the popovers
	links.each(function(){
		var link 		= $(this);
		var site 		= link.attr("data-share");
		var intent 		= link.attr("data-intent");
		var url	 		= link.attr("data-url");
		var placement 	= link.attr("data-placement");
		if(url === undefined){
			url = window.location.href;
		}
		if(placement === undefined){
			placement = "bottom";
		}
		var button = " ";
		switch(site){
			case "facebook":
				button = "<div class='fb-like' data-href='"+url+"' data-layout='button_count' data-action='like' data-show-faces='false' data-share='false'></div>";
			break;
			case "google":
				button = "<div class='g-plusone' data-size='medium' data-href='"+url+"' data-callback='plusOneLoaded'></div>";
			break;
			case "twitter":
				if(intent == "follow"){
					button = "<a href='https://twitter.com/PixelNinjaSan' class='twitter-follow-button' data-show-count='false' data-show-screen-name='false'></a>";
				}else{
					button = "<a href='https://twitter.com/share' class='twitter-share-button' data-url='"+url+"' data-via='PixelNinjaSan'></a>";
				}
			break;
		}
		link.popover({
			content 	: button,
			placement 	: placement,
			trigger		: "focus",
			html		: true
		});
	});
		
	//on hover of links load up social script
	links.on("mouseover show.bs.popover shown.bs.popover",function(e){
		var link = $(this);
		var site = link.attr("data-share");
		switch(site){
			case "facebook":
				loadFBScripts();
			break;
			case "google":
				renderGooglePlus();
			break;
			case "twitter":
				loadTwitterScripts();
			break;
		}
	});
	
}

/*------LOAD UP TWITTER SCRIPTS------*/

function loadTwitterScripts(){

	try {
		twttr.widgets.load();
	}catch(e){
		$.getScript("//platform.twitter.com/widgets.js",function(){
			twttr.widgets.load();
		});
	}
	
}

/*-------LOAD UP GOOGLE SCRIPTS------*/

function loadGoogleScripts(callback){
	
	if(typeof(callback) !== "function"){
		calback = function(){};
	}
	if(typeof(gapi) === "undefined"){
		(function() {
			var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			po.src = "https://apis.google.com/js/plusone.js";
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		})();
		var timer = setInterval(function(){
			if(typeof(gapi) !== "undefined"){
				callback();
				clearInterval(timer);
			}
		},500);
	}else{
		callback();
	}

}

/*---------LOAD UP G+ BUTTON---------*/

function renderGooglePlus(callback){
	
	if(typeof(callback) !== "function"){
		calback = function(){};
	}
	try{
		gapi.plusone.go();
		callback();
	}catch(e){
		loadGoogleScripts(function(){
			window.plusOneLoaded = function(){
				gapi.plusone.go();
				callback();
			};
		});
	}

}

/*----LOAD UP A FACEBOOK SCRIPTS-----*/

function loadFBScripts(callback){
	
	if(typeof(callback) !== "function"){
		calback = function(){};
	}
	try{
		FB.XFBML.parse(null,callback);
	}catch(e){
		(function(d,s,id){
		var js,fjs = d.getElementsByTagName(s)[0];
		if(d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&appId=1495195434046623&version=v2.0";
		fjs.parentNode.insertBefore(js,fjs);
		}(document,"script","facebook-jssdk"));
		window.fbAsyncInit = function(){
			FB.XFBML.parse(null,callback);
		};
	};
	
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
		
			//force load google comments
			googleComments(true);
			
			//fix the folio pods
			moreInfo();
			
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

	



