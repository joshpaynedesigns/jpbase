<?php

/**
 * Functions
 */

// Define the theme version
$theme = wp_get_theme();
$theme_version = $theme->get('Version');
// actual theme version is set in style.css
define('ACCEL_VERSION', $theme_version);

// Helper functions
require_once get_stylesheet_directory() . '/inc/_helpers/00-load-helpers.php';

// Load in the header file
require_once get_stylesheet_directory() . '/inc/_layout/header.php';

// Load in the footer file
require_once get_stylesheet_directory() . '/inc/_layout/footer.php';

// Load in scripts (enqueue all the things)
require_once get_stylesheet_directory() . '/inc/scripts.php';

// Load in the custom post types
require_once get_stylesheet_directory() . '/inc/_post-types/00-load-cpts.php';

// Load in the custom widgets
require_once get_stylesheet_directory() . '/inc/_widgets/00-load-widgets.php';

// Run the banner logic
require_once get_stylesheet_directory() . '/inc/_layout/banner.php';

/**
 * Theme Setup
 * This setup function attaches all of the site-wide functions
 * to the correct hooks and filters. All the functions themselves
 * are defined below this setup function.
 */

 // Crop Images
if (false === get_option('medium_crop')) {
    add_option('medium_crop', '1');
} else {
    update_option('medium_crop', '1');
}

// Image Resize
update_option('medium_size_w', 400);
update_option('medium_size_h', 400);
add_image_size('med_landscape', 800, 600, true);

