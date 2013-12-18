/**
 *  Install Add-ons
 *  
 *  The following code will include all 4 premium Add-Ons in your theme.
 *  Please do not attempt to include a file which does not exist. This will produce an error.
 *  
 *  All fields must be included during the 'acf/register_fields' action.
 *  Other types of Add-ons (like the options page) can be included outside of this action.
 *  
 *  The following code assumes you have a folder 'add-ons' inside your theme.
 *
 *  IMPORTANT
 *  Add-ons may be included in a premium theme as outlined in the terms and conditions.
 *  However, they are NOT to be included in a premium / free plugin.
 *  For more information, please read http://www.advancedcustomfields.com/terms-conditions/
 */ 

// Fields 
add_action('acf/register_fields', 'my_register_fields');

function my_register_fields()
{
	//include_once('add-ons/acf-repeater/repeater.php');
	//include_once('add-ons/acf-gallery/gallery.php');
	//include_once('add-ons/acf-flexible-content/flexible-content.php');
}

// Options Page 
//include_once( 'add-ons/acf-options-page/acf-options-page.php' );


/**
 *  Register Field Groups
 *
 *  The register_field_group function accepts 1 array which holds the relevant data to register a field group
 *  You may edit the array as you see fit. However, this may result in errors if the array is not compatible with ACF
 */

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_allm%c3%a4nt',
		'title' => 'Allmänt',
		'fields' => array (
			array (
				'key' => 'field_14',
				'label' => 'Förening',
				'name' => 'city',
				'type' => 'text',
				'instructions' => 'Vilken Friskis&Svettis-förening?',
				'default_value' => '',
				'formatting' => 'none',
			),
			array (
				'key' => 'field_15',
				'label' => 'Facebook-konto',
				'name' => 'facebook-user',
				'type' => 'text',
				'instructions' => 'Vilket Facebook-konto? (användarnamnet)',
				'default_value' => '',
				'formatting' => 'none',
			),
			array (
				'key' => 'field_16',
				'label' => 'Twitter-konto',
				'name' => 'twitter-user',
				'type' => 'text',
				'instructions' => 'Vilket Twitter-konto? (användarnamnet)',
				'default_value' => '',
				'formatting' => 'none',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'page',
					'operator' => '==',
					'value' => '136',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
				0 => 'the_content',
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => 'acf_bildspel',
		'title' => 'Bildspel',
		'fields' => array (
			array (
				'key' => 'field_1',
				'label' => 'Bildspel',
				'name' => 'image-slider',
				'type' => 'repeater',
				'instructions' => 'Bildstorlek: 1000x400px.',
				'sub_fields' => array (
					array (
						'key' => 'field_2',
						'label' => 'Bild',
						'name' => 'image',
						'type' => 'image',
						'column_width' => '',
						'save_format' => 'url',
						'preview_size' => 'thumbnail',
					),
					array (
						'key' => 'field_3',
						'label' => 'Sidlänk',
						'name' => 'page-link',
						'type' => 'page_link',
						'column_width' => '',
						'post_type' => array (
							0 => '',
						),
						'allow_null' => 1,
						'multiple' => 0,
					),
					array (
						'key' => 'field_17',
						'label' => 'Alt',
						'name' => 'alt',
						'type' => 'text',
						'column_width' => '',
						'default_value' => '',
						'formatting' => 'html',
					),
					array (
						'key' => 'field_18',
						'label' => 'Title',
						'name' => 'title',
						'type' => 'text',
						'column_width' => '',
						'default_value' => '',
						'formatting' => 'html',
					),
				),
				'row_min' => 0,
				'row_limit' => '',
				'layout' => 'table',
				'button_label' => 'Add Row',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'page_template',
					'operator' => '==',
					'value' => 'page-home.php',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => 'acf_h%c3%b6gerspalt',
		'title' => 'Högerspalt',
		'fields' => array (
			array (
				'key' => 'field_13',
				'label' => 'Högerspalt',
				'name' => 'sidebar-right',
				'type' => 'wysiwyg',
				'default_value' => '',
				'toolbar' => 'full',
				'media_upload' => 'yes',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'post',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
			array (
				array (
					'param' => 'page_template',
					'operator' => '==',
					'value' => 'default',
					'order_no' => 0,
					'group_no' => 1,
				),
			),
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'post',
					'order_no' => 0,
					'group_no' => 2,
				),
			),
			array (
				array (
					'param' => 'page_template',
					'operator' => '==',
					'value' => 'default',
					'order_no' => 0,
					'group_no' => 3,
				),
			),
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'fs_news',
					'order_no' => 0,
					'group_no' => 4,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => 'acf_puffar-startsida',
		'title' => 'Puffar startsida',
		'fields' => array (
			array (
				'key' => 'field_4',
				'label' => 'Puffar',
				'name' => 'boxes',
				'type' => 'repeater',
				'instructions' => 'Bildstorlek: 200x175px',
				'sub_fields' => array (
					'field_5' => array (
						'key' => 'field_5',
						'label' => 'Bild',
						'name' => 'image',
						'type' => 'image',
						'column_width' => '',
						'save_format' => 'url',
						'preview_size' => 'thumbnail',
					),
					'field_6' => array (
						'key' => 'field_6',
						'label' => 'Rubrik',
						'name' => 'headline',
						'type' => 'text',
						'column_width' => '',
						'default_value' => '',
						'formatting' => 'none',
					),
					'field_7' => array (
						'key' => 'field_7',
						'label' => 'Text',
						'name' => 'text',
						'type' => 'textarea',
						'column_width' => '',
						'default_value' => '',
						'formatting' => 'br',
					),
					'field_8' => array (
						'key' => 'field_8',
						'label' => 'Sidlänk',
						'name' => 'page-link',
						'type' => 'page_link',
						'column_width' => '',
						'post_type' => array (
							0 => '',
						),
						'allow_null' => 0,
						'multiple' => 0,
					),
					'field_19' => array (
						'key' => 'field_19',
						'label' => 'Alt',
						'name' => 'alt',
						'type' => 'text',
						'column_width' => '',
						'default_value' => '',
						'formatting' => 'html',
					),
					'field_20' => array (
						'key' => 'field_20',
						'label' => 'Title',
						'name' => 'title',
						'type' => 'text',
						'column_width' => '',
						'default_value' => '',
						'formatting' => 'html',
					),
				),
				'row_min' => 0,
				'row_limit' => 3,
				'layout' => 'table',
				'button_label' => 'Add Row',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'page_template',
					'operator' => '==',
					'value' => 'page-home.php',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => 'acf_sidfot',
		'title' => 'Sidfot',
		'fields' => array (
			array (
				'key' => 'field_9',
				'label' => 'Sidfot vänster',
				'name' => 'footer-left',
				'type' => 'wysiwyg',
				'default_value' => '',
				'toolbar' => 'full',
				'media_upload' => 'yes',
				'the_content' => 'yes',
			),
			array (
				'key' => 'field_10',
				'label' => 'Sidfot höger',
				'name' => 'footer-right',
				'type' => 'wysiwyg',
				'default_value' => '',
				'toolbar' => 'full',
				'media_upload' => 'yes',
				'the_content' => 'yes',
			),
			array (
				'key' => 'field_11',
				'label' => 'Twitterflöde',
				'name' => 'twitter',
				'type' => 'select',
				'choices' => array (
					'show' => 'Visa Twitter-flöde',
					'hide' => 'Göm Twitter-flöde',
				),
				'default_value' => '',
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_12',
				'label' => 'Twitter användarnamn',
				'name' => 'twitter-username',
				'type' => 'text',
				'default_value' => '',
				'formatting' => 'none',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'page',
					'operator' => '==',
					'value' => '85',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
				0 => 'the_content',
			),
		),
		'menu_order' => 0,
	));
}
