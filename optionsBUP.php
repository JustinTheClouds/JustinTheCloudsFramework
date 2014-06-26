<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 */

function optionsframework_option_name() {

	// This gets the theme name from the stylesheet
	$themename = get_option( 'stylesheet' );
	$themename = preg_replace("/\W/", "_", strtolower($themename) );

	$optionsframework_settings = get_option( 'optionsframework' );
	$optionsframework_settings['id'] = $themename;
	update_option( 'optionsframework', $optionsframework_settings );
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'justintheclouds'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */

function optionsframework_options() {

	// Pull all the categories into an array
	$options_categories = array();
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
		$options_categories[$category->cat_ID] = $category->cat_name;
	}
	
	// Pull all tags into an array
	$options_tags = array();
	$options_tags_obj = get_tags();
	foreach ( $options_tags_obj as $tag ) {
		$options_tags[$tag->term_id] = $tag->name;
	}

	// Pull all the pages into an array
	$options_pages = array();
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
		$options_pages[$page->ID] = $page->post_title;
	}

	$options = array();
    
    
    $options[] = array(
		'name' => __('Design', 'justintheclouds'),
		'type' => 'heading');
    $options[] = array(
		'name' => __('Header Logo', 'justintheclouds'),
		'desc' => __('', 'justintheclouds'),
		'id' => 'design_logo',
		'type' => 'upload');
    
    $options[] = array(
		'name' => __('Contact', 'justintheclouds'),
		'type' => 'heading');
    $options[] = array(
		'name' => __('Address', 'justintheclouds'),
		'desc' => __("123 Superman St. #123", 'justintheclouds'),
		'id' => 'contact_address',
		'std' => '',
		'type' => 'text');
    $options[] = array(
		'name' => __('City', 'justintheclouds'),
		'desc' => __("Austin", 'justintheclouds'),
		'id' => 'contact_city',
		'std' => '',
		'type' => 'text');
    $options[] = array(
		'name' => __('State', 'justintheclouds'),
		'desc' => __("TX", 'justintheclouds'),
		'id' => 'contact_state',
		'std' => '',
		'type' => 'text');
    $options[] = array(
		'name' => __('Zip', 'justintheclouds'),
		'desc' => __("78753", 'justintheclouds'),
		'id' => 'contact_zip',
		'std' => '',
		'type' => 'text');
    $options[] = array(
		'name' => __('Phone', 'justintheclouds'),
		'desc' => __("(123)123-1234", 'justintheclouds'),
		'id' => 'contact_phone',
		'std' => '',
		'type' => 'text');
    $options[] = array(
		'name' => __('Fax', 'justintheclouds'),
		'desc' => __("(123)123-1234", 'justintheclouds'),
		'id' => 'contact_fax',
		'std' => '',
		'type' => 'text');
    $options[] = array(
		'name' => __('Hours', 'justintheclouds'),
		'desc' => __("Monday to Saturday, 10AM to 7PM and Sundays, 12PM to 5PM", 'justintheclouds'),
		'id' => 'contact_hours',
		'std' => '',
		'type' => 'text');
    
    $options[] = array(
		'name' => __('Social', 'justintheclouds'),
		'type' => 'heading');
    $options[] = array(
		'name' => __('Facebook Link', 'justintheclouds'),
		'desc' => __("http://www.facebook.com/username", 'justintheclouds'),
		'id' => 'social_facebook',
		'std' => '',
		'type' => 'text');
    $options[] = array(
		'name' => __('Twitter Link', 'justintheclouds'),
		'desc' => __("http://www.twitter.com/username", 'justintheclouds'),
		'id' => 'social_twitter',
		'std' => '',
		'type' => 'text');
    $options[] = array(
		'name' => __('Instagram Link', 'justintheclouds'),
		'desc' => __("http://www.instagram.com/username", 'justintheclouds'),
		'id' => 'social_instagram',
		'std' => '',
		'type' => 'text');
    $options[] = array(
		'name' => __('Pinterest Link', 'justintheclouds'),
		'desc' => __("http://www.pinterest.com/username", 'justintheclouds'),
		'id' => 'social_pinterest',
		'std' => '',
		'type' => 'text');

	$options[] = array(
		'name' => __('Header Meta', 'justintheclouds'),
		'type' => 'heading');

