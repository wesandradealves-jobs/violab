<?php
function cpt() {
	function gp_register_taxonomy_for_object_type() {
		register_taxonomy_for_object_type( 'post_tag', 'produtos' );
		register_taxonomy_for_object_type( 'post_tag', 'tracks' );
	};

	register_post_type('tracks', array(
		'labels' => array(
		'name' => _x('Tracks', 'post type general name'),
		'singular_name' => _x('Track', 'post type singular name'),
		'add_new' => _x('Novo', 'Track'),
		'add_new_item' => __('Novo '.'Track'),
		'edit_item' => __('Editar '.'Track'),
		'new_item' => __('Novo '.'Track'),
		'view_item' => __('Ver '.'Track'),
		'search_items' => __('Buscar '.'Tracks'),
		'not_found' =>  __('Nada encontrado'),
		'not_found_in_trash' => __('Nada encontrado'),
		'parent_item_colon' => ''
		),
		'exclude_from_search' => false, // the important line here!
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => true,
		'show_in_nav_menus' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'show_in_rest' => true,
		'rest_base' => 'Tracks',
		'menu_position' => -2,
		'supports' => array('title')
	));	

	register_post_type('metodos', array(
		'labels' => array(
		'name' => _x('Métodos', 'post type general name'),
		'singular_name' => _x('Método', 'post type singular name'),
		'add_new' => _x('Novo', 'Método'),
		'add_new_item' => __('Novo '.'Método'),
		'edit_item' => __('Editar '.'Método'),
		'new_item' => __('Novo '.'Método'),
		'view_item' => __('Ver '.'Método'),
		'search_items' => __('Buscar '.'Métodos'),
		'not_found' =>  __('Nada encontrado'),
		'not_found_in_trash' => __('Nada encontrado'),
		'parent_item_colon' => ''
		),
		'exclude_from_search' => false, // the important line here!
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => true,
		'show_in_nav_menus' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'show_in_rest' => true,
		'rest_base' => 'Métodos',
		'menu_position' => -2,
		'supports' => array('title')
	));

	register_post_type('partituras', array(
		'labels' => array(
		'name' => _x('Partituras', 'post type general name'),
		'singular_name' => _x('Partitura', 'post type singular name'),
		'add_new' => _x('Novo', 'Partitura'),
		'add_new_item' => __('Novo '.'Partitura'),
		'edit_item' => __('Editar '.'Partitura'),
		'new_item' => __('Novo '.'Partitura'),
		'view_item' => __('Ver '.'Partitura'),
		'search_items' => __('Buscar '.'Partituras'),
		'not_found' =>  __('Nada encontrado'),
		'not_found_in_trash' => __('Nada encontrado'),
		'parent_item_colon' => ''
		),
		'exclude_from_search' => false, // the important line here!
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => true,
		'show_in_nav_menus' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'show_in_rest' => true,
		'rest_base' => 'Partituras',
		'menu_position' => -2,
		'supports' => array('title')
	));

	register_post_type('videos', array(
		'labels' => array(
		'name' => _x('Videos', 'post type general name'),
		'singular_name' => _x('Video', 'post type singular name'),
		'add_new' => _x('Novo', 'Video'),
		'add_new_item' => __('Novo '.'Video'),
		'edit_item' => __('Editar '.'Video'),
		'new_item' => __('Novo '.'Video'),
		'view_item' => __('Ver '.'Video'),
		'search_items' => __('Buscar '.'Videos'),
		'not_found' =>  __('Nada encontrado'),
		'not_found_in_trash' => __('Nada encontrado'),
		'parent_item_colon' => ''
		),
		'exclude_from_search' => false, // the important line here!
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => true,
		'show_in_nav_menus' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'show_in_rest' => true,
		'rest_base' => 'Videos',
		'menu_position' => -2,
		'supports' => array('title')
	));				

	register_post_type('produtos', array(
		'labels' => array(
		'name' => _x('Produtos', 'post type general name'),
		'singular_name' => _x('Produtos', 'post type singular name'),
		'add_new' => _x('Novo', 'Produtos'),
		'add_new_item' => __('Novo '.'Produtos'),
		'edit_item' => __('Editar '.'Produtos'),
		'new_item' => __('Novo '.'Produtos'),
		'view_item' => __('Ver '.'Produtos'),
		'search_items' => __('Buscar '.'Produtos'),
		'not_found' =>  __('Nada encontrado'),
		'not_found_in_trash' => __('Nada encontrado'),
		'parent_item_colon' => ''
		),
		'exclude_from_search' => false, // the important line here!
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => true,
		'show_in_nav_menus' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'show_in_rest' => true,
		'rest_base' => 'produtos',
		'menu_position' => -2,
		'supports' => array('title', 'editor', 'excerpt', 'thumbnail')
	));	
	
	register_taxonomy( 'produtos_categorias_pt', array( 'produtos' ), array(
		'hierarchical'      => true, // Set this to 'false' for non-hierarchical taxonomy (like tags)
		'labels'            => array(
		'name'              => _x( 'Categorias', 'taxonomy general name' ),
		'singular_name'     => _x( 'Categoria', 'taxonomy singular name' ),
		'search_items'      => __( 'Buscar Categorias' ),
		'all_items'         => __( 'Todas as Categorias' ),
		'parent_item'       => __( 'Categoria Pai' ),
		'parent_item_colon' => __( 'Categoria Pai:' ),
		'edit_item'         => __( 'Editar categoria' ),
		'update_item'       => __( 'Atualizar categoria' ),
		'add_new_item'      => __( 'Adicionar nova categoria' ),
		'new_item_name'     => __( 'Novo nome' ),
		'menu_name'         => __( 'Categorias' ),
		),
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'category' ),
	));

	register_taxonomy( 'tracks_categorias_pt', array( 'tracks' ), array(
		'hierarchical'      => true, // Set this to 'false' for non-hierarchical taxonomy (like tags)
		'labels'            => array(
		'name'              => _x( 'Categorias', 'taxonomy general name' ),
		'singular_name'     => _x( 'Categoria', 'taxonomy singular name' ),
		'search_items'      => __( 'Buscar Categorias' ),
		'all_items'         => __( 'Todas as Categorias' ),
		'parent_item'       => __( 'Categoria Pai' ),
		'parent_item_colon' => __( 'Categoria Pai:' ),
		'edit_item'         => __( 'Editar categoria' ),
		'update_item'       => __( 'Atualizar categoria' ),
		'add_new_item'      => __( 'Adicionar nova categoria' ),
		'new_item_name'     => __( 'Novo nome' ),
		'menu_name'         => __( 'Categorias' ),
		),
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'category' ),
	));	
}
cpt();
