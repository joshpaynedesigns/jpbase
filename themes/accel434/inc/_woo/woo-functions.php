<?php

// All functions having to do with WooCommerce

//declare WC support
function ns_child_wc_support()
{
    add_theme_support('woocommerce');
}
add_action('after_setup_theme', 'ns_child_wc_support');

//Full Width Pages on WooCommerce
function ns_shopping_cpt_layout()
{
    if (class_exists('WooCommerce')) {
        if (is_page(array( 'cart', 'checkout' )) || is_shop() || 'product' == get_post_type()) {
            remove_action('genesis_sidebar', 'genesis_do_sidebar');
            remove_action('genesis_sidebar', 'ns_do_podcast_sidebar');
            return 'full-width-content';
        }
    }
}
add_filter('genesis_site_layout', 'ns_shopping_cpt_layout');

// Function to check if a page is a woo page
function ns_is_woo()
{
    if (function_exists('is_woocommerce')) {
        if (is_woocommerce()) {
            return true;
        } elseif (is_page(array( 'cart', 'checkout', 'my-account' ))) {
            return true;
        } else {
            return false;
        }
    }
}

// Remove script from site
add_action('wp_enqueue_scripts', 'dequeue_woocommerce_cart_fragments', 11);
function dequeue_woocommerce_cart_fragments()
{
    if (is_front_page() || is_single()) {
        wp_dequeue_script('wc-cart-fragments');
    }
}

// Change Woocommerce css breaktpoint from max width: 768px to 800px
add_filter('woocommerce_style_smallscreen_breakpoint', 'woo_custom_breakpoint');

function woo_custom_breakpoint($px)
{
    $px = '900px';
    return $px;
}

/**
 * woo_hide_page_title
 *
 * Removes the "shop" title on the main shop page
 *
 * @access      public
 * @since       1.0
 * @return      void
*/
function ns_woo_hide_page_title()
{
    return false;
}
add_filter('woocommerce_show_page_title', 'ns_woo_hide_page_title');

// Remove Breadcrumbs
function ns_remove_wc_breadcrumbs()
{
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
}
add_action('init', 'ns_remove_wc_breadcrumbs');

// remove rating from archive
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display');

// change shop column to 3
add_filter('loop_shop_columns', 'loop_columns');
if (!function_exists('loop_columns')) {
    function loop_columns()
    {
        if (is_shop() || is_product_category()) {
            return 3; // 3 products per row
        }
        return 4;
    }
}

// Using Shop Sidebar
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
add_action('woocommerce_sidebar', 'cgd_get_shop_sidebar', 10);
function cgd_get_shop_sidebar()
{
    if (! is_search() && ! is_product()) {
        echo '<aside class="shop-sidebar">';
        dynamic_sidebar('shop-sidebar');
        echo '</aside>';
    }
}

// Remove all product tabs
// remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs' );

// Remove product tabs
// add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );
function woo_remove_product_tabs($tabs)
{
    unset($tabs['description']);         // Remove the description tab
    // unset( $tabs['additional_information'] );      // Remove the additional information tab
    return $tabs;
}

// Rename product tabs
// add_filter( 'woocommerce_product_tabs', 'woo_rename_tabs', 98 );
function woo_rename_tabs($tabs)
{
    // $tabs['description']['title'] = __( 'More Information' );       // Rename the description tab
    // $tabs['reviews']['title'] = __( 'Ratings' );                // Rename the reviews tab
    $tabs['additional_information']['title'] = __('Product Information');    // Rename the additional information tab
    return $tabs;
}

// Hide Weight from products tab
add_filter('wc_product_enable_dimensions_display', '__return_false');

// Remove sales flash
// remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' );

// Remove link around the full product in the grid
remove_action('woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);

// Add link around image
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);


if (! function_exists('woocommerce_template_loop_product_thumbnail')) {
    function woocommerce_template_loop_product_thumbnail()
    {
        echo woocommerce_get_product_thumbnail();
    }
}
if (! function_exists('woocommerce_get_product_thumbnail')) {
    function woocommerce_get_product_thumbnail($size = 'shop_catalog', $placeholder_width = 0, $placeholder_height = 0)
    {
        global $post, $woocommerce;
        $output = '<a class="product-image" href="' . get_the_permalink($post->ID) . '">';

        if (has_post_thumbnail()) {
            $output .= get_the_post_thumbnail($post->ID, $size);
        }
        $output .= '</a>';
        return $output;
    }
}

