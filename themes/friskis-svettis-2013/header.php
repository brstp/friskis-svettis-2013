<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage friskis-svettis
 * @since Friskis 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" >
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'twentyten' ), max( $paged, $page ) );

	?></title>
<link rel="shortcut icon" href="<?php echo HOME_URI; ?>/favicon.ico">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>?v=9" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php 
	// Method for adding right responsiv css
	if(get_option('current_page_template') == 'category'
		|| get_option('current_page_template') == '404'
		|| get_option('current_page_template') == 'search'
		|| get_option('current_page_template') == 'single')
	{
		echo '<link rel="stylesheet" type="text/css" media="all" href="' . get_bloginfo( 'template_url' ) . '/responsive-footer-1.css" />';
	}
	
	if(get_option('current_page_template') == 'page'
		|| get_option('current_page_template') == 'page-no-sidebar'
		|| get_option('current_page_template') == 'single-fs_news')
	{
		echo '<link rel="stylesheet" type="text/css" media="all" href="' . get_bloginfo( 'template_url' ) . '/responsive-footer-2.css" />';
	}
?>

<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<?php
	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>
</head>
<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/sv_SE/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div id="wrapper">
	<header>
		<div id="logo">
			<a href="<?php bloginfo('home'); ?>"><img src="<?php echo THEME_IMAGES; ?>/logo.jpg" alt="logotyp"></a>
			<?php echo get_option('association'); ?>
		</div>
		<div id="search">
		<?php get_search_form(); ?>
			<div id="mainMenuButton"><?php _e('Huvudmeny', 'friskis-svettis'); ?></div>
		</div>
			<nav id="mainMenu">
				<?php wp_nav_menu(); ?>
			</nav>
	</header>
<?php
	/*End: Header*/
?>
