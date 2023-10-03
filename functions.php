<?php 
    require get_theme_file_path('/includes/search-route.php');

    function university_custom_rest() {
        register_rest_field('post', 'authorName', array(
            'get_callback' => function() {
                return get_the_author();
            }
        ));
    }
    add_action('rest_api_init', 'university_custom_rest');

    function page_banner($args = NULL) {

        if (!isset($args['title'])) {
            $args['title'] = get_the_title();
        }

        if (!isset($args['subtitle'])) {
            $args['subtitle'] = get_field('page_banner_subtitle');
        }

        if (!isset($args['photo'])) {
            if (get_field('page_banner_background_image') && !is_archive() && !is_home()) {
                $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
            } else {
                $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
            }
        }
        ?>
        <div class="page-banner">
            <div class="page-banner__bg-image" style="background-image: url(
                <?php echo $args['photo'];
                ?>)">
            </div>
            <div class="page-banner__content container container--narrow">
                <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
                <div class="page-banner__intro">
                <p><?php echo $args['subtitle']; ?></p>
                </div>
            </div>
        </div>
        <?php
    }
    function university_files() {
        wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=AIzaSyDJ8W77AUTU5xTZ6rjHJtJVMzIqv61ifa0', NULL, 1.0, true);
        wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), 1.0, true);
        wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
        wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', );
        wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
        wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));

        wp_localize_script('main-university-js', 'universityData', array(
            'root_url' => get_site_url(),
        ));
    }

    add_action('wp_enqueue_scripts', 'university_files');

    function university_features() {
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_image_size('professorLandscape', 400, 260, true);
        add_image_size('professorPortrait', 480, 360, true);
        add_image_size('pageBanner', 1500, 350, true);
        register_nav_menu('headerMenuLocation', 'Header Menu Location');
        register_nav_menu('footerMenuLocation1', 'Footer Menu Location 1');
        register_nav_menu('footerMenuLocation2', 'Footer Menu Location 2');
    }
    add_action('after_setup_theme', 'university_features');

    function university_adjust_queries($query) {
        if (!is_admin() && is_post_type_archive('campus') && $query->is_main_query()) {
            $query->set('posts_per_page', -1);
        }
        
        if (!is_admin() && is_post_type_archive('program') && $query->is_main_query()) {
            $query->set('orderby', 'title');
            $query->set('order', 'ASC');
            $query->set('posts_per_page', -1);
        }

        if (!is_admin() && is_post_type_archive('event') && $query->is_main_query()) {
            $query->set('meta_key', 'event_date');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'ASC');
            $query->set('meta_query', array(
                array(
                    'key' => 'event_date', 
                    'compare' => '>=', 
                    'value' => date('Ymd'),
                    'type' => 'numeric'
                ) 
            ));
        }
    }
    add_action('pre_get_posts', 'university_adjust_queries');

    function university_map_key($api) {
        $api['key'] = 'AIzaSyDJ8W77AUTU5xTZ6rjHJtJVMzIqv61ifa0';
        return $api;
    }

    add_filter('acf/fields/google_map/api', 'university_map_key');


    // Redirect subscriber accounts to home page
    add_action('admin_init', 'redirect_subscriber');
    function redirect_subscriber() {
        $ourCurrentUser = wp_get_current_user();
        if (count($ourCurrentUser->roles) == 1 && $ourCurrentUser->roles[0] == 'subscriber') {
            wp_redirect(site_url('/'));
            exit;
        };
    }

    add_action('wp_loaded', 'no_subs_admin');
    function no_subs_admin() {
        $ourCurrentUser = wp_get_current_user();
        if (count($ourCurrentUser->roles) == 1 && $ourCurrentUser->roles[0] == 'subscriber') {
            show_admin_bar(false);
        };
    }

    // Customize login screen
    add_filter('login_headerurl', 'our_login_logo');
    function our_login_logo() {
        return esc_url(site_url());

    }

    add_action('login_enqueue_scripts', 'ourLoginCss');
    function ourLoginCss() {
        wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
        wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', );
        wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
        wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));

    }

    add_filter('login_headertitle', 'our_login_title');
    function our_login_title() {
        return get_bloginfo('name');
    }

    add_filter('ai1wm_exclude_content_from_export', 'ai1wm_exclude_content');

    function ai1wm_exclude_content($exclude) {
        $exclude[] = 'themes/fictional-university-theme/node_modules';
        return $exclude;
    }
?>