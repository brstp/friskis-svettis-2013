<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 */
update_option('current_page_template','index');
get_header();
?>
<div class="masthead">
    <img src="<?php echo THEME_IMAGES ?>/masthead.png" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" />
</div>
<div class="content">
        <div class="content-main left">
             <?php
            /* Output the latest "Ordförande har ordet"
             */
             get_template_part('loop', 'chairman' );
            ?>
	</div><!-- .content-main.left -->
        <div class="content-news left">
            <?php
            /* Output the latest news posts.
             */
             get_template_part( 'loop', 'news' );
            ?>
        </div>
        <div class="content-secondary right">
            <div class="container gallery">
                <?php
                /* Output random images.
                 */
                 get_template_part( 'featured', 'gallery' );
                ?>
            </div>
            <div class="container ads">
                <?php
                /* Output ads.
                 */
                 get_template_part( 'featured', 'ads' );
                ?>
            </div>
        </div>
</div><!-- .content -->
<?php get_footer(); ?>