<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

class ECW_Basic_Horizontal_Scroll extends Widget_Base {

    public function get_name() {
        return 'ecw_basic_horizontal_scroll';
    }

    public function get_title() {
        return __( 'Basic Horizontal Scroll', 'elementor-custom-widgets' );
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
            'ecw-hr-scroll-js'  // Custom JS for this widget
        ];
    }

    public function get_style_depends() {
        return [
            'ecw-style-reset',  // Custom widget styles
            'ecw-hr-scroll-css'
        ];
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

            $repeater->add_control(
                    'slide_heading',
                    [
                        'label' => __( 'Heading', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::TEXT,
                        'default' => __( 'Slide Heading', 'elementor-custom-widgets' ),
                        'label_block' => true,
                    ]
                );

            $repeater->add_control(
                    'slide_content',
                    [
                        'label' => __( 'Content', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::WYSIWYG,
                        'default' => __( 'Slide content goes here.', 'elementor-custom-widgets' ),
                    ]
                );

            $this->add_control(
                    'slides',
                    [
                        'label' => __( 'Slides List', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::REPEATER,
                        'fields' => $repeater->get_controls(),
                        'default' => [
                            [
                                'slide_heading' => 'Slide 1',
                                'slide_content' => 'Content for slide 1.',
                            ],
                            [
                                'slide_heading' => 'Slide 2',
                                'slide_content' => 'Content for slide 2.',
                            ],
                        ],
                        'title_field' => '{{{ slide_heading }}}',
                    ]
                );
           
            $this->add_responsive_control(
                'slides_gap',
                [
                    'label' => __( 'Slides Gap', 'elementor-custom-widgets' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', 'em', 'rem' ],
                    'range' => [
                        'px' => [ 'min' => 0, 'max' => 200 ],
                    ],
                    'default' => [
                        'size' => 20,
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ecw-hr-slider-content' => 'gap: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'slide_width',
                [
                    'label' => __( 'Slide Width', 'elementor-custom-widgets' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'vw', '%', 'px' ],
                    'range' => [
                        'vw' => [ 'min' => 10, 'max' => 100 ],
                    ],
                    'default' => [
                        'size' => 30,
                        'unit' => 'vw',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ecw-hr-content-slide' => 'min-width: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );


            // $this->add_responsive_control(
            //     'slide_height',
            //     [
            //         'label' => __( 'Slide Height', 'elementor-custom-widgets' ),
            //         'type' => Controls_Manager::SLIDER,
            //         'size_units' => [ 'vh', 'px', '%' ],
            //         'range' => [
            //             'vh' => [ 'min' => 20, 'max' => 100 ],
            //         ],
            //         'selectors' => [
            //             '{{WRAPPER}} .ecw-hr-content-slide' => 'height: {{SIZE}}{{UNIT}};',
            //         ],
            //     ]
            // );


            $this->add_control(
                'slide_background',
                [
                    'label' => __( 'Slide Background', 'elementor-custom-widgets' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ecw-hr-content-slide' => 'background: {{VALUE}};',
                    ],
                ]
            );



            $this->add_control(
                'scroll_trigger_class',
                [
                    'label' => __('Custom Scroll Trigger Class', 'textdomain'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'placeholder' => '.my-custom-class',
                    'description' => __('Add a custom CSS class to use as the ScrollTrigger parent.', 'textdomain'),
                ]
            );


            $this->add_control(
                'parent_overflow',
                [
                    'label' => __( 'Parent Overflow', 'elementor-custom-widgets' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'hidden',
                    'options' => [
                        'visible' => __( 'Visible', 'elementor-custom-widgets' ),
                        'hidden' => __( 'Hidden', 'elementor-custom-widgets' ),
                    ],
                    'description' => __('If you set to Visible apply the hidden to other parent.', 'elementor-custom-widgets'),
                ]
            );
            
            
                $this->add_responsive_control(
                    'parent_height',
                    [
                        'label' => __( 'Parent Height', 'elementor-custom-widgets' ),
                        'type' => \Elementor\Controls_Manager::SLIDER,
                        'size_units' => [ 'px', 'vh', '%' ],
                        'range' => [
                            'px' => [ 'min' => 100, 'max' => 2000 ],
                            'vh' => [ 'min' => 10, 'max' => 200 ],
                            '%'  => [ 'min' => 10, 'max' => 200 ],
                        ],
                        'default' => [
                            'size' => 100,
                            'unit' => 'vh',
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .ecw-hr-slider-parent' => 'height: {{SIZE}}{{UNIT}};',
                        ],
                        'description' => __('Set the height of the slider parent.', 'elementor-custom-widgets'),
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

        // Slide Padding
                $this->add_responsive_control(
                    'slide_padding',
                    [
                        'label' => __( 'Slide Padding', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%', 'em' ],
                        'selectors' => [
                            '{{WRAPPER}} .ecw-hr-content-slide' => 
                                'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );

                // Heading Typography
                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name' => 'heading_typography',
                        'label' => __( 'Heading Typography', 'elementor-custom-widgets' ),
                        'selector' => '{{WRAPPER}} .ecw-hr-slide-heading',
                    ]
                );

                $this->add_control(
                    'heading_color',
                    [
                        'label' => __( 'Heading Color', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .ecw-hr-slide-heading' => 'color: {{VALUE}};',
                        ],
                    ]
                );

                // Content Typography
                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name' => 'content_typography',
                        'label' => __( 'Content Typography', 'elementor-custom-widgets' ),
                        'selector' => '{{WRAPPER}} .ecw-hr-slide-content',
                    ]
                );

                $this->add_control(
                    'content_color',
                    [
                        'label' => __( 'Content Color', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .ecw-hr-slide-content' => 'color: {{VALUE}};',
                        ],
                    ]
                );
            

            $this->end_controls_section();
            // -----------------------
            // Style Tab End
            // -----------------------

    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $trigger_class = $settings['scroll_trigger_class'] ?? '';
        $overflow = $settings['parent_overflow'] ?? 'hidden';
                if ( empty( $settings['slides'] ) ) {
                    return;
                }
                ?>
            <div class="ecw-hr-slider-parent" data-scroll-trigger="<?php echo esc_attr($trigger_class); ?>" data-overflow="<?php echo esc_attr($overflow); ?>">
                <div class="ecw-hr-slider-content">
                    <?php foreach ( $settings['slides'] as $slide ) : ?>
                        <div class="ecw-hr-content-slide">

                            <?php if ( ! empty( $slide['slide_heading'] ) ) : ?>
                                <h2 class="ecw-hr-slide-heading">
                                    <?php echo esc_html( $slide['slide_heading'] ); ?>
                                </h2>
                            <?php endif; ?>

                            <?php if ( ! empty( $slide['slide_content'] ) ) : ?>
                                <div class="ecw-hr-slide-content">
                                    <?php echo wp_kses_post( $slide['slide_content'] ); ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
                <?php
    }

    protected function _content_template() {
                ?>
        <#
        if ( settings.slides.length ) { #>
    <div class="ecw-hr-slider-parent">
            <div class="ecw-hr-slider-content">
                <# _.each( settings.slides, function( slide ) { #>
                    <div class="ecw-hr-content-slide">
                        <h2 class="ecw-hr-slide-heading">{{{ slide.slide_heading }}}</h2>
                        <div class="ecw-hr-slide-content">{{{ slide.slide_content }}}</div>
                    </div>
                <# }); #>
            </div>
    </div>

        <# } #>
        <?php
    }
}
