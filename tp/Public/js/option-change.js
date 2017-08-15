/*
 * jQuery linkage 联动实现
 *
 * author:Tomson
 * eg.
 * $('#pid').linkage();
 */
document.characterSet='UTF-8';
(function($) {
    var options;

    $.fn.linkage = function(opts) {
        options = $.extend({}, $.fn.linkage.defaults, opts);
        initialize($(this));
    };

    $.fn.linkage.defaults = {
        objects: ['pid','sid'],
        method : ["platformlist","serverlist"],
        params : ["pid","sid"],
        width  : ["120px","120px"],
        all    :"-全部-",
        showAll : [true],
        showAllServer : [false],
        value  : 'id',
        text   : 'text',
        url    :"index.php?c=Ajax&a="
    };

    function initialize(obj){
        var len = options.objects.length;
        var plen = options.showAll.length;
        var _objects = options.objects;
        var _selected = options.selected;
        $.each(_objects,function(i,r){
            if(options.width[i]!=undefined)
                $("#"+r).css('width',options.width[i]);
            var q = (i+1<plen) ? i+1: plen-1;

            $("#"+r).change(function(){
                var params = {};
                for(var j=0;j<=i;j++)
                    params[options.params[j]] = $("#"+_objects[j]).val();
                if(_objects[i+1]!=undefined)
                    getData($("#"+_objects[i+1]),{
                        m:options.method[i+1],
                        all:((options.showAll[q]) ? "<option value=''>"+options.all+"</option>" : ''),
                        params:params
                    },_selected);
            });
        });
        getData(obj,{
            m:options.method[0],
            all:((options.showAll[0]) ? "<option value=''>"+options.all+"</option>" : ''),
            params:{}
        },_selected);
    }
	
    function getData(obj,opts,selected){
        var selected_server_val = selected && selected[opts.m] ? selected[opts.m] : '';
        var selected_val = selected && selected[opts.m] ? selected[opts.m] : '';
        $.post(options.url+opts.m,opts.params,function(response){
            if(opts.m=='serverlist'){
                var str = opts.all;
               
                var serverSelect = selected_server_val == '-1' ? ' selected="selected" ' : '';
                str += (options.showAllServer[0]) ? "<option value='-1' "+serverSelect+">All Server</option>" : '';
            }else{
                var str = opts.all;
            }
            var selected_html = '';
            if(response==false || response==null || response==undefined){
            }else{
                var _optionsVal = options.value;
                for(var i=0;i<response.length;i++){
                    selected_html = response[i][_optionsVal] == selected_val ? ' selected="selected" ' : '';
                    str += "<option value='"+response[i][_optionsVal]+"'"+selected_html+">"+response[i][options.text]+"</option>";
                }
            }
            obj.html(str);
            $(obj.selector).change();
        },'json');
    }
})(jQuery);
