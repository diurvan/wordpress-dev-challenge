<?php

if ( ! defined('ABSPATH') ) {
    die('Direct access not permitted.');
}


function citation_meta_content($post) {
    $text = get_post_meta($post->ID, 'mc_citacion_metabox', true);
    wp_editor($text, 'mc_citacion_metabox_ID', $settings = array('textarea_name' => 'mc_citacion_metabox', 'media_buttons' => true, 'tinymce' => true, 'teeny' => false, 'wpautop' => true));
}
// add the metabox to post
add_action('add_meta_boxes', function () {
    add_meta_box('mc_citacion_metabox', 'Citation', 'citation_meta_content', ['post'], 'normal');
});

// save the post metabox
add_action('save_post', function ($post_id) {
    if (!empty($_POST['mc_citacion_metabox'])) {
        $content_citacion = $_POST['mc_citacion_metabox'];
        update_post_meta($post_id, 'mc_citacion_metabox', $content_citacion);
    }
});






/* SECTION CUSTOM POST TYPE VALIDATION */
function mc_citacion_registercustompostype(){
    // Define the 'Portfolio' post type. This is used to represent galleries
    // of photos. This will be our top-level custom post type menu.
    $args = array(
        'labels'	=>	array(
                'all_items'           => 'Validation',
                'menu_name'	      		=>	'Validation',
                'singular_name'       =>	'Validation',
                'edit_item'           =>	'Edit Validation',
                'new_item'            =>	'New Validation',
                'view_item'           =>	'View Validation',
                'items_archive'       =>	'Validation Archive',
                'search_items'        =>	'Search Validation',
                'not_found'	      		=>	'No validations found',
                'not_found_in_trash'  =>	'No validations found in trash'
            ),
        'supports'			=>	array( ),
        'menu_position'	=>	5,
        'public'			=>	true,
        'capabilities' => array(
            'create_posts' => false,
        )
    );
    register_post_type( 'validation', $args );
}
add_action('init', 'mc_citacion_registercustompostype');

add_filter( 'manage_edit-validation_columns', 'my_edit_validation_columns' ) ;
function my_edit_validation_columns( $columns ) {
	$columns = array(
		'cb' => '&lt;input type="checkbox" />',
		'url' => __( 'URL', 'mc-citacion' ),
		'status' => __( 'ESTADO', 'mc-citacion' ),
		'source' => __( 'ORIGEN', 'mc-citacion' )
	);
	return $columns;
}

add_action( 'manage_validation_posts_custom_column', 'my_manage_validation_columns', 10, 2 );
function my_manage_validation_columns( $column, $post_id ) {
	switch( $column ) {
		case 'url' :
			$url = get_post_meta( $post_id, 'url', true );
			if ( empty( $url ) )
				echo __( 'Unknown' );
			else
				printf( __( '%s' ), '<a target="_blank" href="'.$url.'">'.$url.'</a>');
			break;
		case 'status' :
			$status = get_post_meta( $post_id, 'error_type', true );
			if ( empty( $status ) )
				echo __( 'Unknown' );
			else
				printf( __( '%s' ), $status );
			break;
        case 'source' :
            $source = get_post_meta( $post_id, 'post_id', true );
            if ( empty( $source ) )
                echo __( 'Unknown' );
            else
                printf( __( '%s' ), '<a target="_blank" href="'.$source.'">'.$source.'</a>' );
            break;
		default :
			break;
	}
}

add_filter( 'manage_edit-validation_sortable_columns', 'my_validation_sortable_columns' );
function my_validation_sortable_columns( $columns ) {
	$columns['url'] = 'url';
	return $columns;
}

add_action( 'load-edit.php', 'my_edit_validation_load' );
function my_edit_validation_load() {
	add_filter( 'request', 'my_sort_validation' );
}
function my_sort_validation( $vars ) {
	/* Check if we're viewing the 'movie' post type. */
	if ( isset( $vars['post_type'] ) && 'validation' == $vars['post_type'] ) {
		if ( isset( $vars['orderby'] ) && 'url' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array( 'meta_key' => 'url' ) );
		}
	}
	return $vars;
}
/* SECTION CUSTOM POST TYPE VALIDATION */