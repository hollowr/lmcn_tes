<?php
/**
 * lmcn template for displaying the footer
 *
 * @package WordPress
 * @subpackage lmcn
 * @since lmcn 1.0
 */
?>

				<ul class="footer-widgets"><?php
					if ( function_exists( 'dynamic_sidebar' ) ) :
						dynamic_sidebar( 'footer-sidebar' );
					endif; ?>
				</ul>

			</div>
		<?php wp_footer(); ?>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
		<?php
			if ( is_home() ):
		?>
			<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.cycle.all.min.js";
		<?php 
			endif;
		?>
		<script src="<?php echo get_template_directory_uri(); ?>/js//js/jquery.cookie.js";
		<?php if ( is_home() && !get_option('ss_disable') ) : ?>
        <script type="text/javascript">
            (function(jQuery) {
                jQuery(function() {
                    jQuery('#slideshow').cycle({
                        fx:     'scrollHorz',
                        timeout: <?php echo (get_option('ss_timeout')) ? get_option('ss_timeout') : '7000' ?>,
                        next:   '#rarr',
                        prev:   '#larr'
                    });
                })
            })(jQuery);
        </script>
        <?php endif; ?>
	</body>
</html>

<script>
$(document).ready(function () {
	(function(c){c.fn.visible=function(e){var a=c(this),b=c(window),f=b.scrollTop();b=f+b.height();var d=a.offset().top;a=d+a.height();var g=e===true?a:d;return(e===true?d:a)<=b&&g>=f}})(jQuery);

    $('.page-content img,.slideshow img').each(function () {
        $(this).attr("src", "http://www.lamodecnous.com/wp-content/" + $(this).attr("src").replace('http://lmcn.ragnar.mbox/contenu', ''));
    });
    var headerHeight = $('.site-header').height();
    var bottomscroll = 0;
    var windowHeight = $(window).height();
    $('.slideshow').css({'height':windowHeight - (headerHeight+bottomscroll-30)});
    $('#slideshow').css({'height':windowHeight - (headerHeight+bottomscroll+30)});
    <?php if ( is_home() && !get_option('ss_disable') ) : ?>
            (function($) {
                $(function() {
                    $('#slideshow').cycle({
                        fx:     'scrollHorz',
                        timeout: <?php echo (get_option('ss_timeout')) ? get_option('ss_timeout') : '3000' ?>,
                        next:   '#rarr',
                        prev:   '#larr'
                    });
                })
            })(jQuery);
            var timer = null;
            document.querySelector('.site').addEventListener('scroll', function() {
            	if(timer !== null) {
			        clearTimeout(timer); 
			        $('#slideshow').cycle('pause');       
			    }
			    timer = setTimeout(function() {
			    	if ($('.slideshow').visible(true) == false) {
            			console.log('hidden paused')
	            		$('#slideshow').cycle('pause');
		            } else {
		            	console.log('visible resume')
		            	$('#slideshow').cycle('resume');
		            }
			    }, 150);
            },false);
            
    <?php endif; ?>
});
</script>