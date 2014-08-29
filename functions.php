<?php

// TODO pass theme check
// TODO finish comments.php markup
// TODO add author header meta image/name, link to microdata meta
// TODO move framework default options to default and otheres to starter theme
// TODO move to constructor
// TODO apply text domain functions to all methods, create JTCF::__/_e for quicker use
global $JTCFDefaults;
$JTCFDefaults = array(
    // Auto update child
    // This should be the link to the version info file
    'updater'    => null,
    'cleanMode' => false,
    // REMOVE this is not needed since it is no dynamic. Make calls to this static
    'textdomain'     => 'justintheclouds',
    // This language will default to using the frameworks own text domain
    // Since use of variables in language function is incorrect, we handle
    // customization by allowing the user to pass in their own ALREADY lang converted text
    // and ignore the use of the default framework text domain
    // TODO Verify this works and is proper, ask http://ottopress.com/2012/internationalization-youre-probably-doing-it-wrong/ ?
    // TODO pull all lang vars to here
    'language' => array(
        // Articles
        'author_link_title' => __('More articles written by %s', 'justintheclouds'),
        'entry_missing_excerpt_and_content' => '<b>' . __('The post "%s" seems to have no content or excerpt written yet!', 'justintheclouds') . '</b>',
        'entry_edit_post_link_title' => __('Edit the %s %s', 'justintheclouds'),
        'posted_in' => __('Posted in:', 'justintheclouds'),
        // Comments
        'comments_title' => function() {
            return _n( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'justintheclouds');
        },
        'comments_navigation_next' => __( 'Newer Comments &rarr;', 'justintheclouds' ),
        'comments_navigation_previous' => __( '&larr; Olders Comments', 'justintheclouds'),
        'comments_navigation' => __('Comment navigation', 'justintheclouds'),
        'comments_closed' => __('Comments are closed.', 'justintheclouds'),
        'comments_empty' => __('No Comments yet, your thoughts are welcome!', 'justintheclouds'),
        // Titles
        'titles_category' => __('Archive for the &#8216; %s &#8217; category', 'justintheclouds'),
        // Admin Options Page
        'Design' => __('Design', 'justintheclouds'),
        'Contact' => __('Contact', 'justintheclouds'),
        'Header Meta' => __('Header Meta', 'justintheclouds'),
        'Social' => __('Social', 'justintheclouds'),
        'API Settings' => __('API Settings', 'justintheclouds'),
        'Microdata' => __('Microdata', 'justintheclouds'),
        // Debug Bar
        'debug_log_admin_bar_title' => 'Debugger'
    ),
    'folders'        => array(
        'styles'       => 'css',
        'scripts'      => 'js',
        'languages'    => get_template_directory() . '/languages'
    ),
    // Register styles, only file name is needed or an array of register args
    // these will be registered and enqueued on wp_enqueue_scripts
    'styles'     => array(
        // Enqueue after registering, 'true' is the default
        '_enqueue' => true,
        // TODO _autoDepend; This will cause styles enqueued to depend upon the previous enqueued style
        // _autoDepend = true,
        // Or an array of style name that should be enqueued
        // '_enqueue' => array('stylename2'),
        'base' => array('base', get_template_directory_uri() . '/css/base.css'),
        'wp-styles' => array('wp-styles', get_template_directory_uri() . '/css/wp-styles.css'),
        // Auto enquueu themes main style sheet
        'style' => array('style', get_stylesheet_directory_uri() . '/style.css', array('base', 'wp-styles')),
        // Example using all args, works just as wp_enqueue_style()
        // array('style', get_stylesheet_directory_uri() . '/style.css', array('stylename1'), '1.2.3', 'all'),
        // 'stylename1'
    ),
    'scripts'         => array(
        '_enqueue'     => true
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
        // Shorthand widget definition, will use the default defined above
        'Sidebar',
        // Or define the defaults you'd like to change on a per widget basis
        // 'Sidebar Bottom' => array(
        //     'before_title'  => '<h6 class="widget-title">',
        //     'after_title'   => '</h6>'
        // )
    ),
    'hooks' => array(
        /** 
         * Keys are added to these filters only to avoid them being overwritten
         * If you'd like to overwrite/remove an applied filter, just pass in
         * false/your function using the key of the filter to remove.
         */
        // These are core framework hooks and should not be removed
        // unless you know what you are doing.
        'core_after_setup_theme' => array(
            'after_setup_theme',
            array('JTCF', 'hookAfterThemeSetup')
        ),
        'core_wp_enqueue_scripts' => array(
            'wp_enqueue_scripts',
            array('JTCF', 'hookWPEnqueueScripts')
        ),
        'core_widgets_init' => array(
            'widgets_init',
            array('JTCF', 'hookWidgetsInit')
        ),
        'add_analytics_to_footer' => array(
            'wp_footer',
            function() {
                // Google Analytics
                if($_SERVER['HTTP_HOST'] != 'localhost' && of_get_option('apisettings-google-analytics-id') && of_get_option('apisettings-google-analytics-domain')) {
                    echo '<!-- Google analytics-->';
                    echo "<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '" . of_get_option('apisettings-google-analytics-id') . "', 'auto');
  ga('send', 'pageview');

</script>
            <!-- Google analytics-->";
                }
            }
        ),
        // We use this hook to define the doctype
        'define_doctype' => array(
            'JTCF_beforeOpenSectionHtml',
            array('JTCF', 'outputDoctype')
        ),
        // Add head meta content
        'add_head_meta' => array(
            'JTCF_afterOpenSectionHead',
            array('JTCF', 'outputHead')
        ),
        // Add content after main content opening section
        'add_archive_heading_after_open_main' => array(
            'JTCF_afterOpenSectionMain',
            function() {
                // This will output the archive title if on an archive page
                if(is_archive()) {
                    
                    // Hack. Set $post so that the_date() works.
                    global $posts;
                    $post = $posts[0];

                    // If the home blog page
                    if (is_home()) {

                    // TODO support swapping of theese lang vars
                    // If this is a category archive
                    } elseif (is_category()) {
                        $title = JTCF::__('titles_category', single_cat_title('', false));

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

                    $heading = JTCF::$_hasH1 ? (JTCF::$_hasH2 ? 'h3' : 'h2') : 'h1';

                    if($heading == 'h1') JTCF::$_hasH1 = true;

                    if(isset($title)) echo "<$heading>$title</$heading>";
                    
                }
            }
        ),
        'output_wp_nav_menu' => array(
            'JTCF_afterOpenSectionNav',
            function() {
                // Get location
                $location = substr(JTCF::$currentLocation, 0, strrpos(JTCF::$currentLocation, "/"));
                $location = substr(strrchr($location, "/"), 1);
                // If this nav location exists
                if($location && array_key_exists($location, JTCF::get('menus'))) {
                    $defaults = array('container' => false, 'theme_location' => $location);
                    wp_nav_menu($defaults);
                }
            }
        ),
        'add_post_nav_before_main_close' => array(
            'JTCF_beforeCloseSectionMain',
            function() {
                if(is_single()) {
                    echo '<ul>';
                    if(get_next_post_link()) echo '	<li rel="next">'. call_user_func('get_next_post_link') .'</li>';
                    if(get_previous_post_link()) echo '	<li rel="prev">'. call_user_func('get_previous_post_link') .'</li>';
                    echo '</ul>';
                }      
            }
        ),
        'add_pagination_before_main_close' => array(
            'JTCF_beforeCloseSectionMain',
            function() {
                global $wp_query;           
                $args = array(
                    'base' => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                    'format' => '?paged=%#%',
                    'current' => max( 1, get_query_var('paged') ),
                    'total' => $wp_query->max_num_pages
                );
                echo '<div' . JTCF::getClass('archive-pagination') . '>';
                echo paginate_links($args);
                echo '</div>';
            }
        ),
        'call_wp_footer_before_body_close' => array(
            'JTCF_beforeCloseSectionBody',
            function() {
                wp_footer();
            }
        ),
        'add_ajaxurl_variable' => array(
            'wp_head',
            function() {
                echo '<script type="text/javascript">if(typeof ajaxurl == "undefined") var ajaxurl = "' . admin_url('admin-ajax.php') . '";</script>';
            }
        )
    ),
    'filters' => array(
        /** 
         * Keys are added to these filters only to avoid them being overwritten
         * If you'd like to overwrite/remove an applied filter, just pass in
         * false/your function using the key of the filter to remove.
         */
        // Format document title
        'format_wp_title' => array(
            'wp_title',
            function($title, $sep) {
                global $paged, $page;

                if ( is_feed() ) {
                    return $title;
                }

                // Add the site name.
                $title .= ' ' . get_bloginfo( 'name', 'display' );

                // Add the site description for the home/front page.
                $site_description = get_bloginfo( 'description', 'display' );
                if ( $site_description && ( is_home() || is_front_page() ) ) {
                    $title = "$title $sep $site_description";
                }

                // Add a page number if necessary.
                if ( $paged >= 2 || $page >= 2 ) {
                    $title = "$title $sep " . sprintf( __( 'Page %s', 'justintheclouds' ), max( $paged, $page ) );
                }

                return $title;
            },
            10,
            2
        ),
        // Format bloginfo data returned based on location
        'format_bloginfo' => array(
            'bloginfo',
            function($value, $show) {
                
                // Alter how the site title is output in the header
                if(JTCF::$currentLocation != '/html/body/header') return $value;
                
                if($show == 'name') {
                
                    // Home page should use h1
                    if(is_home() || is_front_page() || is_404()) {
                        $wrap = "h1";
                        JTCF::$_hasH1 = true;
                    } else {
                        $wrap = "p";
                    }
                    // Output title
                    $output = "<$wrap>" . '<a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( $value ) . '" rel="home"';

                    // Add itemprop if available
                    $output .= JTCF::getMicrodata('header', 'itemprop', 'url');

                    $output .= '><span' . ( of_get_option('design-logo') ? ' class="screen-reader-text" ' : '');

                    // Add itemprop if available
                    $output .= JTCF::getMicrodata('header', 'itemprop', 'name');

                    $output .=  '>' . get_bloginfo( 'name' ) . '</span>';

                    if(of_get_option('design-logo')) {
                        $output .= '<img src="' . of_get_option('design-logo') . '"';

                        // Add itemprop if available
                        $output .= JTCF::getMicrodata('header', 'itemprop', 'image');

                        $output .= ' title="' . $value  . '" />';
                    }

                    $output .= '</a>' . "</$wrap>";
                    
                } elseif($show == 'description') {
                    
                    // If no tagline, output nothing
                    if(empty($value)) {
                        return;
                    }
                    // Homepage tagline should be h2
                    if(is_home() || is_front_page() || is_404()) {
                        $wrap = "h2";
                        JTCF::$_hasH2 = true;
                    } else {
                        $wrap = "p";
                    }
                    // Output tagline
                    $output = '<' . $wrap;

                    // Add itemprop if available
                    $output .= JTCF::getMicrodata('header', 'itemprop', 'tagline');

                    // Finish tagline
                    $output .= '>' . get_bloginfo( 'description' ) . "</$wrap>";

                }
                
                return $output;

            },
            10,
            2
        ),
        // Intercept wp_nav_menu calls
        'route_wp_nav_menu_to_section' => array(
            'pre_wp_nav_menu',
            function($null, $args) {
                // If we aren't already in a nav location
                if(strpos(JTCF::$currentLocation, '/nav') === false) {
                    // Route through nav section
                    JTCF::section('nav', array(), $args);
                    return '';
                }
            },
            10,
            2
        ),
        // Wrap navs in nav tags
        'format_wp_nav_menu' => array(
            'wp_nav_menu',
            function($menu) {
                // If we are already in a nav, we don't need to warp in nav tags
                if(strpos(JTCF::$currentLocation, '/nav') === false) {
                    $menu = '<nav role="navigation"' . JTCF::getMicrodata('nav', 'scope') . '>' . $menu . '</nav>';
                }
                return $menu;
            }
        ),
        // Add heading tags around the title and links the title if not on single page
        'format_the_title' => array(
            'the_title',
            function($title) {

                // Skip filters on admin
                if(is_admin()) return $title;
                
                if(in_the_loop() && strpos(JTCF::$currentLocation, '/article/header') !== false) {

                    if(is_single() || is_page()) {

                        if(JTCF::$_hasH1) return;
                        JTCF::$_hasH1 = true;
                        return '<h1' . JTCF::getClass('entry-title') . '>' . $title . '</h1>';

                    } else {

                        $heading = JTCF::$_hasH1 ? (JTCF::$_hasH2 ? 'h3' : 'h2') : 'h1';
                        return "<$heading" . JTCF::getClass('entry-title') . ">" . '<a href="' . get_the_permalink() . '">' . $title . '</a>' . "</$heading>";

                    }

                }
                
                return $title;
            }
        ),
        // Convert the content to execerpts automatically if excerpts dont exist
        'format_the_content' => array(
            'the_content',
            function($html) {
                global $post;
                if(is_home() || is_archive()) {
                    if(!$post->post_excerpt) {
                        preg_match("/<p>(.*)<\/p>/", $html, $matches);
                        if(isset($matches[1])) {
                            $html = '<p' . JTCF::getClass('entry-excerpt') . '>' . strip_tags($matches[1]) . '</p>';
                        } else {
                            $html = '<p' . JTCF::getClass('entry-empty-excerpt') . '>' . JTCF::__('entry_missing_excerpt_and_content', $post->post_title) . '</p>';
                        }
                    } else {
                        return $post->post_excerpt;
                    }
                } elseif(is_single() || is_page()) {
                    if(!empty($post->post_content)) {
                        $html = '<div' . JTCF::getClass('entry-content') . JTCF::getMicrodata('article', 'property', 'articleContent') . '>' . $html . '</div>';
                    } else {
                        $html = '<p' . JTCF::getClass('entry-empty-content') . '>' . JTCF::__('entry_missing_excerpt_and_content', $post->post_title) . '</p>';
                    }
                }
                return $html;
            }
        ),
        // Format the post data and add microdata
        'format_the_date' => array(
            'the_date',
            function($date) {
                return '<time datetime="' . get_the_date('c') . '" title="' . $date . ' at ' . get_the_time() .'">'. $date . '</time>';
            }
        ),
        // Format the post author and add microdata
        'format_the_post_author' => array(
            'the_author',
            function($author) {
                return JTCF::getOpenSection('author') . '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" rel="author" title="' . JTCF::__('author_link_title', $author) . '"' . JTCF::getMicrodata('author', 'property', 'url') . '><span' . JTCF::getMicrodata('author', 'property', 'name') . '>'. $author .'</span></a>' . JTCF::getCloseSection('author');
            }
        ),
        // Add links to all thumbnails and add microdata
        'format_the_post_thumbnail' => array(
            'post_thumbnail_html',
            function($html, $postId) {
                // Only add microdata if in loop
                if(in_the_loop()) {
                    $html = str_replace('/>', JTCF::getMicrodata('article', 'itemprop', 'featuredImage') . ' />', $html);
                }
                if(has_post_thumbnail() && !is_single()) {
                    $html = '<a href="' . get_permalink($postId) . '">' . $html . '</a>';
                }
                // Wrap image if not in clean mode
                if(has_post_thumbnail() && JTCF::getClass('entry-featured-image-wrap')) {
                    $html = str_replace('class="', 'class="' . JTCF::getClass('entry-featured-image', false) . ' ', $html);
                    $html = '<div' . JTCF::getClass('entry-featured-image-wrap') . '>'. $html . '</div>';
                }
                return $html;
            }, 10, 2
        ),
        // Add microdata to category links
        'format_the_category' => array(
            'the_category',
            function($html) {
                if(is_page()) return;
                $html = "<span" . JTCF::getClass('entry-meta-categories') . ">" . JTCF::__('posted_in') . ' ' . str_replace(">", JTCF::getMicrodata('article', 'property', 'articleSection') . ">", $html) . "</span>";
                return $html;
            }
        ),
        'format_the_tags' => array(
            'the_tags' ,
            function($html) {
                if(is_page()) return;
                $html = "<span" . JTCF::getClass('entry-meta-tags') . ">" . str_replace(">", JTCF::getMicrodata('article', 'property', 'articleSection') . ">", $html) . "</span>";
                return $html;
            }
        ),
        'format_post_edit_link' => array(
            'edit_post_link' ,
            function($html) {
                $html = "<span" . JTCF::getClass('entry-meta-post-edit-link') . ">" . str_replace(">", JTCF::getMicrodata('article', 'property', 'articleSection') . ' title="' . JTCF::__('entry_edit_post_link_title', get_post_type(), get_the_title()) . '">', $html) . "</span>";
                return $html;
            }
        ),
        'format_comments_popup_link' => array(
            'comments_popup_link' ,
            function($html) {
                $html = "<span" . JTCF::getClass('entry-meta-post-edit-link') . ">" . str_replace(">", JTCF::getMicrodata('article', 'property', 'articleSection') . ' title="' . JTCF::__('entry_edit_post_link_title', get_post_type(), get_the_title()) . '">', $html) . "</span>";
                return $html;
            }
        ),
        'format_comment_reply_link' => array(
            'comment_reply_link',
            function($link) {
                $link = str_replace(">", JTCF::getMicrodata('comments', 'property', 'replyToUrl') . ">", $link);
                return $link;
            }
        ),
        'format_custom_locations' => array(
            'JTCF_sectionLocation',
            function($location) {
                switch($location) {
                    case 'author':
                        return 'span';
                    break;
                    case 'comments':
                        return 'section';
                    break;
                }
                return $location;
            }
        ),
        'exhaust_banner_role' => array(
            'JTCF_outputAttributes',
            function($atts) {
                if(isset($atts['role']) && $atts['role'] == 'banner') {
                    JTCF::$_hasBanner = true;
                }
                return $atts;
            }
        ),
        'body_attributes' => array(
            'JTCF_outputAttributesBody',
            function($atts) {
                $atts['class'] = implode(' ', get_body_class(isset($atts['class']) ? $atts['class'] : ''));
                return $atts;
            }
        ),
        'header_attributes' => array(
            'JTCF_outputAttributesHeader',
            function($atts) {
                // Add banner role, if no role is defined and banner role has not been used
                if(!isset($atts['role']) && !JTCF::$_hasBanner) {
                    $atts['role'] = "banner";
                    JTCF::$_hasBanner = true;
                }
                return $atts;
            }
        ),
        'nav_attributes' => array(
            'JTCF_outputAttributesNav',
            function($atts) {
                if(!isset($atts['role'])) $atts['role'] = 'navigation';
                return $atts;
            }
        ),
        'main_attributes' => array(
            'JTCF_outputAttributesMain',
            function($atts) {
                // Add main role, if no role is defined and main role has not been used
                if(!isset($atts['role']) && !JTCF::$_hasMain) {
                    $atts['role'] = "main";
                    JTCF::$_hasMain = true;
                }
                return $atts;
            }
        ),
        'article_attributes' => array(
            'JTCF_outputAttributesArticle',
            function($atts) {
                // Apply role
                if(!isset($atts['role'])) $atts['role'] = 'article';
                $atts['class'] = implode(' ', get_post_class(isset($atts['class']) ? $atts['class'] : ''));
                return $atts;
            }
        ),
        'aside_attributes' => array(
            'JTCF_outputAttributesAside',
            function($atts) {
                // Apply role
                if(!isset($atts['role'])) $atts['role'] = 'complementary';
                return $atts;
            }
        ),
        'footer_attributes' => array(
            'JTCF_outputAttributesFooter',
            function($atts) {
                // Apply role
                if(!isset($atts['role'])) $atts['role'] = 'contentinfo';
                return $atts;
            }
        ),
        'add_custom_social_options' => array(
            'JTCF_runConfiguration',
            function($configs, $type) {
                switch($type) {
                    case 'options':
                    if(of_get_option('social-additional-networks', false)) {
                        $networks = explode(',', of_get_option('social-additional-networks'));
                        foreach($networks as $network) {
                            $network = trim($network);
                            $configs['Social'][strtolower($network)] = 'text';
                        }
                    }
                    break;
                }
                return $configs;
            }, 10, 2
        ),
        'enable_shortcodes_in_widgets' => array(
            'widget_text', 
            'do_shortcode'
        )
    ),
    // TODO microdata and document
    'shortcodes' => array(
        'output_bloginfo' => array(
            'bloginfo',
            function($atts) {
                if(isset($atts['option'])) {
                    return get_bloginfo($atts['option']);
                }
            }
        ),
        'output_address_stamp' => array(
            'address',
            function() {
                return '<p' . JTCF::getClass('contact-address') . '>' . of_get_option('contact-address') . '<br />' . of_get_option('contact-city') . ', ' . of_get_option('contact-state') . ' ' . of_get_option('contact-zip') . '</p>';
            }
        )
    ),
    'menus' => array(
        'header'   => __( 'Top primary menu', 'justintheclouds' ),
        'footer' => __( 'Footer menu if different from primary', 'justintheclouds' ),
    ),
    // Manageble on intialization, or through the JTCF_runConfiguration filter,
    // or through the use of the JTCF_outputMicrodata filter
    'microdata' => array(
        
        'body' => array(
            'itemtype' => 'http://schema.org/WebPage',
            'properties' => array(
                'primaryImageOfPage' => function() {
                    // Homepage displays logo if have one, fallback to first featured image
                    if(is_home()) {
                        if(of_get_option('design-logo')) {
                            return of_get_option('design-logo');
                        } else {
                            global $post;
                        }
                    }
                },
                'about' => function() {
                    return get_bloginfo('description');
                },
                'author' => function() {
                    return of_get_option('microdata-main-author-of-webpage');
                }
            )
        ),
        'header' => array(
            'itemtype' => function() {
                // This should not apply to article headers
                if(strpos(JTCF::$currentLocation, '/article') === false) {
                    return 'http://schema.org/' . of_get_option('microdata-header-scope-type', 'Organization');
                }
            },
            'properties' => array(
                
            )
        ),
        'main' => array(
            'itemtype' => function() {
                if(is_home() || is_archive() || is_single()) {
                    return 'http://schema.org/Blog';
                }
                if(is_page()) {
                    return 'http://schema.org/CreativeWork';
                }
            },
            'itemprop' => 'mainContentOfPage',
            'properties' => array(
                
            )
        ),
        'article' => array(
            'itemprop' => 'blogPost',
            'itemtype' => 'http://schema.org/BlogPosting',
            'properties' => array(
                'articleContent',
                'articleSection'
            )
        ),
        'author' => array(
            'itemtype' => 'http://schema.org/Person',
            'itemprop' => 'author',
            'properties' => array(
                'name',
                'url'
            )
        ),
        'comments' => array(
            'itemtype' => 'http://schema.org/UserComments',
            'itemprop' => 'comment',
            'properties' => array(
                'replyToUrl'
            )
        ),
        'comment' => array(
            'itemtype' => 'http://schema.org/UserComments',
            'itemprop' => 'comment',
            'properties' => array(
                'replyToUrl'
            )
        )
        /*
        'headerItempropName' => "name",
        'headerItempropUrl' => "url",
        'headerItempropImage' => "image",
        'headerItempropTagline' => "description",
        'headerMeta' => array('about'),
        'navScope' => 'http://schema.org/SiteNavigationElement',

        'mainItemprop' => 'mainContentOfPage',
        'mainItempropAbout' => "",
        'articleScope' => function() {
            if(is_home() || is_archive() || is_single()) {
                return 'http://schema.org/BlogPosting';
            }
            if(is_page()) {
                return 'http://schema.org/CreativeWork';
            }
        },
        'articleItempropFeaturedImage' => 'image',
        'asideScope' => "http://schema.org/WPSidebar",
        'footerScope' => "http://schema.org/WPFooter",
        // Adding custom microdata for the custom 'theTitle' section
        // Add the scope for the section
        'theTitleScope' => "http://schema.org/WPFooter",
        // Add if the section should have an itemprop applied
        'theTitleItemprop' => 'postTitle'
        */
    ),
    // Required plugins
    'plugins' => array('enableArchivePageMenu'),
    // Options using options framework
    // TODO make sure new ids match all of_get_option calls
    'options' => array(
        // The first dimension is the tab headings
        // This is so we can easily add options to specific tabs
        'Design' => array(
            // This is associative to allow manipulation upon initialization
            'logo' => 'upload'
        ),
        'Contact' => array(
            'address' => array(
                'desc' => __("123 Superman St. #123", 'justintheclouds')
            ),
            'city' => array(
                'desc' => __("Austin", 'justintheclouds')
            ),
            'state' => array(
                'desc' => __("TX", 'justintheclouds')
            ),
            'zip' => array(
                'desc' => __("78753", 'justintheclouds')
            ),
            'phone' => array(
                'desc' => __("(123)123-1234", 'justintheclouds')
            ),
            'fax' => array(
                'desc' => __("(123)123-1234", 'justintheclouds')
            ),
            'hours' => array(
                'desc' => __("Monday to Saturday, 10AM to 7PM and Sundays, 12PM to 5PM", 'justintheclouds')
            ),
        ),
        'Social' => array(
            'additional_networks' => array(
                'desc' => __('A comma separated list of additional networks you\'d like available. For ex. youtube, flickr, linkedin', 'justintheclouds')
            ),
            'facebook' => array(
                'desc' => __("http://www.facebook.com/username", 'justintheclouds')
            ),
            'twitter' => array(
                'desc' => __("http://www.twitter.com/username", 'justintheclouds')
            ),
            'google' => array(
                'desc' => __("https://plus.google.com/u/0/YOUR_ID", 'justintheclouds')
            ),
            'instagram' => array(
                'desc' => __("http://www.instagram.com/username", 'justintheclouds')
            ),
            'pinterest' => array(
                'desc' => __("http://www.pinterest.com/username", 'justintheclouds')
            )
        ),
        'Header Meta' => array(
            // TODO test this
            'google_webmasters' => array(
                'desc' => __("Speaking of Google, don't forget to set your site up: <a href='http://google.com/webmasters' target='_blank'>http://google.com/webmasters</a>", 'justintheclouds')
            ),
            'author_name' => array(
                'desc' => __('Populates meta author tag. (If your google+ account is added on the Social tab, the google+ account link will be used instead since it play a large role on how how search results are displayed.', 'justintheclouds')
            ),
            'mobile_viewport' => array(
                'desc' => __('Uncomment to use; use thoughtfully!', 'justintheclouds'),
                'std' => 'width=device-width, initial-scale=1.0'
            ),
            'favicon' => 'upload',
            'apple_touch_icon' => 'upload',
            'windows_8' => array(
                'name' => __('App: Windows 8', 'justintheclouds'),
                'desc' => __('Application Name', 'justintheclouds')
            ),
            'windows_8_tile' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __('Tile Color', 'justintheclouds'),
                'type' => 'color'
            ),
            'windows_8_image' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __('Tile Image', 'justintheclouds'),
                'type' => 'upload'
            ),
            'twitter_card' => array(
                'name' => __('App: Twitter Card', 'justintheclouds'),
                'desc' => __('twitter:card (summary, photo, gallery, product, app, player)', 'justintheclouds')
            ),
            'twitter_card_site' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __('twitter:site (@username of website)', 'justintheclouds')
            ),
            'twitter_card_title' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __("twitter:title (the user's Twitter ID)", 'justintheclouds')
            ),
            'twitter_card_description' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __('twitter:description (maximum 200 characters)', 'justintheclouds'),
                'type' => 'textarea'
            ),
            'twitter_card_url' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __('twitter:url (url for the content)', 'justintheclouds')
            ),
            'facebook_app' => array(
                'name' => __('App: Facebook', 'justintheclouds'),
                'desc' => __('og:title', 'justintheclouds')
            ),
            'facebook_app_description' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __('og:description', 'justintheclouds'),
                'type' => 'textarea'
            ),
            'facebook_app_url' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __('og:url', 'justintheclouds')
            ),
            'facebook_app_image' => array(
                'name' => __('', 'justintheclouds'),
                'desc' => __('og:image', 'justintheclouds'),
                'type' => 'upload'
            )
        ),
        'API Settings' => array(
            'google_analytics_id' => array(
                'desc' => __('UA-XXXXXXX-XX', 'justintheclouds')
            ),
            'google_analytics_domain' => array(
                'desc' => __('domain.com', 'justintheclouds')
            )
        ),
        'Microdata' => array(
            'header_scope_type' => array(
                'options' => array('Person' => 'Person', 'Organization' => 'Organization'),
                'desc' => 'Is this website for company(organization) or for personal(person) use?',
                'type' => 'select'
            ),
            'main_author_of_webpage' => function() {
                $options = array();
                $users = get_users();
                foreach($users as $user) {
                    $options[$user->display_name] = $user->display_name;
                }
                return array(
                    'type' => 'select',
                    'options' => $options,
                    'std' => $users[0]->display_name
                );
            },
            'disable' => array(
                'type' => 'checkbox',
                'desc' => '(Not recommended) this will remove all microdata from your website which is intended to help with search engine rankings'
            )
        )
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
     * @Future Clean/Markup mode, Clean adds no ids or classes but default body/post classes, markup will add classes/ids
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
         * Debug logs
         * 
         * Store all debug logs for output
         * 
         * @since 1.0.0
         */
        private static $_debugs = array();
        
        /**
         * Holds the theme details so we don't have to keep calling wp_get_theme
         */
        public static $theme;
        
        /**
         * If the theme has a h1 used already
         * 
         * This should only occur on the homepage used for the site name.
         */
        public static $_hasH1 = false;
        
        // TODO doc me
        public static $currentLocation = '';
        
        /**
         * If the theme has a h2 for the tagline
         * 
         * This should only occur on the homepage used for the site name.
         */
        public static $_hasH2 = false;
        
        /**
         * If the theme has a banner role
         * 
         * This should occur once, and usually in the main header at the top
         * 
         * @since 1.0.0
         */
        public static $_hasBanner = false;
        
        /**
         * If the theme has a main role
         * 
         * This should occur once
         * 
         * @since 1.0.0
         */
        public static $_hasMain = false;
        
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

        // TODO document me once finished/, try to remove uneeded code from here
        public function __construct($configs) {
          
            // Grab the theme
            self::$theme = wp_get_theme();
            
            // Merge configs with default configs
            global $JTCFDefaults;
            self::$_configs = $configs ? self::arrayMergeRecursiveDistinct($JTCFDefaults, $configs) : $JTCFDefaults;
            
            // Log configs
            self::_debug(array(
                'Defaults' => $JTCFDefaults,
                'Child Configs' => $configs,
                'Initialized With' => self::$_configs
            ), 'Initialization configs', 'debugs');
            
            // Setup options framework
            // Options Framework (https://github.com/devinsays/options-framework-plugin)
            define('OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/includes/options-framework/');
            require_once get_template_directory() . '/includes/options-framework/options-framework.php';
            
            add_shortcode('jtcf_social_icons', array('JTCF', 'socialIcons'));
            
            // Intialize framework on after_theme_stup hook
            //add_action('after_setup_theme', array('JTCF', '_runConfiguration'));
            self::_runConfiguration();
            //add_action('after_setup_theme', array('JTCF', 'hookAfterThemeSetup'));
            
            // Core framework non overwritable hooks
            if ( is_super_admin() && is_admin_bar_showing() ) {
                add_action('admin_bar_menu', array('JTCF', 'hookAdminBarMenu'), 201);
            } else {
                add_action('shutdown', array('JTCF', 'hookAdminBarMenu'), 201);
            }
                
            // Initialize the update checker.
            // TODO update public link to correct repo
            require_once dirname( __FILE__ ) . '/includes/theme-updates/theme-update-checker.php';
            $update_checker = new ThemeUpdateChecker(
                'JustinTheCloudsFramework',
                'https://raw.githubusercontent.com/JustinTheClouds/JustinTheCloudsFramework/master/version-info.json'
            );
            
        }
        
        
        public static function get($name) {
            if(isset(self::$_configs[$name])) return self::$_configs[$name];
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
        public static function _runConfiguration() {
            // Loop through each config and process it
            foreach(self::$_configs as $key => &$value) {
                // Applies a filter to the configs AFTER child theme
                // initalization. This allows plugin to tie into the themes configs
                $value = apply_filters('JTCF_runConfiguration', $value, $key);
                // Get method name
                $method = '_run' . ucfirst($key) . 'Setup';
                if(method_exists('JTCF', $method)) {
                    call_user_func(array('JTCF', $method));
                }
            }
        }
        
        /**
         * Sets up the options framework
         * 
         * This allows shorthand configuration of options.
         * Options can still be added using an options.php file
         * or by using the of_options filter.
         * 
         * @since 1.0.0
         * 
         * The shorthand example are to be defined upon intialization in the
         * options setting.
         * 
         * The first dimension of the options array is the options tabs
         * options => array(
         *     'General' => array(
         *         // options for the general tab
         *     )
         * )
         * 
         * Shorthand examples
         * options => array(
         *     // The general tab, all option define in here will be on this tab
         *     'General' => array(
         *         // This will create a checkbox with the name Enable microdata
         *         'enable_microdata' => 'checkbox',
         *         // Or you can pass an array with other option settings
         *         // This will automatically default the name to Select your favorite color
         *         // but allow you to overwrite the other default settings
         *         'select_your_favorite_color' => array(
         *             'desc' => 'Select your preferred favorite color',
         *             'options' => array('Red', 'Blue', 'Green')
         *         ),
         *         // And of course you can define the option with all settings as normal
         *         'google_analytics_id' => array(
         *             'name' => __('Google Analytics ID', 'justintheclouds'),
         *             'desc' => __('UA-XXXXXXX-XX', 'justintheclouds'),
         *             'id' => 'apis_ga_id',
         *             'std' => '',
         *             'type' => 'text'
         *         )
         *     )
         * )
         */
        private static function _runOptionsSetup() {
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
                        // If callable, execute function and set as option
                        if(is_callable($option)) {
                            $option = (array)call_user_func($option);
                        }
                        // Create defaults
                        $defaults = array(
                            'name' => ucfirst(str_replace('_', ' ', $name)),
                            'id' => strtolower(JTCF::__($tab)) . '-' . ucfirst(str_replace('_', '-', $name)),
                            'type' => isset($option['type']) ? $option['type'] : 'text'
                        );
                        if(is_array($option)) {
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
         * Add Hooks
         * 
         * Hooks must be defined as arrays. The first key being the
         * action name, and the second being the function to call
         * for the action. For the action 'init'
         * 
         * @example
         * 'hooks' => array(
         *     array(
         *         'init',
         *         // This can be either a callable funtion or a closure function
         *         function() {
         *             // Run some code on init here
         *         }
         *     )
         * )
         * 
         * The priority and accepted_args parameters wll default to 10 and 1 respectively.
         * If you need to specify either the priority or accepted_args params. You can do as
         * follows
         * 
         * @example
         * 'hooks => array(
         *     // Key names are not required but can be helpful to allow plugins the
         *     // capability to remove/alter closure functions before hook setup.
         *     'some_key_name_can_be_anything' => array(
         *         'init',
         *         // This can be either a callable funtion or a closure function
         *         function($title) {
         *             // Run some code on init here
         *         },
         *         20,
         *         3
         *     )
         * )
         */
        private static function _runHooksSetup() {
            if(count(self::$_configs['hooks'])) {
                foreach(self::$_configs['hooks'] as $hook) {
                    call_user_func_array('add_action', array(
                        $hook[0],
                        $hook[1],
                        isset($hook[2]) ? $hook[2] : 10,
                        isset($hook[3]) ? $hook[3] : 1
                    ));
                }
            }
        }
        
        /**
         * Add filters
         * 
         * Filters must be defined as arrays. The first key being the
         * filter name, and the second being the function to call
         * for the filter. For the filter 'the_title'
         * 
         * @example
         * 'filters' => array(
         *     array(
         *         'the_title',
         *         // This can be either a callable funtion or a closure function
         *         function($title) {
         *             return $title . '!'; // Adds an exclamation to the end of the title.
         *         }
         *     )
         * )
         * 
         * The priority and accepted_args parameters wll default to 10 and 1 respectively.
         * If you need to specify either the priority or accepted_args params. You can do as
         * follows
         * 
         * @example
         * filters => array(
         *     // Key names are not required but can be helpful to allow plugins the
         *     // capability to remove/alter closure functions before filter setup.
         *     'some_key_name_can_be_anything' => array(
         *         'the_title',
         *         // This can be either a callable funtion or a closure function
         *         function($title) {
         *             return $title . '!'; // Adds an exclamation to the end of the title.
         *         },
         *         20,
         *         3
         *     )
         * )
         */
        private static function _runFiltersSetup() {
            if(count(self::$_configs['filters'])) {
                foreach(self::$_configs['filters'] as $filter) {
                    call_user_func_array('add_filter', array(
                        $filter[0],
                        $filter[1],
                        isset($filter[2]) ? $filter[2] : 10,
                        isset($filter[3]) ? $filter[3] : 1
                    ));
                }
            }
        }
        
        private static function _runShortcodesSetup() {
            if(count(self::$_configs['shortcodes'])) {
                foreach(self::$_configs['shortcodes'] as $shortcode) {
                    call_user_func_array('add_shortcode', array(
                        $shortcode[0],
                        $shortcode[1]
                    ));
                }
            }
        }
        
        /**
         * Creates the menu for use in the admin panel and registers the menu
         * 
         * @since 1.0.0
         * @Future Verify wp_get_nav_menu doesn't hurt performance
         */
        private static function _runMenusSetup() {
            $menus = self::$_configs['menus'];
            if(count($menus)) {
                foreach($menus as $slug => $title) {
                    // If it doesn't exist, let's create it.
                    if(!wp_get_nav_menu_object($slug)){
                        $menuId = wp_create_nav_menu($slug);
                        $locations = get_theme_mod('nav_menu_locations');
                        $locations[$slug] = $menuId;
                        set_theme_mod('nav_menu_locations', $locations);
                    }
                    // Register menu
                    register_nav_menu($slug, $title);
                }
            }
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
        
        /**
         * PUBLIC FACING METHODS
         * -----------------------------------------------------------
         */
        
        public static function getOpenSection() {
            ob_start();
            call_user_func_array(array('JTCF', 'openSection'), func_get_args());
            return ob_get_clean();
        }
        
        public static function getCloseSection() {
            ob_start();
            call_user_func_array(array('JTCF', 'closeSection'), func_get_args());
            return ob_get_clean();
        }
        
        public static function getSection() {
            ob_start();
            call_user_func_array(array('JTCF', 'section'), func_get_args());
            return ob_get_clean();
        }
        
        // TODO Document me
        public static function openSection($location, $atts=array(), $options=array()) {
            
            // Do actions before opening the tag
            do_action('JTCF_beforeOpenSection', $location, $atts, $options);
            do_action('JTCF_beforeOpenSection' . ucfirst($location), $atts, $options);
            
            // Add location to current position
            self::$currentLocation .= "/$location";
            
            // Allow child theme to modify location element tag
            $filteredLocation = apply_filters('JTCF_sectionLocation', $location);

            // Grab tag from sub tag
            if(strpos($filteredLocation, '/') !== false)
                $filteredLocation = substr(strrchr($filteredLocation, "/"), 1);
            
            // Open tag
            echo "<$filteredLocation";
            
            // Apply attributes filter            
            $atts = apply_filters('JTCF_outputAttributes', $atts, $location);
            $atts = apply_filters('JTCF_outputAttributes' . ucfirst($location), $atts, $location);

            // Output each attribute
            if(is_array($atts)) {
                foreach($atts as $name => $value) {
                    if($value !== null) {
                        echo " $name=\"$value\"";   
                    }
                }
            }
                        
            // Output the microdata itemprop if it has one
            self::_outputMicrodata($location, 'itemprop');
            
            // Output the microdata item scope
            self::_outputMicrodata($location, 'itemtype');
            
            // Close opening tag
            echo ">";
            
            // Do actions after opening tag
            do_action('JTCF_afterOpenSection', $location, $atts, $options);
            do_action('JTCF_afterOpenSection' . ucfirst($location), $atts, $options);
        }
        
        /**
         * Closes an open section
         * 
         * This is closes an html section. First the filter
         * JTCF_sectionLocation is applied. This will give theme
         * developers a chance to alter the elements tag before output.
         * 
         * Then two actions are called. JTCF_beforeCloseSection and 
         * JTCF_beforeCloseSection[Locationname]. The first action is useful
         * as it allows code to be run for all sections. The second hook is for
         * specific code to be run only for certain sections.
         * 
         * Next any microdata properties that have not been applied yet will be
         * output into <meta> tags.
         * 
         * Two after close actions will then be called.
         */
        public static function closeSection($location) {
            
            // Allow child theme to modify location/tag, and options
            $filteredLocation = apply_filters('JTCF_sectionLocation', $location);
            
            // Do actions after opening tag
            do_action('JTCF_beforeCloseSection', $location);
            do_action('JTCF_beforeCloseSection' . ucfirst($location));
            
            // Output meta microdata, this is below above hooks, so they have a chance
            // to use some of the properties before any unused properties get
            // added as meta tags.
            self::_outputMicrodata($location, 'properties');
            
            echo "</$filteredLocation>";
                        
            // Remove location to current position
            self::$currentLocation = substr(self::$currentLocation, 0, strrpos(self::$currentLocation, "/$location"));
            
            // Do actions after opening tag
            do_action('JTCF_afterCloseSection', $location);
            do_action('JTCF_afterCloseSection' . ucfirst($location));
        }
        
        /**
         * Short hand method for calling openSection and closeSection
         * 
         * @since 1.0.0
         */
        public static function section($location, $atts=array()) {
            self::openSection($location, $atts);
            self::closeSection($location);
        }

        public static function hookAfterThemeSetup() {
                        
            add_theme_support( 'automatic-feed-links' );	
            add_theme_support( 'structured-post-formats', array( 'link', 'video' ) );
            add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'quote', 'status' ) );
            add_theme_support( 'post-thumbnails' );
            
        }
        
        /**
         * Allows archive pages to be added to menus
         */
        public static function enableArchivePageMenu() {
            require_once get_template_directory . '/includes/enable-custom-archive-pages.php';
        }
        
        /**
         * CORE HOOK METHODS
         */
        
        /**
         * Registers and enqueues all styles and scripts
         * 
         * FUTURE create a _register setting to avoid auto registering every style
         * @since 1.0.0
         */
        public static function hookWPEnqueueScripts() {
            // Load Comments	
		    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' );
            
            // If any styles were added, register and enqueue
            if(count(self::$_configs['styles']) > 1) {
                // Get style settings
                $settings = self::_extractSettings(self::$_configs['styles']);
                // Process styles
                foreach(self::$_configs['styles'] as $key => $style) {
                    // If this is an array, call register_style with passed in values
                    if(is_array($style)) {
                        // Log debug
                        self::_debug($style, "Registering '$style[0]' with args", 'debugs');
                        call_user_func_array('wp_register_style', $style);
                        $style = $style[0];
                    } else {
                        // Log debug
                        self::_debug(array($style, get_stylesheet_directory_uri() . '/' . self::$_configs['folders']['styles'] . '/' . strtolower($style) . '.css', array(), self::$theme->version), "Registering '$style' with args", 'debugs');
                        wp_register_style($style, get_stylesheet_directory_uri() . '/' . self::$_configs['folders']['styles'] . '/' . strtolower($style) . '.css', array(), self::$theme->version);
                    }
                    // Should we enqueue this style
                    if($settings['enqueue'] !== false) {
                        // If true, enqueue all styles; If it's an array, check if this style is in it
                        if($settings['enqueue'] === true || (is_array($settings['enqueue']) && in_array($style, $settings['enqueue']))) {
                            // Log debug
                            self::_debug($style, "Enqueuing '$style' with args", 'debugs');
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
                        wp_register_script($script, get_stylesheet_directory_uri() . '/' . self::$_configs['folders']['scripts'] . '/' . strtolower($script) . '.js', array(), self::$theme->version);
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
         * Output debug logs
         * 
         * This will add all debug logs to the admin bar if a
         * developer is viewing the site. If the adminbar is not present
         * a debug bar will still be available.
         * 
         * @since 1.0.0
         */
        public static function hookAdminBarMenu() {
            
            // Don't execute on ajax calls
            if (defined('DOING_AJAX') && DOING_AJAX) return;
            
            // If is a developer IP or on localhost
            if($_SERVER['HTTP_HOST'] == 'localhost' || isset(self::$_configs['developers']) && in_array($_SERVER['REMOTE_ADDR'], self::$_configs['developers'])) {
            
                // Output computed configs
                self::_debug(self::$_configs, 'JTCF Initialized With', 'Configs');

                // If we have an admin bar, add button to open debug logs
                global $wp_admin_bar;
                if ( is_super_admin() && is_admin_bar_showing() ) {

                    $wp_admin_bar->add_node( array(
                        'parent' => null,
                        'id' => 'JTCF_debug_log_admin_bar',
                        'title' => self::__('debug_log_admin_bar_title') . (isset(self::$_debugs['logs']) ? '<div class="JTCF_debug_log_panel_tab_count JTCF_debug_log_panel_tab_count_logs" style="line-height: 8px !important;">' . count(self::$_debugs['logs']) . '</div>' : '') . (isset(self::$_debugs['errors']) ? '<div class="JTCF_debug_log_panel_tab_count JTCF_debug_log_panel_tab_count_errors" style="line-height: 8px !important;">' . count(self::$_debugs['errors']) . '</div>' : ''),
                        'href' => '#',
                        'meta' => array(
                            'title' => self::__('debug_log_admin_bar_meta_title'),
                            'class' => 'JTCF_debug_log_admin_bar',
                            'onclick' => 'jQuery("#JTCF_debug_log_panel").slideToggle(100); return false;'
                        )
                    ) );

                } else {

                    echo '
                        <style>
                            #JTCF_debug_log_panel {top: 0 !important; display: block !important; padding: 0 !important;}
                            .JTCF_debug_log_panel_tab {margin-bottom: 0 !important;}
                        </style>
                        ';

                }

                // Add the log panel
                echo '
                    <style>
                        #wp-admin-bar-JTCF_debug_log_admin_bar a {background: rgb(175, 165, 92); color: black !important; transition: all .5s ease;}
                        #wp-admin-bar-JTCF_debug_log_admin_bar a:hover {background: rgb(199, 180, 42) !important;}
                        #JTCF_debug_log_panel {position: fixed; top: 32px; left: 0; right: 0; background: #333; padding: 15px; color: #CCC; max-height: 400px; overflow-y: scroll; display: none; box-shadow: 0 3px 5px rgba(0,0,0,.4);}
                        #JTCF_debug_log_panel_tab_close {background: rgb(56, 0, 0);}
                        .JTCF_debug_log_panel_tab {border-radius: 4px; border: 1px solid #232323; border-bottom: none; padding: 5px 10px; margin: 0 5px 15px 0; display: inline-block; cursor: pointer; transition: all .5s ease;}
                        .JTCF_debug_log_panel_tab:hover, .JTCF_debug_log_panel_tab.active {background: #444; color: white;}
                        .JTCF_debug_log_panel_tab_count {color: white !important; display: inline-block !important; padding: 4px 0px !important; margin-left: 7px !important; font-size: 10px !important; background: black !important; height: 8px !important; width: 16px !important; border-radius: 10px !important; line-height: 10px !important; text-align: center !important; -webkit-box-sizing: content-box; -moz-box-sizing: content-box; box-sizing: content-box;}
                        .JTCF_debug_log_panel_log {padding: 5px 10px;}
                        .JTCF_debug_log_panel_tab_count_logs {background: rgb(117, 105, 11) !important;}
                        .JTCF_debug_log_panel_tab_count_errors {background: rgb(117, 11, 11) !important;}
                        .JTCF_debug_log_panel_title {font-weight: bold; color: white; padding-bottom: 3px; margin: 10px 0 0; min-width: 100px;}
                        .JTCF_debug_log_panel_data {background: #232323; padding: 10px; border-radius: 4px; border: 1px dashed #454545;}
                    </style>
                ';
                echo '<div id="JTCF_debug_log_panel">';
                    foreach(self::$_debugs as $tab => $logs) {
                        echo '<div id="JTCF_debug_log_panel_tab_' . $tab . '" class="JTCF_debug_log_panel_tab' . ($tab == 'logs' ? ' active"' : '') . '" onclick="jQuery(\'.JTCF_debug_log_panel_tab\').removeClass(\'active\'); jQuery(this).addClass(\'active\'); jQuery(\'.JTCF_debug_log_panel_log\').hide(); jQuery(\'#JTCF_debug_log_panel_log_' . $tab . '\').show();">' . ucfirst($tab) . '<span class="JTCF_debug_log_panel_tab_count JTCF_debug_log_panel_tab_count_' . $tab . '">' . count($logs) . '</span></div>';
                    }
                    echo '<div title="Close log content" id="JTCF_debug_log_panel_tab_close" class="JTCF_debug_log_panel_tab" onclick="jQuery(\'.JTCF_debug_log_panel_tab\').removeClass(\'active\'); jQuery(\'.JTCF_debug_log_panel_log\').hide();">X</div>';
                    foreach(self::$_debugs as $tab => $logs) {
                        echo '<div id="JTCF_debug_log_panel_log_' . $tab . '" class="JTCF_debug_log_panel_log"' . ($tab != 'logs' || (!is_super_admin() || !is_admin_bar_showing()) ? ' style="display: none;"' : '') . '>';
                        foreach($logs as $title => $log) {
                            echo '<div class="JTCF_debug_log_panel_title">' . $title . '</div>';
                            if($log === null) {
                                echo '<pre class="JTCF_debug_log_panel_data">NULL</pre>';
                            } elseif($log === true) {
                                echo '<pre class="JTCF_debug_log_panel_data">TRUE</pre>';
                            } elseif($log === false) {
                                echo '<pre class="JTCF_debug_log_panel_data">FALSE</pre>';
                            } else {
                                echo '<pre class="JTCF_debug_log_panel_data">'; print_r($log); echo '</pre>';
                            }

                        }
                        echo '</div>';
                    }
                echo '</div>';
            
            }
        }

        /**
         * FILTER METHODS
         * -----------------------------------------------------------
         */

        /**
         * HELPER METHODS
         * -----------------------------------------------------------
         */
        
        /**
         * Return a language term by name
         * 
         * The framework uses it's own text-domain for languages.
         * Since using variables in language functions is incorrect,
         * we allow the child theme to overwrite the frameworks default terms.
         * 
         * This method is just a quick way of calling the term while
         * reminding us that it's a mofidiable term.
         * 
         * @param string $name The name of the language term. This should be defined
         * under $_configs['language'][$name]. A JTCF debug error will be added for
         * missing terms.
         * 
         * @since 1.0.0
         */
        public static function __($name) {
            if(!isset(self::$_configs['language'][$name])) {
                JTCF::_debug(self::$_configs['language'], "The language term '$name' is not defined", 'errors');
                return $name;
            }
            $text = (is_callable(self::$_configs['language'][$name]) ? call_user_func(self::$_configs['language'][$name]) : self::$_configs['language'][$name]);
            $args = func_get_args();
            array_shift($args);
            return vsprintf($text, $args);
        }
        
        public static function _e($name) {
            echo call_user_func_array(array('JTCF', '__'), func_get_args());
        }
        
        /**
         * Gets the class for the specified target
         * 
         * If clean mode is enabled, no class will be defined
         * otherwise the class will be grabbed from the configs['classes'][$default] option
         * or default to the default param.
         * 
         * @param string $target The default class if not defined in configs
         * @param bool $retAttr Whether to wrap the class in the class="" attr
         * @since 1.0.0
         */
        public static function getClass($default, $retAttr=true) {
            if(self::$_configs['cleanMode']) return;
            if($retAttr) {
                return ' class="' . (!isset(self::$_configs['classes'][$default]) ? $default : self::$_configs['classes'][$default]) . '"';
            } else {
                return (!isset(self::$_configs['classes'][$default]) ? $default : self::$_configs['classes'][$default]);
            }
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
        
        
        /**
         * DISPLAY METHODS
         * -----------------------------------------------------------
         */
        
        // TODO document me
        // TODO meta
        private static function _outputMicrodata($location, $type, $property="") {
            echo self::getMicrodata($location, $type, $property);
        }
        
        public static function getMicrodata($location, $output, $property="") {
            
            $md = &self::$_configs['microdata'];
            $data = null;
            
            // If location !isset, return
            // TODO make sure no errors without this
            if(!isset($md[$location])) return;
            
            // Grab $output value or call $output function
            switch($output) {
                case 'itemtype':
                case 'itemprop':
                    if(!isset($md[$location][$output])) break;
                    // Call closure
                    if(is_callable($md[$location][$output])) {
                        $data = call_user_func($md[$location][$output]);
                    // Set value
                    } else {
                        $data = $md[$location][$output];
                    }
                break;
                case 'properties':
                    // If no properties, return
                    if(!isset($md[$location]['properties']) || !count($md[$location]['properties'])) break;
                    $data = '';
                    // Loop each property
                    foreach($md[$location]['properties'] as $itemprop => $content) {
                        $content = JTCF::getMicrodata($location, 'value', $itemprop);
                        $itemprop = JTCF::getMicrodata($location, 'property', $itemprop);
                        // Make sure itemprop and content isnt empty
                        if(empty($itemprop) || empty($content)) continue;
                        // Add output
                        $data .= '<meta' . $itemprop . ' content="' . $content . '" />' . "\n";
                    }
                    return $data;
                break;
                case 'property':
                    if($location == 'author') JTCF::_debug('test' . $property);
                    // If property not set, return
                    if(!isset($md[$location]['properties'][$property]) && array_search($property, $md[$location]['properties']) === false) break;
                    $data = $property;
                break;
                case 'value':
                    // If propery not set, return
                    if(!isset($md[$location]['properties'][$property])) break;
                    // Call closures
                    if(is_callable($md[$location]['properties'][$property])) {
                        $data = call_user_func($md[$location]['properties'][$property]);
                    } else {
                        $data = $md[$location]['properties'][$property];
                    }
                break;
                default:
                    return;
                break;
            }
            
            // Apply filter
            $data = apply_filters('JTCF_getMicrodata', $data, $location, $output, $property);
            
            // Output the data
            switch($output) {
                case 'itemtype':
                    if(!is_string($data)) return;
                    return ' itemscope="itemscope" itemtype="' . $data . '"';
                break;
                case 'itemprop':
                    if(!is_string($data)) return;
                    return ' itemprop="' . $data . '"';
                break;
                case 'property':
                    // Output itemprop name
                    if(!is_string($data)) return;
                    // Unset the property since it was used
                    unset($md[$location]['properties'][$property]);
                    return ' itemprop="' . $data . '"';
                break;
                case 'value':
                    // Output value of property
                    if(!is_string($data)) return;
                    return $data;
                break;
            }
        }
        
        // remove
        public static function getMicrodataDEPRECATED($location, $type, $property="") {
            
            // Define method name
            $method = $location . ucfirst($type) . ucfirst($property);
            
            // Is microdata disabled?
            if(of_get_option('microdata-disable', false) || !isset(self::$_configs['microdata'][$method])) return;
                    
            if(is_callable(self::$_configs['microdata'][$method])) {
                $data = call_user_func_array(self::$_configs['microdata'][$method], func_get_args());
            } elseif(true || is_string(self::$_configs['microdata'][$method])) {
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
                    return ' itemscope="itemscope" itemtype="' . $data . '"';
                break;
                case 'itemprop':
                    // Make sure this location is defined/being used and output itemprop="property"
                    if(isset(self::$_scopes[$location])) {
                        return ' itemprop="' . $data . '"';
                    }
                break;
                case 'meta':
                    // Make sure this location is defined/being used and output itemprop="property"
                    if(isset(self::$_scopes[$location])) {
                        $metas = "";
                        foreach($data as $property) {
                            if(!empty($property) && $value = JTCF::getMicrodata($location, 'value', $property)) {
                                $metas .= '<meta' . JTCF::getMicrodata($location, 'itemprop', $property) . ' content="' . $value . '" />' . "\n";
                            }
                        }
                        return $metas;
                    }
                break;
                case 'value':
                    return $data;
                break;
            }
        
        }
        
        // TODO convert JTCF plugin
        // TODO microdata
        public static function socialIcons() {
            
            $networks = of_get_options();
            
            $networks = array_filter($networks, function($var) use(&$networks) {
                $r = false;
                if(strpos(key($networks), 'social-') !== false) {
                    if(key($networks) != 'social-additional-networks' && !empty($var)) $r = true;
                }
                next($networks);
                return $r;
            });

            if(!count($networks)) return;

            $output = '<div' . JTCF::getClass('social-icons') . '>';
            foreach($networks as $network => $url) {
                $output .= '<a href="' . $url . '"' . JTCF::getClass(str_replace('social-', 'social-icons-', $network)) . ' target="_blank"><span' . JTCF::getClass(str_replace('social-', 'social-icons-', $network) . '-icon') . '></span></a>';
            } 
            $output .= '</div>';
            
            return $output;
        }

        /**
         * Output all head info for the theme
         * 
         * @since 1.0.0
         */
        public static function outputHead() {

            // Output meta
            self::_outputHeadMeta();

            // Output page title
            echo "<title>"; wp_title(); echo "</title>";

            // Call default wp_head
            wp_head();

        }

        /*
         * Outputs all head meta data
         * 
         * @since 1.0.0
         */
        private static function _outputHeadMeta() {
            
            $output = array();            
            $output['charset'] = '<meta charset="' . get_bloginfo('charset') . '" />';
            
            // Always force latest IE rendering engine (even in intranet) & Chrome Frame
            $output['X-UA-Compatible'] = '<!--[if IE ]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /><![endif]-->';
            
            $output['title'] = '<meta name="title" content="' . wp_title( '|', false, 'right' ) . '">';

            $output['description'] = '<meta name="description" content="' . get_bloginfo('description') . '" />';

            // Copyright information
            $output['copyright'] = '<meta name="Copyright" content="Copyright &copy; ' . get_bloginfo('name') . ' ' . date('Y') . '. All Rights Reserved.">';

            // Author meta
            if (of_get_option('meta_author') || of_get_option('social_google')) {
                $output['author'] = '<meta name="author" content="' . (of_get_option("social_google") ? of_get_option("social_google") : of_get_option("meta_author")) . '" />';
            }
            
            if (is_search()) {
                $output['robots'] = '<meta name="robots" content="noindex, nofollow" />';
            }
            
            // Google site verifier for google web master tools
            if (true == of_get_option('headermeta-google-webmasters')) {
                $output['google_site_verification'] = '<meta name="google-site-verification" content="' . of_get_option("headermeta-google-webmasters") . '" />';
            }

            // Viewport
            if (true == of_get_option('meta_viewport')) {
                $output['viewport'] = '<meta name="viewport" content="' . of_get_option("meta_viewport") . '" />';
            }

            // Favicon
            if (true == of_get_option('head_favicon')) {
                $output['mobile_web_app_capable'] = '<meta name="mobile-web-app-capable" content="yes">';
                $output['favicon'] = '<link rel="shortcut icon" sizes="1024x1024" href="' . of_get_option("head_favicon") . '" />';
            }

            // IOS Webclip
            if (true == of_get_option('head_apple_touch_icon')) {
                $output['apple_touch_icon'] = '<link rel="apple-touch-icon" href="' . of_get_option("head_apple_touch_icon") . '">';
            }

            // Application-specific meta tags
            // Windows 8
            if (true == of_get_option('meta_app_win_name')) {
                $output['application_name'] = '<meta name="application-name" content="' . of_get_option("meta_app_win_name") . '" /> ';
                $output['msapplication_tilecolor'] = '<meta name="msapplication-TileColor" content="' . of_get_option("meta_app_win_color") . '" /> ';
                $output['msapplication_tileimage'] = '<meta name="msapplication-TileImage" content="' . of_get_option("meta_app_win_image") . '" />';
            }

            // Twitter
            if (true == of_get_option('meta_app_twt_card')) {
                $output['twitter_card'] = '<meta name="twitter:card" content="' . of_get_option("meta_app_twt_card") . '" />';
                $output['twitter_site'] = '<meta name="twitter:site" content="' . of_get_option("meta_app_twt_site") . '" />';
                $output['twitter_title'] = '<meta name="twitter:title" content="' . of_get_option("meta_app_twt_title") . '">';
                $output['twitter_description'] = '<meta name="twitter:description" content="' . of_get_option("meta_app_twt_description") . '" />';
                $output['twitter_url'] = '<meta name="twitter:url" content="' . of_get_option("meta_app_twt_url") . '" />';
            }

            // Facebook
            if (true == of_get_option('meta_app_fb_title')) {
                $output['og_title'] = '<meta property="og:title" content="' . of_get_option("meta_app_fb_title") . '" />';
                $output['og_description'] = '<meta property="og:description" content="' . of_get_option("meta_app_fb_description") . '" />';
                $output['og_url'] = '<meta property="og:url" content="' . of_get_option("meta_app_fb_url") . '" />';
                $output['og_image'] = '<meta property="og:image" content="' . of_get_option("meta_app_fb_image") . '" />';
            }

            // Pingback URL
            $output['pingback'] = '<link rel="pingback" href="' . get_bloginfo('pingback_url') . '" />';
            
            echo implode("\t\r\n", apply_filters('JTCF_outputHeadMeta', $output));
        }

        /**
         * Output HTML5 doctype and HTML opening tag
         * 
         * @since 1.0.0
         */
        public static function outputDoctype() {
            ob_start();
            language_attributes();
            $langAtts = ob_get_clean();
             
            $output = '<!DOCTYPE html>';
            $output .= '<!--[if lt IE 7 ]> <html class="ie ie6 ie-lt10 ie-lt9 ie-lt8 ie-lt7 no-js" ' . $langAtts . '> <![endif]-->';
            $output .= '<!--[if IE 7 ]>    <html class="ie ie7 ie-lt10 ie-lt9 ie-lt8 no-js" ' . $langAtts .'> <![endif]-->';
            $output .= '<!--[if IE 8 ]>    <html class="ie ie8 ie-lt10 ie-lt9 no-js" ' . $langAtts . '> <![endif]-->';
            $output .= '<!--[if IE 9 ]>    <html class="ie ie9 ie-lt10 no-js" ' . $langAtts . '> <![endif]-->';
            
            echo apply_filters('JTCF_outputDoctype', $output);
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
        
        public static function _debug($data, $title=null, $type='logs') {
            // If is a developer IP or on localhost
            if($_SERVER['HTTP_HOST'] == 'localhost' || isset(self::$_configs['developers']) && in_array($_SERVER['REMOTE_ADDR'], self::$_configs['developers'])) {
                if(!isset(self::$_debugs[$type])) self::$_debugs[$type] = array();
                if($title) {
                    self::$_debugs[$type][$title] = $data;
                } else {
                    self::$_debugs[$type][] = $data;
                }
            }
        }
    }
}

// Initialize the framework
global $JTCF, $JTCFSettings;
$JTCF = JTCF::getInstance($JTCFSettings);


?>