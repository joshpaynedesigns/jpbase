<?php
/*
Plugin Name: FacetWP - Map Facet
Description: Map facet type
Version: 1.1
Author: FacetWP, LLC
Author URI: https://facetwp.com/
GitHub URI: facetwp/facetwp-map-facet
Text Domain: facetwp-map-facet
Domain Path: /languages
*/

defined( 'ABSPATH' ) or exit;

define( 'FACETWP_MAP_FACET_VERSION', '1.1' );


/**
 * FacetWP registration hook
 */
add_filter( 'facetwp_facet_types', function( $facet_types ) {
    $facet_types['map'] = new FacetWP_Facet_Map_Addon();
    return $facet_types;
});


/**
 * Hierarchy Select facet class
 */
class FacetWP_Facet_Map_Addon
{

    public $label;
    public $map_facet;
    public $proximity_facet;
    public $proximity_coords;


    function __construct() {
        $this->label = __( 'Map', 'facetwp-map-facet' );

        define( 'FACETWP_MAP_URL', plugins_url( '', __FILE__ ) );

        add_action( 'init', [ $this, 'load_textdomain' ] );
        add_filter( 'facetwp_index_row', [ $this, 'index_latlng' ], 1, 2 );
        add_filter( 'facetwp_render_output', [ $this, 'add_marker_data' ], 10, 2 );

        // ajax load of marker content
        add_action( 'facetwp_init', function() {
            if ( isset( $_POST['action'] ) && 'facetwp_map_marker_content' == $_POST['action'] ) {
                $post_id = (int) $_POST['post_id'];
                $facet_name = $_POST['facet_name'];

                echo $this->get_marker_content( $post_id, $facet_name );
                wp_die();
            }
        });
    }


    function load_textdomain() {
        load_plugin_textdomain( 'facetwp-map-facet', false, basename( dirname( __FILE__ ) ) . '/languages' );
    }


    function get_map_design( $slug ) {
        $designs = [
            'light-dream' => '[{"featureType":"landscape","stylers":[{"hue":"#FFBB00"},{"saturation":43.400000000000006},{"lightness":37.599999999999994},{"gamma":1}]},{"featureType":"road.highway","stylers":[{"hue":"#FFC200"},{"saturation":-61.8},{"lightness":45.599999999999994},{"gamma":1}]},{"featureType":"road.arterial","stylers":[{"hue":"#FF0300"},{"saturation":-100},{"lightness":51.19999999999999},{"gamma":1}]},{"featureType":"road.local","stylers":[{"hue":"#FF0300"},{"saturation":-100},{"lightness":52},{"gamma":1}]},{"featureType":"water","stylers":[{"hue":"#0078FF"},{"saturation":-13.200000000000003},{"lightness":2.4000000000000057},{"gamma":1}]},{"featureType":"poi","stylers":[{"hue":"#00FF6A"},{"saturation":-1.0989010989011234},{"lightness":11.200000000000017},{"gamma":1}]}]',
            'avocado-world' => '[{"featureType":"water","elementType":"geometry","stylers":[{"visibility":"on"},{"color":"#aee2e0"}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"color":"#abce83"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"color":"#769E72"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#7B8758"}]},{"featureType":"poi","elementType":"labels.text.stroke","stylers":[{"color":"#EBF4A4"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"visibility":"simplified"},{"color":"#8dab68"}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#5B5B3F"}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"color":"#ABCE83"}]},{"featureType":"road","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#A4C67D"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#9BBF72"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#EBF4A4"}]},{"featureType":"transit","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#87ae79"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#7f2200"},{"visibility":"off"}]},{"featureType":"administrative","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"},{"visibility":"on"},{"weight":4.1}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#495421"}]},{"featureType":"administrative.neighborhood","elementType":"labels","stylers":[{"visibility":"off"}]}]',
            'blue-water' => '[{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]}]',
            'midnight-commander' => '[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"color":"#000000"},{"lightness":13}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#144b53"},{"lightness":14},{"weight":1.4}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#08304b"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#0c4152"},{"lightness":5}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#0b434f"},{"lightness":25}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"road.arterial","elementType":"geometry.stroke","stylers":[{"color":"#0b3d51"},{"lightness":16}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"}]},{"featureType":"transit","elementType":"all","stylers":[{"color":"#146474"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#021019"}]}]',
        ];

        return isset( $designs[ $slug ] ) ? json_decode( $designs[ $slug ] ) : '';
    }


