<?php 

    require_once('_inc/__php-config.php');

    require_once('_inc/__cpt.php');

    require_once('_inc/__widgets.php');

    require_once('_inc/__scripts.php');

    require_once('_inc/__admin-styles.php');

    require_once('_inc/__menus.php');

    require_once('_inc/__customizer.php');

    require_once('_inc/__rest.php');

    remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); 
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' ); 
    remove_action( 'wp_print_styles', 'print_emoji_styles' ); 
    remove_action( 'admin_print_styles', 'print_emoji_styles' );

    function custom_pagination( $numpages = '', $pagerange = '', $page='' ) {



      if (empty($pagerange)) {

        $pagerange = 2;

      }



      if (empty($page)) {

        $page = 1;

      }

      if ($numpages == '') {

        global $wp_query;

        $numpages = $wp_query->max_num_pages;

        if(!$numpages) {

            $numpages = 1;

        }

      }



      $pagination_args = array(

        'base'               => '%_%',

        'format'             => '?page=%#%',

        'total'           => $numpages,

        'current' => max( 1, get_query_var('page') ),

        'show_all'        => False,

        'end_size'        => 1,

        'mid_size'        => 2,

        'prev_next'       => false,

        'prev_text'       => '',

        'next_text'       => '',

        'type'            => 'plain',

        'add_args'        => false,

        'add_fragment'    => ''

      );



      $paginate_links = paginate_links($pagination_args);



      if ($paginate_links) {

        echo "<nav class='nav-links'>";

          echo $paginate_links;

        echo "</nav>";

      }



    }



    if (!is_admin()) {

        function wpb_search_filter($query) {

        if ($query->is_search) {

            $query->set('post_type', array('produtos', 'post'));

        }

        return $query;

        }

        add_filter('pre_get_posts','wpb_search_filter');

    }



    function title_filter( $where, $wp_query )

    {

        global $wpdb;

        if ( $search_term = $wp_query->get( 'post_title' ) ) {

            $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $search_term ) ) . '%\'';

        }

        return $where;

    }



    add_filter( 'posts_where', 'title_filter', 10, 2 );





    // function wpse18703_posts_where( $where, &$wp_query )

    // {

    //     global $wpdb;

    //     if ( $post_title = $wp_query->get( 'post_title' ) ) {

    //         $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'' . esc_sql( $wpdb->esc_like( $post_title ) ) . '%\'';

    //     }

    //     return $where;

    // }



    function my_post_queries( $query ) {

      // do not alter the query on wp-admin pages and only alter it if it's the main query

      if (!is_admin() && $query->is_main_query()){



        // alter the query for the home and category pages 



        if(is_category() || is_archive() || is_search()){

          $query->set('posts_per_page', 10);

        }



      }

    }

    add_action( 'pre_get_posts', 'my_post_queries' );  





    function is_login_page() {



        return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));



    }



    function hide_editor() {

        $post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;

        if( !isset( $post_id ) ) return;

        $template_file = get_post_meta($post_id, '_wp_page_template', true);

        // add_post_type_support( 'page', 'excerpt' );

        if($template_file == 'page-templates/home.php' || $template_file == 'page-templates/a-ype.php'){ 

            remove_post_type_support('page', 'editor');

            remove_post_type_support('page', 'excerpt');

        }

        if($template_file == 'page-templates/produtos.php' || $template_file == 'page-templates/guia-de-limpeza.php'){ 

            remove_post_type_support('page', 'editor');

        }        

        remove_post_type_support('page', 'thumbnail');

        // remove_post_type_support('page', 'editor');

        // remove_post_type_support('page', 'author');

        // remove_post_type_support('page', 'comments');

        // remove_post_type_support('page', 'revisions');

        // remove_post_type_support('page', 'custom-fields');    

    }    



    add_filter('query_vars', 'add_my_var');



    function add_my_var($public_query_vars) {



        $public_query_vars[] = 'paged';



        return $public_query_vars;



    }



    function get_custom_field_data($key, $echo = false) {



		$value = get_post_meta($post->ID, $key, true);



		if($echo == false) {



		return $value;



		} else {



		echo $value;



		}



	}

    function wpse_mime_types_webp( $mimes ) {
        $mimes['webp'] = 'image/webp';
        $mimes['svg'] = 'image/svg+xml';

      return $mimes;
    }


	function df_terms_clauses($clauses, $taxonomy, $args) {



        if (!empty($args['post_type'])) {



        global $wpdb;



        $post_types = array();



        foreach($args['post_type'] as $cpt) {



        $post_types[] = "'".$cpt."'";



        }



        if(!empty($post_types)) {



        $clauses['fields'] = 'DISTINCT '.str_replace('tt.*', 'tt.term_taxonomy_id, tt.term_id, tt.taxonomy, tt.description, tt.parent', $clauses['fields']).', COUNT(t.term_id) AS count';



        $clauses['join'] .= ' INNER JOIN '.$wpdb->term_relationships.' AS r ON r.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN '.$wpdb->posts.' AS p ON p.ID = r.object_id';



        $clauses['where'] .= ' AND p.post_type IN ('.implode(',', $post_types).')';



        $clauses['orderby'] = 'GROUP BY t.term_id '.$clauses['orderby'];



        }



        }



        return $clauses;



	}



    function sanitize( $input, $setting ) {



        global $wp_customize;



    



        $control = $wp_customize->get_control( $setting->id );



    



        if ( array_key_exists( $input, $control->choices ) ) {



            return $input;



        } else {



            return $setting->default;



        }



	}



    function count_post_visits() {

        if( is_single() ) {

            global $post;

            $views = get_post_meta( $post->ID, 'my_post_viewed', true );

            if( $views == '' ) {

            update_post_meta( $post->ID, 'my_post_viewed', '1' );

            } else {

            $views_no = intval( $views );

            update_post_meta( $post->ID, 'my_post_viewed', ++$views_no );

            }

        }

    }





    function remove_thumbnail_support(){



        remove_post_type_support('page','comments');



        remove_post_type_support('page','revisions');



        remove_post_type_support('post','comments');



        remove_post_type_support('post','revisions');



    }



    add_action('init','remove_thumbnail_support');



	add_theme_support( 'post-thumbnails' );



    add_post_type_support( 'page', 'excerpt' );



    add_action( 'wp_head', 'count_post_visits' );



	add_action( 'init', 'menu' );



	add_action( 'init', 'the_widgets_init' );



	add_action( 'admin_menu', 'remove_menus' );



	add_action( 'wp_enqueue_scripts', 'regScripts' );



	add_filter( 'show_admin_bar', 'hide_admin_bar' );



	add_filter( 'contextual_help', 'mytheme_remove_help_tabs', 999, 3 );



	add_action( 'customize_register', 'remove_customizer_settings', 20 );



	add_action( 'customize_register', 'customizer' );



	// add_filter('screen_options_show_screen', '__return_false'); 	



	add_filter('terms_clauses', 'df_terms_clauses', 10, 3);



    add_action( 'admin_init', 'hide_editor' );

    

    add_action( 'init', 'gp_register_taxonomy_for_object_type' );



    // add_filter( 'posts_where', 'wpse18703_posts_where', 10, 2 );

    add_filter( 'upload_mimes', 'wpse_mime_types_webp' );
    



