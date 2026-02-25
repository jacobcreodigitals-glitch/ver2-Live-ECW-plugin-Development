<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;



class ECW_Arc_Scroll_Widget extends Widget_Base {

    public function get_name() {
        return 'ecw_arc_scroll';
    }

    public function get_title() {
        return __( 'Arc Scroll Effect', 'elementor-custom-widgets' );
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
            'ecw-arc-scroll-js'   // Custom JS for this widget
        ];
    }

    public function get_style_depends() {
        return [
            'ecw-arc-scroll-css'  // Custom widget styles
        ];
    }


    protected function _register_controls() {

        $repeater = new \Elementor\Repeater();

        // ---------------- REPEATER FIELDS ----------------
        $repeater->add_control(
            'collection',
            [
                'label'   => __( 'Collection Name', 'elementor-custom-widgets' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Collection',
            ]
        );

        $repeater->add_control(
            'subhead',
            [
                'label'   => __( 'Subhead', 'elementor-custom-widgets' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Subhead',
            ]
        );

        $repeater->add_control(
            'title',
            [
                'label'   => __( 'Title', 'elementor-custom-widgets' ),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => 'Title',
            ]
        );

        $repeater->add_control(
            'albums',
            [
                'label'       => __( 'Texarea', 'elementor-custom-widgets' ),
                'type'        => \Elementor\Controls_Manager::TEXTAREA,
                'default'     => 'Album 1, Album 2',
                'description' => 'Comma separated',
            ]
        );

        $repeater->add_control(
            'bg_color',
            [
                'label'   => __( 'Card Background Color', 'elementor-custom-widgets' ),
                'type'    => \Elementor\Controls_Manager::COLOR,
                'default' => '#FC4C3B',
            ]
        );

        $repeater->add_control(
            'text_color',
            [
                'label'   => __( 'Card Text Color', 'elementor-custom-widgets' ),
                'type'    => \Elementor\Controls_Manager::COLOR,
                'default' => '#702626',
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => __( 'Image', 'elementor-custom-widgets' ),
                'type'  => \Elementor\Controls_Manager::MEDIA,
            ]
        );

        // ---------------- REPEATER BORDER CONTROL ----------------
        $repeater->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name'     => 'card_border',
                'label'    => __( 'Border', 'elementor-custom-widgets' ),
                'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .ecw-arc-cards__item',
            ]
        );

        $repeater->add_control(
            'card_border_radius',
            [
                'label'      => __( 'Border Radius', 'elementor-custom-widgets' ),
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .ecw-arc-cards__item' =>
                        'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // ---------------- CONTENT SECTION ----------------
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'elementor-custom-widgets' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'cards',
            [
                'label'       => __( 'Cards', 'elementor-custom-widgets' ),
                'type'        => \Elementor\Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ title }}}',
                'default'     => [],
            ]
        );

        $this->end_controls_section();

        // ---------------- TYPOGRAPHY STYLE SECTION ----------------
        $this->start_controls_section(
            'card_typography_section',
            [
                'label' => __( 'Typography', 'elementor-custom-widgets' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        

        /* Collection */
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'collection_typography',
                'label'    => __( 'Collection', 'elementor-custom-widgets' ),
                'selector' => '{{WRAPPER}} .ecw-arc-cards .ecw-arc-cards__item .ecw-arc-cards__item-header .ecw-arc-sub-text-1',
            ]
        );

        /* Subhead */
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'subhead_typography',
                'label'    => __( 'Subhead', 'elementor-custom-widgets' ),
                'selector' => '{{WRAPPER}} .ecw-arc-cards .ecw-arc-cards__item .ecw-arc-cards__item-header .ecw-arc-sub-text-2',
            ]
        );

        /* Title */
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'label'    => __( 'Title', 'elementor-custom-widgets' ),
                'selector' => '{{WRAPPER}} .ecw-arc-cards .ecw-arc-cards__item h2',
            ]
        );

        /* Albums */
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'albums_typography',
                'label'    => __( 'Lorem', 'elementor-custom-widgets' ),
                'selector' => '{{WRAPPER}} .ecw-arc-cards .ecw-arc-cards__item p',
            ]
        );

        $this->add_responsive_control(
            'card_padding',
            [
                'label'      => __( 'Card Padding', 'elementor-custom-widgets' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'selectors'  => [
                    '{{WRAPPER}} .ecw-arc-cards__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    'body:not(.elementor-editor-active) {{WRAPPER}} .ecw-arc-cards__ring' =>
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



    protected function render() {
        $settings = $this->get_settings_for_display();
         $angle_spacing = ! empty( $settings['cards_angle_spacing']['size'] )
            ? floatval( $settings['cards_angle_spacing']['size'] )
            : 3;
        ?>
        <section class="ecw-arc-cards" data-angle="<?php echo esc_attr( $angle_spacing ); ?>">
            <div class="ecw-arc-cards__pin">
                <div class="ecw-arc-cards__viewport">
                    <div class="ecw-arc-cards__track">
                        <?php foreach ( $settings['cards'] as $index => $card ) : ?>
                            <div class="ecw-arc-cards__ring elementor-repeater-item-<?php echo esc_attr( $card['_id'] ); ?>">
                                <div class="ecw-arc-cards__item" style="
                                    background-color: <?php echo esc_attr( $card['bg_color'] ); ?>;
                                    color: <?php echo esc_attr( $card['text_color'] ); ?>;
                                    <?php if ( ! empty( $card['image']['url'] ) ) : ?>
                                        background-image: url('<?php echo esc_url( $card['image']['url'] ); ?>');
                                        background-size: cover;
                                        background-position: center center;
                                    <?php endif; ?>
                                ">
                                    <div class="ecw-arc-cards__item-header">
                                        <p><span class="ecw-arc-sub-text-1"><?php echo esc_html( $card['collection'] ); ?></span></p>
                                        <p><span class="ecw-arc-sub-text-2"><?php echo esc_html( $card['subhead'] ); ?></span></p>
                                    </div>

                                    <div>
                                        <h2><?php echo esc_html( $card['title'] ); ?></h2>
                                    </div>

                                    <div>
                                        <?php
                                        $albums = explode( ',', $card['albums'] );
                                        foreach ( $albums as $album ) {
                                            echo '<p>' . esc_html( trim( $album ) ) . '</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }


    protected function _content_template() {
        ?>
        <section class="ecw-arc-cards">
            <div class="ecw-arc-cards__pin">
                <div class="ecw-arc-cards__viewport">
                    <div class="ecw-arc-cards__track">
                        <# _.each( settings.cards, function( card ) { #>
                            <div class="ecw-arc-cards__ring elementor-repeater-item-{{ card._id }}">
                                <div class="ecw-arc-cards__item" style="
                                    background-color: {{ card.bg_color }};
                                    color: {{ card.text_color }};
                                    <# if ( card.image.url ) { #>
                                        background-image: url('{{ card.image.url }}');
                                        background-size: cover;
                                        background-position: center center;
                                    <# } #>
                                ">
                                    <div class="ecw-arc-cards__item-header">
                                        <p><span class="ecw-arc-sub-text-1">{{ card.collection }}</span></p>
                                        <p><span class="ecw-arc-sub-text-2">{{ card.subhead }}</span></p>
                                    </div>

                                    <div>
                                        <h2>{{ card.title }}</h2>
                                    </div>

                                    <div>
                                        <# var albums = card.albums.split(','); _.each(albums, function(album){ #>
                                            <p>{{ album.trim() }}</p>
                                        <# }); #>
                                    </div>
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