add_action('genesis_setup', 'child_theme_setup', 15);
function child_theme_setup()
{

    define('CHILD_THEME_VERSION', filemtime(get_stylesheet_directory() . '/style.css'));

    // For the Classic Editor
    add_editor_style();

    // For the Block Editor.
    add_theme_support('editor-styles');

    // Remove Gutenberg Block Library CSS from loading on the frontend
    function smartwp_remove_wp_block_library_css()
    {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('wc-blocks-style'); // Remove WooCommerce block CSS
    }
    add_action('wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100);

    // ** Backend **

    // Meta Title - Move to the top
    // remove_action( 'wp_head', '_wp_render_title_tag', 1 );
    // add_action( 'genesis_title', '_wp_render_title_tag', 1 );

    // Structural Wraps
    add_theme_support('genesis-structural-wraps', array( 'header', 'inner', 'footer-widgets', 'footer' ));

    // Menus
    add_theme_support(
        'genesis-menus',
        array(
            'primary'     => 'Primary Navigation Menu',
            'mobile-menu' => 'Mobile Navigation Menu',
            'secondary-nav' => 'Secondary Navigation Menu',
        )
    );

    // * Reposition the primary navigation menu
    remove_action('genesis_after_header', 'genesis_do_nav');

    // CPT Archive Nav Fix
    function fix_nav_menu($query)
    {
        if (! is_admin()) {
            if ($query->get('post_type') === 'nav_menu_item') {
                // $query->set( 'tax_query', '' );
                $query->set('meta_key', '');
                $query->set('orderby', '');
            }
        }
    }
    add_action('pre_get_posts', 'fix_nav_menu');

    // * Add HTML5 markup structure
    add_theme_support('html5', array( 'search-form', 'comment-form', 'comment-list' ));

    // Sidebars
    unregister_sidebar('sidebar-alt');
    unregister_sidebar('header-right');

    add_theme_support('genesis-footer-widgets', 4);

    // Remove Unused Page Layouts
    genesis_unregister_layout('content-sidebar-sidebar');
    genesis_unregister_layout('sidebar-sidebar-content');
    genesis_unregister_layout('sidebar-content-sidebar');

    // Remove Unused User Settings
    add_filter('user_contactmethods', 'ns_contactmethods');
    add_action('admin_init', 'ns_remove_user_settings');

    // Editor Styles
    // add_editor_style( 'editor-style.css' );

    // Reposition Genesis Metaboxes
    remove_action('admin_menu', 'genesis_add_inpost_seo_box');
    // add_action( 'admin_menu', 'ns_add_inpost_seo_box' );
    remove_action('admin_menu', 'genesis_add_inpost_layout_box');
    // add_action( 'admin_menu', 'ns_add_inpost_layout_box' );

    // Remove Genesis Widgets
    add_action('widgets_init', 'ns_remove_genesis_widgets', 20);

    // Remove Genesis Theme Settings Metaboxes
    add_action('genesis_theme_settings_metaboxes', 'ns_remove_genesis_metaboxes');

    // Don't update theme
    add_filter('http_request_args', 'ns_dont_update_theme', 5, 2);

    // ** Frontend **

    // Remove Edit link
    add_filter('genesis_edit_post_link', '__return_false');

    // Footer
    remove_action('genesis_footer', 'genesis_do_footer');
    add_action('genesis_footer', 'ns_footer');

    // Register Widgets
    add_action(
        'widgets_init',
        function () {
            register_widget('ns_Contact_Widget');
        }
    );

    // Remove Blog & Archive Template From Genesis
    add_filter('theme_page_templates', 'bourncreative_remove_page_templates');
    function bourncreative_remove_page_templates($templates)
    {
        unset($templates['page_blog.php']);
        unset($templates['page_archive.php']);
        return $templates;
    }

    // Add class to Body when there is no banner
    add_filter('genesis_attr_body', 'ns_add_css_attr_body');
    function ns_add_css_attr_body($attributes)
    {
        $hide_banner_options = ns_get_field('hide_banner_options');
        if ($hide_banner_options == 'hide_banner') {
            $attributes['class'] .= ' no-banner';
        }
        if ($hide_banner_options == 'hide_banner_image') {
            $attributes['class'] .= ' no-banner-image';
        }

        $attributes['class'] .= ' fixed-header';

        // Add class to Body when it's Alert Bar
        $show_alert_bar = ns_get_field('show_alert_bar', 'options');
        if ($show_alert_bar) {
            $attributes['class'] .= ' show-alert-bar';
        }
        // return the attributes
        return $attributes;
    }

    // ADD REL ATTRIBUTE TO GALLERY
    add_filter('wp_get_attachment_link', 'rc_add_rel_attribute');
    function rc_add_rel_attribute($link)
    {
        global $post;
        return str_replace('<a href', '<a rel="gallery" href', $link);
    }
}

/**
 * Get taxonomies terms links.
 **/
function custom_taxonomies_terms_links()
{
    global $post, $post_id;
    // get post by post id
    $post = &get_post($post->ID);
    // get post type by post
    $post_type = $post->post_type;
    // get post type taxonomies
    $taxonomies = get_object_taxonomies($post_type);
    foreach ($taxonomies as $taxonomy) {
        // get the terms related to post
        $terms = get_the_terms($post->ID, $taxonomy);
        if (! empty($terms)) {
            $out = array();
            foreach ($terms as $term) {
                $out[] = '<a href="' . get_term_link($term->slug, $taxonomy) . '">' . $term->name . '</a>';
            }
            $return = join(' ', $out);
        }
    }
    return $return;
}

/**
 * Get taxonomies terms.
 **/
function custom_taxonomies_terms($post_id, $taxonomy, $separator = " ", $links = false)
{
    $return = "";
    $terms = get_the_terms($post_id, $taxonomy);
    if (! empty($terms)) {
        $out = array();
        foreach ($terms as $term) {
            $link = get_term_link($term);

            $term_string = "<span>";
            if ($links) {
                $term_string .= "<a href='$link'>";
            }
            $term_string .= $term->name;
            if ($links) {
                $term_string .= "</a>";
            }
            $term_string .= "<span>";

            $out[] = $term_string;
        }
        $return = join($separator, $out);
    }
    return $return;
}

// ** Backend Functions ** //

/**
 * Customize Contact Methods
 *
 * @since 1.0.0
 *
 * @author Bill Erickson
 * @link http://sillybean.net/2010/01/creating-a-user-directory-part-1-changing-user-contact-fields/
 *
 * @param array $contactmethods
 * @return array
 */
function ns_contactmethods($contactmethods)
{
    unset($contactmethods['aim']);
    unset($contactmethods['yim']);
    unset($contactmethods['jabber']);

    return $contactmethods;
}

/**
 * Remove Use Theme Settings
 */
function ns_remove_user_settings()
{
    remove_action('show_user_profile', 'genesis_user_options_fields');
    remove_action('edit_user_profile', 'genesis_user_options_fields');
    remove_action('show_user_profile', 'genesis_user_archive_fields');
    remove_action('edit_user_profile', 'genesis_user_archive_fields');
    remove_action('show_user_profile', 'genesis_user_seo_fields');
    remove_action('edit_user_profile', 'genesis_user_seo_fields');
    remove_action('show_user_profile', 'genesis_user_layout_fields');
    remove_action('edit_user_profile', 'genesis_user_layout_fields');
}

/**
 * Register a new meta box to the post / page edit screen, so that the user can
 * set SEO options on a per-post or per-page basis.
 *
 * @category Genesis
 * @package Admin
 * @subpackage Inpost-Metaboxes
 *
 * @since 0.1.3
 *
 * @see genesis_inpost_seo_box() Generates the content in the meta box
 */
function ns_add_inpost_seo_box()
{

    if (genesis_detect_seo_plugins()) {
        return;
    }

    foreach ((array) get_post_types(array( 'public' => true )) as $type) {
        if (post_type_supports($type, 'genesis-seo')) {
            add_meta_box('genesis_inpost_seo_box', __('Theme SEO Settings', 'genesis'), 'genesis_inpost_seo_box', $type, 'normal', 'default');
        }
    }
}

/**
 * Register a new meta box to the post / page edit screen, so that the user can
 * set layout options on a per-post or per-page basis.
 *
 * @category Genesis
 * @package Admin
 * @subpackage Inpost-Metaboxes
 *
 * @since 0.2.2
 *
 * @see genesis_inpost_layout_box() Generates the content in the boxes
 *
 * @return null Returns null if Genesis layouts are not supported
 */
function ns_add_inpost_layout_box()
{

    if (! current_theme_supports('genesis-inpost-layouts')) {
        return;
    }

    foreach ((array) get_post_types(array( 'public' => true )) as $type) {
        if (post_type_supports($type, 'genesis-layouts')) {
            add_meta_box('genesis_inpost_layout_box', __('Layout Settings', 'genesis'), 'genesis_inpost_layout_box', $type, 'normal', 'default');
        }
    }
}

/**
 * Remove Genesis widgets
 *
 * @since 1.0.0
 */
function ns_remove_genesis_widgets()
{
    unregister_widget('Genesis_eNews_Updates');
    unregister_widget('Genesis_Featured_Page');
    unregister_widget('Genesis_Featured_Post');
    unregister_widget('Genesis_Latest_Tweets_Widget');
    unregister_widget('Genesis_User_Profile_Widget');
}

/**
 * Remove Genesis Theme Settings Metaboxes
 *
 * @since 1.0.0
 * @param string $_genesis_theme_settings_pagehook
 */
function ns_remove_genesis_metaboxes($_genesis_theme_settings_pagehook)
{
    // remove_meta_box( 'genesis-theme-settings-feeds',      $_genesis_theme_settings_pagehook, 'main' );
    // remove_meta_box( 'genesis-theme-settings-header',     $_genesis_theme_settings_pagehook, 'main' );
    remove_meta_box('genesis-theme-settings-nav', $_genesis_theme_settings_pagehook, 'main');
    // remove_meta_box( 'genesis-theme-settings-layout',    $_genesis_theme_settings_pagehook, 'main' );
    // remove_meta_box( 'genesis-theme-settings-breadcrumb', $_genesis_theme_settings_pagehook, 'main' );
    // remove_meta_box( 'genesis-theme-settings-comments',   $_genesis_theme_settings_pagehook, 'main' );
    // remove_meta_box( 'genesis-theme-settings-posts',      $_genesis_theme_settings_pagehook, 'main' );
    remove_meta_box('genesis-theme-settings-blogpage', $_genesis_theme_settings_pagehook, 'main');
    // remove_meta_box( 'genesis-theme-settings-scripts',    $_genesis_theme_settings_pagehook, 'main' );
}

/**
 * Don't Update Theme
 *
 * @since 1.0.0
 *
 * If there is a theme in the repo with the same name,
 * this prevents WP from prompting an update.
 *
 * @author Mark Jaquith
 * @link http://markjaquith.wordpress.com/2009/12/14/excluding-your-plugin-or-theme-from-update-checks/
 *
 * @param array  $r, request arguments
 * @param string $url, request url
 * @return array request arguments
 */

function ns_dont_update_theme($r, $url)
{
    if (0 !== strpos($url, 'http://api.wordpress.org/themes/update-check')) {
        return $r; // Not a theme update request. Bail immediately.
    }
    $themes = unserialize($r['body']['themes']);
    unset($themes[ get_option('template') ]);
    unset($themes[ get_option('stylesheet') ]);
    $r['body']['themes'] = serialize($themes);
    return $r;
}

// ** Frontend Functions ** //

// * Display a custom favicon
add_filter('genesis_pre_load_favicon', 'ns_favicon_filter');
function ns_favicon_filter($favicon_url)
{
    return '';
}

/**
 * Add Theme options
 *
 * @author Wesley Cole
 * @link http://objectiv.co/
 */

if (function_exists('acf_add_options_page')) {
    acf_add_options_page(
        array(
            'page_title' => 'Theme Settings',
            'menu_title' => 'Theme Settings',
            'menu_slug'  => 'theme-general-settings',
            'icon_url'   => 'dashicons-art',
            'capability' => 'edit_posts',
            'position'   => 59.5,
            'redirect'   => false,
        )
    );
}

// Email obfuscation
function ns_hide_email($email)
{

    $character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';
    $key           = str_shuffle($character_set);
    $cipher_text   = '';
    $id            = 'e' . rand(1, 999999999);
    for ($i = 0; $i < strlen($email); $i += 1) {
        $cipher_text .= $key[ strpos($character_set, $email[ $i ]) ];
    }
    $script  = 'var a="' . $key . '";var b=a.split("").sort().join("");var c="' . $cipher_text . '";var d="";';
    $script .= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));';
    $script .= 'document.getElementById("' . $id . '").innerHTML="<a href=\\"mailto:"+d+"\\">"+d+"</a>"';
    $script  = 'eval("' . str_replace(array( '\\', '"' ), array( '\\\\', '\"' ), $script) . '")';
    $script  = '<script type="text/javascript">/*<![CDATA[*/' . $script . '/*]]>*/</script>';

    return '<span id="' . $id . '">[javascript protected email address]</span>' . $script;
}