    function get_gmaps_url() {

        // hard-coded
        $api_key = defined( 'GMAPS_API_KEY' ) ? GMAPS_API_KEY : '';

        // admin ui
        $tmp_key = FWP()->helper->get_setting( 'gmaps_api_key' );
        $api_key = empty( $tmp_key ) ? $api_key : $tmp_key;

        // API key hook
        $api_key = apply_filters( 'facetwp_gmaps_api_key', $api_key );

        // URL hook (e.g. for adding a "region" param)
        $url = '//maps.googleapis.com/maps/api/js?libraries=places&key=' . $api_key . '&callback=Function.prototype';

        return apply_filters( 'facetwp_gmaps_url', $url );
    }


    /**
     * Generate the facet HTML
     */
    function render( $params ) {

        $width = $params['facet']['map_width'];
        $width = empty( $width ) ? 600 : $width;
        $width = is_numeric( $width ) ? $width . 'px' : $width;

        $height = $params['facet']['map_height'];
        $height = empty( $height ) ? 300 : $height;
        $height = is_numeric( $height ) ? $height . 'px' : $height;

        $class = '';
        $btn_label = ( isset( $params['facet']['btn_label'] ) && '' != $params['facet']['btn_label'] ) ? $params['facet']['btn_label'] :  __( 'Enable map filtering', 'facetwp-map-facet' );

        if ( $this->is_map_filtering_enabled() ) {
            $class = ' enabled';
            $btn_label = ( isset( $params['facet']['reset_label'] ) && '' != $params['facet']['reset_label'] ) ? $params['facet']['reset_label'] : __( 'Reset', 'facetwp-map-facet' ) ;
        }

        $output = '<div id="facetwp-map" style="width:' . $width . '; height:' . $height . '"></div>';
        $output .= '<div><button class="facetwp-map-filtering' . $class . '">' . esc_html( facetwp_i18n( $btn_label ) ) . '</button></div>';
        return $output;
    }


    /**
     * Is filtering turned on for the map?
     * @return bool
     */
    function is_map_filtering_enabled() {
        foreach ( FWP()->facet->facets as $facet ) {
            if ( 'map' == $facet['type'] && ! empty( $facet['selected_values'] ) ) {
                return true;
            }
        }

        return false;
    }


    /**
     * Is a proximity facet in use? If so, return a lat/lng array
     * @return mixed array of coordinates, or FALSE
     */
    function is_proximity_in_use() {
        foreach ( FWP()->facet->facets as $facet ) {
            if ( 'proximity' == $facet['type'] && ! empty( $facet['selected_values'] ) ) {
                $this->proximity_facet = $facet;

                return [
                    'lat'       => (float) $facet['selected_values'][0],
                    'lng'       => (float) $facet['selected_values'][1],
                    'radius'    => (int) $facet['selected_values'][2]
                ];
            }
        }

        return false;
    }


