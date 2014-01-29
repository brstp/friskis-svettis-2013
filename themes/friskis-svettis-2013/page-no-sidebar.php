<?php
/*
	Template name: Utan högerspalt
*/
update_option('current_page_template','page-no-sidebar');
get_header(); 
?>
	<div id="gap"></div>
	<div id="content" class="clearfix">
	<div id="sidebar">
	<div class="subSidebarBox">
		<img class="yellow" src="<?php echo THEME_IMAGES; ?>/yellow.png" alt="tape">
		<h3><span><?php
			$parent_title = get_the_title($post->post_parent);
			echo $parent_title;
		?></span></h3>
		<nav class="sidebarMenu">
			<ul>
					<?php
			if($post->post_parent){
				$children .= wp_list_pages("title_li=&child_of=".$post->post_parent."&echo=0");
			} else {
				$children = wp_list_pages("title_li=&child_of=".$post->ID."&echo=0");
			}
			if ($children) { ?>
				<ul class="submenu">
					<?php echo $children; ?>
				</ul>
			<?php } ?>
			</ul>
			
		</nav>
	</div>
	
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
						<p><a href="<?php the_permalink(); ?>"><?php echo get_the_excerpt(); ?>...</a> <a class="readMore" href="<?php the_permalink(); ?>"><?php _e('Läs mer', 'friskis-svettis'); ?> »</a></p>
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
	<div id="mainContentNoSidebar" class="mainContent">
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
			<?php do_action('posts_logout_link', 'Logga ut från lösenordsskyddade sidor',  'logout_link'); ?>
			<?php the_content(); 
		endwhile;?>
	</div>
	
	<div class="clearfix"></div>
<?php get_footer(); ?>