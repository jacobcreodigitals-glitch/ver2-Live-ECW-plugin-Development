<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ECW_Plugin {

public function __construct() {
    add_action( 'elementor/elements/categories_registered', [ $this, 'add_category' ] );
    add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
}




public function add_category( $elements_manager ) {
    $elements_manager->add_category(
            'ecw-category',
            [
                'title' => __( 'ECW Widgets', 'elementor-custom-widgets' ),
                'icon' => 'dashicons-admin-plugins', 
            ]
    );
}

 
    public function register_widgets($widgets_manager) {

            require_once __DIR__ . '/widgets/arc-scroll-widget.php';
            if ( class_exists( 'ECW_Arc_Scroll_Widget' ) ) {
                $widgets_manager->register( new ECW_Arc_Scroll_Widget() );
            }

            require_once __DIR__ . '/widgets/arc-scroll-templated-widget.php';
            if ( class_exists( 'ECW_Arc_Scroll_Templated_Widget' ) ) {
                $widgets_manager->register( new ECW_Arc_Scroll_Templated_Widget() );
            }

            require_once __DIR__ . '/widgets/basic-horizontal-scroll-widget.php';
            if ( class_exists( 'ECW_Basic_Horizontal_Scroll_Widget' ) ) {
                $widgets_manager->register( new ECW_Basic_Horizontal_Scroll_Widget() );
            }
            
            require_once __DIR__ . '/widgets/basic-horizontal-scroll-templated-widget.php';
            if ( class_exists( 'ECW_Basic_Horizontal_Scroll_Templated_Widget' ) ) {
                $widgets_manager->register( new ECW_Basic_Horizontal_Scroll_Templated_Widget() );
            }

    }
}
