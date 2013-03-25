<?php
// Definition of Theme Constants
define('HOME_URI', get_bloginfo('url'));
define('THEME_URI', get_stylesheet_directory_uri());
define('THEME_IMAGES', THEME_URI . '/images');
define('THEME_CSS', THEME_URI . '/css');
define('THEME_JS', THEME_URI . '/js');
define('THEMELIB', TEMPLATEPATH . '/lib');

register_nav_menus( array(
        'primary' => __( 'Primär navigation', 'primary' ),
		'footer' => __( 'Sidfotsmeny', 'footer-menu' )
) );

function init_jquery() {
    if (!is_admin()) {
        wp_deregister_script( 'jquery' );
        wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
        wp_enqueue_script( 'jquery' );
    }
	/*
    wp_register_script('jquerytmpl', 'http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js');
    wp_enqueue_script('jquerytmpl');
	*/
}    

function load_theme_js() {
    if (!is_admin()) {
        wp_enqueue_script('functions', THEME_JS .'/functions.js', array('jquery'));
    }
}

add_action('init', 'init_jquery');
add_action('init', 'load_theme_js');

// Add Post Thumbnail Theme Support
if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
}

// Show start page in menu editor
function home_page_menu_args( $args ) {
    $args['show_home'] = true;
    return $args;
}
add_filter( 'wp_page_menu_args', 'home_page_menu_args' );

// Add custom image sizes
function image_size_setup() {
    add_image_size('featured-gallery', 230, 300, false);
}
add_action( 'after_setup_theme', 'image_size_setup' );

// Add breadcrumbs
function the_breadcrumb() {
	if (!is_home()) {
		echo '<a href="';
		echo get_option('home');
		echo '">';
		echo 'Start';
		echo "</a> » ";
		if (is_category() || is_single()) {
			the_category('title_li=');
			if (is_single()) {
				echo " » ";
				the_title();
			}
		} elseif (is_page()) {
			echo the_title();
		}
	}
}

//Excerpt length

function custom_excerpt_length( $length ) {
	return 12;
}

add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

// Remove [...]

function trim_excerpt($text) {
  return rtrim($text,'[...]');
}
add_filter('get_the_excerpt', 'trim_excerpt');

//Fix for empty searches
add_filter( 'request', 'my_request_filter' );
function my_request_filter( $query_vars ) {
    if( isset( $_GET['s'] ) && empty( $_GET['s'] ) ) {
        $query_vars['s'] = " ";
    }
    return $query_vars;
}

// Add CSS file to admin section
function mytheme_add_init() {
    if ( is_admin() ) {
        $file_dir=get_bloginfo('template_directory');
        wp_enqueue_style("functions", $file_dir."/style-admin.css", false, "1.0", "all");
    }
}

add_action( 'admin_head', 'mytheme_add_init' );

//Widgets
//Adds two Footer widgets
if ( function_exists('register_sidebar') )
{ 
	register_sidebar(
		array(
			'name'=>'Sidbar'
		)
	);
		
	register_sidebars(
		2, 
		array(
			'name'=>'Sidfot %d',
			'before_title'	=> '',
			'after_title'	=> ''			
		)
	);
}

//Widgets for Footer
class Facebook extends WP_Widget
{
  function facebook()
  {
    $widget_ops = array('classname' => 'facebook', 'description' => 'Visar en Facebook-sida. Lägg till URL för Facebook-sidan.' );
    $this->WP_Widget('facebook', 'Facebook', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'facebookPageId' => '' ) );
    
    $facebookPageId   = $instance['facebookPageId'];
?>
    <p><label for="<?php echo $this->get_field_id('facebookPageId'); ?>">Lägg till URL för Facebook-sidan: <input class="widefat" id="<?php echo $this->get_field_id('facebookPageId'); ?>" name="<?php echo $this->get_field_name('facebookPageId'); ?>" type="text" value="<?php echo attribute_escape($facebookPageId); ?>" /></label></p>
       
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    
    // Retrieve Fields
    $instance['facebookPageId']   = strip_tags($new_instance['facebookPageId']);
    
    return $instance;
  }
 
  function widget($args, $instance)
  {
	$facebookPage = substr( $instance['facebookPageId'], strrpos( $instance['facebookPageId'], '/' )+1 );		
	echo '<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2F'.$facebookPage.'&amp;width=290&amp;height=258&amp;show_faces=true&amp;colorscheme=light&amp;stream=false&amp;border_color=%23fff&amp;header=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:290px; height:290px;" allowTransparency="true"></iframe>';
  }
}

