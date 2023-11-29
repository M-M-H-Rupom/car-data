<?php
/**
 * Plugin Name: car data
 * Author: Rupom
 * Version: 1.0.0
 * Description: cars information
 * Text Domain: mmh-car
 */








// Register the shortcode
function car_table_shortcode() {
    ob_start();

   include_once(plugin_dir_path( __FILE__ ).'/templates/query_table.php');

    return ob_get_clean();
}
add_shortcode('mycar', 'car_table_shortcode');





// Register Custom Post Type car
function create_car_cpt() {

    $labels = array(
        'name'                  => __('Car', 'Post Type General Name', 'mmh-car'),
        'singular_name'         => __('car', 'Post Type Singular Name', 'mmh-car'),
        'menu_name'             => __('Cars', 'Admin Menu text', 'mmh-car'),
        'name_admin_bar'        => __('car', 'Add New on Toolbar', 'mmh-car'),
        'archives'              => __('car Archives', 'mmh-car'),
        'attributes'            => __('car Attributes', 'mmh-car'),
        'parent_item_colon'     => __('Parent car:', 'mmh-car'),
        'all_items'             => __('All cars', 'mmh-car'),
        'add_new_item'          => __('Add New car', 'mmh-car'),
        'add_new'               => __('Add New', 'mmh-car'),
        'new_item'              => __('New car', 'mmh-car'),
        'edit_item'             => __('Edit car', 'mmh-car'),
        'update_item'           => __('Update car', 'mmh-car'),
        'not_found'             => __('Not found', 'mmh-car'),
        'not_found_in_trash'    => __('Not found in Trash', 'mmh-car'),
        'featured_image'        => __('Featured Image', 'mmh-car'),
        'set_featured_image'    => __('Set featured image', 'mmh-car'),
        'remove_featured_image' => __('Remove featured image', 'mmh-car'),
        'use_featured_image'    => __('Use as featured image', 'mmh-car'),
        'insert_into_item'      => __('Insert into car', 'mmh-car'),
    );
    $args = array(
        'label'               => __('car', 'mmh-car'),
        'labels'              => $labels,
        'menu_icon'           => 'dashicons-store',
        'supports'            => array('title', 'editor', 'thumbnail'),
        'taxonomies'          => array(),
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'show_in_admin_bar'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'hierarchical'        => true,
        'exclude_from_search' => false,
        'show_in_rest'        => true,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
    );
    register_post_type('car', $args);

}
add_action('init', 'create_car_cpt', 0);

// Register Taxonomy cartype
function create_cartype_tax() {

    $labels = array(
        'name'              => __('cartype', 'taxonomy general name', 'mmh-car'),
        'singular_name'     => __('cartype', 'taxonomy singular name', 'mmh-car'),
        'search_items'      => __('Search cartype', 'mmh-car'),
        'all_items'         => __('All cartype', 'mmh-car'),
        'parent_item'       => __('Parent cartype', 'mmh-car'),
        'parent_item_colon' => __('Parent cartype:', 'mmh-car'),
        'edit_item'         => __('Edit cartype', 'mmh-car'),
        'update_item'       => __('Update cartype', 'mmh-car'),
        'add_new_item'      => __('Add New cartype', 'mmh-car'),
        'new_item_name'     => __('New cartype Name', 'mmh-car'),
        'menu_name'         => __('Cartype', 'mmh-car'),
    );
    $args = array(
        'labels'             => $labels,
        'description'        => __('', 'mmh-car'),
        'hierarchical'       => true,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_nav_menus'  => true,
        'show_tagcloud'      => true,
        'show_in_quick_edit' => true,
        'show_admin_column'  => false,
    );
    register_taxonomy('cartype', array('car'), $args);

}
add_action('init', 'create_cartype_tax');


// Meta Box Class: Car Information
class carinformationMetabox {

	private $screen = array(
		'post',
		'page',
		'car',
	);

	private $meta_fields = array(
		array(
			'label' => 'Total Driven',
			'id' => 'total_driven',
			'type' => 'text',
		),
		array(
			'label' => 'Millage',
			'id' => 'millage',
			'type' => 'text',
		),
	);

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_fields' ) );
	}

	public function add_meta_boxes() {
		foreach ( $this->screen as $single_screen ) {
			add_meta_box(
				'carinformation',
				__( 'Car Information', 'mmh-car' ),
				array( $this, 'meta_box_callback' ),
				$single_screen,
				'advanced',
				'default'
			);
		}
	}

	public function meta_box_callback( $post ) {
		wp_nonce_field( 'carinformation_data', 'carinformation_nonce' );
		$this->field_generator( $post );
	}

	public function field_generator( $post ) {
		$output = '';
		foreach ( $this->meta_fields as $meta_field ) {
			$label = '<label for="' . $meta_field['id'] . '">' . $meta_field['label'] . '</label>';
			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
			if ( empty( $meta_value ) ) {
				if ( isset( $meta_field['default'] ) ) {
					$meta_value = $meta_field['default'];
				}
			}
			switch ( $meta_field['type'] ) {
				default:
					$input = sprintf(
						'<input %s id="%s" name="%s" type="%s" value="%s">',
						$meta_field['type'] !== 'color' ? 'style="width: 100%"' : '',
						$meta_field['id'],
						$meta_field['id'],
						$meta_field['type'],
						$meta_value
					);
			}
            $output .= $label . $input;
        }
        echo $output;
	}

	public function save_fields( $post_id ) {
		if ( ! isset( $_POST['carinformation_nonce'] ) )
			return $post_id;
		$nonce = $_POST['carinformation_nonce'];
		if ( !wp_verify_nonce( $nonce, 'carinformation_data' ) )
			return $post_id;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
		foreach ( $this->meta_fields as $meta_field ) {
			if ( isset( $_POST[ $meta_field['id'] ] ) ) {
				switch ( $meta_field['type'] ) {
					case 'text':
						$_POST[ $meta_field['id'] ] = sanitize_text_field( $_POST[ $meta_field['id'] ] );
						break;
				}
				update_post_meta( $post_id, $meta_field['id'], $_POST[ $meta_field['id'] ] );
			}
		}
	}
}

if (class_exists('carinformationMetabox')) {
	new carinformationMetabox;
};