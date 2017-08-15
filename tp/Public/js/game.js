document.characterSet='UTF-8';
var platformItems = [{pid:'#pid',gid:'#gid',sid:'#sid'}];
var platid = 5;
$(function(){
	//loadGame(platformItems[0],platid);
});

function loadGame(value,platid){
	$(value.gid).combobox({
		url:'/index.php?m=ajax&a=gamelist&pid='+platid,
		width:100,
		onSelect:function(item){
			if(!item.id)return;
			game_change(item.id,value,platid);
		}
	});
}
game_change = function(gid,value,platid){
	if(value==undefined)return;
	if(0 == gid){
		clear_option(value.sid);
		return;
	}
	$(value.sid).combobox({
		url:'/index.php?m=ajax&a=serverlist&pid='+platid+'&gid='+gid,
		width:120
	});
}

clear_option = function(select){
	$(select).combobox('loadData',[{'id':'','text':'','selected':1}]);
}