// Standard Meta
	$options[] = array(
		'name' => __('Head ID', 'justintheclouds'),
		'desc' => __("", 'justintheclouds'),
		'id' => 'meta_headid',
		'std' => 'www-sitename-com',
		'type' => 'text');
	$options[] = array(
		'name' => __('Google Webmasters', 'justintheclouds'),
		'desc' => __("Speaking of Google, don't forget to set your site up: <a href='http://google.com/webmasters' target='_blank'>http://google.com/webmasters</a>", 'justintheclouds'),
		'id' => 'meta_google',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => __('Author Name', 'justintheclouds'),
		'desc' => __('Populates meta author tag.', 'justintheclouds'),
		'id' => 'meta_author',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => __('Mobile Viewport', 'justintheclouds'),
		'desc' => __('Uncomment to use; use thoughtfully!', 'justintheclouds'),
		'id' => 'meta_viewport',
		'std' => 'width=device-width, initial-scale=1.0',
		'type' => 'text');

// Icons
	$options[] = array(
		'name' => __('Site Favicon', 'justintheclouds'),
		'desc' => __('', 'justintheclouds'),
		'id' => 'head_favicon',
		'type' => 'upload');
	$options[] = array(
		'name' => __('Apple Touch Icon', 'justintheclouds'),
		'desc' => __('', 'justintheclouds'),
		'id' => 'head_apple_touch_icon',
		'type' => 'upload');

// App: Windows 8
	$options[] = array(
		'name' => __('App: Windows 8', 'justintheclouds'),
		'desc' => __('Application Name', 'justintheclouds'),
		'id' => 'meta_app_win_name',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => __('', 'justintheclouds'),
		'desc' => __('Tile Color', 'justintheclouds'),
		'id' => 'meta_app_win_color',
		'std' => '',
		'type' => 'color');
	$options[] = array(
		'name' => __('', 'justintheclouds'),
		'desc' => __('Tile Image', 'justintheclouds'),
		'id' => 'meta_app_win_image',
		'std' => '',
		'type' => 'upload');

// App: Twitter
	$options[] = array(
		'name' => __('App: Twitter Card', 'justintheclouds'),
		'desc' => __('twitter:card (summary, photo, gallery, product, app, player)', 'justintheclouds'),
		'id' => 'meta_app_twt_card',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => __('', 'justintheclouds'),
		'desc' => __('twitter:site (@username of website)', 'justintheclouds'),
		'id' => 'meta_app_twt_site',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => __('', 'justintheclouds'),
		'desc' => __("twitter:title (the user's Twitter ID)", 'justintheclouds'),
		'id' => 'meta_app_twt_title',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => __('', 'justintheclouds'),
		'desc' => __('twitter:description (maximum 200 characters)', 'justintheclouds'),
		'id' => 'meta_app_twt_description',
		'std' => '',
		'type' => 'textarea');
	$options[] = array(
		'name' => __('', 'justintheclouds'),
		'desc' => __('twitter:url (url for the content)', 'justintheclouds'),
		'id' => 'meta_app_twt_url',
		'std' => '',
		'type' => 'text');

// App: Facebook
	$options[] = array(
		'name' => __('App: Facebook', 'justintheclouds'),
		'desc' => __('og:title', 'justintheclouds'),
		'id' => 'meta_app_fb_title',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => __('', 'justintheclouds'),
		'desc' => __('og:description', 'justintheclouds'),
		'id' => 'meta_app_fb_description',
		'std' => '',
		'type' => 'textarea');
	$options[] = array(
		'name' => __('', 'justintheclouds'),
		'desc' => __('og:url', 'justintheclouds'),
		'id' => 'meta_app_fb_url',
		'std' => '',
		'type' => 'text');
	$options[] = array(
		'name' => __('', 'justintheclouds'),
		'desc' => __('og:image', 'justintheclouds'),
		'id' => 'meta_app_fb_image',
		'std' => '',
		'type' => 'upload');
    
    // API Settings
    $options[] = array(
		'name' => __('APIs', 'justintheclouds'),
		'type' => 'heading');
    $options[] = array(
		'name' => __('Google Analytics ID', 'justintheclouds'),
		'desc' => __('UA-XXXXXXX-XX', 'justintheclouds'),
		'id' => 'apis_ga_id',
		'std' => '',
		'type' => 'text');
    $options[] = array(
		'name' => __('Google Analytics Domain', 'justintheclouds'),
		'desc' => __('domain.com', 'justintheclouds'),
		'id' => 'apis_ga_domain',
		'std' => '',
		'type' => 'text');

	return $options;

}