class Twitter extends WP_Widget
{
	function twitter()
	{
		$widget_ops = array('classname' => 'twitter', 'description' => 'Visar Twitter-flöde med fyra inlägg. Lägg till URL för Twitter-kontot.' );
		$this->WP_Widget('twitter', 'Twitter', $widget_ops);
	}
 
	function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'twitterId' => '' ) );  
		$twitterId   = $instance['twitterId'];
?>
		<p><label for="<?php echo $this->get_field_id('twitterId'); ?>">Lägg till URL för Twitter-kontot: <input class="widefat" id="<?php echo $this->get_field_id('twitterId'); ?>" name="<?php echo $this->get_field_name('twitterId'); ?>" type="text" value="<?php echo attribute_escape($twitterId); ?>" /></label></p>
        
<?php
	}
 
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		
		// Retrieve Fields
		$instance['twitterId']   = strip_tags($new_instance['twitterId']);
		
		return $instance;
	}
 
	function widget($args, $instance)
	{
		// Gets the Twitter result and outputs it
		echo '<div id="twitter"><h3><span>Twitter</span></h3>';
		
		$twitterId = $instance['twitterId'];
		
		if(strstr($twitterId, 'https://twitter.com'))
		{
			//Fetch the ID of the Twitter group
			$twitterAccount = substr( $twitterId, strrpos( $twitterId, '/' )+1 );
				
			// Set array
			$twitterStatus = array();
		
			for($i = 1; $i < 5; $i++)
			{
				$url = 'https://api.twitter.com/1/statuses/user_timeline/'.$twitterAccount.'.xml?count='.$i.'&include_rts=1callback=?';
				$xml = simplexml_load_file($url) or die("could not connect");

				foreach($xml->status as $status){
					$created_at = $status->created_at;
					$text = $status->text;
				}
					
				// Fix date to right format
				$date = explode(" ", $created_at);
				$month = $date[1];
				$day = $date[2];
					
				switch($month) {
					case "Jan": $month = "Jan"; break;
					case "Feb": $month = "Feb"; break;
					case "Mar": $month = "Mar"; break;
					case "Apr": $month = "Apr"; break;
					case "May": $month = "Maj"; break;
					case "Jun": $month = "Jun"; break;
					case "Jul": $month = "Jul"; break;
					case "Aug": $month = "Aug"; break;
					case "Sep": $month = "Sep"; break;
					case "Oct": $month = "Okt"; break;
					case "Nov": $month = "Nov"; break;
					case "Dec": $month = "Dec"; break;
				}
					
				// Make URL "clickable" in text
				$text = preg_replace('/https?:\/\/[\w\-\.!~?&+\*\'"(),\/]+/','<a rel="nofollow" href="$0">$0</a>',$text);
					
				// Make hashtag "clickable"
				//https://twitter.com/search?q=%23
				$text = preg_replace('/\#([a-z0-9]+)/i', '<a rel="nofollow" href="https://twitter.com/search?q=%23$1">#$1</a>', $text);
										
				echo '<div class="tweet clearfix"><div class="date">'.$day.' '.$month.'</div> <div class="excerpt">'.$text.'</div></div>';
			}
			$runOneTime++;		
		}
	echo '<br><a href="'.$instance['twitterId'].'" id="toTwitter" rel="nofollow">Till Twitter »</a></div>';
	} 
}

class LatestPost extends WP_Widget
{
	function latestPost()
	{
		$widget_ops = array('classname' => 'latestPost', 'description' => 'Visar senaste inlägget.' );
		$this->WP_Widget('latestPost', 'Senaste inlägget', $widget_ops);	
	}
	
	function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'blockTitle' => '' ) );
		
		$blockTitle   = $instance['blockTitle'];
	?>
		<p><label for="<?php echo $this->get_field_id('blockTitle'); ?>">Lägg till vinjett: <input class="widefat" id="<?php echo $this->get_field_id('blockTitle'); ?>" name="<?php echo $this->get_field_name('blockTitle'); ?>" type="text" value="<?php echo attribute_escape($blockTitle); ?>" /></label></p>
		  
		
	<?php
	 }
	 
	  function update($new_instance, $old_instance)
	  {
		$instance = $old_instance;
		
		// Retrieve Fields
		$instance['blockTitle']   = strip_tags($new_instance['blockTitle']);
		
		return $instance;
	  }

	
	function widget($args, $instance)
	{
		echo '<div id="latestPost"><h3><span>'.$instance['blockTitle'].'</span></h3>';
		
		$args = array( 'numberposts' => '2', 'category' => '-11');
		$recent_posts = wp_get_recent_posts( $args );
		foreach( $recent_posts as $recent ){
				// Get text before <!-- More --> -tag
				$post_content = explode('<!--more-->', $recent['post_content']);
				
				echo '<div class="thePost">';
					echo '<h2>'.$recent['post_title'].'</h2>';
					echo '<p>'.$post_content[0].'</p>';
					echo '<a href="'.$recent['guid'].'">Till bloggen »</a>'; 
				echo '</div>';
		}
		
		echo '</div>';
	}
}

