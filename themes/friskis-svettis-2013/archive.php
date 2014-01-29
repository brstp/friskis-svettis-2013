<?php
/*
Template Name: Archives
*/
update_option('current_page_template','archive');
get_header(); 
?>
	<div id="gap"></div>
	<div id="content" class="clearfix">
	<div id="sidebar">
	
		<div class="subSidebarBox news">
			<img class="yellow" src="<?php echo THEME_IMAGES; ?>/yellow.png" alt="tape">
			<h3><span><?php _e('Nyheter', 'friskis-svettis'); ?></span></h3>
				<ul>
				<?php
				$news_query = new WP_Query(array(
						"post_type" => 'fs_news',
						"posts_per_page" => 4,
					));
				while ($news_query->have_posts()) : $news_query->the_post();
					?>
					<li>
						<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
						<p><a href="<?php the_permalink(); ?>"><?php echo get_the_excerpt(); ?>...</a> <a class="readMore" href="<?php the_permalink(); ?>"><?php _e('Läs mer', 'friskis-svettis'); ?></a></p>
					</li>
				<?php	
					endwhile;
				?>
				</ul>
		</div>
		
				<?php
				if ( !function_exists( 'Sidbar' ) || !dynamic_sidebar() ) : 
					dynamic_sidebar( 'Sidbar' );
				endif; 
			?>
	
		
	</div>
			<style>
	@media screen and (max-width: 960px) {
		#footerRight {
			float: right;
			width: 50%;
		}
	}
	
	@media screen and (max-width: 480px) {
		#footerRight {
			float: none;
			width: 100%;
		}
	}
</style>
	
	<div id="mainContent" class="mainContent">
		<h1>Error 404</h1>
		<h2>Sidan du försökte nå kan inte hittas.</h2>
		<p>Gå tillbaks till <a href="<?php echo HOME_URI; ?>">startsidan</a> eller använd sökformuläret högst upp på sidan för att försöka hitta rätt.</p>
	</div>
	
	<div id="sidebarRight">
		<?php the_field('sidebar-right'); ?>
	</div>
	<div class="clearfix"></div>
<?php get_footer(); ?>