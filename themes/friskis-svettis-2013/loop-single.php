<?php
/**
 * The loop that displays a page.
 *
 */
?>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<h1><?php the_title(); ?></h1>
	<?php
		if(get_post_type( get_the_ID() ) != 'fs_news'):
	?>
			<h4><?php _e('Publicerad', 'friskis-svettis'); ?> <em><?php the_date(); ?></em> <?php _e('av', 'friskis-svettis'); ?> <em><?php the_author(); ?></em> <?php _e('i', 'friskis-svettis'); ?> <em><?php $category = get_the_category(); echo $category[0]->cat_name; ?></em></h4>
			<h4><?php _e('Etiketter', 'friskis-svettis'); ?>: 
			<?php
			$posttags = get_the_tags();
			if ($posttags) {
				foreach($posttags as $tag) {
					echo $tag->name . ', '; 
				}
			}
		?>
			</h4>
	<?php endif ?>
	<?php the_content(); 
	if(get_post_type( get_the_ID() ) != 'fs_news'):
		comments_template();
	endif;?>
<?php endwhile; // end of the loop. ?>    
