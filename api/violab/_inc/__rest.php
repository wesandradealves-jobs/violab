<?php

    // - Endpoints



    // - /wp-json/customizer/v1/social | /wp-json/customizer/v1/bloginfo



    function get_customizer() {

        $social = array();

        

        if(get_theme_mod('facebook')):

            array_push($social,array('slug'=>'facebook','label'=>'Facebook','url'=>esc_url(get_theme_mod('facebook'))));

        endif;   



        if(get_theme_mod('instagram')):

            array_push($social,array('slug'=>'instagram','label'=>'Instagram','url'=>esc_url(get_theme_mod('instagram'))));

        endif;   



        if(get_theme_mod('spotify')):

            array_push($social,array('slug'=>'spotify','label'=>'Spotify','url'=>esc_url(get_theme_mod('spotify'))));

        endif;   



        if(get_theme_mod('youtube')):

            array_push($social,array('slug'=>'youtube','label'=>'Youtube','url'=>esc_url(get_theme_mod('youtube'))));

        endif;                           



        return $social;        

    }



    function get_customizer_bloginfo() {

        $theme = wp_get_theme();

        $arr = array(

            'logo'=>esc_html(get_theme_mod('logo')),

            'bloginfo' => array(
                'name' => get_bloginfo( 'name' ),
                'description' => get_bloginfo( 'description' ),
                'ogImage' => $theme->get_screenshot()
            ),

            'favico'=>get_site_icon_url(),

            'artist_id'=>get_theme_mod('artist_id'),

            'album_id'=>get_theme_mod('album_id')

        );        



        return $arr;        

    }    



    add_action( 'rest_api_init', function () {

        register_rest_route( 'customizer/v1', '/social', array(

            'methods' => 'GET',

            'callback' => 'get_customizer',

        ) );

        register_rest_route( 'customizer/v1', '/bloginfo', array(

            'methods' => 'GET',

            'callback' => 'get_customizer_bloginfo',

        ) );        

    } );     



    // - /wp-json/wp/v2/artigos?post_type=#### | /wp-json/wp/v2/posts



    class all_posts

    {

        public function __construct()

        {

            $version = '2';

            $namespace = 'wp/v' . $version;

            $base = 'artigos';

            register_rest_route($namespace, '/' . $base, array(

                'methods' => 'GET',

                'callback' => array($this, 'get_all_posts'),

            ));

        }



        public function get_all_posts($object)

        {

            $return = array();



            global $post;

         

            $myposts = get_posts( array(

                'posts_per_page' => -1,

                'post_type' => $_GET['post_type']

            ) );

         

            if ( $myposts ) {

                foreach ( $myposts as $post ) : 

                    setup_postdata( $post );

                    array_push($return, $post);

                endforeach;

                wp_reset_postdata();

            }      



            return new WP_REST_Response($return, 200);

        }

    }



    add_action('rest_api_init', function () {

        $all_posts = new all_posts;

    });



    // - Filters



    add_filter( 'woocommerce_rest_check_permissions', 'my_woocommerce_rest_check_permissions', 90, 4 );



    function my_woocommerce_rest_check_permissions( $permission, $context, $object_id, $post_type  ){

      return true;

    }



    add_action('rest_api_init', 'register_rest_images' );

    function register_rest_images(){

        register_rest_field( array('produtos'),

            'thumbnail',

            array(

                'get_callback'    => 'get_rest_featured_image',

                'update_callback' => null,

                'schema'          => null,

            )

        );
        register_rest_field( array('post'),

            'thumbnail',

            array(

                'get_callback'    => 'get_rest_featured_image',

                'update_callback' => null,

                'schema'          => null,

            )

        );

    }

    function get_rest_featured_image( $object, $field_name, $request ) {

        if( $object['featured_media'] ){

            $img = wp_get_attachment_image_src( $object['featured_media'], 'app-thumb' );

            return $img[0];

        }

        return false;

    } 



    add_filter( 'rest_endpoints', function( $endpoints ){

        if ( ! isset( $endpoints['/wp/v2/posts'] ) ) {

            return $endpoints;

        }

        $endpoints['/wp/v2/posts'][0]['args']['per_page']['default'] = 100;

        return $endpoints;

    });



    add_action( 'rest_api_init', 'add_custom_fields' );

    function add_custom_fields() {

        register_rest_field(

        'post', 

        'destaque', //New Field Name in JSON RESPONSEs

        array(

            'get_callback'    => 'get_custom_fields', // custom function name 

            'update_callback' => null,

            'schema'          => null,

             )

        );

    }



    function get_custom_fields( $object, $field_name, $request ) {

        return get_field( $field_name, $object['id'] );

    }    

    add_action('rest_api_init', function() {
        remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
    }, 15);

    function wpse_287931_register_categories_names_field() {
        register_rest_field( 'post',
            'categories',
            array(
                'get_callback'    => 'wpse_287931_get_categories_names',
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }

    function wpse_287931_register_categories_names_field_products() {
        register_rest_field( 'produtos',
            'categories',
            array(
                'get_callback'    => 'wpse_287931_get_categories_names_products',
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }

    add_action( 'rest_api_init', 'wpse_287931_register_categories_names_field_products' );

    add_action( 'rest_api_init', 'wpse_287931_register_categories_names_field' );

    function wpse_287931_get_categories_names_products( $object, $field_name, $request ) {

        $formatted_categories = array();

        $categories = wp_get_post_terms($object['id'], 'produtos_categorias_pt', array("fields" => "all"));

        foreach ($categories as $category) {
            $formatted_categories[] = $category->slug;
        }

        return $formatted_categories;
    }        

    function wpse_287931_get_categories_names( $object, $field_name, $request ) {

        $formatted_categories = array();

        $categories = get_the_category( $object['id'] );

        foreach ($categories as $category) {
            $formatted_categories[] = $category->slug;
        }

        return $formatted_categories;
    }    