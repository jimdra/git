//全局变量	 

  
Date.prototype.format = function(format){  //格式话时间戳 ，使用方法 new Date(parseInt(想要格式化的时间戳) * 1000).format("yyyy-MM-dd hh:mm:ss"); ;
	 var o = {
	 "M+" : this.getMonth()+1, //month
	 "d+" : this.getDate(),    //day
	 "h+" : this.getHours(),   //hour
	 "m+" : this.getMinutes(), //minute
	 "s+" : this.getSeconds(), //second
	 "q+" : Math.floor((this.getMonth()+3)/3),  //quarter
	 "S" : this.getMilliseconds() //millisecond
	 }
	 if(/(y+)/.test(format)) format=format.replace(RegExp.$1,
	 (this.getFullYear()+"").substr(4 - RegExp.$1.length));
	 for(var k in o)if(new RegExp("("+ k +")").test(format))
	 format = format.replace(RegExp.$1,
	 RegExp.$1.length==1 ? o[k] :
	 ("00"+ o[k]).substr((""+ o[k]).length));
	 return format;
}

function popNewWin(title, url, width, height, id)   //打开新的窗口
{
	var id = id || "popFormWin";
    var target = $("#" + id);
	if(target.length > 0){
		target.window('options').href="";
	}

    target.window({
        title: title,
        href: url,
        modal: true,
		minimizable: false,
		inline: true,
        width: width,
        height: height,
        onClose:function(){  
        	$(this).html("");
			$(this).window('options').href="";
   		},
		closed: true
    });
    //alert(($(document).height()  / 2);								
    var m_top = ((550 - height) / 2);
	if(m_top<0) m_top = 35;
	m_top = m_top+document.documentElement.scrollTop;
    target.window("move", {
        left: ($(document).width() - width) / 2,
        top: m_top
    });

    $(window).resize(function(){
	     target.window("move",{
		     left: ($(document).width() - width) / 2,
			 top:   m_top
		});						  
	});
 	target.window("open");
	
}

function closePopWin(id)
{
    var id = id || "popFormWin";
    var target = $("#" + id);
    target.window('close');
}

function alertMsg(msg)
{
    $.messager.alert('提示', '<span style="height:30px;line-height:30px;">' + msg + '</span>');
}

String.prototype.isTime = function() {
	var reg = /^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2}) (\d{1,2}):(\d{1,2}):(\d{1,2})$/;	
	var reg1 = /^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/;
	var r = this.match(reg);
	var r1 = this.match(reg1);
	if(r==null && r1==null) return false;
	if(r!=null){
		var d= new Date(r[1], r[3]-1,r[4],r[5],r[6],r[7]);
		return (d.getFullYear()==r[1] && (d.getMonth()+1)==r[3] && d.getDate()==r[4] && 
			d.getHours()==r[5] && d.getMinutes()==r[6] && d.getSeconds()==r[7]);
	}
	if(r1!=null){
		var d1= new Date(r1[1], r1[3]-1,r1[4]);
		return (d1.getFullYear()==r1[1] && (d1.getMonth()+1)==r1[3] && d1.getDate()==r1[4]);
	}
	return false;
}

function getUrlParam(url)
{
    var m = String(url).match(/(\?|&)([^\=]+\=[^\&]+)/gi);

    var param = {};
    //检测到参数传递
    if (m) {
        $(m).each(function(i) {
            var item = m[i].replace(/(\?|&)/gi, '');
            if (item.split('=').length == 2) {
                param[item.split('=')[0]] = decodeURIComponent(item.split('=')[1]);
            }
        });
    } else {
        param = null;
    }
    
    if (param && arguments.length > 1) {
        if (typeof(param[arguments[1]]) != 'undefined') {
            return param[arguments[1]];
        } 

        return null;
    } else {
        return param;
    }
}

