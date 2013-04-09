<?php
/**
 * The loop that displays a page.
 *
 */
?>
<div class="container">
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
    <h1 class="entry-title"><?php the_title(); ?></h1>
    <hr>
    <div class="page-content">
            <?php the_content(); ?>
    </div><!-- .entry-content -->

<?php endwhile; // end of the loop. ?>
</div>