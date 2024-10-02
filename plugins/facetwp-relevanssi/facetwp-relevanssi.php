<?php
/*
Plugin Name: FacetWP - Relevanssi integration
Description: Relevanssi integration for FacetWP
Version: 0.8.2
Author: FacetWP, LLC
Author URI: https://facetwp.com/
GitHub URI: facetwp/facetwp-relevanssi
*/

defined( 'ABSPATH' ) or exit;

class FacetWP_Relevanssi
{

    public $keywords;
    public $posts = [];
    public $first_run = true;


    function __construct() {
        add_action( 'init', [ $this, 'init' ] );
    }


    function init() {
        if ( function_exists( 'relevanssi_search' ) && function_exists( 'FWP' ) ) {
            add_filter( 'facetwp_is_main_query', [ $this, 'is_main_query' ], 10, 2 );
            add_action( 'pre_get_posts', [ $this, 'pre_get_posts' ], 1000, 2 );
            add_filter( 'posts_pre_query', [ $this, 'posts_pre_query' ], 10, 2 );
            add_filter( 'posts_results', [ $this, 'posts_results' ], 10, 2 );
            add_filter( 'facetwp_facet_filter_posts', [ $this, 'search_facet' ], 10, 2 );
            add_filter( 'facetwp_facet_search_engines', [ $this, 'search_engines' ] );
            add_filter( 'facetwp_settings_admin', [ $this, 'admin_settings' ], 10, 2 );
        }
    }


    /**
     * Run and cache the relevanssi_do_query()
     *
     * @return bool
     * @since 0.7
     */
    function is_main_query( $is_main_query, $query ) {

        $is_enabled = ( 'no' === FWP()->helper->get_setting( 'rel_disable_default_search', 'no' ) );

        if ( $query->is_main_query() && $query->is_search() && ! $is_enabled ) {
            return false;
        }
        elseif ( $is_main_query && $query->is_search() && ! empty( $query->get( 's' ) ) ) {
            $this->keywords = $query->get( 's' );
            $this->posts = $this->run_query( $this->keywords );
            $query->set( 'using_relevanssi', true );

            remove_filter( 'the_posts', 'relevanssi_query', 99, 2 ); // < 4.10.2
            remove_filter( 'posts_pre_query', 'relevanssi_query', 99 ); // 4.10.2 and above
            remove_filter( 'posts_request', 'relevanssi_prevent_default_request', 10 );
        }

        return $is_main_query;
    }


    /**
     * Modify FacetWP's render() query to use Relevanssi's results while bypassing
     * WP core search. We're using this additional query to support custom query
     * modifications, such as for FacetWP's sort box.
     *
     * The hook priority (1000) is important because this needs to run after
     * FacetWP_Request->update_query_vars()
     */
    function pre_get_posts( $query ) {
        if ( true === $query->get( 'using_relevanssi' ) ) {
            if ( true === $query->get( 'facetwp' ) && ! $this->first_run ) {
                $query->set( 's', '' );

                $post_ids = FWP()->filtered_post_ids;
                $post_ids = empty( $post_ids ) ? [ 0 ] : $post_ids;
                $query->set( 'post__in', $post_ids );

                if ( '' === $query->get( 'post_type' ) ) {
                    $query->set( 'post_type', 'any' );
                    $query->set( 'post_status', 'any' );
                }
                else {
                    $query->set( 'post_types', $query->get( 'post_type' ) );
                }

                if ( '' === $query->get( 'orderby' ) ) {
                    $query->set( 'orderby', 'post__in' );
                }
            }
        }
    }


