<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class ECW_Flip_Filter_Gallery_Widget extends Widget_Base {

    public function get_name() {
        return 'ecw_flip_filter_gallery_widget';
    }

    public function get_title() {
        return __( 'Flip Filter Gallery', 'elementor-custom-widgets' );
    }

    public function get_icon() {
        return 'fa fa-image';
    }

    public function get_categories() {
        return [ 'ecw-category' ];
    }

    public function get_script_depends() {
        return [
            'ecw-gsap',
            'ecw-flip',
            'ecw-flip-filter-gallery-js'
        ];
    }

    public function get_style_depends() {
        return [
            'ecw-style-reset',
            'ecw-flip-filter-gallery-css'
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

        // Filter tags input
        $this->add_control(
            'filter_tags',
            [
                'label' => __( 'Filter Tags', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'cat1,cat2,cat3',
                'description' => __( 'Comma separated tags (e.g. cat1,cat2,cat3)', 'elementor-custom-widgets' ),
            ]
        );

        // Repeater for gallery items
        $repeater = new Repeater();

        $repeater->add_control(
            'item_tag',
            [
                'label' => __( 'Item Tag', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'cat1',
                'description' => __( 'Must match one of the filter tags', 'elementor-custom-widgets' ),
            ]
        );

        $repeater->add_control(
            'item_image',
            [
                'label' => __( 'Image', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'gallery_items',
            [
                'label' => __( 'Gallery Items', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [ 'item_tag' => 'cat1' ],
                    [ 'item_tag' => 'cat2' ],
                ],
                'title_field' => '{{{ item_tag }}}',
            ]
        );

        // Height Behavior Control
            $this->add_control(
                'height_behavior',
                [
                    'label' => __( 'Height Behavior', 'elementor-custom-widgets' ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'approach1',
                    'options' => [
                        'approach1' => __( 'Keep Max Height', 'elementor-custom-widgets' ),
                        'approach2' => __( 'Smooth Height Adjust', 'elementor-custom-widgets' ),
                    ],
                    'description' => __( 'Select how the gallery container height behaves when filtering items.', 'elementor-custom-widgets' ),
                ]
            );


        $this->add_responsive_control(
            'columns',
            [
                'label' => __( 'Columns', 'elementor-custom-widgets' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 12,
                'step' => 1,
                'default' => 4,
                'tablet_default' => 3,
                'mobile_default' => 2,
                'selectors' => [
                    '{{WRAPPER}} .ecw-flip-filter-gallery-item' => 'width: calc((100% / {{VALUE}}) - ({{VALUE}} - 1) / {{VALUE}} * 15px);',
                ],
            ]
        );    

        $this->end_controls_section();
        // -----------------------
        // Content Tab End
        // -----------------------

        // -----------------------
        // Style Tab Start
        // -----------------------
        // -----------------------
// Style Tab Start
// -----------------------
$this->start_controls_section(
    'style_section',
    [
        'label' => __( 'Filters', 'elementor-custom-widgets' ),
        'tab' => Controls_Manager::TAB_STYLE,
    ]
);

// Alignment
$this->add_control(
    'filters_alignment',
    [
        'label' => __( 'Alignment', 'elementor-custom-widgets' ),
        'type' => Controls_Manager::CHOOSE,
        'options' => [
            'flex-start' => [
                'title' => __( 'Left', 'elementor-custom-widgets' ),
                'icon' => 'eicon-text-align-left',
            ],
            'center' => [
                'title' => __( 'Center', 'elementor-custom-widgets' ),
                'icon' => 'eicon-text-align-center',
            ],
            'flex-end' => [
                'title' => __( 'Right', 'elementor-custom-widgets' ),
                'icon' => 'eicon-text-align-right',
            ],
        ],
        'default' => 'flex-start',
        'selectors' => [
            '{{WRAPPER}} .ecw-flip-filter-gallery-filters-tab' => 'justify-content: {{VALUE}};',
        ],
    ]
);

// Typography
$this->add_group_control(
    \Elementor\Group_Control_Typography::get_type(),
    [
        'name' => 'filters_typography',
        'selector' => '{{WRAPPER}} .ecw-flip-filter-gallery-filters-tab label',
    ]
);

// Padding
$this->add_responsive_control(
    'filters_padding',
    [
        'label' => __( 'Padding', 'elementor-custom-widgets' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%', 'em' ],
        'selectors' => [
            '{{WRAPPER}} .ecw-flip-filter-gallery-filters-tab label' => 
                'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
    ]
);

// Border
$this->add_group_control(
    \Elementor\Group_Control_Border::get_type(),
    [
        'name' => 'filters_border',
        'selector' => '{{WRAPPER}} .ecw-flip-filter-gallery-filters-tab label',
    ]
);

// Border Radius
$this->add_responsive_control(
    'filters_border_radius',
    [
        'label' => __( 'Border Radius', 'elementor-custom-widgets' ),
        'type' => Controls_Manager::DIMENSIONS,
        'selectors' => [
            '{{WRAPPER}} .ecw-flip-filter-gallery-filters-tab label' =>
                'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
    ]
);

// Tabs: Normal / Active
$this->start_controls_tabs( 'filters_style_tabs' );

// NORMAL TAB
$this->start_controls_tab(
    'filters_normal_tab',
    [
        'label' => __( 'Normal', 'elementor-custom-widgets' ),
    ]
);

$this->add_control(
    'filters_text_color',
    [
        'label' => __( 'Text Color', 'elementor-custom-widgets' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .ecw-flip-filter-gallery-filters-tab label' => 'color: {{VALUE}};',
        ],
    ]
);

$this->add_control(
    'filters_bg_color',
    [
        'label' => __( 'Background Color', 'elementor-custom-widgets' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
            '{{WRAPPER}} .ecw-flip-filter-gallery-filters-tab label' => 'background-color: {{VALUE}};',
        ],
    ]
);

$this->end_controls_tab();

// ACTIVE TAB
$this->start_controls_tab(
    'filters_active_tab',
    [
        'label' => __( 'Active', 'elementor-custom-widgets' ),
    ]
);

$this->add_control(
    'filters_active_text_color',
    [
        'label' => __( 'Text Color', 'elementor-custom-widgets' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
      
            '{{WRAPPER}} .ecw-flip-filter-gallery-filters-tab label.ecw-active' => 'color: {{VALUE}};',
        ],
    ]
);

$this->add_control(
    'filters_active_bg_color',
    [
        'label' => __( 'Background Color', 'elementor-custom-widgets' ),
        'type' => Controls_Manager::COLOR,
        'selectors' => [
    
            '{{WRAPPER}} .ecw-flip-filter-gallery-filters-tab label.ecw-active' => 'background-color: {{VALUE}};',
        ],
    ]
);

$this->end_controls_tab();

$this->end_controls_tabs();



$this->end_controls_section();

// -----------------------
// Gallery Items Style Section
// -----------------------
$this->start_controls_section(
    'gallery_items_style_section',
    [
        'label' => __( 'Gallery Items', 'elementor-custom-widgets' ),
        'tab' => Controls_Manager::TAB_STYLE,
    ]
);

// Image Width
$this->add_responsive_control(
    'item_img_width',
    [
        'label' => __( 'Image Width', 'elementor-custom-widgets' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', '%' ],
        'range' => [
            'px' => [ 'min' => 0, 'max' => 1000 ],
            '%'  => [ 'min' => 0, 'max' => 100 ],
        ],
        'default' => [
            'unit' => '%',
            'size' => 100,
        ],
        'selectors' => [
            '{{WRAPPER}} .ecw-flip-filter-gallery-item img' => 'width: {{SIZE}}{{UNIT}};',
        ],
    ]
);

// Image Max Width
$this->add_responsive_control(
    'item_img_max_width',
    [
        'label' => __( 'Image Max Width', 'elementor-custom-widgets' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', '%' ],
        'range' => [
            'px' => [ 'min' => 0, 'max' => 1000 ],
            '%'  => [ 'min' => 0, 'max' => 100 ],
        ],
        'default' => [
            'unit' => '%',
            'size' => 100,
        ],
        'selectors' => [
            '{{WRAPPER}} .ecw-flip-filter-gallery-item img' => 'max-width: {{SIZE}}{{UNIT}};',
        ],
    ]
);

// Image Height
$this->add_responsive_control(
    'item_img_height',
    [
        'label' => __( 'Image Height', 'elementor-custom-widgets' ),
        'type' => Controls_Manager::SLIDER,
        'size_units' => [ 'px', 'vh' ],
        'range' => [
            'px' => [ 'min' => 0, 'max' => 1000 ],
            'vh' => [ 'min' => 0, 'max' => 100 ],
        ],
        'default' => [
            'unit' => 'px',
            'size' => 200,
        ],
        'selectors' => [
            '{{WRAPPER}} .ecw-flip-filter-gallery-item img' => 'height: {{SIZE}}{{UNIT}};',
        ],
    ]
);

// Image Object Fit
$this->add_control(
    'item_img_object_fit',
    [
        'label' => __( 'Object Fit', 'elementor-custom-widgets' ),
        'type' => Controls_Manager::SELECT,
        'default' => 'cover',
        'options' => [
            'cover'   => __( 'Cover', 'elementor-custom-widgets' ),
            'contain' => __( 'Contain', 'elementor-custom-widgets' ),
            'fill'    => __( 'Fill', 'elementor-custom-widgets' ),
            'none'    => __( 'None', 'elementor-custom-widgets' ),
        ],
        'selectors' => [
            '{{WRAPPER}} .ecw-flip-filter-gallery-item img' => 'object-fit: {{VALUE}};',
        ],
    ]
);

// Item Border Radius
$this->add_responsive_control(
    'item_border_radius',
    [
        'label' => __( 'Border Radius', 'elementor-custom-widgets' ),
        'type' => Controls_Manager::DIMENSIONS,
        'size_units' => [ 'px', '%' ],
        'selectors' => [
            '{{WRAPPER}} .ecw-flip-filter-gallery-item' => 
                'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
        ],
    ]
);

// Item Background Color
$this->add_control(
    'item_bg_color',
    [
        'label' => __( 'Background Color', 'elementor-custom-widgets' ),
        'type' => Controls_Manager::COLOR,
        'default' => '#f0f0f0',
        'selectors' => [
            '{{WRAPPER}} .ecw-flip-filter-gallery-item' => 'background-color: {{VALUE}};',
        ],
    ]
);

$this->end_controls_section();
// -----------------------
// Gallery Items Style Section End
// -----------------------


        // -----------------------
        // Style Tab End
        // -----------------------
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
            
        // Safe tags
        $tags_raw = isset($settings['filter_tags']) ? $settings['filter_tags'] : '';
        $tags_array = is_string($tags_raw) ? explode(',', $tags_raw) : [];
        $tags = array_filter(array_map(function($tag) {
            $tag = trim($tag);
            return $tag !== '' ? $tag : null;
        }, $tags_array));


$height_class = isset($settings['height_behavior']) ? 'ecw-' . $settings['height_behavior'] : 'ecw-approach1';
?>

<div class="ecw-flip-filter-gallery-parent <?php echo esc_attr($height_class); ?>">

    <!-- Filters -->
    <div class="ecw-flip-filter-gallery-filters-tab">

        <label>
            <input type="checkbox" class="filter-all" checked> All
        </label>

        <?php if (!empty($tags)) : ?>
            <?php foreach ($tags as $tag):
                $tag_slug = sanitize_title($tag);
                if (empty($tag_slug)) continue;
            ?>
                <label>
                    <input
                        type="checkbox"
                        class="filter"
                        data-filter="<?php echo esc_attr($tag_slug); ?>"
                    >
                    <?php echo esc_html(ucfirst($tag)); ?>
                </label>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <!-- Gallery -->
    <div class="ecw-flip-filter-gallery-content">

        <?php if (!empty($settings['gallery_items']) && is_array($settings['gallery_items'])): ?>
            <?php foreach ($settings['gallery_items'] as $item):

                // Safe tag
                $raw_tag = isset($item['item_tag']) ? $item['item_tag'] : '';
                $tag_slug = sanitize_title($raw_tag);
                if (empty($tag_slug)) $tag_slug = 'no-tag';

                // Safe image
                $image = '';
                if (
                    isset($item['item_image']) &&
                    is_array($item['item_image']) &&
                    !empty($item['item_image']['url'])
                ) {
                    $image = $item['item_image']['url'];
                } else {
                    $image = Utils::get_placeholder_image_src();
                }

            ?>
                <div class="ecw-flip-filter-gallery-item <?php echo esc_attr($tag_slug); ?>">
                    <img src="<?php echo esc_url($image); ?>" alt="">
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="ecw-empty">No items found</div>
        <?php endif; ?>

    </div>

</div>

<?php
    }

    protected function _content_template() {
        // Optional live preview code (for editor)
    }

}