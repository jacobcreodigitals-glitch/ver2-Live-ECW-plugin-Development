<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class ECW_SplitText_Widget extends Widget_Base {

    public function get_name() {
        return 'ecw_SplitText_widget';
    }

    public function get_title() {
        return __( 'Split Text', 'elementor-custom-widgets' );
    }

    public function get_icon() {
        return 'fa fa-header';
    }

    public function get_categories() {
        return [ 'ecw-category' ];
    }

    public function get_script_depends() {
        return [
            'ecw-gsap',           // GSAP core
            'ecw-scrolltrigger',  // ScrollTrigger plugin
            'ecw-splittext',    // SlitText plugin
            'ecw-split-text-js'   // Custom JS for this widget
        ];
    }

    public function get_style_depends() {
        return [
            'ecw-style-reset'  // Custom widget styles
        ];
    }

    protected function _register_controls() {

        // -----------------------
        // Content Tab
        // -----------------------
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'elementor-custom-widgets' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Heading Text (Textarea)
        $this->add_control(
            'heading_text',
            [
                'label' => __( 'Split Text', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __( 'Hello World', 'elementor-custom-widgets' ),
                'placeholder' => __( 'Enter your heading', 'elementor-custom-widgets' ),
            ]
        );

        // HTML Tag Control
        $this->add_control(
            'heading_tag',
            [
                'label' => __( 'HTML Tag', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'DIV',
                    'span' => 'SPAN',
                ],
                'default' => 'h2',
            ]
        );

        $this->end_controls_section();

        // -----------------------
        // Style Tab
        // -----------------------
        $this->start_controls_section(
            'style_section',
            [
                'label' => __( 'Style', 'elementor-custom-widgets' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Alignment
        $this->add_control(
            'alignment',
            [
                'label' => __( 'Alignment', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'elementor-custom-widgets' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'elementor-custom-widgets' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'elementor-custom-widgets' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __( 'Justify', 'elementor-custom-widgets' ),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .ecw-SplitText-widget' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        // Typography
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'label' => __( 'Typography', 'elementor-custom-widgets' ),
                'selector' => '{{WRAPPER}} .ecw-SplitText-widget',
            ]
        );

        // Color
        $this->add_control(
            'text_color',
            [
                'label' => __( 'Text Color', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .ecw-SplitText-widget' => 'color: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'entrance_animation',
            [
                'label' => __( 'Entrance Animation', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'none' => __( 'None', 'elementor-custom-widgets' ),
                    'chars' => __( 'Characters', 'elementor-custom-widgets' ),
                    'words' => __( 'Words', 'elementor-custom-widgets' ),
                    'lines' => __( 'Lines', 'elementor-custom-widgets' ),
                ],
                'default' => 'none',
            ]
        );

        $this->add_control(
    'animation_style',
    [
        'label' => __( 'Animation Style', 'elementor-custom-widgets' ),
        'type' => Controls_Manager::SELECT,
        'options' => [
            '1' => __( 'Animation 1', 'elementor-custom-widgets' ),
            '2' => __( 'Animation 2', 'elementor-custom-widgets' ),
            '3' => __( 'Animation 3', 'elementor-custom-widgets' ),
        ],
        'default' => '1',
    ]
);


        $this->end_controls_section();
    }

            protected function render() {
                $settings = $this->get_settings_for_display();
                $heading = $settings['heading_text'] ?? '';
                $tag = $settings['heading_tag'] ?? 'h2';
                $animation_type = $settings['entrance_animation'] ?? 'none';
                $widget_id = 'ecw-splittext-' . $this->get_id(); // unique ID
                $animation_style = $settings['animation_style'] ?? '1';

                echo sprintf(
                   '<%1$s id="%3$s" class="ecw-SplitText-widget" data-animation="%4$s" data-animation-style="%5$s">%2$s</%1$s>',
                    esc_attr( $tag ),
                    esc_html( $heading ),
                    esc_attr( $widget_id ),
                    esc_attr( $animation_type ),
                    esc_attr( $animation_style )
                );
            }

    protected function _content_template() {
        ?>
        <{{{ settings.heading_tag }}} class="ecw-SplitText-widget">{{{ settings.heading_text }}}</{{{ settings.heading_tag }}}>
        <?php
    }
}