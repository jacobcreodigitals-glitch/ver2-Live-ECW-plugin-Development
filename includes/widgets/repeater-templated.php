<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

use Elementor\Repeater;
use Elementor\Plugin;

class ECW_Repeater_Templated_Widget extends Widget_Base {

    public function get_name() {
        return 'ecw_Repeater_Templated_widget';
    }

    public function get_title() {
        return __( 'Repeater Templated', 'elementor-custom-widgets' );
    }

    public function get_icon() {
        return 'fa fa-header';
    }

    public function get_categories() {
        return [ 'ecw-category' ];
    }

  protected function _register_controls() {

            // -----------------------
            // Content Tab Start
            // -----------------------
        
            $this->start_controls_section(
                'content_section',
                [
                    'label' => __( 'Content', 'elementor-custom-widgets' ),
                    'tab' => Controls_Manager::TAB_CONTENT,
                ]
            );

			$repeater = new Repeater();

    // Get all Elementor saved templates
    $templates = [];


$args = [
    'post_type'      => 'elementor_library',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'meta_query'     => [
        [
            'key'     => '_elementor_template_type',
            'value'   => [ 'container', 'page' ],
            'compare' => 'IN',
        ],
    ],
];

$posts = get_posts( $args );

if ( ! empty( $posts ) ) {
    foreach ( $posts as $post ) {

        $type = get_post_meta( $post->ID, '_elementor_template_type', true );

        // Optional: Add label prefix for clarity
        $label = ucfirst( $type ) . ' - ' . $post->post_title;

        $templates[ $post->ID ] = $label;
    }
}

    $repeater->add_control(
        'template_id',
        [
            'label'   => __( 'Select Template', 'elementor-custom-widgets' ),
            'type'    => Controls_Manager::SELECT,
            'options' => $templates,
            'default' => '',
        ]
    );

    $this->add_control(
        'repeater_list',
        [
            'label'       => __( 'Repeater Items', 'elementor-custom-widgets' ),
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'title_field' => '{{{ template_id }}}',
        ]
    );
            

            $this->end_controls_section();

            // -----------------------
            // Content Tab End
            // -----------------------

// -----------------------------------------------------------------------------

            // -----------------------
            // Style Tab Start
            // -----------------------
			$this->start_controls_section(
                'style_section',
                [
                    'label' => __( 'Style', 'elementor-custom-widgets' ),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );

			// add Styling control typograph, colors etc

            $this->end_controls_section();
            // -----------------------
            // Style Tab End
            // -----------------------

    }

protected function render() {

    $settings = $this->get_settings_for_display();

    // Safety: Check repeater exists
    if ( empty( $settings['repeater_list'] ) || ! is_array( $settings['repeater_list'] ) ) {
        return;
    }

    echo '<div class="ecw-repeater-wrapper">';

    foreach ( $settings['repeater_list'] as $item ) {

        // Safety: template not selected
        if ( empty( $item['template_id'] ) ) {
            continue;
        }

        $template_id = absint( $item['template_id'] );

        // Safety: template does not exist
        if ( ! get_post_status( $template_id ) ) {
            echo '<div class="ecw-repeater-item ecw-template-missing">';
            echo esc_html__( 'Selected template not found.', 'elementor-custom-widgets' );
            echo '</div>';
            continue;
        }

        echo '<div class="ecw-repeater-item">';

        // Safety: Elementor loaded
        if ( class_exists( '\Elementor\Plugin' ) ) {

            echo \Elementor\Plugin::instance()
                ->frontend
                ->get_builder_content_for_display( $template_id );

        } else {

            echo esc_html__( 'Elementor is not loaded.', 'elementor-custom-widgets' );
        }

        echo '</div>';
    }

    echo '</div>';
}


    protected function _content_template() {

    }
}