function getWinParam()
{
    var win = ((arguments.length > 1 && arguments[1])?$('#' + arguments[1]):$("#popFormWin"));
    var options = win.window('options');

    if (options.href) {
        if (arguments.length > 0) {
            return getUrlParam(options.href, arguments[0]);
        }

        return getUrlParam(options.href);
    }

    return null;
}
//数字颜色
function colournum(value,row,index){
	if(value < 0)return 'color:red;';
}
function gsfeecolourrow(value,row,index){
	if(0 == value)return 'color:red;';
	if(1 == value)return 'color:#00FF00;';
	if(2 == value)return 'color:green;';
}
//状态
function formatstatus(value,row,index){
	var str = '';
	if(1 == row.IsMerge){
		str = '&nbsp;&nbsp;<a href="javascript:void(0)" style="color:green" onclick="view_order('+row.FeeID+');" >查看</a>'
	}
	if(1 == value)return '审请中'+str;
	if(2 == value)return '通过'+str;
	if(0 == value)return '驳回'+str;
}
//格式化数字
function formatnum(value,row,index){
	var _m = '';
	if(null == value)return;
	reg = new RegExp(/^(\d+)(\.{1}\d+)/);
	if(0>value){
		_m = '-';
		value = Math.abs(value);
	}
	_reg=reg.exec(value);
	if(_reg){
		var str = _reg[1].toString();
	}else{
		var str = value.toString();
	}
	var step = 3;
	var splitor = ',';
	var len = str.length;

	if(len > step) {
		l1 = len%step; 
		l2 = parseInt(len/step);
		arr = [];
		first = str.substr(0, l1);
		if(first != ''){
			arr.push(first);
		}
		for(var i=0; i<l2 ; i++) {
			arr.push(str.substr(l1 + i*step, step));									 
		}
		str = arr.join(splitor);
	 }
	 if(_reg)return _m+str+_reg[2];
	 return _m+str;
}
//格式化时间
function formatdate(ns){
	if(!/\d+/.exec(ns)){
		return ns;
	}
	if(null == ns)return '';
	return new Date(parseInt(ns) * 1000).format("yyyy-MM-dd");
	reg = new RegExp(/\d{10}/)
	if(!reg.exec(ns))return ns;
	return new Date(ns * 1000).toLocaleDateString().replace(/年|月/g,'-').replace(/日/g, '');
}
function formattime(ns){
	if(ns==0 || ns=='') return '';
	if(!/\d+/.exec(ns)){
		return ns;
	}
	return new Date(parseInt(ns) * 1000).format("yyyy-MM-dd hh:mm:ss");
	reg = new RegExp(/\d{10}/)
	if(!reg.exec(ns))return ns;
	return new Date(ns * 1000).toLocaleDateString().replace(/年|月/g,'-').replace(/日/g, '');
}
Array.prototype.min = function() { 
	var min = this[0]; 
	var len = this.length; 
	for (var i = 1; i < len; i++){ 
		if (this[i] < min)min = this[i]; 
	} 
	return min; 
} 
Array.prototype.max = function() { 
	var max = this[0]; 
	var len = this.length; 
		for (var i = 1; i < len; i++){ 
			if (this[i] > max)max = this[i]; 
		} 
	return max; 
}
//编辑
function formatedit(id,row){
	if(row.operate){
		return '<a href="javascript:void(0)" style="color:green" onclick="showstep('+id+','+row.step+');" >审核</a>';
	}
	return '<a href="javascript:void(0)" style="color:green" onclick="showstep('+id+','+row.step+');" >查看</a>';
}

