<?php
/**
 * The loop that displays a page.
 *
 */
?>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<h1><?php the_title(); ?></h1>
	<?php the_content(); 
	if(in_category('Nyheter')):
		
	else:
		comments_template();
	endif;?>
<?php endwhile; // end of the loop. ?>    
