<?php

    //Initialize the update checker.
    require_once dirname( __FILE__ ) . '/includes/theme-updates/theme-update-checker.php';
    $update_checker = new ThemeUpdateChecker(
        'Starter',
        'https://raw.githubusercontent.com/JustinTheClouds/Wordpress-Starter-Theme/master/version-info.json'
    );

// TODO add author header meta image/name, link to microdata meta
// TODO move framework default options to default and otheres to starter theme
// TODO move to constructor
// TODO apply text domain functions to all methods, create JTCF::__/_e for quicker use
global $JTCFDefaults;
$JTCFDefaults = array(
    // REMOVE this is not needed since it is no dynamic. Make calls to this static
    'textdomain'     => 'justintheclouds',
    // This language will default to using the frameworks own text domain
    // Since use of variables in language function is incorrect, we handle
    // customization by allowing the user to pass in their own ALREADY lang converted text
    // and ignore the use of the default framework text domain
    // TODO Verify this works and is proper, ask http://ottopress.com/2012/internationalization-youre-probably-doing-it-wrong/ ?
    // TODO pull all lang vars to here
    'language' => array(
        'Design' => __('Design', 'justintheclouds'),
        'Contact' => __('Contact', 'justintheclouds'),
        'Header Meta' => __('Header Meta', 'justintheclouds'),
        'Social' => __('Social', 'justintheclouds'),
        'API Settings' => __('API Settings', 'justintheclouds'),
        'Microdata' => __('Microdata', 'justintheclouds')
    ),
    'folders'        => array(
        'styles'       => '/css',
        'scripts'      => '/js',
        'languages'    => get_template_directory() . '/languages'
    ),
    'styles'         => array(
        '_enqueue'     => true
    ),
    'scripts'         => array(
        '_enqueue'     => true
    ),
    'hooks' => array(
        'after_theme_setup' => array('JTCF', 'hookAfterThemeSetup')
        '
    ),
    'microdata' => array(
        'bodyScope' => "http://schema.org/WebPage",
        'headerScope' => array('JTCF', '_getMicrodataHeaderScope'),
        'headerItempropName' => "name",
        'headerItempropUrl' => "url",
        'headerItempropImage' => "image",
        'headerItempropTagline' => "description",
        'headerMeta' => array('JTCF', '_getMicrodataHeaderMeta'),
        'mainScope' => array('JTCF', '_getMicrodataMainScope'),
        'mainItemprop' => 'mainContentOfPage',
        'mainItempropAbout' => "",
        'articleScope' => array('JTCF', '_getMicrodataArticleScope'),
        'asideScope' => "http://schema.org/WPSidebar",
        'footerScope' => "http://schema.org/WPFooter"
    )
);

