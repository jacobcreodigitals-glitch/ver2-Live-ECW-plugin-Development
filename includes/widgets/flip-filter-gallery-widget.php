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
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Additional style controls can go here (typography, colors, etc.)

        $this->end_controls_section();
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
?>

<div class="ecw-flip-filter-gallery-parent">

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