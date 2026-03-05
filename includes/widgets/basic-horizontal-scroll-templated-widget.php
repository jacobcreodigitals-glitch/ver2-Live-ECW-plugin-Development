<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

class ECW_Basic_Horizontal_Scroll_Templated_Widget extends Widget_Base {

    public function get_name() {
        return 'ecw_basic_horizontal_scroll_templated';
    }

    public function get_title() {
        return __( 'Basic Horizontal Scroll Templated', 'elementor-custom-widgets' );
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
            'ecw-hr-scroll-templated-js'
        ];
    }

    public function get_style_depends() {
        return [
            'ecw-style-reset',
            'ecw-hr-scroll-templated-css'
        ];
    }

    // -------------------------------------------------------------------------
    // Helper: fetch all published Elementor templates (container + page)
    // -------------------------------------------------------------------------
    private function get_elementor_templates() {
        $templates = [ '' => __( '— Select Template —', 'elementor-custom-widgets' ) ];
        $posts = get_posts( [
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
        ] );

        if ( ! empty( $posts ) ) {
            foreach ( $posts as $post ) {
                $type = get_post_meta( $post->ID, '_elementor_template_type', true );
                $templates[ $post->ID ] = ucfirst( $type ) . ' — ' . $post->post_title;
            }
        }
        return $templates;
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

        // Replace heading/content with template selector
        $repeater->add_control(
            'slide_template',
            [
                'label' => __( 'Slide Template', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::SELECT,
                'options' => $this->get_elementor_templates(),
            ]
        );

        $repeater->add_control(
            'slide_title',
            [
                'label' => __( 'Slide Title', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'label_block' => true,
                'placeholder' => __( 'Optional: Enter a title for this slide', 'elementor-custom-widgets' ),
            ]
        );

        $this->add_control(
            'slides',
            [
                'label' => __( 'Slides List', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [],
                'title_field' => '{{{ slide_title || slide_template }}}',
            ]
        );

        $this->add_responsive_control(
            'slides_gap',
            [
                'label' => __( 'Slides Gap', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range' => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
                'default' => [ 'size' => 20, 'unit' => 'px' ],
                'selectors' => [ '{{WRAPPER}} .ecw-hr-slider-templated-content' => 'gap: {{SIZE}}{{UNIT}};' ],
            ]
        );

        $this->add_responsive_control(
            'slide_width',
            [
                'label' => __( 'Slide Width', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'vw', '%', 'px' ],
                'range' => [ 'vw' => [ 'min' => 10, 'max' => 100 ] ],
                'default' => [ 'size' => 30, 'unit' => 'vw' ],
                'selectors' => [ '{{WRAPPER}} .ecw-hr-content-templated-slide' => 'min-width: {{SIZE}}{{UNIT}};' ],
            ]
        );

        $this->add_control(
            'slide_background',
            [
                'label' => __( 'Slide Background', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [ '{{WRAPPER}} .ecw-hr-content-templated-slide' => 'background: {{VALUE}};' ],
            ]
        );

        $this->add_control(
            'scroll_trigger_class',
            [
                'label' => __('Custom Scroll Trigger Class', 'textdomain'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => '.my-custom-class',
            ]
        );

        $this->add_control(
            'parent_overflow',
            [
                'label' => __( 'Parent Overflow', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'hidden',
                'options' => [
                    'visible' => __( 'Visible', 'elementor-custom-widgets' ),
                    'hidden' => __( 'Hidden', 'elementor-custom-widgets' ),
                ],
            ]
        );

        $this->add_responsive_control(
            'parent_height',
            [
                'label' => __( 'Parent Height', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'vh', '%' ],
                'range' => [
                    'px' => [ 'min' => 100, 'max' => 2000 ],
                    'vh' => [ 'min' => 10, 'max' => 200 ],
                    '%'  => [ 'min' => 10, 'max' => 200 ],
                ],
                'default' => [ 'size' => 100, 'unit' => 'vh' ],
                'selectors' => [ '{{WRAPPER}} .ecw-hr-slider-templated-parent' => 'height: {{SIZE}}{{UNIT}};' ],
            ]
        );

        $this->add_control(
            'scroll_end_offset',
            [
                'label' => __('End Scroll Offset', 'textdomain'),
                'type' => Controls_Manager::NUMBER,
                'default' => '',
                'min' => 0,
                'step' => 10,
            ]
        );

        $this->end_controls_section();
        // -----------------------
        // Content Tab End
        // -----------------------

    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $trigger_class = $settings['scroll_trigger_class'] ?? '';
        $end_offset = $settings['scroll_end_offset'] ?? '';
        $overflow = $settings['parent_overflow'] ?? 'hidden';

        if ( empty( $settings['slides'] ) ) return;

        ?>
        <div class="ecw-hr-slider-templated-parent" data-scroll-trigger="<?php echo esc_attr($trigger_class); ?>" data-overflow="<?php echo esc_attr($overflow); ?>"  data-end-offset="<?php echo esc_attr($end_offset); ?>">
            <div class="ecw-hr-slider-templated-content">
                <?php foreach ( $settings['slides'] as $slide ) : ?>
                    <div class="ecw-hr-content-templated-slide">
                        <?php
                        if ( ! empty( $slide['slide_template'] ) ) {
                            echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $slide['slide_template'] );
                        }
                        ?>
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
            <div class="ecw-hr-slider-templated-parent">
                <div class="ecw-hr-slider-templated-content">
                    <# _.each( settings.slides, function( slide ) { #>
                        <div class="ecw-hr-content-templated-slide">
                            {{{ slide.slide_title ? slide.slide_title : slide.slide_template }}}
                        </div>
                    <# }); #>
                </div>
            </div>
        <# } #>
        <?php
    }
}