    function add_marker_data( $output, $params ) {
        if ( ! $this->is_map_active() ) {
            return $output;
        }

        // Exit if paging and limit = "all"
        if ( 0 < (bool) FWP()->facet->ajax_params['soft_refresh'] ) {
            if ( 'all' == FWP()->helper->facet_is( $this->map_facet, 'limit', 'all' ) ) {
                $output['settings']['map'] = '';
                return $output;
            }
        }

        $settings = [
            'locations' => []
        ];

        $settings['config'] = [
            'default_lat'   => (float) $this->map_facet['default_lat'],
            'default_lng'   => (float) $this->map_facet['default_lng'],
            'default_zoom'  => (int) $this->map_facet['default_zoom'],
            'spiderfy'      => [
                'markersWontMove'   => true,
                'markersWontHide'   => true,
                'basicFormatEvents' => true,
                'keepSpiderfied'    => true
            ]
        ];

        if ( 'yes' == $this->map_facet['cluster'] ) {
            $settings['config']['cluster'] = [
                'imagePath' => FACETWP_MAP_URL . '/assets/img/m',
                'imageExtension' => 'png',
                'zoomOnClick' => false,
                'maxZoom' => 15,
            ];
        }

        $settings['init'] = [
            'gestureHandling' => 'auto',
            'styles' => $this->get_map_design( $this->map_facet['map_design'] ),
            'zoom' => (int) $this->map_facet['default_zoom'] ?: 5,
            'minZoom' => (int) $this->map_facet['min_zoom'] ?: 1,
            'maxZoom' => (int) $this->map_facet['max_zoom'] ?: 20,
            'center' => [
                'lat' => (float) $this->map_facet['default_lat'],
                'lng' => (float) $this->map_facet['default_lng'],
            ],
        ];

        $settings = apply_filters( 'facetwp_map_init_args', $settings );

        // Get the proximity facet's coordinates (if available)
        $this->proximity_coords = $this->is_proximity_in_use();

        if ( false !== $this->proximity_coords ) {
            $marker_args = [
                'content' => __( 'Your location', 'facetwp-map-facet' ),
                'position' => $this->proximity_coords,
                'icon' => [
                    'path' => 'M8,0C3.582,0,0,3.582,0,8s8,24,8,24s8-19.582,8-24S12.418,0,8,0z M8,12c-2.209,0-4-1.791-4-4 s1.791-4,4-4s4,1.791,4,4S10.209,12,8,12z',
                    'fillColor' => 'gold',
                    'fillOpacity' => 0.8,
                    'scale' => 0.8,
                    'anchor' => [ 'x' => 8.5, 'y' => 32 ]
                ]
            ];

            $marker_args = apply_filters( 'facetwp_map_proximity_marker_args', $marker_args );

            if ( ! empty( $marker_args ) ) {
                $settings['locations'][] = $marker_args;
            }
        }

        // get all post IDs
        if ( isset( $this->map_facet['limit'] ) && 'all' == $this->map_facet['limit'] ) {
            $post_ids = isset( FWP()->filtered_post_ids ) ?
                FWP()->filtered_post_ids :
                FWP()->facet->query_args['post__in'];
        }
        // get paginated post IDs
        else {
            $post_ids = (array) wp_list_pluck( FWP()->facet->query->posts, 'ID' );
        }

        // remove duplicates
        $post_ids = array_unique( $post_ids );

        $all_coords = $this->get_coordinates( $post_ids, $this->map_facet );

        foreach ( $post_ids as $post_id ) {
            if ( isset( $all_coords[ $post_id ] ) ) {
                foreach ( $all_coords[ $post_id ] as $coords ) {
                    $args = [
                        'position' => $coords,
                        'post_id' => $post_id,
                    ];

                    if ( 'yes' !== $this->map_facet['ajax_markers'] ) {
                        $args['content'] = $this->get_marker_content( $post_id );
                    }

                    $args = apply_filters( 'facetwp_map_marker_args', $args, $post_id );

                    if ( false !== $args ) {
                        $settings['locations'][] = $args;
                    }
                }
            }
        }

        $output['settings']['map'] = $settings;

        return $output;
    }


    /**
     * Grab all coordinates from the index table
     */
    function get_coordinates( $post_ids, $facet ) {
        global $wpdb;

        $output = [];

        if ( ! empty( $post_ids ) ) {
            $post_ids = implode( ',', $post_ids );

            $sql = "
            SELECT post_id, facet_value AS lat, facet_display_value AS lng
            FROM {$wpdb->prefix}facetwp_index
            WHERE facet_name = '{$facet['name']}' AND post_id IN ($post_ids)";

            $result = $wpdb->get_results( $sql );

            foreach ( $result as $row ) {
                $output[ $row->post_id ][] = [
                    'lat' => (float) $row->lat,
                    'lng' => (float) $row->lng,
                ];
            }

            // Support ACF repeaters
            if ( false !== $this->proximity_coords) {
                foreach ( $output as $post_id => $coords ) {
                    if ( 1 < count( $coords ) ) {
                        $output[ $post_id ] = [];

                        foreach ( $coords as $latlng ) {
                            if ( $this->is_within_bounds( $latlng ) ) {
                                $output[ $post_id ][] = $latlng;
                            }
                        }
                    }
                }
            }
        }

        return $output;
    }


