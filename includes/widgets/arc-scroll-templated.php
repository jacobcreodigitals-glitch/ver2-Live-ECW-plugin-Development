<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Plugin;

class ECW_Arc_Scroll_Templated_Widget extends Widget_Base {

    public function get_name() {
        return 'ecw_arc_scroll_templated_widget';
    }

    public function get_title() {
        return __( 'Arc Scroll Templated', 'elementor-custom-widgets' );
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
            'ecw-arc-scroll-templated-js',
        ];
    }

    public function get_style_depends() {
        return [
            'ecw-arc-scroll-templated-css',
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

    // -------------------------------------------------------------------------
    // Controls
    // -------------------------------------------------------------------------
    protected function _register_controls() {

        $templates = $this->get_elementor_templates();

        // ---- Repeater ----
        $repeater = new Repeater();

        $repeater->add_control(
            'card_label',
            [
                'label'   => __( 'Card Label (for editor)', 'elementor-custom-widgets' ),
                'type'    => Controls_Manager::TEXT,
                'default' => __( 'Card', 'elementor-custom-widgets' ),
            ]
        );

        $repeater->add_control(
            'template_id',
            [
                'label'   => __( 'Select Template', 'elementor-custom-widgets' ),
                'type'    => Controls_Manager::SELECT,
                'options' => $templates,
                'default' => '',
            ]
        );

        $repeater->add_control(
            'bg_color',
            [
                'label'   => __( 'Card Background Color', 'elementor-custom-widgets' ),
                'type'    => Controls_Manager::COLOR,
                'default' => '#bbbbbb',
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => __( 'Card Background Image', 'elementor-custom-widgets' ),
                'type'  => Controls_Manager::MEDIA,
            ]
        );

        $repeater->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name'     => 'card_border',
                'label'    => __( 'Border', 'elementor-custom-widgets' ),
                'selector' => '{{WRAPPER}} .elementor-repeater-item-{{ID}} .ecw-arc-cards_TEMP_item',
            ]
        );

        $repeater->add_control(
            'card_border_radius',
            [
                'label'      => __( 'Border Radius', 'elementor-custom-widgets' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'vw' ],
                'selectors'  => [
                    '{{WRAPPER}} .elementor-repeater-item-{{ID}} .ecw-arc-cards_TEMP_item' =>
                        'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // ---- Content Section ----
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Cards', 'elementor-custom-widgets' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'cards',
            [
                'label'       => __( 'Cards', 'elementor-custom-widgets' ),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ card_label }}}',
                'default'     => [],
            ]
        );






        $this->end_controls_section();

        // ---- Style Section ----
        $this->start_controls_section(
            'style_section',
            [
                'label' => __( 'Cards Style', 'elementor-custom-widgets' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label'      => __( 'Card Padding', 'elementor-custom-widgets' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors'  => [
                    '{{WRAPPER}} .ecw-arc-cards_TEMP_item' =>
                        'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'card_border_radius_global',
            [
                'label'      => __( 'Card Border Radius', 'elementor-custom-widgets' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'default'    => [
                    'top'    => '2',
                    'right'  => '2',
                    'bottom' => '2',
                    'left'   => '2',
                    'unit'   => 'px',
                ],
                'selectors'  => [
                    '{{WRAPPER}} .ecw-arc-cards_TEMP_item' =>
                        'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_control(
            'arc_radius',
            [
                'label'       => __( 'Arc Radius (vw)', 'elementor-custom-widgets' ),
                'description' => __( 'Size of the circular track. Larger = flatter arc. Default: 250', 'elementor-custom-widgets' ),
                'type'        => Controls_Manager::SLIDER,
                'size_units'  => [ 'vw' ],
                'range'       => [
                    'vw' => [
                        'min'  => 100,
                        'max'  => 600,
                        'step' => 10,
                    ],
                ],
                'default'     => [
                    'unit' => 'vw',
                    'size' => 250,
                ],
                'selectors'   => [
                    'body:not(.elementor-editor-active) {{WRAPPER}} .ecw-arc-cards_TEMP_ring' =>
                        'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'cards_angle_spacing',
            [
                'label'       => __( 'Cards Angle Spacing (°)', 'elementor-custom-widgets' ),
                'description' => __( 'Angular gap between each card on the arc. Default: 3', 'elementor-custom-widgets' ),
                'type'        => Controls_Manager::SLIDER,
                'size_units'  => [ 'px' ],
                'range'       => [
                    'px' => [
                        'min'  => 1,
                        'max'  => 20,
                        'step' => 1,
                    ],
                ],
                'default'     => [
                    'size' => 3,
                ],
            ]
        );

        $this->end_controls_section();
    }

    // -------------------------------------------------------------------------
    // Render (PHP / front-end)
    // -------------------------------------------------------------------------
    protected function render() {

        $settings = $this->get_settings_for_display();

        if ( empty( $settings['cards'] ) || ! is_array( $settings['cards'] ) ) {
            return;
        }

        $angle_spacing = ! empty( $settings['cards_angle_spacing']['size'] )
            ? floatval( $settings['cards_angle_spacing']['size'] )
            : 3;
        ?>
        <section class="ecw-arc-cards" data-angle-temp="<?php echo esc_attr( $angle_spacing ); ?>">
            <div class="ecw-arc-cards_TEMP_pin">
                <div class="ecw-arc-cards_TEMP_viewport">
                    <div class="ecw-arc-cards_TEMP_track">

                        <?php foreach ( $settings['cards'] as $card ) : ?>

                            <?php
                            // Build inline style for card wrapper
                            $styles = '';

                            if ( ! empty( $card['bg_color'] ) ) {
                                $styles .= 'background-color:' . esc_attr( $card['bg_color'] ) . ';';
                            }

                            if ( ! empty( $card['image']['url'] ) ) {
                                $styles .= 'background-image:url(' . esc_url( $card['image']['url'] ) . ');';
                                $styles .= 'background-size:cover;background-position:center center;';
                            }
                            ?>

                            <div class="ecw-arc-cards_TEMP_ring elementor-repeater-item-<?php echo esc_attr( $card['_id'] ); ?>">
                                <div class="ecw-arc-cards_TEMP_item" style="<?php echo $styles; ?>">

                                    <?php if ( ! empty( $card['template_id'] ) ) : ?>

                                        <?php
                                        $template_id = absint( $card['template_id'] );

                                        if ( get_post_status( $template_id ) && class_exists( '\Elementor\Plugin' ) ) {

                                            echo \Elementor\Plugin::instance()
                                                ->frontend
                                                ->get_builder_content_for_display( $template_id );

                                        } else {
                                            echo '<p>' . esc_html__( 'Template not found or Elementor not loaded.', 'elementor-custom-widgets' ) . '</p>';
                                        }
                                        ?>

                                    <?php else : ?>
                                        <p><?php esc_html_e( 'No template selected.', 'elementor-custom-widgets' ); ?></p>
                                    <?php endif; ?>

                                </div>
                            </div>

                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
        </section>
        <?php
    }

    // -------------------------------------------------------------------------
    // Editor preview (JS template) — static placeholder per card
    // -------------------------------------------------------------------------
    protected function _content_template() {
        ?>
        <section class="ecw-arc-cards">
            <div class="ecw-arc-cards_TEMP_pin">
                <div class="ecw-arc-cards_TEMP_viewport">
                    <div class="ecw-arc-cards_TEMP_track">
                        <# _.each( settings.cards, function( card ) { #>
                            <div class="ecw-arc-cards_TEMP_ring elementor-repeater-item-{{ card._id }}">
                                <div class="ecw-arc-cards_TEMP_item" style="
                                    background-color: {{ card.bg_color }};
                                    <# if ( card.image && card.image.url ) { #>
                                        background-image: url('{{ card.image.url }}');
                                        background-size: cover;
                                        background-position: center center;
                                    <# } #>
                                ">
                                    <p style="opacity:0.5;text-align:center;padding:2em 0;">
                                        {{ card.card_label }} — Template ID: {{ card.template_id }}
                                    </p>
                                </div>
                            </div>
                        <# }); #>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}