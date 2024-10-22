// http://devintorr.es/blog/2010/05/26/turn-css-rules-into-inline-style-attributes-using-jquery/

$(window).load(function(){
    (function ($) {
      var rules = document.styleSheets[document.styleSheets.length-1].cssRules;
      for (var idx = 0, len = rules.length; idx < len; idx++) {
        $(rules[idx].selectorText).each(function (i, elem) {
          elem.style.cssText += rules[idx].style.cssText;
        });
      }
      $('script, link').not('.keep').remove();
    })(jQuery);
});
