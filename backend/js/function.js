var navi_delay=365,navi_timer=0,navi_item=0,dialog_req,dialog_obj;
var popup_opt={
	//'titleShow'     : false,
	'titlePosition'	: 'over',
	'transitionIn'	: 'elastic',
	'transitionOut'	: 'elastic',
	'easingIn'      : 'easeOutBack',
	'easingOut'     : 'easeInBack'
};

function set_search_form(url,kw){
	$("#search_form").attr("action",url);
	if(typeof kw!='undefined'&&''!=kw){
		$("#search_keyword").val(kw);
	}
}

function navi_open(){
	navi_cancel();
	navi_close();
	navi_item=$(this).find('ul').eq(0).css('visibility','visible')
		.css('left',$(this).position().left-1);
	if(navi_item.size()>0){
		$(this).find("a:first").addClass("popup");
	}
}
function navi_close(){
	$(".navi > li > a").removeClass("popup");
	if(navi_item)navi_item.css('visibility','hidden');
}
function navi_canceling(){
	navi_timer=window.setTimeout(navi_close,navi_delay);
}
function navi_cancel(){
	if(navi_timer){
		window.clearTimeout(navi_timer);
		navi_timer=null;
	}
}

function scroll_position(){
	var p={left:0,top:0,width:0,height:0};
	if(self.innerHeight){
		p.height=self.innerHeight;
		p.width=self.innerWidth;
		p.left=self.pageXOffset;
		p.top=self.pageYOffset;
	}else if(document.documentElement&&document.documentElement.clientHeight){//ie6
		p.height=document.documentElement.clientHeight;
		p.width=document.documentElement.clientWidth;
		p.top=document.documentElement.scrollTop;
		p.left=document.documentElement.scrollLeft;
	}else if(document.body){
		p.height=document.body.clientHeight;
		p.width=document.body.clientWidth;
		p.left=document.body.scrollLeft;
		p.top=document.body.scrollTop;
	}
	return p;
}

function js_delete(id,link,flag){
	var tr=$("tr[rel='"+id+"']");
	tr.find("td:last a[rel=delete]").hide().after('<img src="'+base_url+'css/loading.gif" width="16" height="16" align="absmiddle" rel="delete_loading" />');
	$.ajax({
		url:link,
		type:"GET",
		dataType:"json",
		success:function(json){
			if(json.result==1){
				tr.find("td:last img[rel=delete_loading]").hide()
				.after('<a href="javascript:location.reload();" style="color:#f30;">失败</a>');
			}else{
				if(flag==1){
					location.reload();
				}else{
					var ht=tr.find("td:first").height();
					tr.find("td").empty().height(ht).slideUp("fast");
				}
			}
		},
		error:function(xhr){
			tr.find("td:last img[rel=delete_loading]").hide()
				.after('<a href="javascript:location.reload();" style="color:#f30;">请刷新</a>');
		}
	});
}

function js_toggle(id,link){
	var ob=$("#"+id),t;
	t=ob.hide().after('<img src="css/loading.gif" width="16" height="16" align="absmiddle" id="'+id+'_loading" />').parents("tr");
	t.hasClass("active")?t.removeClass("active"):t.addClass("active");
	$.ajax({
		url:link,
		type:"GET",
		dataType:"json",
		success:function(json){
			var oload=$("#"+id+"_loading"),err='<a href="javascript:location.reload();" style="color:#f30;">失败</a>';
			if(json.result!=0){
				oload.hide().after(err);
			}else{
				oload.hide();
				ob.html(json.text).show();
			}
		},
		error:function(xhr){
			$("#"+id+"_loading").hide()
				.after('<a href="javascript:location.reload();" style="color:#f30;">请刷新</a>');
		}
	});
}

function load_dialog(s){
	$("textarea").each(function(){
		if(this.xheditor)$(this).xheditor(false);
	});
	show_dialog('<div class="dialog-title"><h2>加载中</h2><span><a href="javascript:close_dialog();">取消 [X]</a></span></div><div class="dialog-loading"><em /></div>');
	dialog_size(500);
	dialog_req=$.ajax({
		url:s,
		type:"GET",
		dataType:"html",
		success:function(data){
			show_dialog(data);
			update_dialog();
		},
		error:function(xhr){
			var t=(typeof xhr.responseText=='undefined')?'无法加载页面:'+s:xhr.responseText;
			show_dialog('<div class="dialog-title"><h2>加载失败</h2><span><a href="javascript:close_dialog();">关闭 [X]</a></span></div><div style="padding:10px;">'+t+'</div>');
		}
	});
}