    /**
     * Is the current point within the proximity bounds?
     */
    function is_within_bounds( $latlng ) {
        $lat1 = $latlng['lat'];
        $lng1 = $latlng['lng'];
        $lat2 = $this->proximity_coords['lat'];
        $lng2 = $this->proximity_coords['lng'];

        $radius = $this->proximity_coords['radius'];
        $unit = $this->proximity_facet['unit'];

        if ( $lat1 == $lat2 && $lng1 == $lng2 ) {
            return true;
        }

        $dist = sin( deg2rad( $lat1 ) ) * sin( deg2rad( $lat2 ) ) + cos( deg2rad( $lat1 ) ) * cos( deg2rad( $lat2 ) ) * cos( deg2rad( $lng1 - $lng2 ) );
        $dist = min( max( $dist, -1 ), 1 ); // force value between -1 and 1
        $dist = rad2deg( acos( $dist ) );

        $miles = $dist * 60 * 1.1515;
        $needle = ( 'km' == $unit ) ? $miles * 1.609344 : $miles;
        return $needle <= $radius;
    }


    /**
     * Is this page using a map facet?
     */
    function is_map_active() {
        foreach ( FWP()->facet->facets as $name => $facet ) {
            if ( 'map' == $facet['type'] ) {
                $this->map_facet = $facet; // save the facet
                return true;
            }
        }

        return false;
    }


    /**
     * Get marker content (pulled via ajax)
     */
    function get_marker_content( $post_id, $facet_name = false ) {
        if ( false !== $facet_name ) {
            $facet = FWP()->helper->get_facet_by_name( $facet_name );
            $content = $facet['marker_content'];
        }
        else {
            $content = $this->map_facet['marker_content'];
        }

        if ( empty( $content ) ) {
            return '';
        }

        global $post;

        ob_start();

        // Set the main $post object
        $post = get_post( $post_id );

        setup_postdata( $post );

        // Remove UTF-8 non-breaking spaces
        $html = preg_replace( "/\xC2\xA0/", ' ', $content );

        eval( '?>' . $html );

        // Reset globals
        wp_reset_postdata();

        // Store buffered output
        return ob_get_clean();
    }


    /**
     * Filter the query based on the map bounds
     */
    function filter_posts( $params ) {
        global $wpdb;

        $facet = $params['facet'];
        $selected_values = (array) $params['selected_values'];

        $swlat = (float) $selected_values[0];
        $swlng = (float) $selected_values[1];
        $nelat = (float) $selected_values[2];
        $nelng = (float) $selected_values[3];

        // @url https://stackoverflow.com/a/35944747
        if ( $swlng < $nelng ) {
            $compare_lng = "facet_display_value BETWEEN $swlng AND $nelng";
        }
        else {
            $compare_lng = "facet_display_value BETWEEN $swlng AND 180 OR ";
            $compare_lng .= "facet_display_value BETWEEN -180 AND $nelng";
        }

        $sql = "
        SELECT DISTINCT post_id FROM {$wpdb->prefix}facetwp_index
        WHERE facet_name = '{$facet['name']}' AND
        facet_value BETWEEN $swlat AND $nelat AND ($compare_lng)";

        return $wpdb->get_col( $sql );
    }