// Add link around title
add_action('woocommerce_shop_loop_item_title', 'cgd_title_open_link', 5);
function cgd_title_open_link()
{
    global $post;
    echo '<a class="product-title-link" href="' . get_the_permalink($post->ID) . '">';
}

add_action('woocommerce_after_shop_loop_item_title', 'cgd_title_close_link', 15);
function cgd_title_close_link()
{
    echo '</a>';
}

/**
 * Set up structure of the single product
 */
remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 30);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 35);
add_action('woocommerce_single_product_summary', 'product_brand', 1);
function product_brand()
{
    global $product;
    $product_brand = $product->get_attribute('brand');
    $product_brand_slug = array_shift(woocommerce_get_product_terms($product->id, 'pa_brand', 'slugs'));
    ?>
    <h5 class="product-brand"><a href="<?php bloginfo("url"); ?>/<?php echo $product_brand_slug ?>"><?php echo $product_brand ?></a></h5>
<?php }
// add_action( 'woocommerce_single_product_summary', 'ns_product_features', 25 );
function ns_product_features()
{
    global $product;

    echo '<div class="cgd-product-features">';

    if (have_rows('product_features', $product->ID)) {
        while (have_rows('product_features', $product->ID)) :
            the_row();

            echo '<p class="cgd-product-feature"><span class="cgd-product-feature-label">' . get_sub_field('label') . ' </span>' . get_sub_field('content') . '</p>';
        endwhile;
    }

    echo '</div>';
}
// add_action( 'woocommerce_single_product_summary', 'shipping_returns', 35 );
function shipping_returns()
{
    ?>
    <a class="shipping-returns" href="<?php bloginfo("url"); ?>/shipping-returns">Shipping & Returns</a>
<?php }

add_filter('woocommerce_sale_price_html', 'cgd_price_discount', 10, 2);
function cgd_price_discount($price, $product)
{
    if (is_product()) {
        $percentage = round(( ( $product->regular_price - $product->sale_price ) / $product->regular_price ) * 100);
        $html = '<span class="percentage">' . $percentage . '% OFF</span>';
        return $price . $html;
    } else {
        return $price;
    }
}

// add_action( 'woocommerce_single_product_summary', 'cgd_shipping_notice', 30 );
function cgd_shipping_notice()
{
    global $product;
    if (have_rows('sales_notices', 'option')) {
        while (have_rows('sales_notices', 'option')) :
            the_row();
            $display_options = get_sub_field('display_options');
            $category = get_sub_field('product_category');
            $n_label = get_sub_field('notice_label');
            $notice = get_sub_field('notice_text');
            $notice_text = '<span class="product-categories-label">' . $n_label . '</span> ' . $notice;

            if ($display_options == 'category') {
                $terms = wp_get_post_terms($product->id, 'product_cat');
                foreach ((array) $terms as $term) {
                    if (in_array($term->term_id, $category)) {
                        echo '<span class="sales-notice">' . $notice_text . '</span>';
                    }
                }
            } elseif ($display_options == 'global') {
                echo '<span class="sales-notice">' . $notice_text . '</span>';
            }
        endwhile;
    }
}

// Add the Accordion Section if its set up.
// add_action( 'woocommerce_after_single_product_summary', 'cgd_product_flexible_sections', 5 );
function cgd_product_flexible_sections()
{
    $id = get_the_ID();
    $ac_items = ns_get_field('sp_accordion_itmes', $id);
    ?>

    <?php if (! empty($ac_items)) : ?>
        <section class="accordion-block single-product-accordion">
                <?php if (! empty($ac_items)) : ?>
                    <div class="accordion-wrap">
                        <?php foreach ($ac_items as $it) : ?>
                            <?php
                            $d_name = $it['title'];
                            $d_info = $it['content'];
                            ?>
                            <div class="accordion-row">
                                <div class="accordion-title transition_3sec">
                                    <div class="title"><?php echo $d_name; ?></div>
                                    <svg class="icon-plus" enable-background="new 0 0 100 100" id="Layer_1" version="1.1" viewBox="0 0 100 100" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <polygon class="plus_fill" fill="#a6a6a6" points="80.2,51.6 51.4,51.6 51.4,22.6 48.9,22.6 48.9,51.6 19.9,51.6 19.9,54.1 48.9,54.1 48.9,83.1   51.4,83.1 51.4,54.1 80.4,54.1 80.4,51.6 "/>
                                    </svg>
                                </div>
                                <div class="accordion-info">
                                    <?php echo $d_info; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
    <?php
}

