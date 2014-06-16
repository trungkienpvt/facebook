/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


(function($) {
    $.easy = {
        facebook_construct: function(){
            window.fbAsyncInit = function() {
        FB.init({
          appId      : '451633734906371',
          xfbml      : true,
          version    : 'v2.0'
        });
      };
      (function(d, s, id){
         var js, fjs = d.getElementsByTagName(s)[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement(s); js.id = id;
         js.src = "//connect.facebook.net/en_US/sdk.js";
         fjs.parentNode.insertBefore(js, fjs);
       }(document, 'script', 'facebook-jssdk'));
        },
        facebook_get_comment:function(url, obj_id){
            $('#' + obj_id).append('<div class="fb-comments" data-href="'+ url +'" data-numposts="5" data-colorscheme="light"></div>');
    
        }        
        
    };
})(jQuery);