function dialog_size(width,height){
	if(typeof height!='undefined')$("#drmain .form:first").css("height",height);
	dialog_obj.css("width",width+18);
	show_dialog();
}

function dialog_editor(selector,hgt){

	var p=scroll_position(),edt;
	if(typeof hgt=='undefined')hgt=400;

	editor = KindEditor.create(selector, {
		resizeType : 1,
		uploadJson : 'kindeditor/upload_json.php',
		fileManagerJson : 'kindeditor/file_manager_json.php',
		allowFileManager : false,
		height: hgt,
		filterMode:false
	});

	window.scroll(p.left,p.top);
	show_dialog();
	//window.scroll(p.left,p.top);
	window.scroll(0,0);
	return edt;
}

function dialog_upload(id,limit,type,meta,thumb){
    console.log(base_url);
	var swf='uploadify-multi.swf';
	if(typeof limit=='undefined'){
		limit=100;
	}
	if(limit<2){
		swf='uploadify.swf';
	}
	if(typeof type=='undefined'){
		type="web";
	}
	if(typeof thumb=='undefined'){
		thumb='uploadify/upload/'+meta;
	}else{
		thumb='uploadify/upload/'+meta+'/'+thumb;
	}
	switch(type){
		case 'doc':
			ext='*.zip;*.rar;*.doc;*.docx;*.ppt;*.pptx;*.xls;*.xlsx;*.pdf';
			desc='文档';
			break;
		case 'pic':
			ext='*.jpg;*.jpeg;*.gif;*.png';
			desc='图像';
			break;
		case 'flv':
			ext='*.flv';
			desc='flv视频';
			break;
		case 'flash':
			ext='*.swf';
			desc='Flash';
			break;
		case 'video':
			ext='*.swf';
			desc='视频';
			break;
		default:
			ext='*.jpg;*.jpeg;*.gif;*.png;*.zip;*.rar;*.doc;*.docx;*.ppt;*.pptx;*.xls;*.xlsx;*.pdf;*.flv;*.swf';
			desc='Web文件';
	}
	$(id).uploadify({
		uploader:base_url+'js/'+swf,
		cancelImg:base_url+'js/cancel.png',
		folder:'/upload',
		expressInstall:'js/expressInstall.swf',
		script:thumb,
		scriptAccess:'always',
		multi:true,
		auto:true,
		sizeLimit:200000000,
		queueSizeLimit:limit,
		fileExt:ext,
		fileDesc:desc,
		onSelectOnce:function(event,data){
			var ob=$(id+"_state").parents("fieldset");
			$(id+"_state > li > img").each(function(){
				if($(this).attr('src').indexOf('upload')<1)return;
				var t=$(this).parent().find("em a:first").attr("href");
				ob.after('<input type="hidden" name="upload_clear[]" value="'+$(this).attr('href')+'" />');
				if($(this).attr('src')!=t){
					ob.after('<input type="hidden" name="upload_clear[]" value="'+t+'" />');
				}
			});
			$(id+"_state").empty();
		},
		onError:function(a,b,c,d){
			if(d.status==404)
				alert('无法加载上传脚本');
			else if(d.type==="HTTP")
				alert('error '+d.type+": "+d.status);
			else if(d.type==="File Size")
				alert(c.name+' '+d.type+' Limit: '+Math.round(d.sizeLimit/1024)+'KB');
			else
				alert('error '+d.type+": "+d.text);
		},
        onComplete:function(event,queueID,fileObj,info,data){
            var ob=$(id+"_state");
            if(typeof info=='string'&&info.indexOf('upload/')>0){
                if('doc'==type){
                    ob.append('<li><img src="css/doc.gif" /><em><a href="'+info+'" target="_blank">下载</a><a href="uploadify/delete/'+$.base64.encodex(info)+'" rel="delete_img">删除</a></em></li>');
                    ob.append('<input type="hidden" name="'+id.substr(1)+'_list[]" value="'+info+'" style="display:none;" />');
                }else if('flv'==type){
                    ob.append('<li><img src="css/flv.gif" /><em><a href="flv.php?'+$.base64.encodex(info)+'" target="_blank">播放</a><a href="uploadify/delete/'+$.base64.encodex(info)+'" rel="delete_img">删除</a></em></li>');
                    ob.append('<input type="hidden" name="'+id.substr(1)+'_list[]" value="'+info+'" style="display:none;" />');
                }else if(info.indexOf(',')>0){
                    var ary=info.split(',');
                    ob.append('<li><img src="'+ary[1]+'" /><em><a href="'+ary[0]+'" rel="popup">查看</a><a href="uploadify/delete/'+$.base64.encodex(ary[1])+'" rel="delete_img">删除</a></em></li>');
                    for(var i in ary){
                        ob.append('<input type="hidden" name="'+id.substr(1)+'_list[]" value="'+ary[i]+'" style="display:none;" />');
                    }
                }else{
                    ob.append('<li><img src="'+info+'" /><em><a href="'+info+'" rel="popup">查看</a><a href="uploadify/delete/'+$.base64.encodex(info)+'" rel="delete_img">删除</a></em></li>');
                    ob.append('<input type="hidden" name="'+id.substr(1)+'_list[]" value="'+info+'" style="display:none;" />');
                }
            }else{
                ob.append('<li>err:'+info+'</li>');
            }
        },
		onAllComplete:function(event,data){
			var ob=$(id+"_state");
			$(id+"_button").hide();
			ob.find("a[rel*=popup]").fancybox(popup_opt);
			dialog_obj.find('fieldset ul li > img').hover(function(){
				$(this).parent().find('em').addClass('hover');
			},function(){
				$(this).parent().find('em').removeClass('hover');
			});
			dialog_obj.find('fieldset ul li em').hover(function(){
				$(this).addClass('hover');
			},function(){
				$(this).removeClass('hover');
			});
			dialog_obj.find("a[rel*=delete_img]").click(function(){
				var me=$(this);
				$.getJSON(me.attr("href"),function(json){
					if(json.result){
						alert('删除失败，请重试\ncode:'+json.result);
					}else{
						var t=$.base64.decodex(me.attr("href").split("/").pop());
						me.parents("fieldset").after('<input type="hidden" name="upload_clear[]" value="'+t+'" />');
						me.parent().parent().hide("normal",function(){
							$(this).remove();
						});
					}
				});
				return false;
			});
			show_dialog();
		}
	});
}