// Opening hours
class OpeningHours extends WP_Widget
{
	function openingHours()
	{
		$widget_ops = array('classname' => 'openingHours', 'description' => 'Visar öppetider för en anläggning.', 'width' => 450 );
		$control_ops = array('width' => 280);
		$this->WP_Widget('openingHours', 'Öppetider', $widget_ops, $control_ops);
	}
 
	function form($instance)
	{
		$instance 				= wp_parse_args( (array) $instance, array( 
									'facility' => '',
									'openingMonday' => '',
									'gymClosingMonday' => '',
									'localClosingMonday' => '',
									'openingTuesday' => '',
									'gymClosingTuesday' => '',
									'localClosingTuesday' => '',
									'openingWednesday' => '',
									'gymClosingWednesday' => '',
									'localClosingWednesday' => '',
									'openingThursday' => '',
									'gymClosingThursday' => '',
									'localClosingThursday' => '',
									'openingFriday' => '',
									'gymClosingFriday' => '',
									'localClosingFriday' => '',
									'openingSaturday' => '',
									'gymClosingSaturday' => '',
									'localClosingSaturday' => '',
									'openingSunday' => '',
									'gymClosingSunday' => '',
									'localClosingSunday' => ''
								) );  
								
		$facility   			= $instance['facility'];
		// Monday
		$openingMonday 			= $instance['openingMonday'];
		$gymClosingMonday 		= $instance['gymClosingMonday'];
		$localClosingMonday		= $instance['localClosingMonday'];
		// Tuesday
		$openingTuesday 	 	= $instance['openingTuesday'];
		$gymClosingTuesday 	 	= $instance['gymClosingTuesday'];
		$localClosingTuesday 	= $instance['localClosingTuesday'];
		// Wednesday
		$openingWednesday 	 	= $instance['openingWednesday'];
		$gymClosingWednesday 	= $instance['gymClosingWednesday'];
		$localClosingWednesday 	= $instance['localClosingWednesday'];
		// Thursday
		$openingThursday 	 	= $instance['openingThursday'];
		$gymClosingThursday 	= $instance['gymClosingThursday'];
		$localClosingThursday 	= $instance['localClosingThursday'];
		// Friday
		$openingFriday 	 		= $instance['openingFriday'];
		$gymClosingFriday 		= $instance['gymClosingFriday'];
		$localClosingFriday 	= $instance['localClosingFriday'];
		// Saturday
		$openingSaturday 	 	= $instance['openingSaturday'];
		$gymClosingSaturday 	= $instance['gymClosingSaturday'];
		$localClosingSaturday 	= $instance['localClosingSaturday'];
		// Sunday
		$openingSunday 	 		= $instance['openingSunday'];
		$gymClosingSunday 		= $instance['gymClosingSunday'];
		$localClosingSunday 	= $instance['localClosingSunday'];
		
		
		//Allow iFrames
    add_filter('tiny_mce_before_init', create_function( '$a', '$a["extended_valid_elements"] = "iframe[id|class|title|style|align|frameborder|height|longdesc|marginheight|marginwidth|name|scrolling|src|width]"; return $a;') );
?>
		<p>
			<label for="<?php echo $this->get_field_id('facility'); ?>">Namn på anläggningen: 
				<input class="widefat" id="<?php echo $this->get_field_id('facility'); ?>" name="<?php echo $this->get_field_name('facility'); ?>" type="text" value="<?php echo attribute_escape($facility); ?>" />
			</label>
		</p>
		
		<table>
			<tr>
				<td></td>
				<td>Öppnar</td>
				<td>Sista inpassering</td>
				<td>Stänger</td>
			</tr>
			<tr>
				<td>Måndag</td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($openingMonday); ?>" name="<?php echo $this->get_field_name('openingMonday'); ?>"></td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($gymClosingMonday); ?>" name="<?php echo $this->get_field_name('gymClosingMonday'); ?>"></td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($localClosingMonday); ?>" name="<?php echo $this->get_field_name('localClosingMonday'); ?>"></td>
			</tr>
			<tr>
				<td>Tisdag</td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($openingTuesday); ?>" name="<?php echo $this->get_field_name('openingTuesday'); ?>"></td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($gymClosingTuesday); ?>" name="<?php echo $this->get_field_name('gymClosingTuesday'); ?>"></td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($localClosingTuesday); ?>" name="<?php echo $this->get_field_name('localClosingTuesday'); ?>"></td>
			</tr>
			<tr>
				<td>Onsdag</td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($openingWednesday); ?>" name="<?php echo $this->get_field_name('openingWednesday'); ?>"></td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($gymClosingWednesday); ?>" name="<?php echo $this->get_field_name('gymClosingWednesday'); ?>"></td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($localClosingWednesday); ?>" name="<?php echo $this->get_field_name('localClosingWednesday'); ?>"></td>
			</tr>
			<tr>
				<td>Torsdag</td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($openingThursday); ?>" name="<?php echo $this->get_field_name('openingThursday'); ?>"></td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($gymClosingThursday); ?>" name="<?php echo $this->get_field_name('gymClosingThursday'); ?>"></td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($localClosingThursday); ?>" name="<?php echo $this->get_field_name('localClosingThursday'); ?>"></td>
			</tr>
			<tr>
				<td>Fredag</td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($openingFriday); ?>" name="<?php echo $this->get_field_name('openingFriday'); ?>"></td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($gymClosingFriday); ?>" name="<?php echo $this->get_field_name('gymClosingFriday'); ?>"></td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($localClosingFriday); ?>" name="<?php echo $this->get_field_name('localClosingFriday'); ?>"></td>
			</tr>
			<tr>
				<td>Lördag</td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($openingSaturday); ?>" name="<?php echo $this->get_field_name('openingSaturday'); ?>"></td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($gymClosingSaturday); ?>" name="<?php echo $this->get_field_name('gymClosingSaturday'); ?>"></td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($localClosingSaturday); ?>" name="<?php echo $this->get_field_name('localClosingSaturday'); ?>"></td>
			</tr>
			<tr>
				<td>Söndag</td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($openingSunday); ?>" name="<?php echo $this->get_field_name('openingSunday'); ?>"></td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($gymClosingSunday); ?>" name="<?php echo $this->get_field_name('gymClosingSunday'); ?>"></td>
				<td><input type="text" class="openingHoursInput" value="<?php echo attribute_escape($localClosingSunday); ?>" name="<?php echo $this->get_field_name('localClosingSunday'); ?>"></td>
			</tr>
		</table>


<?php
	}
 
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		
		// Retrieve Fields
		$instance['facility']   				= strip_tags($new_instance['facility']);
		$instance['openingMonday']   			= strip_tags($new_instance['openingMonday']);
		$instance['gymClosingMonday']   		= strip_tags($new_instance['gymClosingMonday']);
		$instance['localClosingMonday']   		= strip_tags($new_instance['localClosingMonday']);
		$instance['openingTuesday']  			= strip_tags($new_instance['openingTuesday']);
		$instance['gymClosingTuesday']   		= strip_tags($new_instance['gymClosingTuesday']);
		$instance['localClosingTuesday']   		= strip_tags($new_instance['localClosingTuesday']);
		$instance['openingWednesday']   		= strip_tags($new_instance['openingWednesday']);
		$instance['gymClosingWednesday']   		= strip_tags($new_instance['gymClosingWednesday']);
		$instance['localClosingWednesday']   	= strip_tags($new_instance['localClosingWednesday']);
		$instance['openingThursday']   			= strip_tags($new_instance['openingThursday']);
		$instance['gymClosingThursday']   		= strip_tags($new_instance['gymClosingThursday']);
		$instance['localClosingThursday']   	= strip_tags($new_instance['localClosingThursday']);
		$instance['openingFriday']   			= strip_tags($new_instance['openingFriday']);
		$instance['gymClosingFriday']   		= strip_tags($new_instance['gymClosingFriday']);
		$instance['localClosingFriday']   		= strip_tags($new_instance['localClosingFriday']);		
		$instance['openingSaturday']   			= strip_tags($new_instance['openingSaturday']);
		$instance['gymClosingSaturday']   		= strip_tags($new_instance['gymClosingSaturday']);
		$instance['localClosingSaturday']   	= strip_tags($new_instance['localClosingSaturday']);
		$instance['openingSunday']   			= strip_tags($new_instance['openingSunday']);
		$instance['gymClosingSunday']   		= strip_tags($new_instance['gymClosingSunday']);
		$instance['localClosingSunday']   		= strip_tags($new_instance['localClosingSunday']);
		
