<?php 
update_option('current_page_template','category');
get_header(); ?>
<div id="gap"></div>
	<div id="content" class="clearfix">
	<div id="sidebar">
		<div class="subSidebarBox">
			<img class="yellow" src="<?php echo THEME_IMAGES; ?>/yellow.png" alt="tape">
			<h3><span>Nyheter</span></h3>
				<ul>
				<?php
				$news_query = new WP_Query(array(
						"post_type" => 'fs_news',
					));
				while ($news_query->have_posts()) : $news_query->the_post();
					?>
					<li>
						<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
						<p><a href="<?php the_permalink(); ?>"><?php echo get_the_excerpt(); ?>...</a> <a class="readMore" href="<?php the_permalink(); ?>">Läs mer »</a></p>
					</li>
				<?php	
					endwhile;
				?>
				</ul>
		</div>
		
	</div>
	<div id="mainContent" class="mainContent" style="margin-bottom: 50px;">
	<h1>Kategori: <?php
		$category = get_the_category(); 
		echo $category[0]->cat_name;
		?></h1>
             <?php
            /* Output the latest "Ordförande har ordet"
             */
             get_template_part('loop', 'category' );
            ?>
	</div>
	<div id="sidebarRight">
		<?php the_field('sidebar-right');?>
	</div>
<div class="clearfix"></div>
<?php get_footer(); ?>