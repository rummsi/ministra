module.exports=function(e){var t={};function n(r){if(t[r])return t[r].exports;var o=t[r]={i:r,l:!1,exports:{}};return e[r].call(o.exports,o,o.exports,n),o.l=!0,o.exports}return n.m=e,n.c=t,n.d=function(e,t,r){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:r})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)n.d(r,o,function(t){return e[t]}.bind(null,o));return r},n.n=function(e){var t=e&&e.__esModule?function(){return e["default"]}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=0)}([function(e,t,n){"use strict";var r=n(1);e.exports={onAppInit:function(e,t){t(null,new r(e.app.packageName))}}},function(e,t,n){"use strict";var r=n(2),o=r.save,a=r.load,u=function(){function e(e){Object.defineProperty(this,"appName",{value:e,enumerable:!1,configurable:!1,writable:!1})}var t=e.prototype;return t.load=function(e){var t=this;setTimeout(function(){e(null,a(t.appName+"."+t.filename))},0)},t.loadSync=function(){return a(this.appName+"."+this.filename)},t.save=function(e,t){var n=this;setTimeout(function(){t(o(n.appName+"."+n.filename,e))},0)},t.saveSync=function(e){return o(this.appName+"."+this.filename,e)},e}();u.prototype.filename="config",e.exports=u},function(e,t,n){"use strict";var r,o;top.gSTB&&top.gSTB.LoadUserData?(r=top.gSTB.SaveUserData,o=top.gSTB.LoadUserData):(r=function(e,t){localStorage.setItem(e,t)},o=function(e){return localStorage.getItem(e)}),e.exports={save:r,load:o}}]);
//# sourceMappingURL=main.js.map