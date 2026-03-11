<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class ECW_Fill_Text_Widget extends Widget_Base {

    public function get_name() {
        return 'ecw_fill_text_widget'; // unique slug
    }

    public function get_title() {
        return __( 'Fill Text', 'elementor-custom-widgets' );
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
            'ecw-fill-text-js'   // Custom JS for this widget
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

        $this->add_control(
            'heading_text',
            [
                'label' => __( 'Heading Text', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::WYSIWYG,
                'default' => __( 'Hello World', 'elementor-custom-widgets' ),
                'placeholder' => __( 'Enter your heading', 'elementor-custom-widgets' ),
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

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'label' => __( 'Typography', 'elementor-custom-widgets' ),
                'selector' => '{{WRAPPER}} .ecw-fill-text-widget',
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
                    '{{WRAPPER}} .ecw-fill-text-widget' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        // Initial Color
        $this->add_control(
            'initial_color',
            [
                'label' => __( 'Initial Color', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#cccccc',
            ]
        );

        // Final Color
        $this->add_control(
            'final_color',
            [
                'label' => __( 'Final Color', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#000000',
            ]
        );


        // Scrub control
        $this->add_control(
            'scrub',
            [
                'label' => __( 'Scrub Duration', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 1.5,
                'step' => 0.1,
                'description' => __( 'Duration for scroll smoothing (scrub)', 'elementor-custom-widgets' ),
            ]
        );

        // Stagger control
        $this->add_control(
            'stagger',
            [
                'label' => __( 'Stagger', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 0.05,
                'step' => 0.01,
                'description' => __( 'Delay between each character animation', 'elementor-custom-widgets' ),
            ]
        );

        $this->end_controls_section();
    }



protected function render() {
    $settings = $this->get_settings_for_display();
    $heading = $settings['heading_text'] ?? '';
    
    // Remove wrapping <p> but keep inner HTML and line breaks
    $heading = preg_replace('/^<p>(.*)<\/p>$/i', '$1', $heading);

    $initial_color = $settings['initial_color'];
    $final_color = $settings['final_color'];
    $scrub = $settings['scrub'] ?? 1.5;
    $stagger = $settings['stagger'] ?? 0.05;

    echo '<div class="ecw-fill-text-widget elementor-widget-ecw_fill_text_widget"
        data-initial-color="'.esc_attr($initial_color).'"
        data-final-color="'.esc_attr($final_color).'"
        data-scrub="'.esc_attr($scrub).'"
        data-stagger="'.esc_attr($stagger).'">
        <span class="ecw-fill-text">'. $heading .'</span>
    </div>';
}



protected function _content_template() {
    ?>
    <div class="ecw-fill-text-widget elementor-widget-ecw_fill_text_widget"
        data-initial-color="{{{ settings.initial_color }}}"
        data-final-color="{{{ settings.final_color }}}"
        data-scrub="{{{ settings.scrub }}}"
        data-stagger="{{{ settings.stagger }}}"
    >
        <span class="ecw-fill-text" style="color: {{{ settings.initial_color }}};">
            {{{
                settings.heading_text.replace(/^<p>(.*)<\/p>$/i, '$1')
            }}}
        </span>
    </div>
    <?php
}


}