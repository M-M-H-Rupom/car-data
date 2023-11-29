<?php

    $table_headers = array(
        'Car Model',
        'Car Type',
        'Brand',
        'Model Year',
        'Millage',
        'Total Driven',
        'Seller Description'
    );

    // Start building the table
    echo '<table>';
    echo '<thead><tr>';
    foreach ($table_headers as $header) {
        echo '<th>' . esc_html($header) . '</th>';
    }
    echo '</tr></thead>';
    echo '<tbody>';

    
    $car_query = new WP_Query(array('post_type' => 'car', 'posts_per_page' => -1));

    if ($car_query->have_posts()) {
        while ($car_query->have_posts()) {
            $car_query->the_post();
            $car_model = get_the_title();
            $seller_description = get_the_content();

            echo '<tr>';
            echo '<td>' . esc_html($car_model) . '</td>';
            echo '<td>' . esc_html(get_post_meta(get_the_ID(), 'car_type', true)) . '</td>';
            echo '<td>' . esc_html(get_post_meta(get_the_ID(), 'brand', true)) . '</td>';
            echo '<td>' . esc_html(get_post_meta(get_the_ID(), 'model_year', true)) . '</td>';
            echo '<td>' . esc_html(get_post_meta(get_the_ID(), 'millage', true)) . '</td>';
            echo '<td>' . esc_html(get_post_meta(get_the_ID(), 'total_driven', true)) . '</td>';
            echo '<td>' . esc_html($seller_description) . '</td>';
            echo '</tr>';
        }
        wp_reset_postdata();
    }
    echo '</tbody></table>';