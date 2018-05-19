//from ppdrag
(function($){
	$.fn.dragme=function(options){
		if(typeof options=='string'){
			if(options=='destroy')return this.each(function(){
				$.dragme.removeEvent(this,'mousedown',$.dragme.start,false);
				$.data(this,'dragme-ob',null);
			});
		}
		return this.each(function(){
			var opt={options:$.extend({},options)};
			if(typeof opt.options.control=='object'){
				this.control=opt.options.control[0];
			}else if(typeof opt.options.control=='string'){
				this.control=$(opt.options.control)[0];
			}
			$(this).css({cursor:"move"});
			$.data(this,'dragme-ob',opt);
			$.dragme.addEvent(this,'mousedown',$.dragme.start,false);
		});
	};
	$.dragme={
		start:function(event){
			if(!$.dragme.current){
				var ob=(typeof this.control=='object')?this.control:this;
				$.dragme.current={
					el:this,
					ctrl:ob,
					oleft:parseInt(ob.style.left)||0,
					otop:parseInt(ob.style.top)||0,
					ox:event.pageX||event.screenX,
					oy:event.pageY||event.screenY
				};
				var current=$.dragme.current;
				var data=$.data(current.el,'dragme-ob');
				if(data.options.zIndex){
					current.zIndex=current.ctrl.style.zIndex;
					current.ctrl.style.zIndex=data.options.zIndex;
				}
				$.dragme.addEvent(document,'mouseup',$.dragme.stop,true);
				$.dragme.addEvent(document,'mousemove',$.dragme.drag,true);
			}
			if(event.stopPropagation)event.stopPropagation();
			if(event.preventDefault)event.preventDefault();
			return false;
		},
		drag:function(event){
			if(!event)var event=window.event;
			var current=$.dragme.current;
			current.ctrl.style.left=(current.oleft+(event.pageX||event.screenX)-current.ox)+'px';
			current.ctrl.style.top=(current.otop+(event.pageY||event.screenY)-current.oy)+'px';
			if(event.stopPropagation)event.stopPropagation();
			if(event.preventDefault)event.preventDefault();
			return false;
		},
		stop:function(event){
			var current=$.dragme.current;
			var data=$.data(current.el,'dragme-ob');
			$.dragme.removeEvent(document,'mousemove',$.dragme.drag,true);
			$.dragme.removeEvent(document,'mouseup',$.dragme.stop,true);
			if(data.options.zIndex){
				current.ctrl.style.zIndex=current.zIndex;
			}
			if(data.options.stop){
				data.options.stop.apply(current.el,[current.el]);
			}
			$.dragme.current=null;
			if(event.stopPropagation)event.stopPropagation();
			if(event.preventDefault)event.preventDefault();
			return false;
		},
		addEvent:function(obj,type,fn,mode){
			if(obj.addEventListener)
				obj.addEventListener(type,fn,mode);
			else if(obj.attachEvent){
				obj["e"+type+fn]=fn;
				obj[type+fn]=function(){return obj["e"+type+fn](window.event);}
				obj.attachEvent("on"+type,obj[type+fn]);
			}
		},
		removeEvent:function(obj,type,fn,mode){
			if(obj.removeEventListener)
				obj.removeEventListener(type,fn,mode);
			else if(obj.detachEvent){
				obj.detachEvent("on"+type,obj[type+fn]);
				obj[type+fn]=null;
				obj["e"+type+fn]=null;
			}
		}
	};
})(jQuery);

