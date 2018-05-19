(function($){
$.extend({
utf8:{
	encode:function(s){
		s=s.replace(/\r\n/g,"\n");
		var t="",n,c;
		for(n=0;n<s.length;n++){
			c=s.charCodeAt(n);
			if(c<128)t+=String.fromCharCode(c);
			else if((c>127)&&(c<2048))t+=String.fromCharCode((c>>6)|192)+String.fromCharCode((c&63)|128);
			else t+=String.fromCharCode((c>>12)|224)+String.fromCharCode(((c>>6)&63)|128)+String.fromCharCode((c&63)|128);
		}return t;
	},
	decode:function(s){
		var m="",i=c=c1=c2=0;
		while(i<s.length){
			c=s.charCodeAt(i);
			if(c<128){
				m+=String.fromCharCode(c);
				i++;
			}else if((c>191)&&(c<224)){
				c2=s.charCodeAt(i+1);
				m+=String.fromCharCode(((c&31)<<6)|(c2&63));
				i+=2;
			}else{
				c2=s.charCodeAt(i+1);
				c3=s.charCodeAt(i+2);
				m+=String.fromCharCode(((c&15)<<12)|((c2&63)<<6)|(c3&63));
				i+=3;
			}
		}return m;
	},
	length:function(s){
		var totalLength=0,i,charCode;
		for(i=0;i<s.length;i++){
			charCode=s.charCodeAt(i);
			if(charCode<0x007f)
				totalLength=totalLength+1;
			else if(0x0080<=charCode&&charCode<=0x07ff)
				totalLength+=2;
			else if(0x0800<=charCode&&charCode<=0xffff)
				totalLength+=3;
		}
		return totalLength;
	},
	//sub string for gbk,chinese words length=2,minus value is not supported
	substr:function(str,start,len){
		var t='',cnt=0,i,n;
		len=parseInt(len);
		if(isNaN(len))len=0;
		for(i=0;i<str.length;i++){
			n=str.charCodeAt(i);
			if(n<0x007f)
				cnt++;
			else if(0x0080<=n&&n<=0x07ff)
				cnt+=2;
			else if(0x0800<=n&&n<=0xffff)
				cnt+=2;//for displaying, this char is occupied 2 ascii
			if(cnt>=start){
				t+=str.substr(i,1);
				//cnt starts from 1, but start starts from 0, so no need "+1"
				if(len>0&&cnt-start>=len)return t;
			}
		}
		return t;
	},
	tail:function(str,len,tailstr){
		if(typeof tailstr==='undefined')tailstr='...';
		var t=this.substr(str,0,len);
		if(t.length!=str.length)t+=tailstr;
		return t;
	}
},
cookie:{
	set:function(n,v,e){
		var s,t='';
		if(typeof e!="undefined"&&e>0){
			s=new Date();
			s.setTime(e.getTime()+e);
			t=';expires='+s.toGMTString();
		}document.cookie=n+'='+escape(v)+';path=/'+t;
		return v;
	},
	get:function(n){
		var m=n+'=',y=document.cookie,l=y.length,b=0,g,x;
		while(b<l){
			g=b+m.length;
			if(y.substring(b,g)==m){
				x=y.indexOf(';',g);
				if(-1==x)x=l;
				return unescape(y.substring(g,x));
			}
			b=y.indexOf(' ',b)+1;
			if(0==b)break;
		}return null;
	},
	del:function(n){
		document.cookie=n+'='+';expires=Thu, 01-Jan-70 00:00:01 GMT'+';path=/';
	},
	setx:function(n,v,e){
		return this.set(n,$.utf8.encode(v),e);
	},
	getx:function(n){
		var t=this.get(n);
		return (null==t)?null:unescape($.utf8.decode(t));
	}
},
base64:{
	_e:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
	_d:[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,62,-1,-1,-1,63,52,53,54,55,56,57,58,59,60,61,-1,-1,-1,-1,-1,-1,-1,0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,-1,-1,-1,-1,-1,-1,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,-1,-1,-1,-1,-1],
	_16_8:function(s){
		var i,c,t="",E=String.fromCharCode;
		for(i=0;i<s.length;i++){
		c=s.charCodeAt(i);
		if((c>=0x0001)&&(c<=0x007F)){
		t+=s.charAt(i);
		}else if(c>0x07FF){
		t+=E.call(0xE0|((c>>12)&0x0F));
		t+=E.call(0x80|((c>>6)&0x3F));
		t+=E.call(0x80|((c>>0)&0x3F));
		}else{
		t+=E.call(0xC0|((c>>6)&0x1F));
		t+=E.call(0x80|((c>>0)&0x3F));}}return t;
	},
	_8_16:function(_1){
		var t="",i=0,_4=_1.length,c,_2,_3;
		while(i<_4){
		c=_1.charCodeAt(i++);
		switch(c>>4){
		case 0:case 1:case 2:case 3:case 4:case 5:case 6:case 7:
		t+=_1.charAt(i-1);
		break;
		case 12:case 13:
		_2=_1.charCodeAt(i++);
		t+=String.fromCharCode(((c&0x1F)<<6)|(_2&0x3F));
		break;
		case 14:
		_2=_1.charCodeAt(i++);
		_3=_1.charCodeAt(i++);
		t+=String.fromCharCode(((c&0x0F)<<12)|
		((_2&0x3F)<<6)|
		((_3&0x3F)<<0));
		break;}}return t;
	},
	_c:function(s){
		var t="",i=0,_0=s.length,_1,_2,_3;
		while(i<_0){
		_1=s.charCodeAt(i++)&0xff;
		if(i==_0){
		t+=$.base64._e.charAt(_1>>2);
		t+=$.base64._e.charAt((_1&0x3)<<4);
		t+="==";
		break;}
		_2 = s.charCodeAt(i++);
		if(i==_0){
		t+=this._e.charAt(_1>>2);
		t+=this._e.charAt(((_1&0x3)<< 4)|((_2&0xF0)>>4));
		t+=this._e.charAt((_2&0xF)<<2);
		t+="=";
		break;}
		_3=s.charCodeAt(i++);
		t+=this._e.charAt(_1>>2);
		t+=this._e.charAt(((_1&0x3)<< 4)|((_2&0xF0)>>4));
		t+=this._e.charAt(((_2&0xF)<<2)|((_3&0xC0) >>6));
		t+=this._e.charAt(_3&0x3F);}return t;
	},
	_x:function(s){
		var _1,_2,_3,_4,i=0,_9=s.length,t="";
		while(i<_9){
		do{_1=this._d[s.charCodeAt(i++)&0xff];}while(i<_9&&_1==-1);
		if(_1==-1)break;
		do{_2=this._d[s.charCodeAt(i++)&0xff];}while(i<_9&&_2==-1);
		if(_2==-1)break;
		t+=String.fromCharCode((_1<<2)|((_2&0x30)>>4));
		do{_3=s.charCodeAt(i++)&0xff;
		if(_3==61)return t;
		_3=this._d[_3];}while(i<_9&&_3==-1);
		if(_3==-1)break;
		t+=String.fromCharCode(((_2&0XF)<<4)|((_3&0x3C)>>2));
		do{_4=s.charCodeAt(i++)&0xff;
		if(_4==61)return t;
		_4=this._d[_4];}while(i<_9&&_4==-1);
		if(_4==-1)break;
		t+=String.fromCharCode(((_3&0x03)<<6)|_4);}return t;
	},
	encode:function(s){
		return this._c(this._16_8(escape(s)));//for utf8
		//return this._c(this._16_8(s));//for gbk
	},
	decode:function(s){
		return unescape(this._8_16(this._x(s)));//for utf8
		//return this._8_16(this._x(s));//for gbk
	},
	encodex:function(s){ // for uri slasher
		for(var i=0,t="",s=this.encode(s);i<s.length;i++)
		t+=(s.charAt(i)!='/')?s.charAt(i):'|';
		return t;
	},
	decodex:function(s){ // for uri slasher
		for(var i=0,t="";i<s.length;i++)
		t+=(s.charAt(i)!='|')?s.charAt(i):'/';
		return this.decode(t);
	}
},
gbk:{
	length:function(str){
		var cnt=0,i,n;
		for(i=0;i<str.length;i++){
			n=str.charCodeAt(i);
			if((n>=0&&n<=126)||(n>=65377&&n<=65424))cnt++; 
			else cnt+=2;
		}
		return cnt;
	},
	//sub string for gbk,chinese words length=2,minus value is not supported
	substr:function(str,start,len){
		var t='',cnt=0,i,n;
		len=parseInt(len);
		if(isNaN(len))len=0;
		for(i=0;i<str.length;i++){
			n=str.charCodeAt(i);
			if((n>=0&&n<=126)||(n>=65377&&n<=65424))cnt++; 
			else cnt+=2;
			if(cnt>=start){
				t+=str.substr(i,1);
				//cnt starts from 1, but start starts from 0, so no need "+1"
				if(len>0&&cnt-start>=len)return t;
			}
		}
		return t;
	},
	tail:function(str,len,tailstr){
		if(typeof tailstr==='undefined')tailstr='...';
		var t=this.substr(str,0,len);
		if(t.length!=str.length)t+=tailstr;
		return t;
	}
},
php:{
	strip_tags:function(input, allowed) {
	   allowed = (((allowed || "") + "")
	      .toLowerCase()
	      .match(/<[a-z][a-z0-9]*>/g) || [])
	      .join('');
	   var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
	       langTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
	   return input.replace(langTags, '').replace(tags, function($0, $1){
	      return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
	   });
	},
	nl2br:function(str, is_xhtml) {
	    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
	    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
	}
}
});
$.fn.tipsy=function(opts){opts=$.extend({fade:false,gravity:'n'},opts||{});var tip=null,cancelHide=false;this.hover(function(){$.data(this,'cancel.tipsy',true);var tip=$.data(this,'active.tipsy');if(!tip){tip=$('<div class="tipsy"><div class="tipsy-inner">'+$(this).attr('title')+'</div></div>');tip.css({position:'absolute',zIndex:100000});$(this).attr('title','');$.data(this,'active.tipsy',tip);}var pos=$.extend({},$(this).offset(),{width:this.offsetWidth,height:this.offsetHeight});tip.remove().css({top:0,left:0,visibility:'hidden',display:'block'}).appendTo(document.body);var actualWidth=tip[0].offsetWidth,actualHeight=tip[0].offsetHeight;switch (opts.gravity.charAt(0)){case 'n':tip.css({top:pos.top+pos.height,left:pos.left+pos.width/2-actualWidth/2}).addClass('tipsy-north');break;case 's':tip.css({top:pos.top-actualHeight,left:pos.left+pos.width/2-actualWidth/2}).addClass('tipsy-south');break;case 'e':tip.css({top:pos.top+pos.height/2-actualHeight/2,left:pos.left-actualWidth}).addClass('tipsy-east');break;case 'w':tip.css({top:pos.top+pos.height/2-actualHeight/2,left:pos.left+pos.width}).addClass('tipsy-west');break;}if(opts.fade){tip.css({opacity:0,display:'block',visibility:'visible'}).animate({opacity:1});}else{tip.css({visibility:'visible'});}},function(){$.data(this,'cancel.tipsy',false);var self=this;setTimeout(function(){if($.data(this,'cancel.tipsy'))return;var tip=$.data(self,'active.tipsy');if(opts.fade){tip.stop().fadeOut(function(){ $(this).remove(); });}else{tip.remove();}},100);});};
})(jQuery);