/** * Remove editor menu
 */
function remove_editor_menu()
{
    remove_action('admin_menu', '_add_themes_utility_last', 101);
}
add_action('_admin_menu', 'remove_editor_menu', 1);

/**
 * Add Custom Post Type archive to WordPress search link query
 * Author: https://github.com/mthchz/editor-archive-post-link/blob/master/editor-archive-post-link.php
 */
add_filter('wp_link_query', 'cab_add_custom_post_type_archive_link', 10, 2);
function cab_add_custom_post_type_archive_link($results, $query)
{

    if ($query['offset'] > 0) : // Add only on the first result page
        return $results;
    endif;
    $match = '/' . str_remove_accents($query['s']) . '/i';
    foreach ($query['post_type'] as $post_type) :
        $pt_archive_link = get_post_type_archive_link($post_type);
        $pt_obj          = get_post_type_object($post_type);
        if ($pt_archive_link !== false && $pt_obj->has_archive !== false) : // Add only post type with 'has_archive'
            if (preg_match($match, str_remove_accents($pt_obj->labels->name)) > 0) :
                array_unshift(
                    $results,
                    array(
                        'ID'        => $pt_obj->has_archive,
                        'title'     => trim(esc_html(strip_tags($pt_obj->labels->name))),
                        'permalink' => $pt_archive_link,
                        'info'      => 'Archive',
                    )
                );
            endif;
        endif; // end post type archive links in link_query
    endforeach;
    return $results;
}
// * Remove accents
function str_remove_accents($str, $charset = 'utf-8')
{
    $str = htmlentities($str, ENT_NOQUOTES, $charset);
    $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
    $str = preg_replace('#&[^;]+;#', '', $str);
    return $str;
}
add_filter('wp_link_query', 'cab_wp_link_query_term_linking', 99, 2);
/**
 * Add Term links to WordPress search link query
 * Modified from: https://gist.github.com/emzo/6f86f50199c09d2f4ce6863401a307fb
 * Ref: https://codex.wordpress.org/Function_Reference/get_taxonomies
 *      https://developer.wordpress.org/reference/classes/wp_term_query/__construct/
 *      https://developer.wordpress.org/reference/functions/get_terms/
 *      http://php.net/manual/en/function.array-diff.php
 */
