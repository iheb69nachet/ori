(()=>{function o(e){return o="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(o){return typeof o}:function(o){return o&&"function"==typeof Symbol&&o.constructor===Symbol&&o!==Symbol.prototype?"symbol":typeof o},o(e)}function e(e,r){for(var t=0;t<r.length;t++){var n=r[t];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,(i=n.key,a=void 0,a=function(e,r){if("object"!==o(e)||null===e)return e;var t=e[Symbol.toPrimitive];if(void 0!==t){var n=t.call(e,r||"default");if("object"!==o(n))return n;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===r?String:Number)(e)}(i,"string"),"symbol"===o(a)?a:String(a)),n)}var i,a}var r=function(){function o(){!function(o,e){if(!(o instanceof e))throw new TypeError("Cannot call a class as a function")}(this,o)}var r,t,n;return r=o,(t=[{key:"init",value:function(){$(document).on("click",".approve-vendor-for-selling-button",(function(o){o.preventDefault(),$("#confirm-approve-vendor-for-selling-button").data("action",$(o.currentTarget).prop("href")),$("#approve-vendor-for-selling-modal").modal("show")})),$(document).on("click","#confirm-approve-vendor-for-selling-button",(function(o){o.preventDefault();var e=$(o.currentTarget);e.addClass("button-loading"),$.ajax({type:"POST",cache:!1,url:e.data("action"),success:function(o){o.error?Botble.showError(o.message):(Botble.showSuccess(o.message),window.location.href=route("marketplace.unverified-vendors.index")),e.removeClass("button-loading"),e.closest(".modal").modal("hide")},error:function(o){Botble.handleError(o),e.removeClass("button-loading")}})}))}}])&&e(r.prototype,t),n&&e(r,n),Object.defineProperty(r,"prototype",{writable:!1}),o}();$(document).ready((function(){(new r).init()}))})();