<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Group_Control_Border;

class ECW_Gallery_Depth_Widget extends Widget_Base {

    public function get_name() {
        return 'ecw_gallery_depth';
    }

    public function get_title() {
        return __( 'Gallery Depth', 'elementor-custom-widgets' );
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
            'ecw-gallery-depth-js'   // Custom JS for this widget
        ];
    }

    public function get_style_depends() {
        return [
            'ecw-style-reset',  // Custom widget styles
            'ecw-gallery-depth-css' 
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
                    'card_heading',
                    [
                        'label' => __( 'Heading', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::TEXT,
                        'default' => __( 'Heading', 'elementor-custom-widgets' ),
                        'label_block' => true,
                    ]
                );

                $repeater->add_control(
                    'card_content_wysiwyg',
                    [
                        'label' => __( 'Content', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::WYSIWYG,
                        'default' => __( 'Content here...', 'elementor-custom-widgets' ),
                    ]
                );

                 $repeater->add_control(
                    'card_image',
                    [
                        'label' => __( 'Image', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::MEDIA,
                        'default' => [
                            'url' => '',
                        ],
                    ]
                );

                $this->add_control(
                    'cards',
                    [
                        'label' => __( 'Cards', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::REPEATER,
                        'fields' => $repeater->get_controls(),
                        'default' => [
                            [ 'card_content' => 'Card 1' ],
                            [ 'card_content' => 'Card 2' ],
                        ],
                        'title_field' => '{{{ card_heading }}}',
                    ]
                );

                $this->add_control(
                    'heading_tag',
                    [
                        'label' => __( 'Heading Tag', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'h3',
                        'options' => [
                            'h2' => 'H2',
                            'h3' => 'H3',
                            'h4' => 'H4',
                            'h5' => 'H5',
                            'h6' => 'H6',
                            'p'  => 'P',
                            'span' => 'Span',
                        ],
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

                $this->add_responsive_control(
                    'card_padding',
                    [
                        'label' => __( 'Card Padding', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%', 'vw' ],
                        'selectors' => [
                            '{{WRAPPER}} .ecw-gallery-depth-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );

                $this->add_responsive_control(
                    'img_width',
                    [
                        'label' => __( 'Width', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::SLIDER,
                        'size_units' => [ 'px', '%', 'vw' ],
                        'default' => [
                            'unit' => '%',
                            'size' => 100,
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .ecw-gallery-depth-img' => 'width: {{SIZE}}{{UNIT}};',
                        ],
                    ]
                );

                $this->add_responsive_control(
                    'img_max_width',
                    [
                        'label' => __( 'Max Width', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::SLIDER,
                        'size_units' => [ 'px', '%', 'vw' ],
                        'selectors' => [
                            '{{WRAPPER}} .ecw-gallery-depth-img' => 'max-width: {{SIZE}}{{UNIT}};',
                        ],
                    ]
                );

                $this->add_responsive_control(
                    'img_height',
                    [
                        'label' => __( 'Height', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::SLIDER,
                        'size_units' => [ 'px', '%', 'vw' ],
                        'selectors' => [
                            '{{WRAPPER}} .ecw-gallery-depth-img' => 'height: {{SIZE}}{{UNIT}};',
                        ],
                    ]
                );

                $this->add_control(
                    'img_object_fit',
                    [
                        'label' => __( 'Object Fit', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::SELECT,
                        'options' => [
                            'cover' => 'Cover',
                            'contain' => 'Contain',
                            'fill' => 'Fill',
                            'none' => 'None',
                        ],
                        'default' => 'cover',
                        'selectors' => [
                            '{{WRAPPER}} .ecw-gallery-depth-img' => 'object-fit: {{VALUE}};',
                        ],
                    ]
                );

                $this->add_responsive_control(
                    'img_border_radius',
                    [
                        'label' => __( 'Border Radius', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%', 'vw' ],
                        'selectors' => [
                            '{{WRAPPER}} .ecw-gallery-depth-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );

                $this->add_group_control(
                    Group_Control_Border::get_type(),
                    [
                        'name' => 'img_border',
                        'selector' => '{{WRAPPER}} .ecw-gallery-depth-img',
                    ]
                );


            $this->add_control(
                'card_layout',
                [
                    'label' => __( 'Layout Direction', 'elementor-custom-widgets' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'row',
                    'options' => [
                        'row' => __( 'All Row', 'elementor-custom-widgets' ),
                        'alternate' => __( 'Alternate (Even Reverse)', 'elementor-custom-widgets' ),
                    ],
                    'prefix_class' => 'ecw-gallery-depth-layout-', // adds class like ecw-gallery-depth-layout-alternate
                ]
            );

            $this->add_responsive_control(
                'card_gap',
                [
                    'label' => __( 'Card Gap', 'elementor-custom-widgets' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'vw' ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 20,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ecw-gallery-depth-card' => 'gap: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'card_content_gap',
                [
                    'label' => __( 'Card Gap mobile', 'elementor-custom-widgets' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'vw' ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 20,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ecw-gallery-depth-content' => 'gap: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );


            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'heading_typography',
                    'label' => __( 'Heading Typography', 'elementor-custom-widgets' ),
                    'selector' => '{{WRAPPER}} .ecw-gallery-depth-heading',
                ]
            );

            $this->add_control(
                'heading_color',
                [
                    'label' => __( 'Heading Color', 'elementor-custom-widgets' ),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ecw-gallery-depth-heading' => 'color: {{VALUE}};',
                    ],
                ]
            );


            $this->add_responsive_control(
                'heading_alignment',
                [
                    'label' => __( 'Heading Alignment', 'elementor-custom-widgets' ),
                    'type' => \Elementor\Controls_Manager::CHOOSE,
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
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ecw-gallery-depth-heading' => 'text-align: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'text_typography',
                    'label' => __( 'Text Typography', 'elementor-custom-widgets' ),
                    'selector' => '{{WRAPPER}} .ecw-gallery-depth-text',
                ]
            );

            $this->add_control(
                'text_color',
                [
                    'label' => __( 'Text Color', 'elementor-custom-widgets' ),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ecw-gallery-depth-text' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'text_alignment',
                [
                    'label' => __( 'Text Alignment', 'elementor-custom-widgets' ),
                    'type' => \Elementor\Controls_Manager::CHOOSE,
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
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ecw-gallery-depth-text' => 'text-align: {{VALUE}};',
                    ],
                ]
            );


            $this->add_responsive_control(
                'inner_card_content_gap',
                [
                    'label' => __( 'Txt Gap', 'elementor-custom-widgets' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'vw' ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 20,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ecw-gallery-depth-inner-content' => 'gap: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
            'gallery_inner_con_width',
                [
                    'label' => __( 'Content Width', 'elementor-custom-widgets' ),
                    'type' => \Elementor\Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'vw' ],
                    'selectors' => [
                        '{{WRAPPER}} .ecw-gallery-depth-inner-content' => 'width: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );


            $this->add_control(
                'gallery_parent_overflow',
                [
                    'label' => __( 'Parent Overflow', 'elementor-custom-widgets' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'default' => 'visible',
                    'options' => [
                        'visible' => __( 'Visible', 'elementor-custom-widgets' ),
                        'hidden'  => __( 'Hidden', 'elementor-custom-widgets' ),
                        'scroll'  => __( 'Scroll', 'elementor-custom-widgets' ),
                        'auto'    => __( 'Auto', 'elementor-custom-widgets' ),
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ecw-gallery-depth-parent' => 'overflow: {{VALUE}};',
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
    $tag = $settings['heading_tag'];
    ?>
    <div class="ecw-gallery-depth-parent">
        <div class="ecw-gallery-depth-content">
            <?php if ( ! empty( $settings['cards'] ) ) : ?>
                <?php foreach ( $settings['cards'] as $item ) : ?>
                    <div class="ecw-gallery-depth-card">

                        <?php if ( ! empty( $item['card_image']['url'] ) ) : ?>
                            <img class="ecw-gallery-depth-img" src="<?php echo esc_url( $item['card_image']['url'] ); ?>" alt="">
                        <?php endif; ?>

                        <div class="ecw-gallery-depth-inner-content">
                            <?php if ( ! empty( $item['card_heading'] ) ) : ?>
                                <<?php echo esc_attr( $tag ); ?> class="ecw-gallery-depth-heading">
                                    <?php echo esc_html( $item['card_heading'] ); ?>
                                </<?php echo esc_attr( $tag ); ?>>
                            <?php endif; ?>

                            <?php if ( ! empty( $item['card_content_wysiwyg'] ) ) : ?>
                                <div class="ecw-gallery-depth-text">
                                    <?php echo $item['card_content_wysiwyg']; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php
}


    protected function _content_template() {

    }
}