/**
 * Add share icons next to gallery
 */
// add_action( 'woocommerce_product_thumbnails', 'cgd_share_buttons', 36 );
function cgd_share_buttons()
{
    global $product;
    ?>
    <div class="thumb-share-icons">
        <div class="facebook-share">
            <a class="share" href="http://www.facebook.com/sharer.php?u=<?php echo get_the_permalink($product->id); ?>" target="_blank" title="<?php echo get_the_title($product->id); ?>">
                <svg width="60px" height="60px" viewBox="0 0 60 60" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <!-- Generator: Sketch 41.2 (35397) - http://www.bohemiancoding.com/sketch -->
                    <title>facebook</title>
                    <desc>Created with Sketch.</desc>
                    <defs></defs>
                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g id="facebook">
                            <rect id="bg" fill="#CCCCCC" x="0" y="0" width="60" height="60"></rect>
                            <path d="M41.28,22.4416667 L34.1666667,22.4416667 L34.1666667,19.2666667 C34.1666667,17.7733333 35.1566667,17.425 35.8533333,17.425 L40.8333333,17.425 L40.8333333,10.855 L33.6183333,10.8333333 C27.0733333,10.8333333 25.8333333,15.73 25.8333333,18.8666667 L25.8333333,22.44 L20.8333333,22.44 L20.8333333,29.1066667 L25.8333333,29.1066667 L25.8333333,49.1066667 L34.1666667,49.1066667 L34.1666667,29.1066667 L40.585,29.1066667 L41.28,22.4416667 Z" id="facebook-icon" fill="#FFFFFF"></path>
                        </g>
                    </g>
                </svg>
            </a>
        </div>
        <div class="pinterest-share">
            <svg width="60px" height="60px" viewBox="0 0 60 60" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <!-- Generator: Sketch 41.2 (35397) - http://www.bohemiancoding.com/sketch -->
                <title>pinterest</title>
                <desc>Created with Sketch.</desc>
                <defs></defs>
                <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                    <g id="pinterest">
                        <rect id="bg" fill="#CCCCCC" x="0" y="0" width="60" height="60"></rect>
                        <path d="M30.2283333,10.8333333 C19.7666667,10.8333333 14.4916667,18.3333333 14.4916667,24.5866667 C14.4916667,28.3733333 15.925,31.745 19,32.9983333 C19.505,33.205 19.9566667,33.005 20.1016667,32.4483333 C20.205,32.0633333 20.445,31.0883333 20.5516667,30.68 C20.6983333,30.1283333 20.6433333,29.9366667 20.235,29.4533333 C19.3483333,28.41 18.7816667,27.055 18.7816667,25.1366667 C18.7816667,19.5716667 22.945,14.59 29.6233333,14.59 C35.5366667,14.59 38.785,18.2033333 38.785,23.0266667 C38.785,29.375 35.975,34.7333333 31.8033333,34.7333333 C29.5,34.7333333 27.7766667,32.83 28.3283333,30.4916667 C28.99,27.7 30.2733333,24.6916667 30.2733333,22.6783333 C30.2733333,20.8766667 29.3066667,19.3733333 27.3033333,19.3733333 C24.9483333,19.3733333 23.0566667,21.8083333 23.0566667,25.0733333 C23.0566667,27.1516667 23.76,28.5566667 23.76,28.5566667 C23.76,28.5566667 21.3516667,38.7666667 20.9283333,40.555 C20.0866667,44.1166667 20.8016667,48.4816667 20.8616667,48.9216667 C20.8966667,49.1833333 21.235,49.2466667 21.385,49.0516667 C21.6016667,48.7666667 24.4066667,45.3016667 25.36,41.8433333 C25.63,40.8616667 26.9083333,35.79 26.9083333,35.79 C27.6733333,37.25 29.91,38.5333333 32.2883333,38.5333333 C39.3666667,38.5333333 44.1683333,32.0816667 44.1683333,23.445 C44.1666667,16.9166667 38.6366667,10.8333333 30.2283333,10.8333333 Z" id="facebook-icon" fill="#FFFFFF"></path>
                    </g>
                </g>
            </svg>
        </div>
    </div>
    <?php
}

