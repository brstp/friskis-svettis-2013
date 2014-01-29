			<?php 
				echo '<div id="footerLeft">';
					echo '<ul>';
						if ( !function_exists( 'Sidfot 1' ) || !dynamic_sidebar() ) : 
							dynamic_sidebar( 'Sidfot 1' );
						endif; 
					echo '</ul>';
				echo '</div>';				
				
				echo '<div id="footerRight">';
					echo '<ul>';
						if ( !function_exists( 'Sidfot 2' ) || !dynamic_sidebar() ) : 
							dynamic_sidebar( 'Sidfot 2' );
						endif; 
					echo '</ul>';
				echo '</div>';
				echo '<div id="social">';
					$twitterAccount 	= get_option('twitterAccount');
					$facebookAccount	= get_option('facebookAccount');
			
					if(strlen(trim($facebookAccount))>0): ?>
						<a href="http://www.facebook.com/<?php echo $facebookAccount; ?>" title="<?php _e('Följ oss på', 'friskis-svettis'); ?> Facebook!"><img src="<?php echo THEME_IMAGES; ?>/facebook.png" alt="facebook"></a>
						<?php endif; ?>
						<?php if(strlen(trim($twitterAccount))>0): ?>
						<a href="http://www.twitter.com/<?php echo $twitterAccount; ?>" title="<?php _e('Följ oss på', 'friskis-svettis'); ?> Twitter!"><img src="<?php echo THEME_IMAGES; ?>/twitter.png" alt="twitter"></a>
					<?php endif; 
				echo '</div>';
			?>
		</div>
</div>
<footer>
	<nav id="footerMenu">
		<?php wp_nav_menu( array('menu' => 'Sidfotsmeny' )); ?>
	</nav>
	<div id="copyright">
		<?php _e('Producerad av', 'friskis-svettis'); ?> <a href="http://strop.se" rel="nofollow">Strop Digital Studio</a> | Copyright © 2013 Friskis&amp;Svettis
	</div>
	<div id="mobileFooter">
		Copyright © 2013 Friskis&amp;Svettis<br>
		<?php _e('Producerad av', 'friskis-svettis'); ?> <a href="http://strop.se"rel="nofollow">Strop Digital Studio</a>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>