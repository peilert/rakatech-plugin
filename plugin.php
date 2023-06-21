<?php
/**
* Plugin Name: Rakatech-task
* Plugin URI: https://www.your-site.com/
* Description: Test.
* Version: 0.1
* Author: Michał Pietrzok
* Author URI: https://www.your-site.com/
**/

/*
Description: A plugin that works with a shortcode to display data from a JSON file.
Version: 1.0
*/


// Register shortcode
add_shortcode( 'custom_shortcode', 'custom_shortcode_handler' );



// Shortcode handler function
function custom_shortcode_handler( $atts ) {

    /**
     * Include CSS file for custom plugin.
     */
    function myplugin_scripts() {
        wp_register_style( 'plugin-styles',  plugin_dir_url( __FILE__ ) . 'assets/style.css' );
        wp_enqueue_style( 'plugin-styles' );
        wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
    }
    add_action( 'wp_enqueue_scripts', 'myplugin_scripts' );


    // Parse shortcode attributes
    $args = shortcode_atts( array(
        'sorting' => 'a',
    ), $atts );

    // Load JSON data from file
    $json_file = plugin_dir_path( __FILE__ ) . 'data.json';
    $json_data = file_get_contents( $json_file );

    // Decode JSON data
    $data = json_decode( $json_data, true );

    //Merge a few array into one
    $products = call_user_func_array('array_merge', $data['toplists']);

    

    // Sort data by position or natural sort order
    if ( $args['sorting'] == '0' ) {
        usort($products, fn($a, $b) => $a['position'] <=> $b['position']);
    } elseif ( $args['sorting'] = '1' ) {
        usort($products, fn($a, $b) => $b['position'] <=> $a['position']);
    } elseif ( $args['sorting'] = 'a' ) {
    }



    // Build table HTML
    $html = '<table width="100%" class="table">';
    $html .= '<thead>
                <tr>
                    <th>Casion</th>
                    <th>Bonus</th>
                    <th>Features</th>
                    <th>Play</th>
                </tr>
             </thead>';

                if(count($products) != 0){
                    foreach ($products as $info) {
                        $html .= '<tr>';

                        $html .= '<td><img src="'.$info['logo'].'" alt=""></br><a href="'.get_home_url().'/'.$info['brand_id'].'">Review</a> </td>';
                        
                        $html .= '<td>';
                            for ($x = 0; $x < $info['info']['rating']; $x++) {
                            $html .= '<span class="fa fa-star checked"></span>';
                            }
                            for ($x = 0; $x < 5-$info['info']['rating']; $x++) {
                            $html .= '<span class="fa fa-star"></span>';
                            }
                            $html .= '</br>'.$info['info']['bonus'];
                        $html .= '</td>';

                        $html .= '<td>';
                             $html .= '<ul>';
                                foreach ($info['info']['features'] as $feature) {
                                $html .= '<li>'.$feature.'</li>';
                                }
                                $html .= '</ul>';
                        $html .= '</td>';

                        $html .= '<td>';
                        $html .= '<a class="table-button" href="'.$info['play_url'].'" target="_blank">Play now</a></br>';
                        $html .= '<span>'.$info['terms_and_conditions'].'</span>';
                        $html .= '</td>';


                        $html .= '<tr>';

                      }
                }
            
    $html .= '</table>';

    // Return table HTML
    return $html;
}