		return $instance;
	}
 
	function widget($args, $instance)
	{
		echo '<div class="openingHoursSingle">';
			echo '<table width="100%" border-collapse: collapse;>';
				echo '<tr>';
					echo '<td><h4>'.$instance['facility'].'</h4></td>';
					echo '<td><strong>Mån</strong></td>';
					echo '<td><strong>Tis</strong></td>';
					echo '<td><strong>Ons</strong></td>';
					echo '<td><strong>Tors</strong></td>';
					echo '<td><strong>Fre</strong></td>';
					echo '<td><strong>Lör</strong></td>';
					echo '<td><strong>Sön</strong></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<th>Öppnar</th>';
					echo '<td>'.$instance['openingMonday'].'</td>';
					echo '<td>'.$instance['openingTuesday'].'</td>';
					echo '<td>'.$instance['openingWednesday'].'</td>';
					echo '<td>'.$instance['openingThursday'].'</td>';
					echo '<td>'.$instance['openingFriday'].'</td>';
					echo '<td>'.$instance['openingSaturday'].'</td>';
					echo '<td>'.$instance['openingSunday'].'</td>';
				echo '</tr>';
        if ( strlen( trim( $instance['gymClosingMonday'].$instance['gymClosingTuesday'].$instance['gymClosingWednesday'].$instance['gymClosingThursday'].$instance['gymClosingFriday'].$instance['gymClosingSaturday'].$instance['gymClosingSunday'])) > 0 )
          {
    				echo '<tr>';
    					echo '<th>Sista inpassering</th>';
    					echo '<td>'.$instance['gymClosingMonday'].'</td>';
    					echo '<td>'.$instance['gymClosingTuesday'].'</td>';
    					echo '<td>'.$instance['gymClosingWednesday'].'</td>';
    					echo '<td>'.$instance['gymClosingThursday'].'</td>';
    					echo '<td>'.$instance['gymClosingFriday'].'</td>';
    					echo '<td>'.$instance['gymClosingSaturday'].'</td>';
    					echo '<td>'.$instance['gymClosingSunday'].'</td>';
    				echo '</tr>';
  				}
				echo '<tr>';
					echo '<th>Stänger</th>';
					echo '<td>'.$instance['localClosingMonday'].'</td>';
					echo '<td>'.$instance['localClosingTuesday'].'</td>';
					echo '<td>'.$instance['localClosingWednesday'].'</td>';
					echo '<td>'.$instance['localClosingThursday'].'</td>';
					echo '<td>'.$instance['localClosingFriday'].'</td>';
					echo '<td>'.$instance['localClosingSaturday'].'</td>';
					echo '<td>'.$instance['localClosingSunday'].'</td>';
				echo '</tr>';
			echo '</table>';
		echo '</div>';
	} 
}

