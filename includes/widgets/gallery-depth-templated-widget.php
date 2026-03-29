<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Border;

class ECW_Gallery_Depth_Templated_Widget extends Widget_Base {

    public function get_name() {
        return 'ecw_gallery_depth_templated';
    }

    public function get_title() {
        return __( 'Gallery Depth Templated', 'elementor-custom-widgets' );
    }

    public function get_icon() {
        return 'fa fa-header';
    }

    public function get_categories() {
        return [ 'ecw-category' ];
    }

    public function get_script_depends() {
        return [
            'ecw-gsap',
            'ecw-scrolltrigger',
            'ecw-gallery-depth-templated-js',
        ];
    }

    public function get_style_depends() {
        return [
            'ecw-style-reset',
            'ecw-gallery-depth-templated-css',
        ];
    }

    /**
     * Get published Elementor templates (containers/sections/pages).
     *
     * @return array  Associative array of [ template_id => 'Template Name' ]
     */
    private function get_elementor_templates() {
        $options = [ '' => __( '— Select Template —', 'elementor-custom-widgets' ) ];

        $templates = get_posts( [
            'post_type'      => 'elementor_library',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'meta_query'     => [
                [
                    'key'     => '_elementor_template_type',
                    'value'   => 'container',
                    'compare' => '=',
                ],
            ],
        ] );

        foreach ( $templates as $template ) {
            $options[ $template->ID ] = $template->post_title;
        }

        return $options;
    }

    protected function _register_controls() {

        // -----------------------
        // Content Tab
        // -----------------------
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'elementor-custom-widgets' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

            $repeater = new Repeater();

            $repeater->add_control(
                'card_template_id',
                [
                    'label'       => __( 'Template', 'elementor-custom-widgets' ),
                    'type'        => Controls_Manager::SELECT,
                    'options'     => $this->get_elementor_templates(),
                    'default'     => '',
                    'label_block' => true,
                    'description' => __( 'Select a published Elementor template to render inside this card.', 'elementor-custom-widgets' ),
                ]
            );

            $this->add_control(
                'cards',
                [
                    'label'       => __( 'Cards', 'elementor-custom-widgets' ),
                    'type'        => Controls_Manager::REPEATER,
                    'fields'      => $repeater->get_controls(),
                    'default'     => [
                        [ 'card_template_id' => '' ],
                        [ 'card_template_id' => '' ],
                    ],
                    'title_field' => __( 'Card', 'elementor-custom-widgets' ),
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
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

            $this->add_responsive_control(
                'card_padding',
                [
                    'label'      => __( 'Card Padding', 'elementor-custom-widgets' ),
                    'type'       => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'vw' ],
                    'selectors'  => [
                        '{{WRAPPER}} .ecw-gallery-depth-temp-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'card_layout',
                [
                    'label'        => __( 'Layout Direction', 'elementor-custom-widgets' ),
                    'type'         => Controls_Manager::SELECT,
                    'default'      => 'row',
                    'options'      => [
                        'row'       => __( 'All Row', 'elementor-custom-widgets' ),
                        'alternate' => __( 'Alternate (Even Reverse)', 'elementor-custom-widgets' ),
                    ],
                    'prefix_class' => 'ecw-gallery-depth-temp-layout-',
                ]
            );

            $this->add_responsive_control(
                'card_gap',
                [
                    'label'      => __( 'Card Gap', 'elementor-custom-widgets' ),
                    'type'       => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'vw' ],
                    'default'    => [
                        'unit' => 'px',
                        'size' => 20,
                    ],
                    'selectors'  => [
                        '{{WRAPPER}} .ecw-gallery-depth-temp-card' => 'gap: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'card_content_gap',
                [
                    'label'      => __( 'Card Gap Mobile', 'elementor-custom-widgets' ),
                    'type'       => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'vw' ],
                    'default'    => [
                        'unit' => 'px',
                        'size' => 20,
                    ],
                    'selectors'  => [
                        '{{WRAPPER}} .ecw-gallery-depth-temp-content' => 'gap: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'gallery_parent_overflow',
                [
                    'label'    => __( 'Parent Overflow', 'elementor-custom-widgets' ),
                    'type'     => Controls_Manager::SELECT,
                    'default'  => 'visible',
                    'options'  => [
                        'visible' => __( 'Visible', 'elementor-custom-widgets' ),
                        'hidden'  => __( 'Hidden', 'elementor-custom-widgets' ),
                        'scroll'  => __( 'Scroll', 'elementor-custom-widgets' ),
                        'auto'    => __( 'Auto', 'elementor-custom-widgets' ),
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ecw-gallery-depth-temp-parent' => 'overflow: {{VALUE}};',
                    ],
                ]
            );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="ecw-gallery-depth-temp-parent">
            <div class="ecw-gallery-depth-temp-content">
                <?php if ( ! empty( $settings['cards'] ) ) : ?>
                    <?php foreach ( $settings['cards'] as $item ) : ?>
                        <div class="ecw-gallery-depth-temp-card">
                            <?php
                            $template_id = ! empty( $item['card_template_id'] ) ? (int) $item['card_template_id'] : 0;

                            if ( $template_id ) {
                                echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template_id, true );
                            }
                            ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    protected function _content_template() {
        // Live preview not available for dynamic template rendering.
    }
}