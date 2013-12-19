<?php
/*
	Template name: Utan högerspalt eller vänsterspalt
*/
update_option('current_page_template','page-no-sidebar-what-so-ever');
get_header(); 
?>
	<div id="gap"></div>
	<div id="content" class="clearfix">
	
	<div id="mainContentNoSidebarWhatSoEver" class="mainContent">
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
			<?php the_content(); 
		endwhile;?>
	</div>
	
	<div class="clearfix"></div>
<?php get_footer(); ?>