function cab_wp_link_query_term_linking($results, $query)
{
    // * Query taxonomy terms.
    $taxonomies = get_taxonomies(
        array(
            'show_in_nav_menus' => true,
        ),
        'names'
    );

    // * Add to the array any taxonomies you do not want
    $exclude = array(
        'media_category',
        'tag',
    );

    $taxonomies = array_diff($taxonomies, $exclude);

    // * Get the terms of the taxonomies
    // $terms = get_terms( $taxonomies, array(
    // 'name__like' => $query['s'],
    // 'number'     => 20,
    // 'hide_empty' => true,
    // ));
    // * Terms
    // if ( ! empty( $terms ) && ! is_wp_error( $terms ) ):
    // foreach( $terms as $term ):
    // $results[] = array(
    // 'ID'        => 'term-' . $term->term_id,
    // 'title'     => html_entity_decode($term->name, ENT_QUOTES, get_bloginfo('charset')) ,
    // 'permalink' => get_term_link(intval($term->term_id) , $term->taxonomy) ,
    // 'info'      => get_taxonomy($term->taxonomy)->labels->singular_name,
    // );
    // endforeach;
    // endif;

    return $results;
}

/**
 * Hide editor for content builder pages.
 */
function ns_hide_editor()
{

    // Get the Post ID.
    $post_id = ns_key_value($_GET, 'post');
    $post_param = ns_key_value($_POST, 'post_ID');
    if (! $post_id) {
        $post_id = $post_param;
    }

    if (! isset($post_id)) {
        return;
    }

    // Get the name of the Page Template file.
    $template_file = get_post_meta($post_id, '_wp_page_template', true);

    if ($template_file == 'template-content-builder.php') { // edit the template name
        remove_post_type_support('page', 'editor');
    }
}
add_action('admin_init', 'ns_hide_editor');