if(!class_exists('JTCF')) {
    
    /**
     * JustinTheClouds Framework
     * 
     * This class holds all the functionality for the wordpress
     * parent framework. It's goal is to be simple to use and learn
     * while streamlining child theme development.
     * 
     * @author Justin Carlson
     * @since 1.0.0
     * 
     * @Todo Convert all language to using JTCF::__
     * @TODO Document all actions/filters
     * @Future Integrate MrMetaBox
     * @Future Integrate Custom Post Types
     */
    class JTCF {

        /**
         * Main instance
         * 
         * Holds the singleton instance that gets created upon first
         * getInstance call.
         * 
         * @since 1.0.0
         */
        private static $_instance = null;

        /**
         * Theme configs
         * 
         * These configs can be passed in on framework initialization
         * to allow more control for child themes.
         * 
         * @since 1.0.0
         */
        private static $_configs = array();
        
        /**
         * Holds the theme details so we don't have to keep calling wp_get_theme
         */
        public static $theme;
        
        /**
         * If the theme has a h1 used already
         * 
         * This should only occur on the homepage used for the site name.
         */
        private static $_hasH1 = false;
        
        /**
         * If the theme has a h2 for the tagline
         * 
         * This should only occur on the homepage used for the site name.
         */
        private static $_hasH2 = false;
        
        /**
         * If the theme has a banner role
         * 
         * This should occur once, and usually in the main header at the top
         * 
         * @since 1.0.0
         */
        private static $_hasBanner = false;
        
        /**
         * Defined microdata scope locations
         * 
         * These are used to track if itemprop="$property" should
         * be outputted when called.
         * 
         * @since 1.0.0
         */
        private static $_scopes = array();
        

        /**
         * Framework developer contact email
         * 
         * ** THIS SHOULD NOT BE CHANGED **
         * ** It is used for support and debugging on client websites **
         *
         * @since 1.0.0
         */
        private $_contactEmail = "justin@justintheclouds.com";

        /**
         * INITIALIZATION METHODS
         * -----------------------------------------------------------
         */

        /**
         * Returns the singleton instance of JTCF
         * 
         * Upon first initialization, $configs may be passed in to
         * configure the themes functionality and settings
         * 
         * @param array $configs array
         * @since 1.0.0
         * @return Instance of JTCF class
         */
        public static function getInstance($configs=array()) {
            if(self::$_instance === null) {
                self::$_instance = new JTCF($configs);
            }
            return self::$_instance;
        }

        public function __construct($configs) {

            // Grab the details
            self::$theme = wp_get_theme();
            
            // Merge configs with default configs
            global $JTCFDefaults;
            self::$_configs = self::arrayMergeRecursiveDistinct($JTCFDefaults, $configs);
            
            // Setup and run initialization configuration
            self::_runConfiguration(self::$_configs);

            // TODO Setup update checker here

            // Define all hooks
            add_action('wp_enqueue_scripts', array('JTCF', 'hookWPEnqueueScripts'));
            
            // Define all filters
            
            // FIXME Do we need these?
            add_action('wp_footer', array('JTCF', 'footer'));
        }
        
        // TODO document me
        private static function _runOptionsSetup() {
            
            // Setup options framework
            // Options Framework (https://github.com/devinsays/options-framework-plugin)
            define('OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/includes/options-framework/');
            require_once get_template_directory() . '/includes/options-framework/options-framework.php';
                        
            add_filter( 'of_options', function( $options ) {
                $options = $options ? $options : array();
                
                // Apply shorthand options
                foreach(self::$_configs['options'] as $tab => $tabOptions) {
                    // First loop is tab headings
                    $options[] = array(
                        'name' => JTCF::__($tab),
                        'type' => 'heading'
                    );
                    foreach($tabOptions as $name => $option) {
                        // Create defaults
                        $defaults = array(
                            'name' => ucfirst(str_replace('_', ' ', $name)),
                            'id' => strtolower(JTCF::__($tab)) . '-' . ucfirst(str_replace('_', '-', $name))
                        );
                        if(is_array($option)) {
                            $defaults['type'] = $option['type'];
                            $option = array_merge($defaults, $option);
                        } else {
                            $defaults['type'] = $option;
                            $option = $defaults;
                        }
                        $options[] = $option;
                    }// specifiedcss
                }
                return $options;
                
            });

        }

        /**
         * Begin theme configuration
         * 
         * This loops through each passed in configuration option and runs
         * its cooresponsing configuration method.
         * 
         * @param $configs array The merged configs, including the default
         * configs and child theme initiation configs.
         * @since 1.0.0
         */
        private static function _runConfiguration($configs) {
            // Loop through each config and process it
            foreach($configs as $key => $value) {
                // Skip folder option, no configuration for it
                if($key == 'folders') continue;
                // Get method name
                $method = '_run' . ucfirst($key) . 'Setup';
                if(method_exists('JTCF', $method)) {
                    call_user_func(array('JTCF', $method));
                } else {
                    // The option does not exist, warn user
                    trigger_error ( __("The option '$key' is not a valid param to initialize JTCF with"), E_USER_NOTICE );
                }
            }
        }
        
        // TODO document me
        private static function _runHooksSetup() {
            if(count(self::$_configs['hooks'])) {
                foreach(self::$_configs['hooks'] as $action => $hook) {
                    echo "adding $action:$hook";
                    add_action($action, $hook);
                }
            }
        }
        
        /**
         * Store widgets for the widgets_init hook
         * 
         * @since 1.0.0
         */
        private static function _runWidgetsSetup() {
            // FIXME Remove this?
            //self::$_widgets = $widgets;
            add_action('widgets_init', array('JTCF', 'hookWidgetsInit'));
        }
        
        /**
         * Store styles for the wp_enqueue_scripts hook
         * 
         * @since 1.0.0
         */
        private static function _runStylesSetup() {
            // FIXME Need anything here?
            //self::$_styles = $styles;
        }
        
        /**
         * Store scripts for the wp_enqueue_scripts hook
         * 
         * @since 1.0.0
         */
        private static function _runScriptsSetup() {
            // FIXME Need anything here?
            //self::$_scripts = $scripts;
        }
        
        /**
         * Sets up language text domain
         * // REMOVE
         * @since 1.0.0
         */
        private static function _runTextdomainSetup() {
            // Setup language text domain
            load_theme_textdomain( self::$_configs['textdomain'], self::$_configs['folders']['languages'] );
        }
        
        /**
         * Sets up auto updating for the child theme
         * 
         * @since 1.0.0
         */
        private static function _runUpdaterSetup() {
            // Make sure we have an update url
            if(isset(self::$_configs['updater'])) {
                // Updater should already be included for the parent theme
                $update_checker = new ThemeUpdateChecker(
                    self::$theme->get_stylesheet(),
                    self::$_configs['updater']
                );
            }
        }

        public static function hookAfterThemeSetup() {

            
            add_theme_support( 'automatic-feed-links' );	
            add_theme_support( 'structured-post-formats', array( 'link', 'video' ) );
            add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'quote', 'status' ) );
            add_theme_support( 'post-thumbnails' );

            // This theme uses wp_nav_menu() in two locations.
            register_nav_menus( array(
                'primary'   => __( 'Top primary menu', 'justintheclouds' ),
                'footer' => __( 'Footer menu if different from primary', 'justintheclouds' ),
            ) );
            

            add_editor_style( array( 'css/entry-content-base.css', get_stylesheet_directory_uri() ) );
        }


        /**
         * Allows archive pages to be added to menus
         */
        public static function enableArchivePageMenu() {
            require_once get_template_directory . '/includes/enable-custom-archive-pages.php';
        }
        
        /**
         * HOOK METHODS
         */
        
        /**
         * Registers and enqueues all styles and scripts
         * 
         * FUTURE create a _register setting to avoid auto registering every style
         * @since 1.0.0
         */
        public static function hookWPEnqueueScripts() {
            // If any styles were added, register and enqueue
            if(count(self::$_configs['styles']) > 1) {
                // Get style settings
                $settings = self::_extractSettings(self::$_configs['styles']);
                // Process styles
                foreach(self::$_configs['styles'] as $key => $style) {
                    // If this is an array, call register_style with passed in values
                    if(is_array($style)) {
                        call_user_func_array('wp_register_style', $style);
                        $style = $style[0];
                    } else {
                        wp_register_style($style, get_stylesheet_directory_uri() . '/' . self::$_configs['folders']['styles'] . '/' . strtolower($style) . '.css', array(), self::$theme->version);
                    }
                    // Should we enqueue this style
                    if($settings['enqueue'] !== false) {
                        // If true, enqueue all styles; If it's an array, check if this style is in it
                        if($settings['enqueue'] === true || (is_array($settings['enqueue']) && in_array($style, $settings['enqueue']))) {
                            wp_enqueue_style($style);  
                        }
                    }
                }   
            }
            // Register and enqueue all scripts
            if(count(self::$_configs['scripts'])) {
                // Get script settings
                $settings = self::_extractSettings(self::$_configs['scripts']);
                foreach(self::$_configs['scripts'] as $script) {
                    // If this is an array, call enqueue with passed in values
                    if(is_array($script)) {
                        call_user_func_array('wp_register_script', $script);
                        $script = $script[0];
                    } else {
                        wp_register_script($script, get_stylesheet_directory_uri() . '/' . self::$_configs['folders']['scripts'] . '/' . strtolower($script) . '.css', array(), self::$theme->version);
                        wp_enqueue_script($script);
                    }
                    // Should we enqueue this script
                    if($settings['enqueue'] !== false) {
                        // If true, enqueue all scripts; If it's an array, check if this script is in it
                        if($settings['enqueue'] === true || (is_array($settings['enqueue']) && in_array($script, $settings['enqueue']))) {
                            wp_enqueue_script($script);  
                        }
                    }
                }   
            }
        }
        
        /**
         * Registers widgets
         * 
         * @since 1.0.0
         */
        public static function hookWidgetsInit() {
            if(count(self::$_configs['widgets'])) {
                $settings = self::_extractSettings(self::$_configs['widgets']);
                foreach(self::$_configs['widgets'] as $widgetName => $widget) {
                    
                    // Grab widget name
                    if(!is_array($widget)) $widgetName = $widget;
                    
                    // Create default name, id, and class
                    $defaults['name']  = __( ucfirst($widgetName), self::$_configs['textdomain'] );
                    $defaults['id']    = 'widget-' . sanitize_title_with_dashes( $widgetName );
                    $defaults['class'] = 'widget-' . sanitize_title_with_dashes( $widgetName );
                    
                    $defaults = array_merge($defaults, $settings['defaults']);
                    
                    // If this is an array, call with passed in values
                    if(is_array($widget)) {
                        // Merge passed in settings with default settings
                        $widget = array_merge($defaults, $widget);
                        call_user_func_array('register_sidebar', $widget);
                    } else {
                        register_sidebar($defaults);
                    }
                }   
            }
        }

        /**
         * FILTER METHODS
         * -----------------------------------------------------------
         */

        /**
         * Create a nicely formatted and more specific title element text for output
         * in head of document, based on current view.
         *
         * @since Twenty Fourteen 1.0
         *
         * @param string $title Default title text for current view.
         * @param string $sep Optional separator.
         * @return string The filtered title.
         * 
         * FIXME rename accordingly
         */
        public static function twentyfourteen_wp_title( $title, $sep ) {
            global $paged, $page;

            if ( is_feed() ) {
                return $title;
            }

            // Add the site name.
            $title .= get_bloginfo( 'name', 'display' );

            // Add the site description for the home/front page.
            $site_description = get_bloginfo( 'description', 'display' );
            if ( $site_description && ( is_home() || is_front_page() ) ) {
                $title = "$title $sep $site_description";
            }

            // Add a page number if necessary.
            if ( $paged >= 2 || $page >= 2 ) {
                $title = "$title $sep " . sprintf( __( 'Page %s', 'twentyfourteen' ), max( $paged, $page ) );
            }

            return $title;
        }

        /**
         * HELPER METHODS
         * -----------------------------------------------------------
         */
        
        /**
         * Return a language term by name
         * 
         * The framework uses it's own text-domain for languages.
         * Since using variables in language function is incorrect,
         * we allow the child theme to overwrite the frameworks default terms.
         * 
         * This method is just a quickk way of calling the term while
         * reminding us that it's a mofidiable term.
         * 
         * @since 1.0.0
         */
        private static function __($name) {
            if(!isset(self::$_configs['language'][$name])) {
                trigger_error("The language term '$name' is not defined", E_USER_NOTICE);
                return $name;
            }
            return self::$_configs['language'][$name];
        }
        
        /**
         * Extract settings from a config option
         * 
         * This will extract and return any settings from the array.
         * A setting is defined by having an '_' prefixed infront of it.
         * This will remove the setting from the main classes $_configs property as well.
         * 
         * @param array &$option The option to have its settings extracted
         * @return array Returns the settings extracted as an array
         */
        private static function _extractSettings(&$option) {         
            if(!is_array($option)) return null;
            $settings = array();
            foreach($option as $k => $config) {
                if(substr($k, 0, 1) == '_') {
                    $settings[substr($k, 1)] = $config;
                    unset($option[$k]);
                }
            }
            return $settings;    
        }
        
        /**
         * Merge arrays recursively AND distinctly as it should be
         * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
         * keys to arrays rather than overwriting the value in the first array with the duplicate
         * value in the second array, as array_merge does. I.e., with array_merge_recursive,
         * this happens (documented behavior):
         *
         * arrayMergeRecursiveDistinct(array('key' => 'org value'), array('key' => 'new value'));
         *     => array('key' => array('org value', 'new value'));
         *
         * arrayMergeRecursiveDistinct does not change the datatypes of the values in the arrays.
         * Matching keys' values in the second array overwrite those in the first array, as is the
         * case with array_merge, i.e.:
         *
         * arrayMergeRecursiveDistinct(array('key' => 'org value'), array('key' => 'new value'));
         *     => array('key' => array('new value'));
         *
         * Parameters are passed by reference, though only for performance reasons. They're not
         * altered by this function.
         *
         * @param array $array1 The original array to merge ontop of
         * @param array $array2 The new array
         * @return array
         * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
         * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
         * @link http://www.php.net//manual/en/function.array-merge-recursive.php#92195
         */
        public static function arrayMergeRecursiveDistinct ( array &$array1, array &$array2 ) {
            $merged = $array1;
            foreach ( $array2 as $key => &$value ) {
                if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) ) {
                    $merged [$key] = self::arrayMergeRecursiveDistinct ( $merged [$key], $value );
                } else {
                    $merged [$key] = $value;
                }
            }
            return $merged;
        }
        
        // TODO document me and move
        private static function _outputAttributes($location, $atts) {
            switch($location) {
                case 'body':
                    // Pull class attr out
                    if(isset($atts['class'])) {
                        $classes = $atts['class'];
                        unset($atts['class']);
                    } else {
                        $class = '';
                    }
                    // Output body classes
                    echo ' ';
                    body_class($class);
                break;
                case 'header':
                    // Skip role if role defined
                    if(isset($atts['role'])) break;
                    // If this is the first header, set as banner
                    if(self::$_hasBanner) break;
                    echo ' role="banner"';
                    self::$_hasBanner = true;
                break;
                case 'main':
                    echo ' role="main"';
                break;
                case 'article':
                    echo ' role="article"';
                    // Pull class attr out
                    if(isset($atts['class'])) {
                        $classes = $atts['class'];
                        unset($atts['class']);
                    } else {
                        $class = '';
                    }
                    // Output body classes
                    echo ' ';
                    post_class($class);
                break;
                case 'aside':
                    echo ' role="complementary"';
                break;
                case 'footer':
                    echo ' role="contentinfo"';
                break;
            }
            if(count($atts)) {
                foreach($atts as $name => $value) {
                    if($value !== null) {
                        echo " $name=\"$value\"";   
                    }
                }
            }
        }
        
        private static function _getMicrodataHeaderScope() {
            return 'http://schema.org/' . of_get_option('microdata-header-scope-type', 'Organization');
        }
        
        private static function _getMicrodataMainScope() {
            if(is_home() || is_archive() || is_single()) {
                return 'http://schema.org/Blog';
            }
            if(is_page()) {
                return 'http://schema.org/CreativeWork';
            }
        }
        
        private static function _getMicrodataArticleScope() {
            if(is_home() || is_archive() || is_single()) {
                return 'http://schema.org/BlogPosting';
            }
            if(is_page()) {
                return 'http://schema.org/CreativeWork';
            }
        }
        
        private static function _outputMicrodata($location, $type, $property="") {
            
            // Define method name
            $method = $location . ucfirst($type) . ucfirst($property);
            
            // Is microdata disabled?
            if(of_get_option('microdata-disable', false) || !isset(self::$_configs['microdata'][$method])) return;
                    
            if(is_callable(self::$_configs['microdata'][$method])) {
                $data = call_user_func_array(self::$_configs['microdata'][$method], func_get_args());
            } elseif(is_string(self::$_configs['microdata'][$method])) {
                $data = self::$_configs['microdata'][$method];
            } else {
                return;
            }

            // Allow child theme to modify 
            $data = apply_filters('JTCF_outputMicrodata', $data, $location, $type, $property);
            
            // Output data
            switch($type) {
                case 'scope':
                    // Add to defined scopes
                    self::$_scopes[$location] = $data;
                    echo ' itemscope="itemscope" itemtype="' . $data . '"';
                break;
                case 'itemprop':
                    // Make sure this location is defined/being used and output itemprop="property"
                    if(isset(self::$_scopes[$location])) {
                        echo ' itemprop="' . $data . '"';
                    }
                break;
            }
        
        }
                
        // TODO Document me
        public static function openSection($location, $atts=array()) {
            
            // Do action before opening the tag
            do_action('JTCF_beforeOpenSection', $location, $atts);
            
            // Allow child theme to modify location/tag, and attrs
            list($location, $atts) = apply_filters('JTCF_openSection', $location, $atts);
            
            // Open tag
            echo "<$location";
            
            // Output element attributes
            self::_outputAttributes($location, $atts);
            
            // Output the microdata item scope
            self::_outputMicrodata($location, 'scope');
            
            // Output the microdata itemprop if it has one
            self::_outputMicrodata($location, 'itemprop');
            
            // Close opening tag
            echo ">";
            
            // Do action after opening tag
            do_action('JTCF_afterOpenSection', $location, $atts);
        }
        
        // TODO document me
        public static function closeSection($location) {
            
            // TODO Output remaning properties that were not defined in markup
            
            switch($location) {
                case 'body':
                    wp_footer();
                break;
            }
            
            echo "</$location>";
        }
        
        // REMOVE
        private static function _outputMicrodataScope($location, $itemprop=null) {
            // If the scope is defined, then apply it
            if(self::_hasMicrodata($location)) {
                $scope = self::$_configs['microdata'][$location]['scope'];
                // If the user define a function to determine this scope
                if(is_callable($scope, false, $callName)) {
                    // Call user defined scope function for this location
                    echo ' itemscope="itemscope" itemtype="' . call_user_func($scope) . '"';
                } else {
                    // Output scope since it's not a callable function
                    echo ' itemscope="itemscope" itemtype="' . $scope . '"';
                }
            }
            
            // TODO or
            // uses _microdataItemProp to outputs itemprop="someprop" itemscope itemtype="http://schema.org/Blog"
            // unsets microdata/location/$itemscope (since it's defined it won't be added to a metta tag)
        }
        
        // REMOVE
        private static function _outputMicrodataItemProp($location) {
            if(self::_hasMicrodata($location) && isset(self::$_configs['microdata'][$location]['itemprop'])) {
                echo ' itemprop="' . self::$_configs['microdata'][$location]['itemprop'] . '"';
                unset(self::$_configs['microdata'][$location]['properties'][self::$_configs['microdata'][$location]['itemprop']]);
            }
            // outputs itemprop="$itemprop"
            // only if microdata/location/itemprop isset
        }
        
        // REMOVE
        private static function _outputMicrodataMainScope() {
            if(is_home() || is_archive() || is_single()) {
                return "http://schema.org/Blog";
            }
            if(is_page()) {
                return "http://schema.org/CreativeWork";
            }
        }
        
        // TODO document and move
        // REMOVE ?
        private static function _hasMicrodata($location) {
            return isset(self::$_configs['microdata'][$location]) && isset(self::$_configs['microdata'][$location]['scope']) && !empty(self::$_configs['microdata'][$location]['scope']);
        }
        
        // TODO document and move
        // REMOVE ?
        private static function _getMicrodata($location) {
            return self::$_configs['microdata'][$location]['properties'];
        }
        
        // Todo document me
        private static function _outputMicrodataMeta($location) {
            // outputs remaining unused itemprops for this location into meta tags
            if(self::_hasMicrodata($location)) {
                foreach(self::_getMicrodata($location) as $name => $data) {
                    if(is_callable($data)) {
                        $data = call_user_func($data);
                    }
                    if(is_string($data) && !empty($data)) {
                        echo '<meta itemprop="' . $name . '" content="' . $data . '" />';
                    }
                }
            }
        }
        
        /**
         * Microdata meta data for the page body
         * 
         * @since 1.0.0
         */
        public static function microdataBodyMeta() {
            return;
            $meta = array(
                'aboout' 
            );
            if(get_bloginfo('description')) $meta['about'] = get_bloginfo('description');
            return '<meta itemprop="about" content="' . get_the_title() . '" />' .
                   '<meta itemprop="url" content="' . get_permalink() . '" />' .
                   '<meta itemprop="articleBody" content="' . get_the_content() . '" />' .
                   '<meta itemprop="datePublished" content="' . get_the_date('c') . '" />';
        }
        
        public static function microdataArticleMeta() {
            return;
            return '<meta itemprop="name" content="' . get_the_title() . '" />' .
                   '<meta itemprop="url" content="' . get_permalink() . '" />' .
                   '<meta itemprop="articleBody" content="' . get_the_content() . '" />' .
                   '<meta itemprop="datePublished" content="' . get_the_date('c') . '" />';
        }

        /**
         * DISPLAY METHODS
         * -----------------------------------------------------------
         */
        
        public static function outputMicrodata($location, $type) {
            
            $location = ucfirst(strtolower($location));
            $type     = ucfirst(strtolower($type));
            $function = 'microdata' . $location . $type;
                        
            if(function_exists($function)) {
                echo call_user_func($function);
            } elseif(method_exists('JTCF', $function)) {
                echo call_user_func(array('JTCF', $function));
            }
        }       
        
        // TODO document me
        public static function outputNavigation($configs) {
            // Open nav tag
            self::openSection('nav');
            // Output wordpress nav
            $defaults = array('container' => false);
            call_user_func_array('wp_nav_menu', array($configs));
            // Close nav tag
            self::openSection('nav');
        }
                
        /**
         * Output archive pagination links
         * 
         * Accepts and addition option in the args. 'return'. If 'return' is
         * set and is equal to true, the pagination will not be output and
         * will be returned instead.
         * 
         * @param array $args Array of args available for paginate_links()
         * @since 1.0.0
         * @return string|void Returns void or the string of paginated links.
         */
        public static function outputPagination($args=array()) {
            global $wp_query;           
            $args = array_merge(array(
                'base' => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                'format' => '?paged=%#%',
                'current' => max( 1, get_query_var('paged') ),
                'total' => $wp_query->max_num_pages
            ), $args);
            if(isset($args['return']) && $args['return'] == true) {
                return paginate_links($args);
            } else {
                echo paginate_links($args);
            }
        }
        
        /**
         * Displays post navigation
         * 
         * For single posts, it will show the next and previous post links.
         * FIXME The first arg can't work for both functions
         */
        public static function outputPostNavigation() {      
            if(is_single()) {
                echo '<div class="navigation">';
                echo '	<div class="next-post" rel="next">'. call_user_func_array('next_post_link', func_get_args()) .'</div>';
                echo '	<div class="prev-post" rel="prev">'. call_user_func_array('previous_post_link', func_get_args()) .'</div>';
                echo '</div>';
            }      
        }
        
        // TODO document me
        public static function outputSiteTitle() {
            // Home page should use h1
            if(is_home() || is_front_page()) {
                $wrap = "h1";
                self::$_hasH1 = true;
            } else {
                $wrap = "p";
            }
            // Output title
            echo "<$wrap>" . '<a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" rel="home"';
            
            // Add itemprop if available
            self::_outputMicrodata('header', 'itemprop', 'url');
            
            echo '><span ' . ( of_get_option('design_logo') ? 'class="screen-reader-text" ' : '');
            
            // Add itemprop if available
            self::_outputMicrodata('header', 'itemprop', 'name');
            
            echo '>' . get_bloginfo( 'name' ) . '</span>';
            
            if(of_get_option('design_logo')) {
                echo '<img src="' . of_get_option('design_logo') . '"';
                
                // Add itemprop if available
                self::_outputMicrodata('header', 'itemprop', 'image');
                
                echo ' />';
            }
            
            echo '</a>' . "</$wrap>";
        }
        
        public static function outputTagLine() {
            // If no tagline, output nothing
            if(empty(get_bloginfo('description'))) {
                return;
            }
            // Homepage tagline should be h2
            if(is_home() || is_front_page()) {
                $wrap = "h2";
                self::$_hasH2 = true;
            } else {
                $wrap = "p";
            }
            // Output tagline
            echo '<' . $wrap;
            
            // Add itemprop if available
            self::_outputMicrodata('header', 'itemprop', 'tagline');
                
            // Finish tagline
            echo '>' . get_bloginfo( 'description' ) . "</$wrap>";
        }
        
        // TODO Document me
        public static function outputTheTitle($heading=null) {
            
            // If we are outside of the loop and it's an archive or home/blog page
            if(!in_the_loop() && (is_home() || is_archive())) {
                
                // Hack. Set $post so that the_date() works.
                global $posts;
                $post = $posts[0];

                // If the home blog page
                if (is_home()) {

                // TODO support swapping of theese lang vars
                // If this is a category archive
                } elseif (is_category()) {
                    $title = __('Archive for the', 'justintheclouds') . " &#8216; " . single_cat_title('', false) . " &#8217; " . __('Category', 'justintheclouds');

                // If this is a tag archive
                } elseif( is_tag() ) {
                    $title = __('Posts Tagged', 'justintheclouds') . " &#8216; " . single_tag_title('', false) . " &#8217;";

                // If this is a daily archive
                } elseif (is_day()) {
                    $title = __('Archive for', 'justintheclouds') . ' ' . get_the_time('F jS, Y');

                // If this is a monthly archive
                } elseif (is_month()) {
                    $title = __('Archive for', 'justintheclouds') . ' ' . get_the_time('F, Y');

                // If this is a yearly archive
                } elseif (is_year()) {
                   $title = __('Archive for', 'justintheclouds') . ' ' . get_the_time('Y');

                // If this is an author archive
                } elseif (is_author()) {
                    $title = __('Author Archive for', 'justintheclouds') . ' ' . get_the_author();

                // If this is a paged archive
                } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
                    $title = __('Blog Archives', 'justintheclouds');
                }
                
                $heading = $heading ? $heading : (self::$_hasH1 ? (self::$_hasH2 ? 'h3' : 'h2') : 'h1');
                
                if($heading == 'h1') self::$_hasH1 = true;

                if(isset($title)) echo "<$heading class=\"archive-title\">$title</$heading>";
                
            } elseif(in_the_loop()) {
            
                if(is_single()) {

                    if(self::$_hasH1) return;
                    echo '<h1 class="entry-title">' . get_the_title() . '</h1>';
                    self::$_hasH1 = true;

                } else {

                    $heading = $heading ? $heading : (self::$_hasH1 ? (self::$_hasH2 ? 'h3' : 'h2') : 'h1');
                    echo "<$heading>" . '<a href="' . get_the_permalink() . '">' . get_the_title() . '</a>' . "</$heading>";

                }
                
            }
        }
        

        // FIXME rename this properly and move it?
        public static function footer() {

            // Google Analytics
            if($_SERVER['HTTP_HOST'] != 'localhost' && of_get_option('apis_ga_id') && of_get_option('apis_ga_domain')) {

                echo '<!-- Google analytics-->';
                echo '<script>
          (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');
          ga(\'create\', \'' . of_get_option('apis_ga_id') . '\', \'' . of_get_option('apis_ga_domain') . '\');
          ga(\'send\', \'pageview\');
        </script>
        <!-- Google analytics-->';

            }

            // Load functions.js if it exists
            if(file_exists(get_template_directory_uri() . '/js/functions.js')) {
                echo '<script src="' . get_template_directory_uri() . '/js/functions.js"></script>';
            }
        }

        /**
         * Output all head info for the theme
         * 
         * @since 1.0.0
         */
        public static function outputHead() {

            echo '<head>';

            // Output meta
            self::outputHeadMeta();

            // Output page title
            self::outputHeadTitle();

            // Call default wp_head
            wp_head();

            // Child theme can use wp_head hook to add content below wp_head() content
            echo '</head>';

        }

        /*
         * Outputs all head meta data
         * 
         * @since 1.0.0
         */
        private static function outputHeadMeta() {
            echo '<meta charset="' . get_bloginfo('charset') . '">';
            echo '<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->';
            echo '<!--[if IE ]>';
            echo '<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">';
            echo '<![endif]-->';

            if (is_search()) {
                echo '<meta name="robots" content="noindex, nofollow" />';
            }

            // Page title
            echo '<!-- Page title -->';
            echo '<meta name="title" content="' . JTCF::wp_title( '|', false, 'right' ) . '">';

            // Page description
            echo '<!--Google will often use this as its description of your page/site. Make it good.-->';
            echo '<meta name="description" content="' . JTCF::get_bloginfo('description') . '" />';

            // Copyright information
            echo '<meta name="Copyright" content="Copyright &copy; ' . JTCF::get_bloginfo('name') . ' ' . date('Y') . '. All Rights Reserved.">';

            // Author meta
            if (true == JTCF::of_get_option('meta_author')) {
                echo '<meta name="author" content="' . JTCF::of_get_option("meta_author") . '" />';
            }

            // Google site verifier for google web master tools
            if (true == JTCF::of_get_option('meta_google')) {
                echo '<meta name="google-site-verification" content="' . JTCF::of_get_option("meta_google") . '" />';
            }

            // Viewport
            if (true == JTCF::of_get_option('meta_viewport')) {
                echo '<meta name="viewport" content="' . JTCF::of_get_option("meta_viewport") . '" />';
            }

            // Favicon
            if (true == JTCF::of_get_option('head_favicon')) {
                echo '<meta name=”mobile-web-app-capable” content=”yes”>';
                echo '<link rel="shortcut icon" sizes=”1024x1024” href="' . JTCF::of_get_option("head_favicon") . '" />';
            }

            // IOS Webclip
            if (true == JTCF::of_get_option('head_apple_touch_icon')) {
                echo '<link rel="apple-touch-icon" href="' . JTCF::of_get_option("head_apple_touch_icon") . '">';
            }

            // Application-specific meta tags
            echo '<!-- Application-specific meta tags -->';
            // Windows 8
            if (true == JTCF::of_get_option('meta_app_win_name')) {
                echo '<meta name="application-name" content="' . JTCF::of_get_option("meta_app_win_name") . '" /> ';
                echo '<meta name="msapplication-TileColor" content="' . JTCF::of_get_option("meta_app_win_color") . '" /> ';
                echo '<meta name="msapplication-TileImage" content="' . JTCF::of_get_option("meta_app_win_image") . '" />';
            }

            // Twitter
            if (true == JTCF::of_get_option('meta_app_twt_card')) {
                echo '<meta name="twitter:card" content="' . JTCF::of_get_option("meta_app_twt_card") . '" />';
                echo '<meta name="twitter:site" content="' . JTCF::of_get_option("meta_app_twt_site") . '" />';
                echo '<meta name="twitter:title" content="' . JTCF::of_get_option("meta_app_twt_title") . '">';
                echo '<meta name="twitter:description" content="' . JTCF::of_get_option("meta_app_twt_description") . '" />';
                echo '<meta name="twitter:url" content="' . JTCF::of_get_option("meta_app_twt_url") . '" />';
            }

            // Facebook
            if (true == JTCF::of_get_option('meta_app_fb_title')) {
                echo '<meta property="og:title" content="' . JTCF::of_get_option("meta_app_fb_title") . '" />';
                echo '<meta property="og:description" content="' . JTCF::of_get_option("meta_app_fb_description") . '" />';
                echo '<meta property="og:url" content="' . JTCF::of_get_option("meta_app_fb_url") . '" />';
                echo '<meta property="og:image" content="' . JTCF::of_get_option("meta_app_fb_image") . '" />';
            }

            // Pingback URL
            echo '<link rel="pingback" href="' . JTCF::get_bloginfo('pingback_url') . '" />';
        }

        /**
         * Output page title
         * 
         * @since 1.0.0
         */
        private static function outputHeadTitle() {
            echo '<title>' . JTCF::wp_title( '|', false, 'right' ) . '</title>';
        }

        /**
         * Output HTML5 doctype and HTML opening tag
         * 
         * @since 1.0.0
         */
        private static function outputDoctype() {
            ob_start();
            language_attributes();
            $langAtts = ob_get_clean();
            echo '<!DOCTYPE html>';
            echo '<!--[if lt IE 7 ]> <html class="ie ie6 ie-lt10 ie-lt9 ie-lt8 ie-lt7 no-js" ' . $langAtts . '> <![endif]-->';
            echo '<!--[if IE 7 ]>    <html class="ie ie7 ie-lt10 ie-lt9 ie-lt8 no-js" ' . $langAtts .'> <![endif]-->';
            echo '<!--[if IE 8 ]>    <html class="ie ie8 ie-lt10 ie-lt9 no-js" ' . $langAtts . '> <![endif]-->';
            echo '<!--[if IE 9 ]>    <html class="ie ie9 ie-lt10 no-js" ' . $langAtts . '> <![endif]-->';
            echo '<!--[if gt IE 9]><!--><html class="no-js" ' . $langAtts . '><!--<![endif]-->';
            echo '<!-- the "no-js" class is for Modernizr. -->';       
        }

        /**
         * Magic static caller
         *
         * Check whether the method exists on the JTCF class and calls
         * it if it does exist. Otherwise it will call the method as it's
         * own function.
         *
         * The purpose of this is so we can call all wordpress functions
         * through the JTCF class even if they are not tied into yet. This will
         * allow future extendability without having to rework any child themes.
         *
         * @since 1.0.0
         */
        public static function __callStatic($method, $args) {

            // Check if method exists on JTCF class
            if(!method_exists('JTCF', $method)) {
                return call_user_func_array($method, $args);
            } else {
                return call_user_func_array(array('JTCF', $method), $args);
            }
        }

        /**
         * Logs everytime a method is used.
         *
         * This will be hellpful as methods become deprecated. For internal
         * use, we will be able to see what themes and what sites are still using
         * deprecated methods so we can work on removing them.
         *
         * @since 1.0.0
         */
        private function _logMethodUsage() {

        }

        /**
         * Sends the method usage report to framework developers
         *
         * @since 1.0.0
         */
        private function _sendMethodUsageReport() {

            wp_mail( $this->_contactEmail, "JustinTheClouds Framework Method Usage Report", file_get_contents(dirname( __FILE__ ) . 'style.css'), $headers, $attachments );

        }
    }
}

// Initialize framework
global $JTCF;
$JTCF = JTCF::getInstance(array(
    // Define folder locations
    'folders'      => array(
        'styles'     => 'css',
        'scripts'    => 'js',
        'languages'  => 'languages'
    ),
    // Auto update child
    'updater'    => 'link to hosted version info',
    // Set the text domain of the theme
    // This defaults to justintheclouds, if using this option
    // it's important to use it first since the JTCF automatically applies
    // functions during configuration
    'textdomain' => 'justintheclouds',
    // Register styles, only file name is needed or an array of register args
    // these will be registered and enqueued on wp_enqueue_scripts
    'styles'     => array(
        // Enqueue after registering, 'true' is the default
        // '_enqueue' => true,
        // Or an array of style name that should be enqueued
        '_enqueue' => array('stylename2'),
        array('stylename2', get_stylesheet_directory_uri() . '/somestyles.css', array('stylename1'), '1.2.3', 'all'),
        'stylename1'
    ),
    // Register scripts, only file name is needed or and array of register args
    // these will be registered an enqueued on wp_enqueue_scripts
    'scripts'    => array(
        // Should we enqueue these scripts? Defaults to true; can be array of scripts to enqueue
        '_enqueue' => true,
        array('custom', get_stylesheet_directory_uri() . '/js/functions.js', array('jquery'), '1.0.0', true),
        'jquery'
    ),
    // Short hand widget definitiion
    'widgets'    => array(
        // Define default settings for all widgets
        '_defaults' => array(
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h5 class="widget-title">',
            'after_title'   => '</h5>'
        ),
        'Sidebar',
        'Sidebar Bottom' => array(
            'before_title'  => '<h6 class="widget-title">',
            'after_title'   => '</h6>'
        )
    ),
    // Long hand hook definitions; works for hooks
    'filters'    => array(
        'filtername' => array($filterArgs=array())
    ),
    // Required plugins
    'plugins' => array('enableArchivePageMenu'),
    // Options using options framework
    'options' => array(
        // The first dimension is the tab headings
        // This is so we can easily add options to specific tabs
        'Design' => array(
            // This is associative to allow manipulation upon initialization
            'logo' => array(
                'name' => __('Header Logo', 'justintheclouds'),
                'desc' => __('', 'justintheclouds'),
                'id' => 'design_logo',
                'type' => 'upload'
            )
        ),
        'Contact' => array(
            'address' => array(
                'name' => __('Address', 'justintheclouds'),
                'desc' => __("123 Superman St. #123", 'justintheclouds'),
                'id' => 'contact_address',
                'std' => '',
                'type' => 'text'
            ),
            'city' => array(
                'name' => __('City', 'justintheclouds'),
                'desc' => __("Austin", 'justintheclouds'),
                'id' => 'contact_city',
                'std' => '',
                'type' => 'text'
            ),
            'state' => array(
                'name' => __('State', 'justintheclouds'),
                'desc' => __("TX", 'justintheclouds'),
                'id' => 'contact_state',
                'std' => '',
                'type' => 'text'
            ),
            'zip' => array(
                'name' => __('Zip', 'justintheclouds'),
                'desc' => __("78753", 'justintheclouds'),
                'id' => 'contact_zip',
                'std' => '',
                'type' => 'text'
            ),
            'phone' => array(
                'name' => __('Phone', 'justintheclouds'),
                'desc' => __("(123)123-1234", 'justintheclouds'),
                'id' => 'contact_phone',
                'std' => '',
                'type' => 'text'
            ),
            'fax' => array(
                'name' => __('Fax', 'justintheclouds'),
                'desc' => __("(123)123-1234", 'justintheclouds'),
                'id' => 'contact_fax',
                'std' => '',
                'type' => 'text'
            ),
            'hours' => array(
                'name' => __('Hours', 'justintheclouds'),
                'desc' => __("Monday to Saturday, 10AM to 7PM and Sundays, 12PM to 5PM", 'justintheclouds'),
                'id' => 'contact_hours',
                'std' => '',
                'type' => 'text'
            ),
        ),
        'Social' => array(
            'facebook' => array(
                'name' => __('Facebook Link', 'justintheclouds'),
                'desc' => __("http://www.facebook.com/username", 'justintheclouds'),
                'id' => 'social_facebook',
                'std' => '',
                'type' => 'text'
            ),
            'twitter' => array(
                'name' => __('Twitter Link', 'justintheclouds'),
                'desc' => __("http://www.twitter.com/username", 'justintheclouds'),
                'id' => 'social_twitter',
                'std' => '',
                'type' => 'text'
            ),
            'instagram' => array(
                'name' => __('Instagram Link', 'justintheclouds'),
                'desc' => __("http://www.instagram.com/username", 'justintheclouds'),
                'id' => 'social_instagram',
                'std' => '',
                'type' => 'text'
            ),
            'pinterest' => array(
                'name' => __('Pinterest Link', 'justintheclouds'),
                'desc' => __("http://www.pinterest.com/username", 'justintheclouds'),
                'id' => 'social_pinterest',
                'std' => '',
                'type' => 'text'
            )
        ),
        'Header Meta' => array(
            'google_webmasters' => array(
                'name' => __('Google Webmasters', 'justintheclouds'),
                'desc' => __("Speaking of Google, don't forget to set your site up: <a href='http://google.com/webmasters' target='_blank'>http://google.com/webmasters</a>", 'justintheclouds'),
                'id' => 'meta_google',
                'std' => '',
                'type' => 'text'
            ),
            'author_name' => array(
                'name' => __('Author Name', 'justintheclouds'),
                'desc' => __('Populates meta author tag.', 'justintheclouds'),
                'id' => 'meta_author',
                'std' => '',
                'type' => 'text'
            ),
            'mobile_viewport' => array(
                'name' => __('Mobile Viewport', 'justintheclouds'),
                'desc' => __('Uncomment to use; use thoughtfully!', 'justintheclouds'),
                'id' => 'meta_viewport',
                'std' => 'width=device-width, initial-scale=1.0',
                'type' => 'text'
            ),
            'favicon' => array(
                'name' => __('Site Favicon', 'justintheclouds'),
                'desc' => __('', 'justintheclouds'),
                'id' => 'head_favicon',
                'type' => 'upload'
            ),
            'apple_touch_icon' => array(
                'name' => __('Apple Touch Icon', 'justintheclouds'),
                'desc' => __('', 'justintheclouds'),
                'id' => 'head_apple_touch_icon',
                'type' => 'upload'
            ),
            'windows_8_name' => array(
                'name' => __('App: Windows 8', 'justintheclouds'),
                'desc' => __('Application Name', 'justintheclouds'),
                'id' => 'meta_app_win_name',
                'std' => '',
                'type' => 'text'
            ),
            'windows_8_tile' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __('Tile Color', 'justintheclouds'),
                'id' => 'meta_app_win_color',
                'std' => '',
                'type' => 'color'
            ),
            'windows_8_image' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __('Tile Image', 'justintheclouds'),
                'id' => 'meta_app_win_image',
                'std' => '',
                'type' => 'upload'
            ),
            'twitter_card' => array(
                'name' => __('App: Twitter Card', 'justintheclouds'),
                'desc' => __('twitter:card (summary, photo, gallery, product, app, player)', 'justintheclouds'),
                'id' => 'meta_app_twt_card',
                'std' => '',
                'type' => 'text'
            ),
            'twitter_card_site' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __('twitter:site (@username of website)', 'justintheclouds'),
                'id' => 'meta_app_twt_site',
                'std' => '',
                'type' => 'text'
            ),
            'twitter_card_title' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __("twitter:title (the user's Twitter ID)", 'justintheclouds'),
                'id' => 'meta_app_twt_title',
                'std' => '',
                'type' => 'text'
            ),
            'twitter_card_description' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __('twitter:description (maximum 200 characters)', 'justintheclouds'),
                'id' => 'meta_app_twt_description',
                'std' => '',
                'type' => 'textarea'
            ),
            'twitter_card_url' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __('twitter:url (url for the content)', 'justintheclouds'),
                'id' => 'meta_app_twt_url',
                'std' => '',
                'type' => 'text'
            ),
            'facebook_app' => array(
                'name' => __('App: Facebook', 'justintheclouds'),
                'desc' => __('og:title', 'justintheclouds'),
                'id' => 'meta_app_fb_title',
                'std' => '',
                'type' => 'text'
            ),
            'facebook_app_description' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __('og:description', 'justintheclouds'),
                'id' => 'meta_app_fb_description',
                'std' => '',
                'type' => 'textarea'
            ),
            'facebook_app_url' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __('og:url', 'justintheclouds'),
                'id' => 'meta_app_fb_url',
                'std' => '',
                'type' => 'text'
            ),
            'facebook_app_image' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __('og:image', 'justintheclouds'),
                'id' => 'meta_app_fb_image',
                'std' => '',
                'type' => 'upload'
            )
        ),
        'API Settings' => array(
            'google_analytics_id' => array(
                'name' => __('Google Analytics ID', 'justintheclouds'),
                'desc' => __('UA-XXXXXXX-XX', 'justintheclouds'),
                'id' => 'apis_ga_id',
                'std' => '',
                'type' => 'text'
            ),
            'google_analytics_domain' => array(
                'name' => __('Google Analytics Domain', 'justintheclouds'),
                'desc' => __('domain.com', 'justintheclouds'),
                'id' => 'apis_ga_domain',
                'std' => '',
                'type' => 'text'
            )
        ),
        'Microdata' => array(
            'header_scope_type' => array(
                'options' => array('Person' => 'Person', 'Organization' => 'Organization'),
                'desc' => 'Is this website for company(organization) or for personal(person) use?',
                'type' => 'select'
            ),
            'disable' => array(
                'type' => 'checkbox',
                'desc' => '(Not recommended) this will remove all microdata from your website which is intended to help with search engine rankings'
            )
        )
    )
));

// REMOVE
add_filter('JTCF_outputMicrodata', 'tester', 10, 4);
function tester($data, $location, $type, $property) {
    switch($location) {
        case 'body':
            //$data = 'test';
        break;
    }
    return $data;
}


?>