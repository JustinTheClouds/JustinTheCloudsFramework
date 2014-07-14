<?php
/**
 * @package WordPress
 * @subpackage JustinTheCloudsFramework
 * @since 1.0
 */

// Output HTML5 Doctype and opening HTML element with IE version classes
JTCF::outputDoctype();

// Output the page head
// Add all standard meta tag
// Configuable from Theme Options > Meta tab
JTCF::outputHead();

?>

<?php JTCF::openSection('body'); ?>
        
    <?php JTCF::openSection('header'); ?>
        <?php JTCF::outputSiteTitle(); ?>
        <?php JTCF::outputTagLine(); ?>
        <?php JTCF::openSection('nav'); ?>
        <?php JTCF::closeSection('nav', array('theme_location' => 'primary')); ?>
    <?php JTCF::closeSection('header'); ?>

    <?php /**
           * This section utilizes the afterOpenSection hook to add the archive title
           * if it's an archive page. JTCF::outputTheTitle() could be called manually
           * right below this openSection() line for the same effect.
           */ ?> 
    <?php JTCF::openSection('main'); ?>

        <?php if (have_posts()) : ?>

        <?php while (have_posts()) : the_post(); ?>

            <?php JTCF::openSection('article', array('id' => "post-" . get_the_ID())); ?>

                <?php /**
                       * This is a custom section that utilizes hooks/filters
                       * then calls outputTheTitle helper method
                       * This could just be JTCF::outputTheTitle() or just the_title()
                       * Use this as a reference on using custom section types
                       */ ?>
                <?php JTCF::openSection('theTitle'); JTCF::closeSection('theTitle'); ?>
                
                <?php /**
                       * This is a custom section that utilizes hooks/filters
                       * then calls outputTheContent helper method
                       * This could just be JTCF::outputTheContent() or just the_content()
                       * Use this as a reference on using custom section types
                       */ ?>
                <?php JTCF::openSection('theContent'); JTCF::closeSection('theContent'); ?>

                <footer class="postmetadata">
                    <?php the_tags(__('Tags: ','justintheclouds'), ', ', '<br />'); ?>
                    <?php _e('Posted in','justintheclouds'); ?> <?php the_category(', ') ?> | 
                    <?php comments_popup_link(__('No Comments &#187;','justintheclouds'), __('1 Comment &#187;','justintheclouds'), __('% Comments &#187;','justintheclouds')); ?>
                    <address>
                        <?php the_author(); the_author_link(); the_author_meta(); the_author_posts_link();?>
                    </address>
                </footer>

            <?php JTCF::closeSection('article'); ?>

        <?php endwhile; ?>

        <?php // Output archive page type pagination ?>
        <?php JTCF::outputPagination(); ?>

        <?php endif; ?>

    <?php JTCF::closeSection('main'); ?>

    <?php JTCF::openSection('aside'); ?>

        <?php get_search_form(); ?>

        <?php dynamic_sidebar('Sidebar'); ?>

        <h2><?php _e('Archives','justintheclouds'); ?></h2>
        <ul>
            <?php wp_get_archives('type=monthly'); ?>
        </ul>

        <h2><?php _e('Meta','justintheclouds'); ?></h2>
        <ul>
            <?php wp_register(); ?>
            <li><?php wp_loginout(); ?></li>
            <li><a href="http://wordpress.org/" title="<?php _e('Powered by WordPress, state-of-the-art semantic personal publishing platform.','justintheclouds'); ?>"><?php _e('WordPress','justintheclouds'); ?></a></li>
            <?php wp_meta(); ?>
        </ul>

        <h2><?php _e('Subscribe','justintheclouds'); ?></h2>
        <ul>
            <li><a href="<?php bloginfo('rss2_url'); ?>"><?php _e('Entries (RSS)','justintheclouds'); ?></a></li>
            <li><a href="<?php bloginfo('comments_rss2_url'); ?>"><?php _e('Comments (RSS)','justintheclouds'); ?></a></li>
        </ul>

    <?php JTCF::closeSection('aside'); ?>

    <?php JTCF::openSection('footer'); ?>

        <nav class="row" role="navigation">
            <?php wp_nav_menu( array('menu' => 'footer') ); ?>
        </nav>
        <div class="row">
            <small class="copyright columns sic">&copy;<?php echo date("Y"); echo " "; bloginfo('name'); ?></small>
            <div class="columns six built-by"><a href="http://justintheclouds.com" target="_blank"><span class="Justin"><span class="J">J</span>ustin</span><span class="The">The</span><span class="Clouds">Clouds</span></a></div>
        </div>
    <?php JTCF::closeSection('footer'); ?>

	
<?php JTCF::closeSection('body'); ?>

</html>