class blockTitle extends WP_Widget
{
	function blockTitle()
	{
		$widget_ops = array('classname' => 'blockTitle', 'description' => 'Visar en rubrik för valfritt block, detta block ska läggas ovanför den widget man vill ha en rubrik på. Vissa block har rubrik inbyggt, andra inte, kolla aktuellt block först innan du lägger till denna widget.' );
		$this->WP_Widget('blockTitle', 'Rubrik', $widget_ops);	
	}
	
	function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'blockTitle' => '' ) );
		
		$blockTitle   = $instance['blockTitle'];
	?>
		<p><label for="<?php echo $this->get_field_id('blockTitle'); ?>">Skriv rubrik: <input class="widefat" id="<?php echo $this->get_field_id('blockTitle'); ?>" name="<?php echo $this->get_field_name('blockTitle'); ?>" type="text" value="<?php echo attribute_escape($blockTitle); ?>" /></label></p>
		  
		
	<?php
	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;

		$instance['blockTitle']  = strip_tags($new_instance['blockTitle']);
		
		return $instance;
	}

	function widget($args, $instance)
	{
		// For individual id name
		$RemoveChars[] = '/å/';
		$RemoveChars[] = '/ä/';
		$RemoveChars[] = '/ö/';
		$RemoveChars[] = '/Å/';
		$RemoveChars[] = '/Ä/';
		$RemoveChars[] = '/Ö/';

		$ReplaceWith[] = 'a';
		$ReplaceWith[] = 'a';
		$ReplaceWith[] = 'o';
		$ReplaceWith[] = 'A';
		$ReplaceWith[] = 'A';
		$ReplaceWith[] = 'O';

		$idName  		= strtolower(preg_replace($RemoveChars, $ReplaceWith, $instance['blockTitle']));
		$idNameExploded = explode(' ', $idName);
		
		if(count($idNameExploded) > 1)
		{
			$idName = $idNameExploded[0];
			$idName .= ucfirst($idNameExploded[1]);
		}

		echo '<div class="blockTitle" id="'.$idName.'"><h3><span>'.$instance['blockTitle'].'</span></h3></div>';
	}
}


