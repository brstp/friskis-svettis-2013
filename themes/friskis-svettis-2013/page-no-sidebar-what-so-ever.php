<?php
/*
	Template name: Utan högerspalt eller vänsterspalt
*/

get_header(); 
?>
	<div id="gap"></div>
	<div id="content" class="clearfix">
	
	<div id="mainContentNoSidebarWhatSoEver">
		<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
			<?php the_content(); 
		endwhile;?>
	</div>
	
	<div class="clearfix"></div>
<?php get_footer(); ?>