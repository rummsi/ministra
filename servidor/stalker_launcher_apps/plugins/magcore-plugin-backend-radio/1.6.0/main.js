module.exports=function(t){var e={};function n(r){if(e[r])return e[r].exports;var o=e[r]={i:r,l:!1,exports:{}};return t[r].call(o.exports,o,o.exports,n),o.l=!0,o.exports}return n.m=t,n.c=e,n.d=function(t,e,r){n.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:r})},n.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var o in t)n.d(r,o,function(e){return t[e]}.bind(null,o));return r},n.n=function(t){var e=t&&t.__esModule?function(){return t["default"]}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="",n(n.s=2)}([function(t,e,n){"use strict";var r=n(4),o={message:"Bad request error!"};t.exports={getRadioChannels:function(t,e){var n="?";Object.keys(t).forEach(function(e,r,o){n+=e+"="+t[e]+["&",""][+(r===o.length-1)]}),core.backend.request(r.resource+n,{onload:function(t,n){t?e(o):e(null,n)},onerror:function(){e(o)}})},setRadioChannelFavoriteState:function(t,e,n){core.backend.request(r.resource+t,{method:"PUT",body:e,onload:function(t,e){t?n(o):n(null,e)},onerror:function(){n(o)}})}}},function(t,e,n){"use strict";function r(){this.events={}}r.prototype={addListener:function(t,e){this.events[t]=this.events[t]||[],this.events[t].push(e)},once:function(t,e){var n=this;this.events[t]=this.events[t]||[],this.events[t].push(function r(){n.removeListener(t,r),e.apply(n,arguments)})},addListeners:function(t){var e;for(e in t)t.hasOwnProperty(e)&&this.addListener(e,t[e])},removeListener:function(t,e){this.events[t]&&(this.events[t]=this.events[t].filter(function(t){return t!==e}),0===this.events[t].length&&(this.events[t]=void 0))},emit:function(t){var e,n=this.events[t];if(n)for(e=0;e<n.length;e++)n[e].apply(this,Array.prototype.slice.call(arguments,1))}},r.prototype.constructor=r,t.exports=r},function(t,e,n){"use strict";t.exports={onAppInit:n(3),onContentBoardInit:n(5)}},function(t,e,n){"use strict";t.exports=function(t,e){var r=n(0);e(null,{getRadioChannels:function(t,e){r.getRadioChannels(t,function(t,n){var r;if(t)e({message:"Bad request error!"});else try{Array.isArray(n.data)?(r=n.data.map(function(t){return{id:t.id,date:t.number,name:t.name,url:t.url,solution:t.solution,favorite:t.favorite}}),e(null,r)):e({message:"Parse result error!"})}catch(t){e({message:"Parse result error!"})}})},setRadioChannelFavoriteState:function(t,e,n){r.setRadioChannelFavoriteState(t,JSON.stringify({favorite:e}),n)}})}},function(t,e,n){"use strict";t.exports={resource:"radio-channels/"}},function(t,e,n){"use strict";var r,o,a=screen.height,i=1;t.exports=function(t,e){var u=n(6);u.load({name:core.environment.language,path:t.path+"lang"},function(){var s=n(0),l=n(1),c=u.gettext,f=new l,d=t.api,v=d.layouts.static,p=d.geometry.square,m={channelsAmount:10,moreButton:{id:++i,layout:v,geometry:p,data:{name:c("See more"),icon:{normal:t.path+"img/"+a+"/see.more.normal.png",active:t.path+"img/"+a+"/see.more.active.png"}},runApp:!0}};f.search=function(t,e){},s.getRadioChannels({favorite:!1},function(n,u){var s;if(n)e(n);else try{Array.isArray(u.data)?(s=u.data.map(function(e){return{id:++i,layout:v,geometry:p,data:{name:e.name,favorite:e.favorite,icon:{normal:t.path+"img/"+a+"/content.item.normal.png",active:t.path+"img/"+a+"/content.item.active.png"}},onClick:function(){!function t(e,n){var a={action:"play",mime:"content/audio",data:{uri:n.url,title:n.name,solution:n.solution||"auto",mediaId:n.id},context:r,events:{error:function(){r=null},hide:function(){r=null},close:function(){r=null},prev:function(){o=e.indexOf(n),t(e,e[--o%e.length])},next:function(){o=e.indexOf(n),t(e,e[++o%e.length])}}};o||delete a.events.prev,o===e.length-1&&delete a.events.next,core.intent(a,function(t,e){t||(r=e)})}(u.data,e)}}}).sort(function(t,e){return t.data.favorite&&!e.data.favorite?-1:!t.data.favorite&&e.data.favorite?1:0}).slice(0,m.channelsAmount),e(null,{provider:f,data:s.length?s.concat(m.moreButton):[]})):e(!0)}catch(n){e(n)}})})}},function(t,e,n){"use strict";var r=n(1),o=n(7),a=new r;function i(t){var e=new o(t);a._=a.gettext=e.gettext,a.pgettext=e.pgettext,a.ngettext=e.ngettext}a.defaultLanguage="en",a.load=function(t,e){var n;e=e||null,t.ext=t.ext||"json",t.path=t.path||"lang",t.name===a.defaultLanguage?(i(),null!==e&&e(null)):((n=new XMLHttpRequest).onload=function(){var t,r;try{r=JSON.parse(n.responseText)}catch(e){t=e}t?n.onerror(t):(i(r),null!==e&&e(null),a.events["load"]&&a.emit("load"))},n.ontimeout=n.onerror=function(t){i(),null!==e&&e(null),a.events["error"]&&a.emit("error",t)},n.open("GET",t.path+"/"+t.name+"."+t.ext,!0),n.send(null))},t.exports=a},function(module,exports,__webpack_require__){"use strict";function Gettext(config){var data,meta;config=config||{},data=config.data||{},data[""]=data[""]||{},meta=config.meta,this.gettext=function(t){return data[""][t]||t},this.pgettext=function(t,e){return data[t]&&data[t][e]||e},this.ngettext=function(msgId,plural,value){var n,evalResult;return data&&meta&&data[""][msgId]?(evalResult=eval("n = "+value+"; "+meta.plural),"boolean"==typeof evalResult&&(evalResult=+evalResult),data[""][msgId][evalResult]):1===value?msgId:plural}}Gettext.prototype.constructor=Gettext,module.exports=Gettext}]);
//# sourceMappingURL=main.js.map