function show_dialog(data){
	var p=scroll_position(),obj_h,obj_w,pos={left:0,top:0};
	if(typeof data!="undefined")$("#drmain").html(data);
	obj_h=dialog_obj.show().height();
	obj_w=dialog_obj.width();
	pos.left=parseInt(p.width/2-obj_w/2);
	if(pos.left+obj_w>p.left+p.width)pos.left=p.left+p.width-obj_w;
	if(pos.left<0)pos.left=0;
	pos.top=parseInt(p.top+p.height/2-obj_h/2);
	if(pos.top+obj_h>p.top+p.height)pos.top=p.top+p.height-obj_h;
	if(pos.top<p.top)pos.top=p.top;
	dialog_obj.css(pos);
}

function close_dialog(){
	$("textarea").each(function(){
		if(this.xheditor)$(this).xheditor(false);
	});
	dialog_obj.fadeOut("fast");
	if(dialog_req)dialog_req.abort();
}

function update_dialog(){
	//ready iframe for dialog post
	if($("#ajax_frame").size()<1){
		$("body").append('<iframe id="ajax_frame" name="ajax_frame" style="display:block;" src="javascript:;"></iframe>');
	}

	//dialog form
	dialog_obj.find("form").each(function(){
		$(this).append('<input type="hidden" name="iframe" value="1" />');
		this.setAttribute("target","ajax_frame");//for ie sucks
	});

	//buttons ui
	dialog_obj.find(".button,.submit").mousedown(function(){
		$(this).addClass("click");
	}).mouseup(function(){
		$(this).removeClass("click");
	}).mouseout(function(){
		$(this).removeClass("click");
	}).click(function(){
		$(this).blur();
	});

	//active ajax
	dialog_obj.find("a[rel*=ajax]").click(function(){
		load_dialog($(this).attr("href"));
		return false;
	});

	//dialog drag
	dialog_obj.find(".dialog-title:first").dragme({control:dialog_obj});

	//dialog submit loading
	dialog_obj.find("input:submit").click(function(){
		$(this).parent().parent().append('<span class="dialog-loading"><em /></span>');
		$(this).parent().hide();
	});

	//form ui
	dialog_obj.find(".form ul").each(function(){
		$(this).find("> li:odd").addClass("right");
	});

	//date picker
	dialog_obj.find(".mydate").bind('click focus',WdatePicker);
	dialog_obj.find(".mydatetime").bind('click focus',function(){
		WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'});
	});

	//tipsy
	//$(".tipsy").remove();
	dialog_obj.find('.tip').tipsy({fade:false,gravity:'s'});
	dialog_obj.find('.tipdown').tipsy({fade:false,gravity:'n'});
	dialog_obj.find('.tipleft').tipsy({fade:false,gravity:'e'});
	dialog_obj.find('.tipright').tipsy({fade:false,gravity:'w'});

	//fancybox
	dialog_obj.find("a[rel*=popup]").fancybox(popup_opt);

	//vertical align
	if(!$.browser.msie||parseInt($.browser.version)>6){
		$("input:radio,input:checkbox,label").css({verticalAlign:"middle"});
	}

	//data table
	dialog_obj.find(".datable > tbody > tr:odd").addClass("odd");
	dialog_obj.find(".datable > tbody > tr").hover(function(){
		$(this).addClass("hover");
	},function(){
		$(this).removeClass("hover");
	});

	//upload img hover menu
	dialog_obj.find('fieldset ul li > img').hover(function(){
		$(this).parent().find('em').addClass('hover');
	},function(){
		$(this).parent().find('em').removeClass('hover');
	});
	dialog_obj.find('fieldset ul li em').hover(function(){
		$(this).addClass('hover');
	},function(){
		$(this).removeClass('hover');
	});
	dialog_obj.find("a[rel*=delete_img]").click(function(){
		var me=$(this);
		$.getJSON(me.attr("href"),function(json){
			if(json.result){
				alert('删除失败，请重试\ncode:'+json.result);
			}else{
				var t=$.base64.decodex(me.attr("href").split("/").pop());
				me.parents("fieldset").append('<input type="hidden" name="upload_clear[]" value="'+t+'" />');
				me.parent().parent().hide("normal",function(){
					$(this).remove();
				});
			}
		});
		return false;
	});

	if(typeof page_load=='function'){
		page_load();
	}
}

