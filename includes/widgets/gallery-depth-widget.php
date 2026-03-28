<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

class ECW_Gallery_Depth_Widget extends Widget_Base {

    public function get_name() {
        return 'ecw_gallery_depth';
    }

    public function get_title() {
        return __( 'ECW Gallery Depth', 'elementor-custom-widgets' );
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
                    'card_content',
                    [
                        'label' => __( 'Content', 'elementor-custom-widgets' ),
                        'type' => Controls_Manager::TEXT,
                        'default' => __( 'Card Item', 'elementor-custom-widgets' ),
                        'label_block' => true,
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
                        'title_field' => '{{{ card_content }}}',
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
                        'selectors' => [
                            '{{WRAPPER}} .ecw-gallery-depth-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
            ?>
                <div class="ecw-gallery-depth-parent">
                    <div class="ecw-gallery-depth-content">
                        <?php if ( ! empty( $settings['cards'] ) ) : ?>
                            <?php foreach ( $settings['cards'] as $item ) : ?>
                                <div class="ecw-gallery-depth-card">
                                    <?php echo esc_html( $item['card_content'] ); ?>
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