    /**
     * Output any front-end scripts
     */
    function front_scripts() {
        FWP()->display->assets['gmaps'] = $this->get_gmaps_url();
        FWP()->display->assets['oms'] = [ FACETWP_MAP_URL . '/assets/js/oms.min.js', FACETWP_MAP_FACET_VERSION ];
        FWP()->display->assets['markerclusterer'] = [ FACETWP_MAP_URL . '/assets/js/markerclusterer.js', FACETWP_MAP_FACET_VERSION ];
        FWP()->display->assets['facetwp-map-front'] = [ FACETWP_MAP_URL . '/assets/js/front.js', FACETWP_MAP_FACET_VERSION ];

        $btn_label = ( isset( $this->map_facet['btn_label'] ) && '' != $this->map_facet['btn_label'] ) ? $this->map_facet['btn_label'] : __( 'Enable map filtering', 'facetwp-map-facet' ) ;
        FWP()->display->json['map']['filterText'] = facetwp_i18n( $btn_label );

        $reset_label = ( isset( $this->map_facet['reset_label'] ) && '' != $this->map_facet['reset_label'] ) ? $this->map_facet['reset_label'] : __( 'Reset', 'facetwp-map-facet');
        FWP()->display->json['map']['resetText'] = facetwp_i18n( $reset_label );
        FWP()->display->json['map']['facet_name'] = $this->map_facet['name'];
        FWP()->display->json['map']['ajaxurl'] = admin_url( 'admin-ajax.php' );
    }


    /**
    * Output admin settings HTML
    */
    function settings_html() {
        $sources = FWP()->helper->get_data_sources();
?>
        <div class="facetwp-row">
            <div>
                <div class="facetwp-tooltip">
                    <?php _e( 'Longitude', 'facetwp-map-facet' ); ?>:
                    <div class="facetwp-tooltip-content"><?php _e( '(Optional) use a separate longitude field', 'facetwp-map-facet' ); ?></div>
                </div>
            </div>
            <div>
                <data-sources
                    :facet="facet"
                    setting-name="source_other">
                </data-sources>
            </div>
        </div>
        <div class="facetwp-row">
            <div><?php _e( 'Map design', 'facetwp-map-facet' ); ?>:</div>
            <div>
                <select class="facet-map-design">
                    <option value="default"><?php _e( 'Default', 'facetwp-map-facet' ); ?></option>
                    <option value="light-dream"><?php _e( 'Light Dream', 'facetwp-map-facet' ); ?></option>
                    <option value="avocado-world"><?php _e( 'Avocado World', 'facetwp-map-facet' ); ?></option>
                    <option value="blue-water"><?php _e( 'Blue Water', 'facetwp-map-facet' ); ?></option>
                    <option value="midnight-commander"><?php _e( 'Midnight Commander', 'facetwp-map-facet' ); ?></option>
                </select>
            </div>
        </div>
        <div class="facetwp-row">
            <div><?php _e( 'Enable filtering button', 'facetwp-map-facet' ); ?>:</div>
            <div>
                <input type="text" class="facet-btn-label" placeholder="<?php echo facetwp_i18n( __( 'Enable map filtering', 'facetwp-map-facet' ) ); ?>" />
            </div>
        </div>
        <div class="facetwp-row">
            <div><?php _e( 'Reset button', 'facetwp-map-facet' ); ?>:</div>
            <div>
                <input type="text" class="facet-reset-label" placeholder="<?php  echo facetwp_i18n( __( 'Reset', 'facetwp-map-facet' ) ); ?>" />
            </div>
        </div>
        <div class="facetwp-row">
            <div>
                <div class="facetwp-tooltip">
                    <?php _e( 'Marker clustering', 'facetwp-map-facet' ); ?>:
                    <div class="facetwp-tooltip-content"><?php _e( 'Group markers into clusters?', 'facetwp-map-facet' ); ?></div>
                </div>
            </div>
            <div>
                <label class="facetwp-switch">
                    <input type="checkbox" class="facet-cluster" true-value="yes" false-value="no" />
                    <span class="facetwp-slider"></span>
                </label>
            </div>
        </div>
        <div class="facetwp-row">
            <div>
                <div class="facetwp-tooltip">
                    <?php _e( 'Ajax marker content', 'facetwp-map-facet' ); ?>:
                    <div class="facetwp-tooltip-content"><?php _e( 'Dynamically load marker content, which could improve load times for pages containing many map markers', 'facetwp-map-facet' ); ?></div>
                </div>
            </div>
            <div>
                <label class="facetwp-switch">
                    <input type="checkbox" class="facet-ajax-markers" true-value="yes" false-value="no" />
                    <span class="facetwp-slider"></span>
                </label>
            </div>
        </div>
        <div class="facetwp-row">
            <div><?php _e( 'Marker limit', 'facetwp-map-facet' ); ?>:</div>
            <div>
                <select class="facet-limit">
                    <option value="all"><?php _e( 'Show all results', 'facetwp-map-facet' ); ?></option>
                    <option value="paged"><?php _e( 'Show current page results', 'facetwp-map-facet' ); ?></option>
                </select>
            </div>
        </div>
        <div class="facetwp-row">
            <div><?php _e( 'Map width / height', 'facetwp-map-facet' ); ?>:</div>
            <div>
                <input type="text" class="facet-map-width" placeholder="<?php _e( 'Width', 'facetwp-map-facet' ); ?>" style="width:96px" />
                <input type="text" class="facet-map-height" placeholder="<?php _e( 'Height', 'facetwp-map-facet' ); ?>" style="width:96px" />
            </div>
        </div>
        <div class="facetwp-row">
            <div>
                <div class="facetwp-tooltip">
                    <?php _e( 'Zoom min / max', 'facetwp-map-facet' ); ?>:
                    <div class="facetwp-tooltip-content"><?php _e( 'Set zoom bounds (between 1 and 20)?', 'facetwp-map-facet' ); ?></div>
                </div>
            </div>
            <div>
                <input type="text" class="facet-min-zoom" value="1" placeholder="<?php _e( 'Min', 'facetwp-map-facet' ); ?>" style="width:96px" />
                <input type="text" class="facet-max-zoom" value="20" placeholder="<?php _e( 'Max', 'facetwp-map-facet' ); ?>" style="width:96px" />
            </div>
        </div>
        <div class="facetwp-row">
            <div>
                <div class="facetwp-tooltip">
                    <?php _e( 'Fallback lat / lng / zoom', 'facetwp-map-facet' ); ?>:
                    <div class="facetwp-tooltip-content"><?php _e( 'Center the map here if there are no results', 'facetwp-map-facet' ); ?></div>
                </div>
            </div>
            <div>
                <input type="text" class="facet-default-lat" placeholder="<?php _e( 'Latitude', 'facetwp-map-facet' ); ?>" style="width:96px" />
                <input type="text" class="facet-default-lng" placeholder="<?php _e( 'Longitude', 'facetwp-map-facet' ); ?>" style="width:96px" />
                <input type="text" class="facet-default-zoom" placeholder="<?php _e( 'Zoom (1-20)', 'facetwp-map-facet' ); ?>" style="width:96px" />
            </div>
        </div>
        <div class="facetwp-row">
            <div><?php _e( 'Marker content', 'facetwp-map-facet' ); ?>:</div>
            <div>
                <textarea class="facet-marker-content" id="marker-content-editor" v-model="facet.marker_content"></textarea>
                <p class="note"><?php _e( 'To search your code, click in the editor, then use Ctrl+F (Windows/Linux) or Cmd+F (Mac).', 'facetwp-map-facet' ); ?></p>
            </div>
        </div>
<?php
    }


