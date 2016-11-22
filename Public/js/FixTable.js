(function($){  
    $.fn.fixTable = function(options){ 
		var defaults = {  
			fixColumn: 0,
			width:0,
			height:0
		};    
	    var opts = $.extend(defaults, options); 
			var _this = $(this);
			var _clone = _this.clone();
			var _columnClone = _this.clone();
			var _columnDataClone = _this.clone();
			_this.wrap(function() {
               return $("<div id='_fixTableMain'></div>");
            });
			$("#_fixTableMain").css({
				"width":defaults["width"],
				"height":defaults["height"],
				"overflow":"scroll",
				"position":"relative"
			});
			$("#_fixTableMain").wrap(function() {
               return $("<div id='_fixTableBody'></div>");
            });
			$("#_fixTableBody").css({

				"width":defaults["width"],
				"height":defaults["height"],
				"overflow":"hidden",
				"position":"relative"
			});
			$("#_fixTableBody").append("<div id='_fixTableHeader'></div>");
			$(_clone).height($(_clone).find("thead").height());
			$("#_fixTableHeader").append(_clone);
			$("#_fixTableHeader").css({

				"overflow":"hidden",
				"width":defaults["width"]-17,
				"height":_clone.find("thead").find("tr").height()+1,
				"position":"absolute",
				"z-index":"88",
				"top":"0"
			});
			
			$("#_fixTableBody").append("<div id='_fixTableColumn'></div>");
			
			var _fixColumnNum = defaults["fixColumn"];
			var _fixColumnWidth = 0;
			$($(_this).find("thead").find("tr").find("th")).each(function(index, element) {

				if($(element).find("input") && $(element).find("input")[0] && $(element).find("input")[0].className=="checkbox"){
					_fixColumnNum=_fixColumnNum+1;
				}

				if(element.style.display=="none" && index+1<=_fixColumnNum)
				{
					_fixColumnNum=_fixColumnNum+1;
				}
				else if (element.style.display=="none" && index+1>_fixColumnNum){
					_fixColumnNum=_fixColumnNum-1;
				}

				if((index+1)<=_fixColumnNum){
					_fixColumnWidth += $(this).width()+4;
				}
            });
			
			$("#_fixTableColumn").css({
				"overflow":"hidden",
				"width":_fixColumnWidth,
				"height":defaults["height"]-17,
				"position":"absolute",
				"z-index":"99",
				"top":"0",
				"left":"0"
			});
			
			
			$("#_fixTableColumn").append("<div id='_fixTableColumnHeader'></div>");
			$("#_fixTableColumnHeader").css({

				"width":$("#_fixTableColumn").width(),
				"height":_this.find("thead").find("tr").height()+1,
				"overflow":"hidden",
				"position":"absolute",
				"z-index":"200"
			});
			$("#_fixTableColumnHeader").append(_columnClone);
			
			$("#_fixTableColumn").append("<div id='_fixTableColumnBody'></div>");
			$("#_fixTableColumnBody").css({

				"width":$("#_fixTableColumn").width(),
				"height":defaults["height"]-17,
				"overflow":"hidden",
				"position":"absolute",
				"z-index":"99",
				"top":"0"
			});
			$("#_fixTableColumnBody").append(_columnDataClone);
			$("#_fixTableMain").scroll(function(e) {
                $("#_fixTableHeader").scrollLeft($(this).scrollLeft());
				$("#_fixTableColumnBody").scrollTop($(this).scrollTop());
            });
		};
    
})(jQuery); 