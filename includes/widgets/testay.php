<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

class ECW_Draggable_Gallery_Widget extends Widget_Base {

    public function get_name() {
        return 'ecw_draggable_gallery_widget';
    }

    public function get_title() {
        return __( 'Draggable Gallery', 'elementor-custom-widgets' );
    }

    public function get_icon() {
        return 'eicon-code';
    }

    public function get_categories() {
        return [ 'ecw-category' ];
    }

    public function get_script_depends() {
        return [];
    }

    public function get_style_depends() {
        return [];
    }

    protected function _register_controls() {

        // -----------------------
        // Content Tab Start
        // -----------------------
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'elementor-custom-widgets' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'ecw_draggable_gallery_heading',
            [
                'label' => __( 'Heading', 'elementor-custom-widgets' ),
                'type'  => Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $repeater->add_control(
            'ecw_draggable_gallery_content',
            [
                'label' => __( 'Content', 'elementor-custom-widgets' ),
                'type'  => Controls_Manager::WYSIWYG,
                'default' => '',
            ]
        );

        $repeater->add_control(
            'ecw_draggable_gallery_image',
            [
                'label' => __( 'Image', 'elementor-custom-widgets' ),
                'type'  => Controls_Manager::MEDIA,
            ]
        );

        $this->add_control(
            'ecw_draggable_gallery_list',
            [
                'label'       => __( 'Items', 'elementor-custom-widgets' ),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [],
                'title_field' => '{{{ ecw_draggable_gallery_heading }}}',
            ]
        );

        $this->add_control(
            'ecw_draggable_gallery_heading_tag',
            [
                'label'   => __( 'Heading Tag', 'elementor-custom-widgets' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'h2',
                'options' => [ 'h1' => __( 'H1', 'elementor-custom-widgets' ), 'h2' => __( 'H2', 'elementor-custom-widgets' ), 'h3' => __( 'H3', 'elementor-custom-widgets' ), 'h4' => __( 'H4', 'elementor-custom-widgets' ), 'h5' => __( 'H5', 'elementor-custom-widgets' ), 'h6' => __( 'H6', 'elementor-custom-widgets' ), 'p' => __( 'p', 'elementor-custom-widgets' ), 'div' => __( 'div', 'elementor-custom-widgets' ) ],
            ]
        );

        $this->end_controls_section();
        // -----------------------
        // Content Tab End
        // -----------------------

        // -----------------------
        // Style Tab Start
        // -----------------------
        $this->start_controls_section(
            'style_section',
            [
                'label' => __( 'Style', 'elementor-custom-widgets' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // ── Parent Widget (.ecw-widget) ──
        $this->add_responsive_control(
            'ecw_draggable_gallery_widget_gap',
            [
                'label'      => __( 'Gap', 'elementor-custom-widgets' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em' ],
                'range'      => [ 'px' => [ 'min' => 0, 'max' => 1200 ], '%' => [ 'min' => 0, 'max' => 100 ], 'em' => [ 'min' => 0, 'max' => 100 ] ],
                'selectors'  => [ '{{WRAPPER}} .ecw-draggable-gallery-widget' => 'gap: {{SIZE}}{{UNIT}};' ],
            ]
        );

        $this->end_controls_section();
        // -----------------------
        // Style Tab End
        // -----------------------
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $items = $settings['ecw_draggable_gallery_list'] ?? [];
        ?>
        <div class="ecw-draggable-gallery-widget">
            <?php foreach ( $items as $item ) : ?>
                <div class="ecw-draggable-gallery-item elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>">
                    <?php if ( ! empty( $item['ecw_draggable_gallery_heading'] ) ) : ?>
                        <<?php echo esc_attr( ! empty( $settings['ecw_draggable_gallery_heading_tag'] ) ? $settings['ecw_draggable_gallery_heading_tag'] : 'h2' ); ?> class="ecw-draggable-gallery-heading"><?php echo esc_html( $item['ecw_draggable_gallery_heading'] ); ?></<?php echo esc_attr( ! empty( $settings['ecw_draggable_gallery_heading_tag'] ) ? $settings['ecw_draggable_gallery_heading_tag'] : 'h2' ); ?>>
                    <?php endif; ?>
                    <?php if ( ! empty( $item['ecw_draggable_gallery_content'] ) ) : ?>
                        <div class="ecw-draggable-gallery-text"><?php echo wp_kses_post( $item['ecw_draggable_gallery_content'] ); ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $item['ecw_draggable_gallery_image']['url'] ) ) : ?>
                        <img class="ecw-draggable-gallery-image" src="<?php echo esc_url( $item['ecw_draggable_gallery_image']['url'] ); ?>" alt="">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    protected function _content_template() {
        ?>
        <# var items = settings.ecw_draggable_gallery_list; #>
        <div class="ecw-draggable-gallery-widget">
            <# _.each( items, function( item ) { #>
                <div class="ecw-draggable-gallery-item elementor-repeater-item-{{ item._id }}">
                    <# if ( item.ecw_draggable_gallery_heading ) { var _tag = settings.ecw_draggable_gallery_heading_tag || 'h2'; #><<# print(_tag) #> class="ecw-draggable-gallery-heading">{{ item.ecw_draggable_gallery_heading }}</<# print(_tag) #>><# } #>
                    <# if ( item.ecw_draggable_gallery_content ) { #><div class="ecw-draggable-gallery-text">{{{ item.ecw_draggable_gallery_content }}}</div><# } #>
                    <# if ( item.ecw_draggable_gallery_image && item.ecw_draggable_gallery_image.url ) { #><img class="ecw-draggable-gallery-image" src="{{ item.ecw_draggable_gallery_image.url }}" alt=""><# } #>
                </div>
            <# }); #>
        </div>
        <?php
    }
}