    /**
     * Index the coordinates
     * We expect a comma-separated "latitude, longitude"
     */
    function index_latlng( $params, $class ) {

        $facet = FWP()->helper->get_facet_by_name( $params['facet_name'] );

        if ( false !== $facet && 'map' == $facet['type'] ) {
            $latlng = $params['facet_value'];

            // Only handle "lat, lng" strings
            if ( is_string( $latlng ) ) {
                $latlng = preg_replace( '/[^0-9.,-]/', '', $latlng );

                if ( ! empty( $facet['source_other'] ) ) {
                    $other_params = $params;
                    $other_params['facet_source'] = $facet['source_other'];
                    $rows = $class->get_row_data( $other_params );

                    if ( false === strpos( $latlng, ',' ) && ! empty( $rows ) ) {
                        $lng = $rows[0]['facet_display_value'];
                        $lng = preg_replace( '/[^0-9.,-]/', '', $lng );
                        $latlng .= ',' . $lng;
                    }
                }

                if ( preg_match( "/^([\d.-]+),([\d.-]+)$/", $latlng ) ) {
                    $latlng = explode( ',', $latlng );
                    $params['facet_value'] = $latlng[0];
                    $params['facet_display_value'] = $latlng[1];
                }
            }
        }

        return $params;
    }
}
