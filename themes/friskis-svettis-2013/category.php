<?php get_header(); ?>
<div id="gap"></div>
	<div id="content" class="clearfix">
	<div id="sidebar">
		<div class="subSidebarBox">
			<img class="yellow" src="<?php echo THEME_IMAGES; ?>/yellow.png" alt="tape">
			<h3><span>Nyheter</span></h3>
				<ul>
				<?php
				$news_query = new WP_Query(array(
						"post_type" => 'post',
						"category_name" => 'Nyheter'
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
	<div id="mainContent" style="margin-bottom: 50px;">
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
<div class="clearfix"></div>
<?php get_footer(); ?>