/*----------ON DOCUMENT READY----------*/

$(document).ready(function(){
	
	$("#speech").talk({
		speed : 50
	});
	
});

/*----------TALK PLUGIN----------*/

$.fn.talk = function(options){
	
	var defaults = {
		speed : 100,
		pause : 2000
	}
	
	var options = $.extend(defaults,options);
	
	return this.each(function(){
		
		/*------SETUP-----*/
		
		//init vars
		var el 			= $(this);
		var prompt		= "<span class='prompt'>_</span>";
		var holder 		= "<div class='speechHolder'>"+prompt+"</div>";
		var paragraphs	= new Array();
		var elHeight	= el.height();
		
		//remove overflow
		el.css("overflow","hidden");
		
		//add paragraphs to array
		el.find("p").each(function(){
			paragraphs.push(this);
		});
		
		//add prompt and holder
		el.html(holder);
		holder = el.find(".speechHolder");
		animatePrompt();
		
		//kick off writing the first paragraph
		setTimeout(function(){
			sayParagraph(paragraphs);
		},options.pause);
		
		/*-----FUNCTIONS-----*/
		
		//say a paragraph
		function sayParagraph(paragraphs){
			
			//get the text and character length
			var paragraph	= paragraphs.shift();
			var text 		= $(paragraph).text();
			var charLength	= text.length;
			var classes		= $(paragraph).attr("class");
			
			//add the paragraph to add to
			holder.append("<p class='"+classes+"'></p>");
			var p = holder.find("p:last");
			
			//loop the paragraphs letters and add one by one
			$.each(text,function(i,letter){
				setTimeout(function(){
					
					//remove prompt
					holder.find(".prompt").remove();
					
					//get current text and count
					var curText 	= p.text();
					var curLength 	= curText.length;
					
					//add in the prompt and new text
					var newText = curText+letter+prompt;
					p.html(newText);
					
					//if at the end of the cur text move on to next paragraph
					if(curLength+1 == charLength){
						setTimeout(function(){
							sayParagraph(paragraphs);
						},options.pause);
					}
					
					//animate scroll to keep text in the middle
					animateScroll();
					
				},options.speed*i);
			});
		}
		
		//animate the prompt
		function animatePrompt(){
			var prompt 	= holder.find(".prompt");
			var text 	= prompt.html();
			if(text == "_"){
				text = "&nbsp;";
			}else{
				text = "_";
			}
			prompt.html(text);
			setTimeout(function(){
				animatePrompt();
			},500);
		}

		//animate the scroll
		function animateScroll(pos){
			var prompt = holder.find(".prompt");
			var pos = prompt.offset().top-holder.offset().top;
			if(pos > elHeight/2){
				el.scrollTop(holder.height());
			}
		}
		
	});
	
}

