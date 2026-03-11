<?php
if (!defined('ABSPATH')) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class ECW_Text_On_Scroll_Widget extends Widget_Base {

    public function get_name() {
        return 'ecw_text_on_scroll_widget';
    }

    public function get_title() {
        return __('Text On Scroll', 'elementor-custom-widgets');
    }

    public function get_icon() {
        return 'fa fa-text-height';
    }

    public function get_categories() {
        return ['ecw-category'];
    }

    public function get_script_depends() {
        return [
            'ecw-gsap',           // GSAP core
            'ecw-scrolltrigger',  // ScrollTrigger plugin
            'ecw-splittext',    // SlitText plugin
            'ecw-text-on-scroll-js'   // Custom JS for this widget
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
                'label' => __('Content', 'elementor-custom-widgets'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'scroll_text',
            [
                'label'       => __('Text', 'elementor-custom-widgets'),
                'type'        => Controls_Manager::TEXTAREA,
                'default'     => __('Scroll Animation Text', 'elementor-custom-widgets'),
                'placeholder' => __('Enter your text', 'elementor-custom-widgets'),
            ]
        );

        $this->add_control(
            'html_tag',
            [
                'label' => __('HTML Tag', 'elementor-custom-widgets'),
                'type'  => Controls_Manager::SELECT,
                'options' => [
                    'h1'  => 'H1',
                    'h2'  => 'H2',
                    'h3'  => 'H3',
                    'h4'  => 'H4',
                    'h5'  => 'H5',
                    'h6'  => 'H6',
                    'p'   => 'P',
                    'div' => 'DIV',
                    'span'=> 'SPAN',
                ],
                'default' => 'h2',
            ]
        );

        // -----------------------
        // ⚡ Animation select control
        // -----------------------
        $this->add_control(
            'animation_type',
            [
                'label'   => __('Animation Style', 'elementor-custom-widgets'),
                'type'    => Controls_Manager::SELECT,
                'default' => '1',
                'options' => [
                    '1' => __('Option1', 'elementor-custom-widgets'),
                    '2' => __('Option2', 'elementor-custom-widgets'),
                    '3' => __('Option3', 'elementor-custom-widgets'),
                ],
            ]
        );




        $this->add_control(
            'animation_stagger',
            [
                'label' => __('Animation Stagger (seconds)', 'elementor-custom-widgets'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 1,
                'step' => 0.01,
                'default' => 0.03,
            ]
        );

        $this->add_control(
            'ecw_animation_duration',
            [
                'label' => __('Animation Duration (seconds)', 'elementor-custom-widgets'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 5,
                'step' => 0.05,
                'default' => 0.5,
            ]
        );

        $this->end_controls_section();


        // -----------------------
        // Style Tab
        // -----------------------
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'elementor-custom-widgets'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'alignment',
            [
                'label' => __('Alignment', 'elementor-custom-widgets'),
                'type'  => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'elementor-custom-widgets'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'elementor-custom-widgets'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'elementor-custom-widgets'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justify', 'elementor-custom-widgets'),
                        'icon'  => 'eicon-text-align-justify',
                    ],
                ],
                'default'   => 'left',
                'selectors' => [
                    '{{WRAPPER}} .ecw-text-on-scroll' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'   => 'typography',
                'label'  => __('Typography', 'elementor-custom-widgets'),
                'selector' => '{{WRAPPER}} .ecw-text-on-scroll',
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label'   => __('Text Color', 'elementor-custom-widgets'),
                'type'    => Controls_Manager::COLOR,
                'default' => '#000',
                'selectors' => [
                    '{{WRAPPER}} .ecw-text-on-scroll' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $text = $settings['scroll_text'];
        $tag  = $settings['html_tag'];
        $animation = $settings['animation_type'];

            echo sprintf(
                '<%1$s class="ecw-text-on-scroll" data-animation="%3$s" data-duration="%4$s" data-stagger="%5$s">%2$s</%1$s>',
                esc_html($tag),
                esc_html($text),
                esc_attr($animation),
                esc_attr($settings['ecw_animation_durationp']),
                esc_attr($settings['animation_stagger'])
            );
    }

    protected function _content_template() {
        ?>
            <{{{ settings.html_tag }}} 
                class="ecw-text-on-scroll" 
                data-animation="{{{ settings.animation_type }}}"
                data-duration="{{{ settings.ecw_animation_duration }}}"
                data-stagger="{{{ settings.animation_stagger }}}"
            >
                {{{ settings.scroll_text }}}
            </{{{ settings.html_tag }}} >
        <?php
    }
}