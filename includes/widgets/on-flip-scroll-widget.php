<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

class ECW_OnFlip_Widget extends Widget_Base {

    public function get_name() {
        return 'ecw_onflip_widget';
    }

    public function get_title() {
        return __( 'On Flip', 'elementor-custom-widgets' );
    }

    public function get_icon() {
        return 'fa fa-columns';
    }

    public function get_categories() {
        return [ 'ecw-category' ];
    }

    public function get_script_depends() {
        return [
            'ecw-gsap',
            'ecw-scrolltrigger',
            'ecw-flip',
            'ecw-onflip-js'
        ];
    }

    public function get_style_depends() {
        return [
            'ecw-style-reset',
            'ecw-onflip-css'
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
            'no_found_rows'  => true,
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

        // ------------------------------------------------
        // CONTENT
        // ------------------------------------------------
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'elementor-custom-widgets' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'default_columns',
            [
                'label'   => 'Default Columns',
                'type'    => Controls_Manager::NUMBER,
                'default' => 4,
                'min'     => 1,
                'max'     => 6,
            ]
        );

        $this->add_control(
            'new_columns',
            [
                'label'   => 'Columns After Scroll',
                'type'    => Controls_Manager::NUMBER,
                'default' => 2,
                'min'     => 1,
                'max'     => 6,
            ]
        );

        $this->add_control(
            'heading_tag',
            [
                'label'   => 'Heading Tag',
                'type'    => Controls_Manager::SELECT,
                'default' => 'h3',
                'options' => [
                    'h2'   => 'H2',
                    'h3'   => 'H3',
                    'h4'   => 'H4',
                    'h5'   => 'H5',
                    'h6'   => 'H6',
                    'span' => 'Span',
                ],
            ]
        );

        // Repeater
        $repeater = new Repeater();

        $repeater->add_control(
            'card_heading',
            [
                'label'       => 'Heading',
                'type'        => Controls_Manager::TEXT,
                'default'     => 'Card Title',
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'card_icon',
            [
                'label' => __( 'Icon', 'elementor-custom-widgets' ),
                'type'  => Controls_Manager::ICONS,
            ]
        );

        $repeater->add_control(
            'card_content',
            [
                'label'   => 'Content',
                'type'    => Controls_Manager::WYSIWYG,
                'default' => 'Lorem ipsum dummy text.',
            ]
        );

        $repeater->add_control(
            'default_style_heading',
            [
                'label'     => __( 'Default State', 'elementor-custom-widgets' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $repeater->add_control(
            'bg_default',
            [
                'label'     => 'Background Color (Default)',
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecw-onflip-grid-con {{CURRENT_ITEM}}.ecw-onflip-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $repeater->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'border_default',
                'selector' => '{{WRAPPER}} .ecw-onflip-grid-con {{CURRENT_ITEM}}.ecw-onflip-card',
            ]
        );

        $repeater->add_responsive_control(
            'radius_default',
            [
                'label'      => 'Border Radius (Default)',
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors'  => [
                    '{{WRAPPER}} .ecw-onflip-grid-con {{CURRENT_ITEM}}.ecw-onflip-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $repeater->add_responsive_control(
            'padding_default',
            [
                'label'      => 'Padding (Default)',
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors'  => [
                    '{{WRAPPER}} .ecw-onflip-grid-con {{CURRENT_ITEM}}.ecw-onflip-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $repeater->add_control(
            'active_style_heading',
            [
                'label'     => __( 'After Flip State', 'elementor-custom-widgets' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $repeater->add_control(
            'bg_active',
            [
                'label'     => 'Background Color (After Flip)',
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecw-onflip-grid-con.ecw-onflip-new-columns {{CURRENT_ITEM}}.ecw-onflip-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $repeater->add_control(
            'card_template',
            [
                'label'       => 'Elementor Template',
                'type'        => Controls_Manager::SELECT2,
                'options'     => $this->get_elementor_templates(),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'cards',
            [
                'label'       => 'Cards',
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ card_heading }}}',
            ]
        );

        $this->end_controls_section();

        // ------------------------------------------------
        // STYLE — Heading
        // ------------------------------------------------
        $this->start_controls_section(
            'style_heading_section',
            [
                'label' => __( 'Heading', 'elementor-custom-widgets' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'heading_typography',
                'selector' => '{{WRAPPER}} .ecw-onflip-card .ecw-onflip-title',
            ]
        );

        $this->add_control(
            'heading_color',
            [
                'label'     => __( 'Text Color', 'elementor-custom-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecw-onflip-card .ecw-onflip-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'heading_color_active',
            [
                'label'     => __( 'Text Color (After Flip)', 'elementor-custom-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecw-onflip-grid-con.ecw-onflip-new-columns .ecw-onflip-card .ecw-onflip-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_size',
            [
                'label'      => __( 'Icon Size', 'elementor-custom-widgets' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [
                    'px' => [ 'min' => 10, 'max' => 200 ],
                ],
                'separator'  => 'before',
                'selectors'  => [
                    '{{WRAPPER}} .ecw-onflip-head .ecw-onflip-icon'     => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ecw-onflip-head .ecw-onflip-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label'     => __( 'Icon Color', 'elementor-custom-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecw-onflip-head .ecw-onflip-icon i'   => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ecw-onflip-head .ecw-onflip-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color_active',
            [
                'label'     => __( 'Icon Color (After Flip)', 'elementor-custom-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecw-onflip-grid-con.ecw-onflip-new-columns .ecw-onflip-head .ecw-onflip-icon i'   => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ecw-onflip-grid-con.ecw-onflip-new-columns .ecw-onflip-head .ecw-onflip-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'head_direction',
            [
                'label'     => __( 'Head Direction', 'elementor-custom-widgets' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'row'    => [
                        'title' => __( 'Row', 'elementor-custom-widgets' ),
                        'icon'  => 'eicon-arrow-right',
                    ],
                    'column' => [
                        'title' => __( 'Column', 'elementor-custom-widgets' ),
                        'icon'  => 'eicon-arrow-down',
                    ],
                ],
                'default'   => 'row',
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .ecw-onflip-head' => 'flex-direction: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'head_align_items',
            [
                'label'   => __( 'Vertical Alignment', 'elementor-custom-widgets' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'flex-start' => [
                        'title' => __( 'Start', 'elementor-custom-widgets' ),
                        'icon'  => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'elementor-custom-widgets' ),
                        'icon'  => 'eicon-v-align-middle',
                    ],
                    'flex-end' => [
                        'title' => __( 'End', 'elementor-custom-widgets' ),
                        'icon'  => 'eicon-v-align-bottom',
                    ],
                ],
                'default'   => 'center',
                'toggle'    => false,
                'selectors' => [
                    '{{WRAPPER}} .ecw-onflip-head' => 'align-items: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
    'content_text_align',
    [
        'label'   => __( 'Text Alignment', 'elementor-custom-widgets' ),
        'type'    => Controls_Manager::CHOOSE,
        'options' => [
            'left' => [
                'title' => __( 'Left', 'elementor-custom-widgets' ),
                'icon'  => 'eicon-text-align-left',
            ],
            'center' => [
                'title' => __( 'Center', 'elementor-custom-widgets' ),
                'icon'  => 'eicon-text-align-center',
            ],
            'right' => [
                'title' => __( 'Right', 'elementor-custom-widgets' ),
                'icon'  => 'eicon-text-align-right',
            ],
            'justify' => [
                'title' => __( 'Justified', 'elementor-custom-widgets' ),
                'icon'  => 'eicon-text-align-justify',
            ],
        ],
        'default' => 'left',
        'toggle'  => true,
        'selectors' => [
            '{{WRAPPER}} .ecw-onflip-content p' => 'text-align: {{VALUE}};',
        ],
    ]
);


        $this->add_responsive_control(
            'head_icon_gap',
            [
                'label'      => __( 'Gap (Icon to Heading)', 'elementor-custom-widgets' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [
                    'px' => [ 'min' => 0, 'max' => 100 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .ecw-onflip-head' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'head_content_gap',
            [
                'label'      => __( 'Gap (Head to Content)', 'elementor-custom-widgets' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem' ],
                'range'      => [
                    'px' => [ 'min' => 0, 'max' => 100 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .ecw-onflip-card' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

$this->add_responsive_control(
    'grid_gap',
    [
        'label'      => __( 'Grid Gap', 'elementor-custom-widgets' ),
        'type'       => Controls_Manager::SLIDER,
        'size_units' => [ 'px', '%', 'em', 'rem' ],
        'range'      => [
            'px' => [
                'min' => 0,
                'max' => 100,
            ],
        ],
        'default'    => [
            'unit' => 'px',
            'size' => 20,
        ],
        'selectors'  => [
            '{{WRAPPER}} .ecw-onflip-grid-con' => 'gap: {{SIZE}}{{UNIT}};',
        ],
    ]
);


        $this->end_controls_section();

        // ------------------------------------------------
        // STYLE — Content
        // ------------------------------------------------
        $this->start_controls_section(
            'style_content_section',
            [
                'label' => __( 'Content', 'elementor-custom-widgets' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'content_typography',
                'selector' => '{{WRAPPER}} .ecw-onflip-card .ecw-onflip-content',
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label'     => __( 'Text Color', 'elementor-custom-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecw-onflip-card .ecw-onflip-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_color_active',
            [
                'label'     => __( 'Text Color (After Flip)', 'elementor-custom-widgets' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ecw-onflip-grid-con.ecw-onflip-new-columns .ecw-onflip-card .ecw-onflip-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings  = $this->get_settings_for_display();
        $widget_id = 'ecw-onflip-' . $this->get_id();

        ?>

        <div
            id="<?php echo esc_attr( $widget_id ); ?>"
            class="ecw-onflip-grid-con"
            data-default-columns="<?php echo esc_attr( $settings['default_columns'] ); ?>"
            data-new-columns="<?php echo esc_attr( $settings['new_columns'] ); ?>"
        >

            <?php if ( ! empty( $settings['cards'] ) ) : ?>

                <?php foreach ( $settings['cards'] as $card ) : ?>

                    <div class="ecw-onflip-card elementor-repeater-item-<?php echo esc_attr( $card['_id'] ); ?>">

                        <div class="ecw-onflip-head">

                            <?php if ( ! empty( $card['card_icon']['value'] ) ) : ?>
                                <span class="ecw-onflip-icon">
                                    <?php \Elementor\Icons_Manager::render_icon( $card['card_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                                </span>
                            <?php endif; ?>

                            <?php if ( ! empty( $card['card_heading'] ) ) :
                                $tag = ! empty( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h3';
                            ?>
                                <<?php echo esc_attr( $tag ); ?> class="ecw-onflip-title">
                                    <?php echo esc_html( $card['card_heading'] ); ?>
                                </<?php echo esc_attr( $tag ); ?>>
                            <?php endif; ?>

                        </div>

                        <?php if ( ! empty( $card['card_content'] ) ) : ?>
                            <div class="ecw-onflip-content">
                                <?php echo $card['card_content']; ?>
                            </div>
                        <?php endif; ?>

                        <div class="ecw-onflip-con-template">
                            <?php
                            if ( ! empty( $card['card_template'] ) ) {
                                echo \Elementor\Plugin::instance()
                                    ->frontend
                                    ->get_builder_content_for_display( $card['card_template'] );
                            }
                            ?>
                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

        <?php
    }

    protected function _content_template() {}

}