/**
 * Edit Woo Breadcrumb
 */
// Rename "home" in breadcrumb
// add_filter( 'woocommerce_breadcrumb_defaults', 'wcc_change_breadcrumb_home_text' );
function wcc_change_breadcrumb_home_text($defaults)
{
    // Change the breadcrumb home text from 'Home' to 'Apartment'
    $defaults['home'] = 'Shop';
    return $defaults;
}
// Replace the home link URL
add_filter('woocommerce_breadcrumb_home_url', 'woo_custom_breadrumb_home_url');
function woo_custom_breadrumb_home_url()
{
    return home_url().'/shop/';
}

// Move shop count and sorting
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
add_action('ns_after_banner', 'cgd_shop_toolbar');
function cgd_shop_toolbar()
{
    global $product;

    if (function_exists('woocommerce_breadcrumb')) : ?>
        <?php if (is_shop() || is_product_category()) : ?>
            <div class="mobile-bread-crumb-wrap wrap">
                <?php woocommerce_breadcrumb(); ?>
            </div>

        <?php endif; ?>

    <?php endif;

    $term_id = get_queried_object()->term_id;
    $term_meta = get_term_meta($term_id);
    // $headline = $term_meta['headline'][0];
    $intro_text = $term_meta['intro_text'][0];
    if (! empty($headline)) {?>
        <h1 class="page-title"><?php echo( $headline ); ?></h1>
        <?php
    }
    if (! empty($intro_text)) {?>
        <div class="ns-archive-intro mobile">
            <div class="wrap">
                <?php echo( $intro_text ); ?>
            </div>
        </div>
        <?php
    }
    ?>

    <?php if (is_woocommerce()) : ?>
        <div class="toolbar">
            <div class="wrap">
                <?php if (!is_product()) : ?>
                    <div class="shop-sidebar-mobile-toggle">
                        <span class="primary-button">
                            <a href="#">Filter</a>
                        </span>
                    </div>
                <?php endif; ?>
                <?php if (function_exists('woocommerce_breadcrumb')) : ?>
                    <?php woocommerce_breadcrumb(); ?>
                <?php endif; ?>
                <?php if (is_shop() || is_product_category()) : ?>
                    <?php if (function_exists('woocommerce_result_count')) : ?>
                        <div class="woocommerce-result-count">
                            <?php echo do_shortcode('[facetwp counts="true"]') ?>
                        </div>
                    <?php endif; ?>
                    <?php if (function_exists('woocommerce_catalog_ordering')) : ?>
                        <?php woocommerce_catalog_ordering(); ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    <?php
}

// Change Variable product button text
// add_filter( 'woocommerce_product_add_to_cart_text', function( $text ) {
//     global $product;
//     if ( $product->is_type( 'variable' ) ) {
//         $text = $product->is_purchasable() ? __( 'Select Size', 'woocommerce' ) : __( 'Read more', 'woocommerce' );
//     }
//     return $text;
// }, 10 );

add_filter('facetwp_result_count', function ($output, $params) {
    $output = $params['lower'] . '-' . $params['upper'] . ' of ' . $params['total'] . ' results';
    return $output;
}, 10, 2);

// Add My Cart to header
add_filter('wp_nav_menu_items', 'woo_menu_items', 10, 2);
function woo_menu_items($items, $args)
{
    global $woocommerce;

    // return items if not the woo menu
    if ($args->menu->slug !== 'woo-menu') {
        return $items;
    }

    $user_id = get_current_user_id();
    $cart_url = $woocommerce->cart->get_cart_url();
    $cart_count = $woocommerce->cart->cart_contents_count;

    ob_start();
    ?>
    <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-hover cart-number"><a class="cart-ajax" href="<?php echo $cart_url; ?>" itemprop="url"><span class="menu-item-name" itemprop="name">My Cart</span><span class="menu-item-number"><?php echo $cart_count; ?></span></a></li>
    <?php

    $woo_items = ob_get_clean();
    return $items . $woo_items;
}
