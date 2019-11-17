!function(t){var e={};function i(n){if(e[n])return e[n].exports;var o=e[n]={i:n,l:!1,exports:{}};return t[n].call(o.exports,o,o.exports,i),o.l=!0,o.exports}i.m=t,i.c=e,i.d=function(t,e,n){i.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},i.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},i.t=function(t,e){if(1&e&&(t=i(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(i.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var o in t)i.d(n,o,function(e){return t[e]}.bind(null,o));return n},i.n=function(t){var e=t&&t.__esModule?function(){return t["default"]}:function(){return t};return i.d(e,"a",e),e},i.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},i.p="",i(i.s=5)}([function(t,e,i){"use strict";var n=i(1),o=i(7).parse,s=new n;s.query=o(document.location.search.substring(1)),s.config=i(8),s.activePage=null,s.route=function(t,e){var i,n=s.activePage;return!(!t||t.active)&&(t.name,t.id,t.name,t.id,(i=s.activePage)&&i.active&&(i.$node.classList.remove("active"),i.active=!1,s.activePage=null,i.name,i.id,i.name,i.id,i.events["hide"]&&i.emit("hide")),function(t,e){!t||t.active||(t.$node.classList.add("active"),t.active=!0,s.activePage=t,t.name,t.id,t.name,t.id,t.events["show"]&&t.emit("show",{data:e}))}(t,e),this.events["route"]&&this.emit("route",{from:n,to:t}),!0)},t.exports=s},function(t,e,i){"use strict";function n(){this.events={}}n.prototype={addListener:function(t,e){this.events[t]=this.events[t]||[],this.events[t].push(e)},once:function(t,e){var i=this;this.events[t]=this.events[t]||[],this.events[t].push(function n(){i.removeListener(t,n),e.apply(i,arguments)})},addListeners:function(t){var e;for(e in t)t.hasOwnProperty(e)&&this.addListener(e,t[e])},removeListener:function(t,e){this.events[t]&&(this.events[t]=this.events[t].filter(function(t){return t!==e}),0===this.events[t].length&&(this.events[t]=void 0))},emit:function(t){var e,i=this.events[t];if(i)for(e=0;e<i.length;e++)i[e].apply(this,Array.prototype.slice.call(arguments,1))}},n.prototype.constructor=n,t.exports=n},function(t,e,i){"use strict";var n=i(6);t.exports=n},function(t,e,i){"use strict";var n=i(0);t.exports=function(t){var e=document.createElement("link");e.rel="stylesheet",e.href="css/"+t+"."+n.metrics.height+".css",document.head.appendChild(e)}},function(t,e,i){"use strict";var n=i(0),o=i(1),s=0;function a(t){var e,i=this;if(t=t||{},this.visible=!0,this.focusable=!0,this.$node=null,this.$body=null,this.parent=null,this.children=[],this.propagate=!!t.propagate,o.call(this),this.$node=t.$node||document.createElement("div"),this.$body=t.$body||this.$node,this.$node.className=this.name+" "+(t.className||""),this.id=t.id||this.$node.id||"cid"+s++,t.parent&&t.parent.add(this),!1===t.visible&&this.hide(),!1===t.focusable&&(this.focusable=!1),this.defaultEvents)for(e in t.events=t.events||{},this.defaultEvents)t.events[e]=t.events[e]||this.defaultEvents[e];t.events&&Object.keys(t.events).forEach(function(e){i.addListener(e,t.events[e])}),t.children&&this.add.apply(this,t.children),this.$node.addEventListener("click",function(t){i.focus(),i.events["click"]&&i.emit("click",t),t.stopPropagation()}),this.name,this.id,this.name,this.id}a.prototype=Object.create(o.prototype),a.prototype.constructor=a,a.prototype.defaultEvents=null,a.prototype.add=function(t){var e;for(e=0;e<arguments.length;e++)t=arguments[e],this.children.push(t),t.parent=this,t.$node&&null===t.$node.parentNode&&this.$body.appendChild(t.$node),t.name,t.id,this.name,this.id,this.name,this.id,t.name,t.id,this.events["add"]&&this.emit("add",{item:t})},a.prototype.remove=function(){this.parent&&(n.activePage.activeComponent===this&&(this.blur(),this.parent.focus()),this.parent.children.splice(this.parent.children.indexOf(this),1)),this.children.forEach(function(t){t.remove()}),this.$node.parentNode.removeChild(this.$node),this.events["remove"]&&this.emit("remove"),this.events={},this.name,this.id,this.name,this.id},a.prototype.focus=function(t){var e=n.activePage,i=e.activeComponent;return!(!this.focusable||this===i)&&(i&&i.blur(),e.activeComponent=i=this,i.$node.classList.add("focus"),this.name,this.id,this.name,this.id,i.events["focus"]&&i.emit("focus",t),!0)},a.prototype.blur=function(){var t=n.activePage,e=t.activeComponent;return this.$node.classList.remove("focus"),this===e?(t.activeComponent=null,this.name,this.id,this.name,this.id,this.events["blur"]&&this.emit("blur"),!0):(this.name,this.id,this.name,this.id,!1)},a.prototype.show=function(t,e){return!this.visible&&(this.$node.classList.remove("hidden"),this.visible=!0,this.name,this.id,this.name,this.id,this.events["show"]&&this.emit("show",t),"function"==typeof e&&setTimeout(e),!0)},a.prototype.hide=function(t){return!!this.visible&&(this.$node.classList.add("hidden"),this.visible=!1,this.name,this.id,this.name,this.id,this.events["hide"]&&this.emit("hide"),"function"==typeof t&&setTimeout(t),!0)},t.exports=a},function(t,e,i){"use strict";var n,o=i(2),s=i(14),a=new(i(16))({value:0,max:100,min:0}),c=parent.document.createElement("div"),r=parent.document.createElement("div"),d=parent.document.createElement("div"),u=parent.document.createElement("div"),l=parent.document.createElement("div"),h=-1,p=-1,m=null;o.once("load",function(){var t,e;function f(){o.notifyWidget.visible&&(o.notifyWidget.hide(),o.notifyWidget.visible=!1,o.notifyWidget.buttons=!1,core.call("hide"),core.call("blur"))}core.plugins.settingsAudio||(core.plugins.settingsAudio={mute:!1,volume:100,addListener:function(){}}),n=core.plugins.settingsAudio.mute,a.set(core.plugins.settingsAudio.volume),t=location.protocol+"//"+location.host+location.pathname.replace("index.html",""),o.visible=!1,o.pages={main:i(17)},o.route(o.pages.main),o.muteWidget=core.widget({visible:!1,events:{show:function(){}}}),o.muteWidget.$node.style.display="none",o.muteWidget.$node.style.zIndex="1003",o.muteWidget.$node.className+=" theme-main theme-launcher-color active",o.volumeWidget=core.widget({visible:!1,events:{show:function(){}}}),o.volumeWidget.$node.style.display="none",o.volumeWidget.$node.className+=" theme-main theme-launcher-color active",o.volumeWidget.$node.style.zIndex="1002",o.notifyWidget=core.widget({visible:!1,events:{show:function(){}}}),o.notifyWidget.$node.style.display="none",o.muteWidget.$node.innerText="B",(e=new XMLHttpRequest).open("GET",t+"css/app."+o.metrics.height+".css"),e.onload=function(){var t;(t=parent.document.createElement("style")).innerHTML=this.responseText.replace(/#muteWidget/g,"."+o.muteWidget.className).replace(/#volumeWidget/g,"."+o.volumeWidget.className).replace(/#notifyWidget/g,"."+o.notifyWidget.className),parent.document.head.appendChild(t),o.muteWidget.$node.style.display="",o.volumeWidget.$node.style.display="",o.notifyWidget.$node.style.display=""},e.send(null),o.volumeWidget.$body.appendChild(c),c.className="theme-icon icon volume",o.volumeWidget.children.push(a),a.parent=o.volumeWidget,a.$node&&null===a.$node.parentNode&&o.volumeWidget.$body.appendChild(a.$node),a.$node.className="component progressBar",a.$value.className+=" theme-focus",r.className="pointer theme-focus",a.$node.appendChild(r),o.volumeWidget.$node.appendChild(d),d.className="textValue",d.innerText=a.value,core.plugins.settingsAudio.addListener("volume:change",function(t){o.volumeWidget.show(),t.curr<=100&&t.curr>=0&&(d.innerText=t.curr,a.set(t.curr)),clearTimeout(h),h=setTimeout(function(){o.volumeWidget.hide()},5e3)}),core.plugins.settingsAudio.addListener("mute:change",function(t){o.volumeWidget.toggleMute(t.value)}),u.className="icon ",l.className="title",o.notifyWidget.$icon=o.notifyWidget.$body.appendChild(u),o.notifyWidget.$title=o.notifyWidget.$body.appendChild(l),o.volumeWidget.toggleMute=function(t){t!==n&&(n=t,o.muteWidget.$node.style.display="",n?o.muteWidget.show():o.muteWidget.hide())},document.body.style.background="rgba(24, 24, 24, 0.7)",document.body.style.overflow="hidden",o.notifyWidget.init=function(t){var e=t.buttons&&t.buttons.length>0?" control":"";return t.timeout=t.timeout||5e3,t.type=t.type||"","function"==typeof t.onabort&&(m=t.onabort),o.notifyWidget.$body.innerHTML="",o.notifyWidget.$body.appendChild(o.notifyWidget.$icon),o.notifyWidget.$body.appendChild(o.notifyWidget.$title),o.notifyWidget.$icon.className="icon "+t.icon,o.notifyWidget.$title.innerHTML=t.title,o.notifyWidget.$node.className=o.notifyWidget.className+" "+t.type+e,o.notifyWidget.visible=!0,t.buttons&&t.buttons.length>0?(o.notifyWidget.buttons=[],t.buttons.forEach(function(t,e){var i=parent.document.createElement("div");i.className="button",0===e&&(o.notifyWidget.currentButtonIndex=0,i.className="button focus"),i.innerText=t.value,i.onclick=function(){t.click&&t.click(),f()},o.notifyWidget.buttons.push(i),o.notifyWidget.$body.appendChild(i)}),o.notifyWidget.show(),core.call("show"),o.visible=!0,core.call("focus")):(o.visible&&(core.call("hide"),core.call("blur")),o.notifyWidget.buttons=!1,clearTimeout(p),p=setTimeout(function(){o.notifyWidget.hide()},t.timeout)),{close:f,update:function(t){o.notifyWidget.visible&&t.title&&(o.notifyWidget.$title.innerHTML=t.title)}}},parent.extendCorePrototype("notify",o.notifyWidget.init),o.addListener("keydown",function(t){o.notifyWidget.visible&&o.notifyWidget.buttons&&(t.code===s.right?(o.notifyWidget.buttons[o.notifyWidget.currentButtonIndex].className="button",++o.notifyWidget.currentButtonIndex,o.notifyWidget.currentButtonIndex===o.notifyWidget.buttons.length&&(o.notifyWidget.currentButtonIndex=0),o.notifyWidget.buttons[o.notifyWidget.currentButtonIndex].className="button focus"):t.code===s.left?(o.notifyWidget.buttons[o.notifyWidget.currentButtonIndex].className="button",--o.notifyWidget.currentButtonIndex,o.notifyWidget.currentButtonIndex<0&&(o.notifyWidget.currentButtonIndex=o.notifyWidget.buttons.length-1),o.notifyWidget.buttons[o.notifyWidget.currentButtonIndex].className="button focus"):t.code===s.ok?o.notifyWidget.buttons[o.notifyWidget.currentButtonIndex].onclick():t.code===s.back&&(null!==m&&(m(),m=null),f()))}),i(20).load({name:core.environment.language},function(){core.plugins.fs.onMount=function(t){t?core.notify({title:gettext("New device is connected"),icon:"info"}):core.notify({title:gettext("Device is disconnected"),icon:"info"})},o.ready()})})},function(t,e,i){"use strict";var n=i(0),o=i(9);window.core=window.parent.getCoreInstance(window,n),i(10),i(11),i(3)("sdk"),i(13),i(3)("app"),n.platform="mag",n.ready=function(){window.core.call("app:ready")},n.exit=function(){n.events["exit"]&&n.emit("exit"),core.call("exit")},o.load=function(t){document.body.setAttribute("platform",n.platform),core.ready?n.events["load"]&&n.emit("load",{}):core.once("load",function(){n.events[t.type]&&n.emit(t.type,t)})},o.contextmenu=function(t){t.preventDefault()},Object.keys(o).forEach(function(t){window.addEventListener(t,o[t])}),t.exports=n},function(t,e,i){"use strict";t.exports={parse:function(t){var e={};return t.split("&").forEach(function(t){2===(t=t.split("=")).length&&(e[t[0]]=decodeURIComponent(t[1]))}),e},stringify:function(t){var e=[];return Object.keys(t).forEach(function(i){e.push(i+"="+encodeURIComponent(t[i]))}),e.join("&")}}},function(t,e,i){"use strict";t.exports={}},function(t,e,i){"use strict";var n=i(0);t.exports={DOMContentLoaded:function(t){n.events["dom"]&&n.emit("dom",t)},load:function(t){n.events[t.type]&&n.emit(t.type,t)},unload:function(t){n.events[t.type]&&n.emit(t.type,t)},error:function(t){},keydown:function(t){var e,i=n.activePage,o={code:t.keyCode,stop:!1};t.ctrlKey&&(o.code+="c"),t.altKey&&(o.code+="a"),t.shiftKey&&(o.code+="s"),(e=i.activeComponent)&&e!==i&&(e.events[t.type]&&e.emit(t.type,o,t),!o.stop&&e.propagate&&e.parent&&e.parent.events[t.type]&&e.parent.emit(t.type,o,t)),o.stop||(i.events[t.type]&&i.emit(t.type,o,t),t.stop||n.events[t.type]&&n.emit(t.type,o,t))},keypress:function(t){var e=n.activePage;e.activeComponent&&e.activeComponent!==e&&e.activeComponent.events[t.type]&&e.activeComponent.emit(t.type,t)},mousewheel:function(t){var e=n.activePage;e.activeComponent&&e.activeComponent!==e&&e.activeComponent.events[t.type]&&e.activeComponent.emit(t.type,t),t.stop||e.events[t.type]&&e.emit(t.type,t)}}},function(t,e,i){"use strict";if(!document.documentElement.classList){var n=Array.prototype,o=n.indexOf,s=n.slice,a=n.push,c=n.splice,r=n.join;window.DOMTokenList=function(t){if(this._element=t,t.className!==this._classCache){if(this._classCache=t.className,!this._classCache)return;var e,i=this._classCache.replace(/^\s+|\s+$/g,"").split(/\s+/);for(e=0;e<i.length;e++)a.call(this,i[e])}},window.DOMTokenList.prototype={add:function(t){this.contains(t)||(a.call(this,t),this._element.className=s.call(this,0).join(" "))},contains:function(t){return-1!==o.call(this,t)},item:function(t){return this[t]||null},remove:function(t){var e=o.call(this,t);-1!==e&&(c.call(this,e,1),this._element.className=s.call(this,0).join(" "))},toString:function(){return r.call(this," ")},toggle:function(t){return this.contains(t)?this.remove(t):this.add(t),this.contains(t)}},Object.defineProperty(Element.prototype,"classList",{get:function(){return new window.DOMTokenList(this)}})}},function(t,e,i){"use strict";var n=i(0),o=i(12);n.metrics=o[n.query.screenHeight]||o[screen.height]||o[720],n.metrics.availHeight=n.metrics.height-(n.metrics.availTop+n.metrics.availBottom),n.metrics.availWidth=n.metrics.width-(n.metrics.availLeft+n.metrics.availRight)},function(t,e,i){"use strict";t.exports={480:{height:480,width:720,availTop:24,availBottom:24,availRight:32,availLeft:48},576:{height:576,width:720,availTop:24,availBottom:24,availRight:26,availLeft:54},720:{height:720,width:1280,availTop:30,availBottom:30,availRight:40,availLeft:40},1080:{height:1080,width:1920,availTop:45,availBottom:45,availRight:60,availLeft:60}}},function(t,e,i){"use strict";var n,o=i(0);(n=document.createElement("link")).rel="stylesheet",n.href=window.core.theme.path+o.metrics.height+".css",document.head.appendChild(n),t.exports=n},function(t,e,i){"use strict";var n=i(15);n.back=n.backspace,n.channelNext=n.tab,n.channelPrev=n.tab+"s",n.ok=n.enter,n.exit=n.escape,n.volumeUp=107,n.volumeDown=109,n.f1="112c",n.f2="113c",n.f3="114c",n.f4="115c",n.refresh="116c",n.frame="117c",n.phone="119c",n.set="120c",n.tv="121c",n.menu="122c",n.app="123c",n.rewind="66a",n.forward="70a",n.audio="71a",n.standby="74a",n.keyboard="76a",n.usbMounted="80a",n.usbUnmounted="81a",n.playPause="82a",n.play=-1,n.pause=-1,n.stop="83a",n.power="85a",n.record="87a",n.info="89a",n.mute="192a",n.digit0=48,n.digit1=49,n.digit2=50,n.digit3=51,n.digit4=52,n.digit5=53,n.digit6=54,n.digit7=55,n.digit8=56,n.digit9=57,t.exports=n},function(t,e,i){"use strict";t.exports={backspace:8,tab:9,enter:13,escape:27,space:32,pageUp:33,pageDown:34,end:35,home:36,left:37,up:38,right:39,down:40,insert:45,del:46}},function(t,e,i){"use strict";var n=i(4);function o(t){t=t||{},this.max=100,this.min=0,this.value=0,this.step=1,t.focusable=t.focusable||!1,n.call(this,t),this.$value=this.$body.appendChild(document.createElement("div")),this.$value.className="value",this.init(t)}o.prototype=Object.create(n.prototype),o.prototype.constructor=o,o.prototype.name="spa-component-progress-bar",o.prototype.set=function(t){var e=this.value;return this.value!==t&&t<=this.max&&t>=this.min&&(this.value=t,100===(t=Math.abs(this.value-this.min)/this.step)&&this.events["done"]&&this.emit("done"),this.$value.style.width=t+"%",this.events["change"]&&this.emit("change",{curr:this.value,prev:e}),!0)},o.prototype.init=function(t){void 0!==t.max&&(this.max=t.max),void 0!==t.min&&(this.min=t.min),void 0!==t.value&&(this.value=t.value),this.step=Math.abs(this.max-this.min)/100,this.$value.style.width=Math.abs(this.min-this.value)/this.step+"%"},t.exports=o},function(t,e,i){"use strict";var n=i(2),o=new(i(18))({$node:document.getElementById("pageMain")});core.addListener("hide",function(){n.visible&&core.call("hide"),n.visible=!1,n.notifyWidget.hide()}),window.top.document.body.onoffline=document.body.ononline=function(){navigator.onLine?n.notifyWidget.init({title:gettext("The network cable is connected"),icon:"alert",type:"warning"}):n.notifyWidget.init({title:gettext("The network cable is disconnected"),icon:"alert",type:"warning",buttons:[{value:gettext("Ok")}]})},t.exports=o},function(t,e,i){"use strict";t.exports=i(19),t.exports.prototype.name="stb-component-page"},function(t,e,i){"use strict";var n=i(4);function o(t){t=t||{},this.active=!1,this.activeComponent=null,n.call(this,t),this.active=this.$node.classList.contains("active"),null===this.$node.parentNode&&document.body.appendChild(this.$node),this.page=this}o.prototype=Object.create(n.prototype),o.prototype.constructor=o,o.prototype.name="spa-component-page",t.exports=o},function(t,e,i){"use strict";var n=i(1),o=i(21),s=new n;function a(t){var e=new o(t);return window.gettext=window._=e.gettext,window.pgettext=e.pgettext,window.ngettext=e.ngettext,e}s.defaultLanguage="en",s.load=function(t,e){var i;return t.ext=t.ext||"json",t.path=t.path||"lang",t.name===s.defaultLanguage?(a(),e(null),!1):((i=new XMLHttpRequest).onload=function(){try{a(JSON.parse(i.responseText)),e(null),s.events["load"]&&s.emit("load")}catch(t){i.onerror(t)}},i.ontimeout=i.onerror=function(t){a(),e(t),s.events["error"]&&s.emit("error",t)},i.open("GET",t.path+"/"+t.name+"."+t.ext,!0),i.send(null),!0)},t.exports=s},function(module,exports,__webpack_require__){"use strict";function Gettext(config){var data,meta;config=config||{},data=config.data||{},data[""]=data[""]||{},meta=config.meta,this.gettext=function(t){return data[""][t]||t},this.pgettext=function(t,e){return data[t]&&data[t][e]||e},this.ngettext=function(msgId,plural,value){var n,evalResult;return data&&meta&&data[""][msgId]?(evalResult=eval("n = "+value+"; "+meta.plural),"boolean"==typeof evalResult&&(evalResult=+evalResult),data[""][msgId][evalResult]):1===value?msgId:plural}}Gettext.prototype.constructor=Gettext,module.exports=Gettext}]);
//# sourceMappingURL=main.js.map