//EASyui扩展	
$.extend($.fn.validatebox.defaults.rules, {
	//移动手机号码验证
    mobile: {//value值为文本框中的值
        validator: function (value) {
            var reg = /^1[3|4|5|8|9]\d{9}$/;
            return reg.test(value);
        },
        message: '输入手机号码格式不准确.'
       },
	 //验证邮编  
	 zipcode: {
        validator: function (value) {
            var reg = /^[1-9]\d{5}$/;
            return reg.test(value);
        },
        message: '邮编必须是非0开始的6位数字.'
    },
	idcard : {// 验证身份证
        validator : function(value) {
            return /^\d{15}(\d{2}[A-Za-z0-9])?$/i.test(value);
        },
        message : '身份证号码格式不正确'
    },
	
	phone : {// 验证电话号码
        validator : function(value) {
            return /^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/i.test(value);
        },
        message : '格式不正确,请使用下面格式:020-88888888'
    },
	
	msn:{
        validator : function(value){
        return /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(value); 
    },
    message : '请输入有效的msn账号(例：abc@hotnail(msn/live).com)'
	},
	
	qq : {// 验证QQ,从10000开始
        validator : function(value) {
            return /^[1-9]\d{4,9}$/i.test(value);
        },
        message : 'QQ号码格式不正确'
    },
	
	integer : {// 验证整数
        validator : function(value) {
            return /^[+]?[0-9]+\d*$/i.test(value);
        },
        message : '请输入整数'
    },
	
	integer2 : {// 验证大于0的整数
        validator : function(value) {
            return /^[+]?[1-9]+\d*$/i.test(value);
        },
        message : '请输入大于0的整数'
    },	
	
	faxno : {// 验证传真
        validator : function(value) {
            return /^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/i.test(value);
        },
        message : '传真号码不正确'
    },
	
	enstr : {// 验证之只能输入英文
        validator : function(value) {
            return /^([u4e00-u9fa5]|[ufe30-uffa0]|[a-za-z0-9_]){1,30}$/i.test(value);
        },
        message : '只能输入英文'
    },
	
	zhstr : {// 验证之只能输入中文
        validator : function(value) {
            return /^[u4E00-u9FA5]+$/i.test(value);
        },
        message : '只能输入中文'
    },
	//唯一性AJAX验证
	 Unique_validation: {  
            validator: function(value, param) { 
                var m_reg = new RegExp(param[0]); //传递过来的正则字符串中的"\"必须是"\\"  
                if (!m_reg.test(value)) {  
                    $.fn.validatebox.defaults.rules.Unique_validation.message = param[1];  
                    return false;  
                }else{  
                    var postdata = {};  
                    postdata[param[3]] = value;  
                    var result = $.ajax({  
                        url: param[2],  
                        data: postdata,  
                        async: false,  
                        type: "post"  
                    }).responseText;  
                    if (result == "false") {  
                        $.fn.validatebox.defaults.rules.Unique_validation.message = param[4];  
                        return false;  
                    }else{  
                        return true;  
                    }  
                }  
            },  
            message: ''  
    },		
	//时间区间验证
  isAfter: {
    validator: function(value, param){
        var dateA = $.fn.datebox.defaults.parser(value);
        var dateB = $.fn.datebox.defaults.parser($(param[0]).datebox('getValue'));
        return dateA>new Date() && dateA>dateB;
    },
    message: '结束时间不能小于开始时间'
    } ,
    isLaterToday: {
    validator: function(value, param){
        var date = $.fn.datebox.defaults.parser(value);
        return date>new Date();
    },
    message: '开始时间不能小于今天'
    }
});


function loadPayType(value,pid){
	if(pid == undefined || pid=='') pid = 5;
	$(value).combobox({
		url:'/ajax.php?a=paytype_code&pid='+pid,
		width:160
	});
}
var createGridHeaderContextMenu = function(e, field) {
	e.preventDefault();
	var grid = $(this);/* grid本身 */
	if (!this.headerContextMenu) {
		var tmenu = $('<div style="width:100px;"></div>').appendTo('body');
		var fields = grid.datagrid('getColumnFields');
		for ( var i = 0; i < fields.length; i++) {
			var fildOption = grid.datagrid('getColumnOption', fields[i]);
			if (!fildOption.hidden) {
				$('<div iconCls="icon-ok" field="' + fields[i] + '"/>').html(fildOption.title).appendTo(tmenu);
			} else {
				$('<div iconCls="icon-empty" field="' + fields[i] + '"/>').html(fildOption.title).appendTo(tmenu);
			}
		}
		this.headerContextMenu = tmenu.menu({
			onClick : function(item) {
				var field = $(item.target).attr('field');
				if (item.iconCls == 'icon-ok') {
					grid.datagrid('hideColumn', field);
					$(this).menu('setIcon', {
						target : item.target,
						iconCls : 'icon-empty'
					});
				} else {
					grid.datagrid('showColumn', field);
					$(this).menu('setIcon', {
						target : item.target,
						iconCls : 'icon-ok'
					});
				}
			}
		});
	}
	this.headerContextMenu.menu('show', {
		left : e.pageX,
		top : e.pageY
	});
};
$.fn.datagrid.defaults.onHeaderContextMenu = createGridHeaderContextMenu;