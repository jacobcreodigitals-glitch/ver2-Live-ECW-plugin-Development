<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class ECW_Gallery_Zoom_Widget extends Widget_Base {

    public function get_name() {
        return 'ecw_gallery_zoom_widget';
    }

    public function get_title() {
        return __( 'Gallery Zoom', 'elementor-custom-widgets' );
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return [ 'ecw-category' ];
    }

    public function get_script_depends() {
        return [
            'ecw-gsap',
            'ecw-scrolltrigger',
            'ecw-flip',
            'ecw-gallery-zoom-js'
        ];
    }

    public function get_style_depends() {
        return [
            'ecw-gallery-zoom-css'
        ];
    }

    /*
    -------------------------------------
    Helpers
    -------------------------------------
    */
    private function get_layout_config($settings) {
        $layouts = [
            '3x1' => ['cols' => 3, 'rows' => 1, 'expand_row' => $settings['expand_row_3x1'], 'limit' => 3],
            '3x2' => ['cols' => 3, 'rows' => 2, 'expand_row' => $settings['expand_row_3x2'], 'limit' => 6],
            '3x3' => ['cols' => 3, 'rows' => 3, 'expand_row' => $settings['expand_row_3x3'], 'limit' => 9],
        ];

        return $layouts[$settings['layout']];
    }

protected function register_controls() {

    $this->start_controls_section(
        'ecw_gallery_content',
        [
            'label' => __( 'Gallery', 'elementor-custom-widgets' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]
    );

    /*
    -------------------------------------
    LAYOUT PRESET
    -------------------------------------
    */
    $this->add_control(
        'layout',
        [
            'label'   => __( 'Layout Preset', 'elementor-custom-widgets' ),
            'type'    => Controls_Manager::SELECT,
            'default' => '3x3',
            'options' => [
                '3x1' => '3 x 1',
                '3x2' => '3 x 2',
                '3x3' => '3 x 3',
            ],
        ]
    );

    /*
    -------------------------------------
    Expanded Column
    -------------------------------------
    */
    $this->add_control(
        'expand_col',
        [
            'label'   => __( 'Expanded Column', 'elementor-custom-widgets' ),
            'type'    => Controls_Manager::SELECT,
            'default' => '1',
            'options' => [
                '0' => 'Column 1',
                '1' => 'Column 2',
                '2' => 'Column 3',
            ],
        ]
    );

    /*
    -------------------------------------
    Expanded Row Controls
    -------------------------------------
    */
    $this->add_control(
        'expand_row_3x1',
        [
            'label'     => __( 'Expanded Row', 'elementor-custom-widgets' ),
            'type'      => Controls_Manager::SELECT,
            'default'   => '0',
            'options'   => ['0' => 'Row 1'],
            'condition' => ['layout' => '3x1'],
        ]
    );

    $this->add_control(
        'expand_row_3x2',
        [
            'label'     => __( 'Expanded Row', 'elementor-custom-widgets' ),
            'type'      => Controls_Manager::SELECT,
            'default'   => '1',
            'options'   => ['0' => 'Row 1', '1' => 'Row 2'],
            'condition' => ['layout' => '3x2'],
        ]
    );

    $this->add_control(
        'expand_row_3x3',
        [
            'label'     => __( 'Expanded Row', 'elementor-custom-widgets' ),
            'type'      => Controls_Manager::SELECT,
            'default'   => '1',
            'options'   => ['0' => 'Row 1', '1' => 'Row 2', '2' => 'Row 3'],
            'condition' => ['layout' => '3x3'],
        ]
    );

    /*
    -------------------------------------
    Fixed Gallery Media Controls (Max 9)
    Show only relevant images based on layout
    -------------------------------------
    */
    for ($i = 1; $i <= 9; $i++) {
        $condition = [];

        if ($i <= 3) {
            $condition['layout'] = ['3x1', '3x2', '3x3'];
        } elseif ($i <= 6) {
            $condition['layout'] = ['3x2', '3x3'];
        } else {
            $condition['layout'] = ['3x3'];
        }

        $this->add_control(
            "image_$i",
            [
                'label'     => "Image $i",
                'type'      => Controls_Manager::MEDIA,
                'default'   => ['url' => \Elementor\Utils::get_placeholder_image_src()],
                'condition' => $condition,
            ]
        );
    }

    $this->end_controls_section();

     $this->start_controls_section(
        'ecw_gallery_style',
        [
            'label' => __( 'Gallery Style', 'elementor-custom-widgets' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]
    );

    // Gap Control
    $this->add_control(
        'gallery_gap',
        [
            'label' => __( 'Gallery Gap', 'elementor-custom-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'range' => [
                'px' => ['min' => 0, 'max' => 100, 'step' => 1],
                '%'  => ['min' => 0, 'max' => 50, 'step' => 1],
                'em' => ['min' => 0, 'max' => 10, 'step' => 0.1],
            ],
            'default' => ['unit' => 'px', 'size' => 20],
            'selectors' => [
                '{{WRAPPER}} .ecw-gallery-widget' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

    // Border Radius Control
    $this->add_control(
        'gallery_border_radius',
        [
            'label' => __( 'Gallery Border Radius', 'elementor-custom-widgets' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px', '%', 'em'],
            'range' => [
                'px' => ['min' => 0, 'max' => 100, 'step' => 1],
                '%'  => ['min' => 0, 'max' => 50, 'step' => 1],
                'em' => ['min' => 0, 'max' => 10, 'step' => 0.1],
            ],
            'default' => ['unit' => 'px', 'size' => 0],
            'selectors' => [
                '{{WRAPPER}} .ecw-gallery-item-widget img' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]
    );

    $this->end_controls_section();
}




    protected function render() {

        $settings = $this->get_settings_for_display();
        $id = $this->get_id();

        $config = $this->get_layout_config($settings);

        $cols = $config['cols'];
        $rows = $config['rows'];
        $expand_row = $config['expand_row'];
        $limit = $config['limit'];
        $expand_col = $settings['expand_col'];

        // Collect gallery images based on layout limit
        $gallery = [];
        for ($i = 1; $i <= $limit; $i++) {
            $gallery[] = $settings["image_$i"] ?? ['url' => \Elementor\Utils::get_placeholder_image_src()];
        }

        ?>

        <div class="ecw-gallery-wrap-widget">
            <div
                class="ecw-gallery-widget"
                id="ecw-gallery-<?php echo esc_attr($id); ?>"
                data-cols="<?php echo esc_attr($cols); ?>"
                data-rows="<?php echo esc_attr($rows); ?>"
                data-expand-col="<?php echo esc_attr($expand_col); ?>"
                data-expand-row="<?php echo esc_attr($expand_row); ?>"
            >

                <?php foreach ($gallery as $item): ?>
                    <div class="ecw-gallery-item-widget">
                        <img src="<?php echo esc_url($item['url']); ?>" alt="">
                    </div>
                <?php endforeach; ?>

            </div>
        </div>

        <?php
    }

    protected function content_template() {
    ?>
    <#
    var layouts = {
        '3x1': {cols: 3, limit: 3},
        '3x2': {cols: 3, limit: 6},
        '3x3': {cols: 3, limit: 9}
    };
    var config = layouts[ settings.layout ] || layouts['3x3'];
    var limit = config.limit;
    var cols = config.cols;

    // gap & radius will be applied via JS selectors
    #>

    <div class="ecw-gallery-wrap-widget">
        <div class="ecw-gallery-widget" data-cols="{{ cols }}" data-limit="{{ limit }}">
            <# for ( var i = 1; i <= limit; i++ ) {
                var img = settings[ 'image_' + i ] && settings[ 'image_' + i ].url ? settings[ 'image_' + i ].url : '<?php echo \Elementor\Utils::get_placeholder_image_src(); ?>';
            #>
                <div class="ecw-gallery-item-widget">
                    <img src="{{ img }}">
                </div>
            <# } #>
        </div>
    </div>
    <#
    // Apply dynamic CSS for grid and gap so Elementor Style tab won’t reset it
    #>
    <style>
        .ecw-gallery-widget[data-cols="{{ cols }}"] {
            display: grid;
            grid-template-columns: repeat({{ cols }}, 1fr);
            gap: {{ settings.gallery_gap.size || 20 }}{{ settings.gallery_gap.unit || 'px' }};
        }
        .ecw-gallery-item-widget img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: {{ settings.gallery_border_radius.size || 0 }}{{ settings.gallery_border_radius.unit || 'px' }};
        }
    </style>
    <?php
}
}