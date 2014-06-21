<?php

    //Initialize the update checker.
    require_once dirname( __FILE__ ) . '/theme-updates/theme-update-checker.php';
    $update_checker = new ThemeUpdateChecker(
        'Starter',
        'https://raw.githubusercontent.com/JustinTheClouds/Wordpress-Starter-Theme/master/version-info.json'
    );

/**
 * JustinTheClouds Framework
 * 
 * This class holds all the functionality for the wordpress
 * parent framework. It's goal is to be simple to use and learn
 * while streamlining child theme development.
 * 
 * @author Justin Carlson
 * @since 1.0.0
 */
class JTCF {
    
    /**
     * Framework developer contact email
     *
     * @since 1.0.0
     */
    private $_contactEmail = "justin@justintheclouds.com";
    
    public function __construct() {
        
        // Define all hooks
        add_action('wp_head', array('JTCF', 'hookWPHead'));
    }
    
    /**
     * Output all head info for the theme
     * 
     * @since 1.0.0
     */
    public static function hookWPHead() {
        
        // Output meta
        self::outputHeadMeta();
        
    }
    
    private static function outputHeadMeta() {
        echo '<meta charset="' . get_bloginfo('charset') . '">';
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
            call_user_func_array($method, $args);
        } else {
            call_user_func_array(array(self, $method), $args);
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
global $JTCF;
$JTCF = new JTCF();

?>