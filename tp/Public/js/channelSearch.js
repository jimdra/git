/*
 * jQuery cbblinkage 联动实现(使用easyui中combobox)
 *
 * author:tomson
 * eg.
 * $('#rid').cbblinkage();
 */
document.characterSet='UTF-8';
(function($) {
	var options;

	$.fn.cbblinkage = function(opts) {
		options = $.extend({}, $.fn.cbblinkage.defaults, opts);
		initialize($(this));
	};

	$.fn.cbblinkage.defaults = {
		objects: ['rid','cid','aid'],
		method : ["advGroupList","channelList","advList"],
		params : ["rid","cid","aid"],
		width  : ["100","150","180"],
		mode   : ['','remote','remote'],
		all    :"-全部-",
		showAll : [true],
		value  : 'id',
		text   : 'text',
		ext    : [{value:'id',text:'text'}],
		url    :"index.php?m=ajax&a="
	};

	function initialize(obj){
		var len = options.objects.length;
		$.each(options.objects,function(i,r){
			index = 0;
			if(options.width[i]!=undefined)
				index = i;
			getData($("#"+r),{m:options.method[i],params:{}},index);
		});
		
	}
	
	function getData(obj,opts,i){
		field = (options.ext.length>0) ? options.ext[i]: options;
		$(obj.selector).combobox({
			mode:options.mode[i],
			width:options.width[i],
			url:options.url+opts.m,
			valueField:field.value,
			textField:field.text,
			onBeforeLoad:function(param){
				for(var j=0;j<i;j++)
					param[options.params[j]] = $("#"+options.objects[j]).combobox('getValue');
			},
			onSelect:function(selectdata){
				var param = {};
				for(var j=0;j<i;j++)
					param[options.params[j]] = $("#"+options.objects[j]).combobox('getValue');
				if(options.objects[i+1]!=undefined)
					getData($("#"+options.objects[i+1]),{m:options.method[i+1],params:param},i+1);
			}
		});
	}
})(jQuery);