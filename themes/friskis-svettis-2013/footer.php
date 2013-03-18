			<?php 
				echo '<div id="footerLeft">';
					if ( !function_exists( 'Sidfot 1' ) || !dynamic_sidebar() ) : 
						dynamic_sidebar( 'Sidfot 1' );
					endif; 
				echo '</div>';
				
				
				echo '<div id="footerRight">';
					if ( !function_exists( 'Sidfot 2' ) || !dynamic_sidebar() ) : 
						dynamic_sidebar( 'Sidfot 2' );
					endif; 
				echo '</div>';
			?>
			<div id="social">
			<?php if(get_field('facebook-user', 4057)): ?>
				<a href="http://www.facebook.com/<?php the_field('facebook-user', 4057); ?>" title="Följ oss på Facebook!"><img src="<?php echo THEME_IMAGES; ?>/facebook.png" alt="facebook"></a>
				<?php endif; ?>
				<?php if(get_field('twitter-user', 4057)): ?>
				<a href="http://www.twitter.com/<?php the_field('twitter-user', 4057); ?>" title="Följ oss på Twitter!"><img src="<?php echo THEME_IMAGES; ?>/twitter.png" alt="twitter"></a>
				<?php endif; ?>
			</div>	
	</div>
</div>
<footer>
		<nav id="footerMenu">
			<?php wp_nav_menu( array('menu' => 'Sidfotsmeny' )); ?>
		</nav>
		<style>	@media screen and (max-width: 650px) {
				#copyright {
				display: none;
			}
			}
			</style>
		<div id="copyright">
			Producerad av <a href="http://strop.se">Strop Digital Studio</a> | Copyright © 2013 Friskis&amp;Svettis
		</div>
	<div id="mobileFooter">
			Copyright © 2013 Friskis&amp;Svettis<br>
			Producerad av <a href="http://strop.se">Strop Digital Studio</a>
	</div>
	</footer>
	<?php wp_footer(); ?>
</body>
</html>