    /**
     * If [facetwp => false] then it's the get_filtered_post_ids() query. Return
     * the raw Relevanssi results to prevent the additional query.
     *
     * If [facetwp => true] and [first_run => true] then it's the main WP query. Return
     * a non-null value to kill the query, since we don't use the results.
     *
     * If [facetwp => true] and [first_run => false] then it's the FacetWP render() query.
     */
    function posts_pre_query( $posts, $query ) {
        if ( true === $query->get( 'using_relevanssi' ) ) {
            if ( true === $query->get( 'facetwp' ) ) {
                $query->set( 's', $this->keywords );

                // kill the main WP query
                if ( $this->first_run ) {
                    $this->first_run = false;

                    $page = max( $query->get( 'paged' ), 1 );
                    $per_page = (int) $query->get( 'posts_per_page', get_option( 'posts_per_page' ) );
                    $query->found_posts = count( FWP()->filtered_post_ids );
                    $query->max_num_pages = ( 0 < $per_page ) ? ceil( $query->found_posts / $per_page ) : 0;

                    return [];
                }
            }
            else {
                return wp_list_pluck( $this->posts, 'ID' );
            }
        }

        return $posts;
    }


    /**
     * Apply highlighting
     */
    function posts_results( $posts, $query ) {
        if ( true === $query->get( 'using_relevanssi' ) ) {
            if ( true === $query->get( 'facetwp' ) && ! $this->first_run ) {

                // Create a post object lookup array
                // $lookup[post_id] => post_object
                $lookup = [];
                foreach ( $this->posts as $post ) {
                    $lookup[ $post->ID ] = $post;
                }

                // Swap out the "normal" post objects with the Relevanssi-formatted ones
                // This allows for custom excerpts, highlighting, etc.
                foreach ( $posts as $index => $post ) {
                    $posts[ $index ] = $lookup[ $post->ID ];
                }
            }
        }

        return $posts;
    }


    /**
     * For search facets, run relevanssi_do_query() and return the post IDs
     */
    function search_facet( $return, $params ) {
        $facet = $params['facet'];
        $selected_values = $params['selected_values'];
        $selected_values = is_array( $selected_values ) ? $selected_values[0] : $selected_values;

        if ( 'search' == $facet['type'] && 'relevanssi' == $facet['search_engine'] ) {
            $return = [];

            if ( empty( $selected_values ) ) {
                $return = 'continue';
            }
            elseif ( ! empty( FWP()->unfiltered_post_ids ) ) {
                if ( 'no' == $facet['enable_relevance'] ) {
                    add_filter( 'facetwp_use_search_relevancy', '__return_false' );
                }
                $return = $this->run_query( $selected_values, 'ids', FWP()->unfiltered_post_ids );
            }
        }

        return $return;
    }


    /**
     * Run relevanssi_do_query() and return the results
     */
    function run_query( $keywords, $fields = '', $post__in = [] ) {

        // get the max limit
        $limit = -1;

        if ( 'on' == get_option( 'relevanssi_throttle', 'off' ) ) {
            $limit = (int) get_option( 'relevanssi_throttle_limit', 500 );
        }

        // run the search
        $search = new WP_Query();
        $search->set( 's', $keywords );
        $search->set( 'paged', 1 );
        $search->set( 'post__in', $post__in );
        $search->set( 'posts_per_page', $limit );
        if ( $GLOBALS['wp_query']->is_search() ) {
            $search->set( 'post_type', get_query_var( 'post_type' ) );
        }
        $search->set( 'orderby', get_query_var( 'orderby' ) );
        $search->set( 'order', get_query_var( 'order' ) );
        $search->set( 'fields', $fields );

        do_action( 'facetwp_relevanssi_do_query', $search );
        relevanssi_do_query( $search );
        return $search->posts;
    }


    /**
     * Add engines to the search facet
     */
    function search_engines( $engines ) {
        $engines['relevanssi'] = 'Relevanssi';
        return $engines;
    }


    /**
     * Add admin settings
     */
    function admin_settings( $settings, $class ) {

        $settings['relevanssi']['label'] = 'Relevanssi';
        $settings['relevanssi']['fields']['rel_disable_default_search'] = [
            'label' => 'Disable Facet on default search page',
            'notes' => 'Prevents facet from use on default search page',
            'html' => $class->get_setting_html( 'rel_disable_default_search', 'toggle' )
        ];

        return $settings;
    }
}


new FacetWP_Relevanssi();
