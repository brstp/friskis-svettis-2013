<?php
/**
 * The start page template file.
 * Template Name: fs-news
*/
update_option('current_page_template','single-fs_news');

get_header(); ?>
  
<div id="gap"></div>
	<div id="content" class="clearfix">
	<div id="sidebar">
		<div class="subSidebarBox">
			<img class="yellow" src="<?php echo THEME_IMAGES; ?>/yellow.png" alt="tape">
			<h3><span><?php _e('Nyheter', 'friskis-svettis'); ?></span></h3>
				<ul>
				<?php
				$news_query = new WP_Query(array(
						"post_type" => 'fs_news',
					));
				while ($news_query->have_posts()) : $news_query->the_post();
					?>
					<li>
						<h4><a href="<?php the_permalink(); ?>#mainContent"><?php the_title(); ?></a></h4>
						<p><a href="<?php the_permalink(); ?>#mainContent"><?php echo get_the_excerpt(); ?>...</a> <a class="readMore" href="<?php the_permalink(); ?>#mainContent"><?php _e('Läs mer', 'friskis-svettis'); ?> »</a></p>
					</li>
				<?php	
					endwhile;
				?>
				</ul>
		</div>
		<ul>
		<?php
				if ( !function_exists( 'Sidbar' ) || !dynamic_sidebar() ) : 
					dynamic_sidebar( 'Sidbar' );
				endif; 
			?>
		</ul>
	</div>
	<div id="mainContent" class="mainContent" style="margin-bottom: 50px;">
             <?php
            /* Output the latest "Ordförande har ordet"
             */
             get_template_part('loop', 'single' );
            ?>
			
	</div>
	<div id="sidebarRight">
		<?php the_field('sidebar-right');?>
	</div>
<div class="clearfix"></div>
<?php get_footer(); ?>