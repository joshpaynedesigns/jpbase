var FWP_MAP = FWP_MAP || {};

(function($) {

    FWP_MAP.markersArray = [];
    FWP_MAP.markerLookup = {};
    FWP_MAP.is_filtering = false;
    FWP_MAP.is_zooming = false;
    FWP_MAP.map_loaded = false;

    // Get markers for a given post ID
    FWP_MAP.get_post_markers = function(post_id) {
        var output = [];
        if ('undefined' !== typeof FWP_MAP.markerLookup[post_id]) {
            var arrayOfIndexes = FWP_MAP.markerLookup[post_id];
            for (var i = 0; i < arrayOfIndexes.length; i++) {
                var index = FWP_MAP.markerLookup[post_id][i];
                output.push(FWP_MAP.markersArray[index]);
            }
        }
        return output;
    }

    FWP.hooks.addAction('facetwp/refresh/map', function($this, facet_name) {
        var selected_values = [];

        if (FWP_MAP.is_filtering) {
            selected_values = FWP_MAP.map.getBounds().toUrlValue().split(',');
        }

        FWP.facets[facet_name] = selected_values;
        FWP.frozen_facets[facet_name] = 'hard';
    });

    FWP.hooks.addAction('facetwp/reset', function() {
        $.each(FWP.facet_type, function(type, name) {
            if ('map' === type) {
                FWP.frozen_facets[name] = 'hard';
            }
        });
    });

    FWP.hooks.addFilter('facetwp/selections/map', function(label, params) {
        return FWP_JSON['map']['resetText'];
    });

    function do_refresh() {
        if (FWP_MAP.is_filtering && ! FWP_MAP.is_zooming) {
            FWP.autoload();
        }

        FWP_MAP.is_zooming = false;
    }

    $().on('click', '.facetwp-map-filtering', function() {
        var $this = $(this);

        if ($this.hasClass('enabled')) {
            $this.text(FWP_JSON['map']['filterText']);
            FWP_MAP.is_filtering = false;
            FWP.autoload();
        }
        else {
            $this.text(FWP_JSON['map']['resetText']);
            FWP_MAP.is_filtering = true;
            FWP.autoload();
        }

        $this.toggleClass('enabled');
    });

    FWP_MAP.init = function() {
        if ('undefined' === typeof FWP.settings.map || '' === FWP.settings.map) {
            return;
        }

        if ('object' !== typeof google || 'object' !== typeof google.maps) {
            return;
        }

        var config = FWP.settings.map.config;

        if (! FWP_MAP.map_loaded) {

            FWP_MAP.map = new google.maps.Map(document.getElementById('facetwp-map'), FWP.settings.map.init);
            FWP_MAP.infoWindow = new google.maps.InfoWindow();

            FWP_MAP.map.addListener('dragend', function() {
                do_refresh();
            });

            FWP_MAP.map.addListener('zoom_changed', function() {
                do_refresh();
            });

            window.addEventListener('resize', FWP.helper.debounce(function() {
                var center = FWP_MAP.map.getCenter();
                google.maps.event.trigger(FWP_MAP.map, 'resize');
                FWP_MAP.map.setCenter(center);
            }, 500));

            google.maps.event.addListener(FWP_MAP.map, 'click', function() {
                FWP_MAP.infoWindow.close();
            });

            FWP_MAP.oms = new OverlappingMarkerSpiderfier(FWP_MAP.map, config.spiderfy);
            FWP_MAP.map_loaded = true;
        }
        else {
            clearOverlays();
        }

        // this needs to re-init on each refresh
        FWP_MAP.bounds = new google.maps.LatLngBounds();
        FWP_MAP.contentCache = {};

        $.each(FWP.settings.map.locations, function(obj, idx) {
            var args = Object.assign({}, obj);
            args.map = FWP_MAP.map;

            var marker = new google.maps.Marker(args);

            google.maps.event.addListener(marker, 'spider_click', function() {
                if ('undefined' !== typeof marker.content) {
                    FWP_MAP.infoWindow.setContent(marker.content);
                }
                else if ('undefined' === typeof FWP_MAP.contentCache[args.post_id]) {
                    FWP_MAP.infoWindow.setContent('Loading...');
                    FWP_MAP.infoWindow.open(FWP_MAP.map, marker);

                    return $.post(FWP_JSON.map.ajaxurl, {
                        action: 'facetwp_map_marker_content',
                        facet_name: FWP_JSON.map.facet_name,
                        post_id: args.post_id
                    }, {
                        dataType: 'text',
                        contentType: 'application/x-www-form-urlencoded',
                        done: (resp) => {
                            FWP_MAP.contentCache[args.post_id] = resp;
                            FWP_MAP.infoWindow.setContent(resp);
                            FWP.hooks.doAction('facetwp_map/marker/click', marker);
                        }
                    });
                }
                else {
                    var data = FWP_MAP.contentCache[args.post_id];
                    FWP_MAP.infoWindow.setContent(data);
                }

                FWP_MAP.infoWindow.open(FWP_MAP.map, marker);

                // Custom click handler
                FWP.hooks.doAction('facetwp_map/marker/click', marker);
            });

            // Custom mouseover handler
            google.maps.event.addListener(marker, 'mouseover', function() {
                FWP.hooks.doAction('facetwp_map/marker/mouseover', marker);
            });

            // Custom mouseout handler
            google.maps.event.addListener(marker, 'mouseout', function() {
                FWP.hooks.doAction('facetwp_map/marker/mouseout', marker);
            });

            FWP_MAP.oms.addMarker(marker);
            FWP_MAP.markersArray.push(marker);
            FWP_MAP.bounds.extend(marker.getPosition());

            // Create an object to lookup markers based on post ID
            if ( 'undefined' !== typeof FWP_MAP.markerLookup[obj.post_id]) {
                FWP_MAP.markerLookup[obj.post_id].push(idx);
            }
            else {
                FWP_MAP.markerLookup[obj.post_id] = [idx];
            }
        });

        var has_results = FWP.settings.map.locations.length > 0;
        var fit_bounds = FWP.hooks.applyFilters('facetwp_map/fit_bounds', ! FWP_MAP.is_filtering);

        if (fit_bounds && has_results) {
            FWP_MAP.map.fitBounds(FWP_MAP.bounds);
        }
        else if (! FWP_MAP.is_filtering && (0 !== config.default_lat || 0 !== config.default_lng)) {
            FWP_MAP.map.setCenter({
                lat: parseFloat(config.default_lat),
                lng: parseFloat(config.default_lng)
            });
            FWP_MAP.is_zooming = true;
            FWP_MAP.map.setZoom(config.default_zoom);
        }

        if ('undefined' !== typeof config.cluster) {
            FWP_MAP.mc = new MarkerClusterer(FWP_MAP.map, FWP_MAP.markersArray, config.cluster);
        }

        $().trigger('facetwp-maps-loaded');
    };

    $().on('facetwp-loaded', function() {
        FWP_MAP.init();
    });

    // Clear markers
    function clearOverlays() {
        FWP_MAP.oms.removeAllMarkers();
        FWP_MAP.markersArray = [];
        FWP_MAP.markerLookup = {};

        // clear clusters
        if ('undefined' !== typeof FWP_MAP.mc) {
            FWP_MAP.mc.clearMarkers();
        }
    }

})(fUtil);