add_action( 'widgets_init', 'load_widgets' );

function load_widgets() {
	register_widget( 'Facebook' );
	register_widget( 'Twitter' );
	register_widget( 'LatestPost' );
	register_widget( 'OpeningHours' );
	register_widget( 'blockTitle' );
}

// WP-Admin style

add_editor_style('custom-editor-style.css');



function register_settings() {
	register_setting( 'fs_settings', 'association' ); 
	register_setting( 'fs_settings', 'facebookAccount' ); 
	register_setting( 'fs_settings', 'twitterAccount' ); 
} 
add_action( 'admin_init', 'register_settings' );

function fs_settings() {
	add_options_page( 'Friskis&Svettis', 'Friskis&Svettis', 'manage_options', 'my-unique-identifier', 'fs_options' );
}
add_action( 'admin_menu', 'fs_settings' );

function fs_options() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
		echo '<div id="icon-options-general" class="icon32"><br></div>';
		echo '<h2>Friskis&Svettis</h2>';
		echo '<form name="form" method="post" action="options.php">';
			settings_fields( 'fs_settings' );

				$association 		= get_option('association');
				$facebookAccount 	= get_option('facebookAccount');
				$twitterAccount		= get_option('twitterAccount');
			
			echo '<table class="form-table">';
				echo '<tbody>';
					echo '<tr>';
						echo '<th scope="row"><label for="association">Förening</label></th>';
						echo '<td>
								<input type="text" name="association" value="'. $association .'" class="regular-text">
								<p class="description">Vilken Friskis&Svettis-förening?</p>
							</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<th scope="row"><label for="association">Facebook-konto</label></th>';
						echo '<td>
								<input type="text" name="facebookAccount" value="'. $facebookAccount .'" class="regular-text">
								<p class="description">Vilket Facebook-konto? (användarnamnet)</p>
							</td>';
					echo '</tr>';
					echo '<tr>';
						echo '<th scope="row"><label for="association">Twitter-konto</label></th>';
						echo '<td>
								<input type="text" name="twitterAccount" value="'. $twitterAccount .'" class="regular-text">
								<p class="description">Vilket Twitter-konto? (användarnamnet)</p>
							</td>';
					echo '</tr>';
				echo '</tbody>';
			echo '</table>';
			
				submit_button( 'Spara ändringar' );		
		echo '</form>';
	echo '</div>';

}



// Function written to work with ACF and the way the theme "Friskis & Svettis" is built
// with the setting page "Inställningar". 
function fetchSettings() {
	
	global $wpdb;
	
	$posts 		= $wpdb->base_prefix . 'posts';	
	$postmeta	= $wpdb->base_prefix . 'postmeta';	
	$pageName 	= 'Inställningar';
		
	$rows = $wpdb->get_results($wpdb->prepare(
			"
			SELECT
			*
			FROM
			$postmeta
			WHERE
			post_id LIKE
				(SELECT 
				id 
				FROM 
				$posts 
				WHERE 
				post_title LIKE '$pageName' 
				AND 
				post_status = 'publish')
			"
		));
		
	foreach($rows as $res)
	{
		if($res->meta_key == 'city')
		{
			$city = $res->meta_value;
		}
		
		if($res->meta_key == 'facebook-user')
		{
			$facebookUser = $res->meta_value;
		}
		
		if($res->meta_key == 'twitter-user')
		{
			$twitterUser = $res->meta_value;
		}
	}
	
	return array('city' => $city, 'facebookUser' => $facebookUser, 'twitterUser' => $twitterUser);
}

