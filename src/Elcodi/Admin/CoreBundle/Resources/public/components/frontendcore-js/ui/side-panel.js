!function(a){"use strict";a.fn.bigSlide=function(b){var c=this,d=a.extend({menu:"#menu",push:".push",side:"left",menuWidth:"15.625em",speed:"300",state:"closed"},b),e={state:d.state},f={init:function(){g.init()},changeState:function(){e.state="closed"===e.state?"open":"closed"},getState:function(){return e.state}},g={init:function(){this.$menu=a(d.menu),this.$push=a(d.push),this.width=d.menuWidth;var b={position:"fixed",top:"0",bottom:"0","settings.side":"-"+d.menuWidth,width:d.menuWidth,height:"100%"},e={"-webkit-transition":d.side+" "+d.speed+"ms ease","-moz-transition":d.side+" "+d.speed+"ms ease","-ms-transition":d.side+" "+d.speed+"ms ease","-o-transition":d.side+" "+d.speed+"ms ease",transition:d.side+" "+d.speed+"ms ease"};this.$menu.css(b),this.$push.css(d.side,"0"),this.$menu.css(e),this.$push.css(e),c.on("click.bigSlide touchstart.bigSlide",function(a){a.preventDefault(),"open"===f.getState()?g.toggleClose():g.toggleOpen()})},toggleOpen:function(){f.changeState(),this.$menu.css(d.side,"0"),this.$push.css(d.side,this.width)},toggleClose:function(){f.changeState(),this.$menu.css(d.side,"-"+this.width),this.$push.css(d.side,"0")}};f.init()}}(jQuery),TinyCore.AMD.define("side-panel",[],function(){return{sPathCss:oGlobalSettings.sPathCssUI+"?v="+oGlobalSettings.sHash,oDefault:{menu:"#side-panel",push:".push",side:"left",menuWidth:"200px",speed:300,"class":"side-panel-default"},onStart:function(){var a=oTools.getDataModules("side-panel"),b=this;oTools.loadCSS(this.sPathCss),oTools.trackModule("JS_Libraries","call","side-panel"),$(a).each(function(a){b.autobind(this,a)})},autobind:function(a,b){var c,d,e=this,f=a.href,g={},h=document.createElement("a");if(""===a.id&&(a.id="slide-panel-open"+b),null!==a.getAttribute("data-tc-width")&&(g.menuWidth=a.getAttribute("data-tc-width")),$(window).width()<599&&g.menuWidth>599&&(g.menuWidth=$(window).width()+"px"),null!==a.getAttribute("data-tc-position")&&(g.side=a.getAttribute("data-tc-position")),null!==a.getAttribute("data-tc-class")&&(g["class"]=a.getAttribute("data-tc-class")),-1!==f.indexOf("#")&&(g.menu="#"+f.split("#")[1]),c=oTools.mergeOptions(e.oDefault,g),"true"===a.getAttribute("data-tc-clone")){var i="-"+b,j=$(c.menu).attr("id")+"-"+b,k=$(c.menu).clone().attr("id",$(c.menu).attr("id")+i);a.href="#"+j,c.menu="#"+j,k.find("[id]").each(function(){var a=$(this),b=a.attr("id")+i;a.attr("id",b)}),k.find("[href]").each(function(){var a,b=$(this),c=b.attr("href");-1!==c.indexOf("#")&&(a=c+i,b.attr("href",a))}),$("body").append(k[0])}if($(a).bigSlide(c),$(c.menu).addClass("side-panel-default"),-1!==$(c.menu)[0].className.indexOf("navigation")?$(a).on("click",function(){var a=$("#"+this.href.split("#")[1]).width();"right"===c.side?"0px"!==$(c.menu).css("right")?($(c.menu).css("z-index","1000"),$("html").css({position:"absolute",width:$(window).width()+parseInt(c.menuWidth,10)}).animate({left:"-"+a,"padding-right":a})):$("html").css({position:"relative",width:"inherit"}).animate({left:0,"padding-right":0}):"0px"!==$(c.menu).css("left")?($(c.menu).css("z-index","1000"),$("html").css({position:"absolute",overflow:"hidden",width:$(window).width()+parseInt(c.menuWidth,10)}).animate({"padding-left":a})):$("html").css({position:"relative",width:"auto",overflow:"auto"}).animate({"padding-left":0})}):$(a).on("click",function(){var a=$(this.href.split("#")[1]).css("right");"right"===c.side&&"0px"===a?$(c.menu).css("z-index","1000"):$(c.menu).css("z-index","1001")}),d=$(a).parent(c.menu)[0],void 0!==d){var l=$(a).outerWidth();$(a).css("right"===c.side?{left:"-"+(l-1)+"px"}:{right:"-"+(l-1)+"px"}),null!==a.getAttribute("data-tc-tab-top")&&$(a).css("top",a.getAttribute("data-tc-tab-top")),-1!==d.className.indexOf("box")&&$(d).css("overflow","visible")}else("false"!==a.getAttribute("data-tc-close")||$(window).width()+"px"==c.menuWidth)&&($(c.menu).addClass("has-slide-panel-close"),h.id="slide-panel-close"+b,h.className="icon-times slide-panel-close",h.href="#"+a.id,h.style.textAlign="right"===c.side?"left":"right",$(c.menu).append(h),$("#slide-panel-close"+b).on("click",function(b){b.preventDefault(),$("#"+a.id).click()}));"right"===c.side?$(c.menu).css({right:"-"+c.menuWidth}).addClass("side-panel-right"):$(c.menu).css({left:"-"+c.menuWidth}).addClass("side-panel-left")},onStop:function(){this.sPathCss=null},onDestroy:function(){delete this.sPathCss}}});