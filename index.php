<?php
/**
 * Plugin Name: car data
 * Author: Rupom
 * Version: 1.0.0
 * Description: cars information
 */

//custom post
function callback_car_cpt() {
    $labels = array(
        'name'               => 'Car',
        'singular_name'      => 'Car',
        'menu_name'          => 'Car',
        'add_new'            => 'Add Car',
        'add_new_item'       => 'Add New Car',
        'all_items'          => 'All Cars',
        'edit_item'          => 'Edit Car',
        'featured_image'     => 'Car Image',
        'set_featured_image' => 'Set car Image',

    );
    $cardata = array(
        'labels'              => $labels,
        'public'              => true,
        'show_ui'             => true,
        'publicly_queryable'  => true,
        'has_archive'         => true,
        'hierarchical'        => true,
        'exclude_from_search' => false,
        'supports'            => array('title', 'thumbnail', 'editor'),
        'capability_type'     => 'page',
        'menu_position'       => 11,
        'menu_icon'           => 'dashicons-store',
    );

    register_post_type('car', $cardata);
}
add_action('init', 'callback_car_cpt', 1);

//texonomy
function callback_for_taxonomy() {

    $car_type_labels = array(
        'name'          => 'Cartype',
        'singular_name' => 'Cartype',
        'menu_name'     => 'Cartype',
        'all_items'     => 'All Cartype',
        'add_new'       => 'Add New',
        'edit_item'     => 'Edit Cartype',
        'search_items'  => 'Search Cars',
    );

    $data_cartype = array(
        'labels'             => $car_type_labels,
        'hierarchical'       => true,
        'public'             => true,
        'show_ui'            => true,
        'publicly_queryable' => true,
        
    );
    register_taxonomy('Cartype', array('car'), $data_cartype);
}
add_action('init', 'callback_for_taxonomy');



//meta fields
class car_info {

    private $car_datas = array(
        array(
            'label' => 'Millage',
            'type'  => 'number',
            'id'    => 'millage',
        ),
        array(
            'label' => 'Car Type',
            'type'  => 'text',
            'id'    => 'car_type',
        ),
        array(
            'label' => 'Brand',
            'type'  => 'text',
            'id'    => 'brand',
        ),
        array(
            'label' => 'Model Year',
            'type'  => 'text',
            'id'    => 'model_year',
        ),
        array(
            'label' => 'Total Driven',
            'type'  => 'number',
            'id'    => 'total_driven',
        ),
    );
    public function __construct() {
        add_action('admin_menu', array($this, 'callback_for_metabox'));
        add_action('save_post', array($this, 'callback_for_save_post'));
    }
    public function callback_for_metabox() {
        add_meta_box('car_information', 'Information', array($this, 'callback_meta_box'), array('page', 'car'));
    }
    public function callback_meta_box($post) {
        $this->car_data_field($post);
    }
    public function car_data_field($post) {
        $result = '';
        foreach ($this->car_datas as $car_data) {
            $label = '<label>' . '<h4 style="margin: 2px 0; color: #ff6b6b">'. $car_data['label'] .'</h4>' . '</label>';
            $input_value = get_post_meta($post->ID, $car_data['id'], true);
            $input = sprintf('<input type="%s" id="%s" name="%s" value="%s" style="margin-top: 6px; margin-bottom: 12px; width : 100%%;">',
                $car_data['type'],
                $car_data['id'],
                $car_data['id'],
                $input_value,
            );
            $result .= $label . $input .'<br>';
        }
        echo $result;
    }
    public function callback_for_save_post($post_id) {
        foreach ($this->car_datas as $car_data) {
            if (isset($_POST[$car_data['id']])) {
                $car_value = sanitize_text_field($_POST[$car_data['id']]);

                update_post_meta($post_id, $car_data['id'], $_POST[$car_data['id']]);
            }
        }
    }
}
new car_info();