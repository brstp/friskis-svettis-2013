<?php
/**
 * The start page template file.
 * Template Name: Start
*/
get_header(); 
?>
		<div id="slider" class="showcase">
		<?php if(get_field('image-slider')): ?>
			<?php while(the_repeater_field('image-slider')): ?>
			<div class="showcase-slide">
				<div class="showcase-content">
					<div class="showcase-content-wrapper">
							<img src="<?php the_sub_field('image'); ?>" alt="<?php the_sub_field('alt'); ?>" title="<?php the_sub_field('title'); ?>">
							<div id="readMore"><a href="<?php the_sub_field('page-link'); ?>"><img src="<?php echo THEME_IMAGES; ?>/read-more.png" alt="Pil" title="Läs mer"></a></div>
					</div>
				</div>
			</div>
			<?php endwhile;
		endif; ?>
		</div>
	<div class="dotted"></div>
	<div id="content" class="clearfix">
		<div id="boxes" class="clearfix">
		<?php if(get_field('boxes')): ?>
			<?php while(the_repeater_field('boxes')): ?>
			<div class="box">
				<div class="boxImage">
					<a href="<?php the_sub_field('page-link'); ?>"><img src="<?php the_sub_field('image'); ?>" alt="<?php the_sub_field('alt'); ?>" title="<?php the_sub_field('title'); ?>"></a>
				</div>
				<h2><a href="<?php the_sub_field('page-link'); ?>"><?php the_sub_field('headline'); ?></a></h2>
				<p><a href="<?php the_sub_field('page-link'); ?>"><?php the_sub_field('text'); ?></a>  <a class="readMore" href="<?php the_sub_field('page-link'); ?>">Läs mer »</a></p>
			</div>
			<?php endwhile;
			endif; ?>
		</div>
		<div class="sidebarBox">
			<img class="yellow" src="<?php echo THEME_IMAGES; ?>/yellow.png" alt="tape">
			<h3><span>Nyheter</span></h3>
			<ul>
				<?php
				$news_query = new WP_Query(array(
						"post_type" => 'post',
						"posts_per_page" => 4,
						"category_name" => 'Nyheter'
					));
				while ($news_query->have_posts()) : $news_query->the_post();
					?>
					<li>
						<h4><a href="<?php the_permalink(); ?>#mainContent"><?php the_title(); ?></a></h4>
						<p><a href="<?php the_permalink(); ?>#mainContent"><?php echo get_the_excerpt(); ?>...</a> <a class="readMore" href="<?php the_permalink(); ?>#mainContent">Läs mer »</a></p>
					</li>
				<?php	
					endwhile;
				?>
			</ul>
		</div>
		<?php get_footer(); ?>	