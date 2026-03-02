<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class ECW_Heading_Widget extends Widget_Base {

    public function get_name() {
        return 'ecw_heading_widget';
    }

    public function get_title() {
        return __( 'ECW Heading', 'elementor-custom-widgets' );
    }

    public function get_icon() {
        return 'fa fa-header';
    }

    public function get_categories() {
        return [ 'ecw-category' ];
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

			// add control like inputs and tags
            

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

			// add Styling control typograph, colors etc

            $this->end_controls_section();
            // -----------------------
            // Style Tab End
            // -----------------------

    }

    protected function render() {

    }

    protected function _content_template() {

    }
}
