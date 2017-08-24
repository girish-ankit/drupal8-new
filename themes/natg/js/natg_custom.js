/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($) {
    $(document).ready(function () {
        /********* Managed by SKDASS*****************/
        $(function () {
            $('.stellarnav ul li li:has(ul)').parent().parent().addClass('custom__nav__megamenu');
        });
        /****Humburger menu icon */
        $(document).ready(function () {
            $('#niit__humburg').click(function () {
                $(this).toggleClass('open');
                $("#main-nav .menu").toggleClass('menuAction');
            });
            $('.menu-toggle').click(function () {
                $('#niit__humburg').toggleClass('open');
            });

            var windowsize = $(window).width();
            if (windowsize <= 768) {
                $('#niit__humburg').removeClass('open');
                $('.stellarnav ul li li:has(ul)').parent().parent().removeClass('custom__nav__megamenu');
                jQuery(window).scroll(function () {
                    if (jQuery(this).scrollTop() >= 10) {
                        jQuery("body").addClass("apply__stick");
                    } else {
                        jQuery("body").removeClass("apply__stick");
                    }
                });
            }
        });
        /*===stickyheader ===*/
        jQuery(window).scroll(function () {
            if (jQuery(this).scrollTop() >= 500) {
                jQuery("body").addClass("apply__stick");
            } else {
                jQuery("body").removeClass("apply__stick");
            }
        });
        $(document).ready(function () {
            $('.searchBtn').click(function () {
                $("body").toggleClass('activeSearch');
                $("#block-searchform").animate({
                    width: "toggle"
                });
                $('input').focus();
            });
        });

        /// foter Quick links

        $('span.visible-xs').click(function () {
            $('#block-footer').toggle("slow");
            $('.fa-plus').toggleClass('fa-minus');
        });
        
        // jw player code
        
         if(jQuery('#natg-video').length!==0){
             var title = '';
             var image = '';
             var video = '';
             
             title = jQuery('#video-title').attr('title');
             image = jQuery('#video-image').attr('title');
             video = jQuery('#video-file').attr('title');
             
           jwplayer("natg-video").setup({
            file: video,
            image: image,
            width: "100%",
            aspectratio: "16:9"
        });
      
        }
        

        $(".dropdown").hover(function () {
            $('body').toggleClass("result_hover");
         });    



        /****************************/
    });
})(jQuery);