/*
 * Modify TinyMCE editor to remove H1 & Pre.
 */
add_filter('tiny_mce_before_init', 'tiny_mce_remove_unused_formats');
function tiny_mce_remove_unused_formats($init)
{
    // Add block format elements you want to show in dropdown
    $init['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;';
    return $init;
}

add_filter('the_generator', '__return_empty_string');


// Get Short Description
function ns_get_short_description($post_id = null, $length = null)
{

    $excerpt   = get_the_excerpt($post_id);
    $post_type = get_post_type($post_id);

    $excerpt_length = 55;
    if (! empty($length)) {
        $excerpt_length = $length;
    }

    if (empty($excerpt)) {
        if (function_exists('tribe_events_get_event') && $post_type === 'tribe_events') {
            $content = strip_shortcodes(tribe_events_get_event($post_id)->post_content);
        }

        if ($post_type === 'service' || $post_type === 'industry') {
            $content = strip_shortcodes(ns_get_field('content', $post_id));
        }

        if (empty($content)) {
            $post    = get_post($post_id);
            $content = strip_shortcodes($post->post_content);
        }

        $excerpt = $content;
    }

    $excerpt = wp_trim_words($excerpt, $excerpt_length);

    return $excerpt;
}

// Pretty Dump of Variables
function ovdump($data)
{
    print( '<pre>' . print_r($data, true) . '</pre>' );
}

// Add the skip to content nav
add_action('genesis_before', 'ns_skip_to_content_button');
function ns_skip_to_content_button()
{
    ?>
     <a href="#afterBanner" class="skipLink">Skip to content</a>
        <?php
}

add_filter('facetwp_is_main_query', function ($is_main_query, $query) {
    if ($query->is_archive() && $query->is_main_query()) {
        $is_main_query = false;
    }
    return $is_main_query;
}, 10, 2);

// Ignore tribe_events from FacetWP
add_filter('facetwp_is_main_query', function ($is_main_query, $query) {
    if ('tribe_events' == $query->get('post_type')) {
        $is_main_query = false;
    }
    return $is_main_query;
}, 10, 2);

//* Customize header search form label
add_filter('genesis_search_form_label', 'ec_search_form_label');
function ec_search_form_label($text)
{
    return esc_attr('Site Search');
}