if(typeof nav_index=='undefined')nav_index=1;
if(typeof sub_index=='undefined')sub_index=1;

$(function(){
	//topbar to navi
	$("#topbar em a").click(function(){
		var ob=$("#navis > ul").eq($(this).parent().index());
		if(ob.size()<1||ob.is(":visible"))return;
		$("#navis > ul").hide();
		$("#topbar em a").removeClass("active");
		$(this).addClass("active").blur();
		ob.show();
	}).eq(nav_index-1).trigger("click");

	//navi search
	$("#tb-right input:text").hover(function(){
		$(this).addClass("hover");
	},function(){
		$(this).removeClass("hover");
	}).focus(function(){
		$(this).addClass("focus").select();
	}).blur(function(){
		$(this).removeClass("focus");
		if($.trim($(this).val())==""){
			$(this).val($(this).attr("title"));
		}
	}).mouseup(function(){
		return false;
	}).keyup(function(){
		var sz_min=8,sz_max=26,n;
		n=$(this).val().replace(/[^\u0000-\u00ff]/g,"aa").length+1;
		if(n<sz_min)n=sz_min;
		else n=(n<sz_max)?n:sz_max;
		$(this).attr("size",n);
	});

	//navi
	$(".navi").each(function(){
		$(this).find("> li:last").addClass("last");
	});
	$(".navi > li a").mousedown(function(){
		var t="click";
		if($(this).hasClass("popup"))t="popup-"+t;
		else if($(this).hasClass("active"))t="active-"+t;
		$(this).addClass(t);
	}).mouseup(function(){
		var t="click";
		if($(this).hasClass("popup"))t="popup-"+t;
		else if($(this).hasClass("active"))t="active-"+t;
		$(this).removeClass(t);
	}).mouseout(function(){
		var t="click";
		if($(this).hasClass("popup"))t="popup-"+t;
		else if($(this).hasClass("active"))t="active-"+t;
		$(this).removeClass(t);
	});
	$(".navi:eq("+(nav_index-1)+") > li:eq("+(sub_index-1)+") a").addClass("active");

	//navi drop menu
	$('.navi > li').bind('mouseover',navi_open);
	$('.navi > li').bind('mouseout',navi_canceling);


	//side menu
	$("#menu ul li a").mousedown(function(){
		$(this).addClass("click");
	}).mouseup(function(){
		$(this).removeClass("click");
	}).mouseout(function(){
		$(this).removeClass("click");
	});
	//三级菜单特效
	$('.navi li').mouseover(function(){
		$(this).children('.second_menu').css('min-width',$(this).children('a').width());
	})
	$('.navi li ul li').hover(function(){
		$(this).children('ul').css('visibility','visible');
	},function(){
		$(this).children('ul').css('visibility','hidden');

	})

	//ie6 min/max width
	if($.browser.msie&&parseInt($.browser.version)<7){
		var minw=960,maxw=9999;
		$(window).resize(function(){
			var t,p=$(this).width();
			if(p<minw)t=minw;
			if(p>minw&&p<maxw)t="100%";
			//if(p>minw&&p<maxw)t=p;
			if(p>maxw)t=maxw;
			$("#wrap").width(t);
		});
		if($("#wrap").width()<minw){
			$("#wrap").width(minw);
		}else if($("#wrap").width()>maxw){
			$("#wrap").width(maxw);
		}
	}else{
		$("input:radio,input:checkbox,label").css({verticalAlign:"middle"});
	}

	//data table
	$(".datable > tbody > tr:odd").addClass("odd");
	$(".datable > tbody > tr").hover(function(){
		$(this).addClass("hover");
	},function(){
		$(this).removeClass("hover");
	}).click(function(){
		if($(this).attr("value")!='noclick'){
			//var n1=$(this).find(".text,.password,textarea").size();
			//if(n1<1){
				if($(this).hasClass("active")){
					$(this).removeClass("active")
				}else{
					$(this).addClass("active")
				}
				if($(this).attr("rel")&&$(this).attr("rel").length>0){
					$(this).val($(this).hasClass("active")?$(this).attr("rel"):"");
				}
			//}
		}
	});

	//dialog
	$("body").append('<div id="dialog" class="dialog" style="display:none;">\
		<table class="dialog-wrap" border="0" cellpadding="0" cellspacing="0">\
		<tr><td colspan="3" class="dlg-trans"></td></tr>\
		<tr><td class="dlg-transl">&nbsp;</td>\
			<td class="drwrap">\
				<div class="drwrapin"><div class="drmain" id="drmain"></div></div></td>\
			<td class="dlg-transl">&nbsp;</td></tr>\
		<tr><td colspan="3" class="dlg-trans"></td></tr>\
		</table></div>');
	$("a[rel*=ajax]").click(function(){
		load_dialog($(this).attr("href"));
		return false;
	});
	dialog_obj=$("#dialog");

	//buttons ui
	$(".button,.submit").mousedown(function(){
		$(this).addClass("click");
	}).mouseup(function(){
		$(this).removeClass("click");
	}).mouseout(function(){
		$(this).removeClass("click");
	}).click(function(){
		$(this).blur();
	});

	//tipsy
	$(".tipsy").remove();
	$('.tip').tipsy({fade:false,gravity:'s'});
	$('.tipdown').tipsy({fade:false,gravity:'n'});
	$('.tipleft').tipsy({fade:false,gravity:'e'});
	$('.tipright').tipsy({fade:false,gravity:'w'});

	//fancybox
	$("a[rel*=popup]").fancybox(popup_opt);

	//toggle check all
	$("a[rel=check-all]").click(function(){
		$(this).parents("form").find(".datable tbody tr").addClass("active").each(function(){
			$(this).val($(this).attr("rel"));
		});
	});
	$("a[rel=check-off]").click(function(){
		$(this).parents("form").find(".datable tbody tr").removeClass("active").each(function(){
			$(this).val("");
		});
	});

	//delete one item
	$("a[rel=delete]").click(function(){
		if(!confirm("您确定要删除这个项目？")){
			return false;
		}
		return true;
	});

	//access delete?
	$("a[rel=delete-all]").click(function(){
		var fm=$(this).parents("form"),ar=[];
		fm.find(".datable tbody tr").each(function(){
			if($(this).val()!=""){
				ar.push($(this).val());
			}
		});
		if(ar.length<1){
			alert("请至少选择一个项目");
			return false;
		}
		if(!confirm("您确定要删除选定的项目？")){
			return false;
		}
		for(var i in ar){
			fm.append('<input type="hidden" name="checked[]" value="'+ar[i]+'" style="display:none;" />');
		}
		fm.append('<input type="hidden" name="access" value="delete" style="display:none;" />');
		fm.submit();
	});

	//access auditing?
	$("a[rel=auditing-all]").click(function(){
		var fm=$(this).parents("form"),ar=[];
		fm.find(".datable tbody tr").each(function(){
			if($(this).val()!=""){
				ar.push($(this).val());
			}
		});
		if(ar.length<1){
			alert("请至少选择一个项目");
			return false;
		}
		if(!confirm("您确定要审核选定的项目？")){
			return false;
		}
		for(var i in ar){
			fm.append('<input type="hidden" name="checked[]" value="'+ar[i]+'" style="display:none;" />');
		}
		fm.append('<input type="hidden" name="access" value="auditing" style="display:none;" />');
		fm.submit();
	});

	//access cancel_auditing?
	$("a[rel=cancel_auditing-all]").click(function(){
		var fm=$(this).parents("form"),ar=[];
		fm.find(".datable tbody tr").each(function(){
			if($(this).val()!=""){
				ar.push($(this).val());
			}
		});
		if(ar.length<1){
			alert("请至少选择一个项目");
			return false;
		}
		if(!confirm("您确定要取消审核选定的项目？")){
			return false;
		}
		for(var i in ar){
			fm.append('<input type="hidden" name="checked[]" value="'+ar[i]+'" style="display:none;" />');
		}
		fm.append('<input type="hidden" name="access" value="cancel_auditing" style="display:none;" />');
		fm.submit();
	});

	//more access
	$("a[rel=access]").click(function(){
		var fm=$(this).parents("form"),ar=[];
		fm.find(".datable tbody tr").each(function(){
			if($(this).val()!=""){
				ar.push($(this).val());
			}
		})
		if(ar.length<1){
			alert("请至少选择一个项目");
			return false;
		}
		for(var i in ar){
			fm.append('<input type="hidden" name="checked[]" value="'+ar[i]+'" style="display:none;" />');
		}
		fm.append('<input type="hidden" name="access" value="'+$(this).attr("name")+'" style="display:none;" />');
		fm.submit();
	});
	$("select[rel=access]").change(function(){
		var fm=$(this).parents("form"),ar=[];
		fm.find(".datable tbody tr").each(function(){
			if($(this).val()!=""){
				ar.push($(this).val());
			}
		})
		if(ar.length<1){
			alert("请至少选择一个项目");
			this.selectedIndex=0;
			return false;
		}
		for(var i in ar){
			fm.append('<input type="hidden" name="checked[]" value="'+ar[i]+'" style="display:none;" />');
		}
		fm.append('<input type="hidden" name="access" value="'+$(this).attr("name")+'" style="display:none;" />');
		fm.submit();
	});

	$("input:text").focus(function(){
		$(this).addClass("text-hover");
	}).blur(function(){
		$(this).removeClass("text-hover");
	});
}).click(navi_close);
