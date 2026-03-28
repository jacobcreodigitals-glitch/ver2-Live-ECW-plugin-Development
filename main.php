<?php
/**
 * Plugin Name: Elementor Creo Widgets
 * Description: Custom Elementor GSAP widgets plugin.
 * Plugin URI:  https://creodigitals.com/
 * Version:     0.0.0002
 * Author:      Hakob
 * Author URI:  https://creodigitals.com/
 * Text Domain: elementor-custom-widgets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Activation Hook
 * -----------------
 * Ensures Elementor is installed & activated during plugin activation.
 */
register_activation_hook( __FILE__, 'ecw_activate' );

function ecw_activate() {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    if ( ! is_plugin_active( 'elementor/elementor.php' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die(
            esc_html__( 'This plugin requires Elementor to be installed and activated.', 'elementor-custom-widgets' ),
            esc_html__( 'Plugin Activation Error', 'elementor-custom-widgets' ),
            [ 'back_link' => true ]
        );
        exit; // extra safety
    }
}





/**
 * Main Plugin Class
 */
final class ECW_Elementor_Custom_Widgets {

    const VERSION = '0.0.0002';
    const MINIMUM_ELEMENTOR_VERSION = '3.0.0';
    const MINIMUM_PHP_VERSION = '7.4';
    const PLUGIN_FILE = __FILE__;

    public function __construct() {
        add_action( 'plugins_loaded', [ $this, 'init' ] );
        add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );
        add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_styles' ] );
    }

    /**
     * Initialize plugin
     */
    public function init() {

        // Check Elementor runtime
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_missing_elementor' ] );
            return;
        }

        // Minimum Elementor version check
        if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
            return;
        }

        // Minimum PHP version check
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
            return;
        }

        // Load translations
        $this->load_textdomain();

        // Include files & initialize hooks only if Elementor is active
        $this->includes();
        $this->hooks();
    }

    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'elementor-custom-widgets',
            false,
            dirname( plugin_basename( self::PLUGIN_FILE ) ) . '/languages'
        );
    }

    /**
     * Include required files
     */
    public function includes() {
        if ( file_exists( __DIR__ . '/includes/plugin.php' ) ) {
            require_once __DIR__ . '/includes/plugin.php';
        }
    }

    /**
     * Register hooks
     */
    public function hooks() {
        if ( class_exists( 'ECW_Plugin' ) ) {
            new ECW_Plugin();
        }
    }

    /**
     * Admin notices
     */
    public function admin_notice_missing_elementor() {
        echo '<div class="notice notice-warning is-dismissible">
            <p><strong>' . esc_html__( 'Elementor Custom Widgets', 'elementor-custom-widgets' ) . '</strong> ' .
            esc_html__( 'requires Elementor to be installed and activated.', 'elementor-custom-widgets' ) .
            '</p>
        </div>';
    }

    public function admin_notice_minimum_elementor_version() {
        echo '<div class="notice notice-warning is-dismissible">
            <p><strong>Elementor Custom Widgets</strong> ' .
            sprintf(
                esc_html__( 'requires Elementor version %s or greater.', 'elementor-custom-widgets' ),
                self::MINIMUM_ELEMENTOR_VERSION
            ) .
            '</p>
        </div>';
    }

    public function admin_notice_minimum_php_version() {
        echo '<div class="notice notice-warning is-dismissible">
            <p><strong>Elementor Custom Widgets</strong> ' .
            sprintf(
                esc_html__( 'requires PHP version %s or greater.', 'elementor-custom-widgets' ),
                self::MINIMUM_PHP_VERSION
            ) .
            '</p>
        </div>';
    }

    /**
     * Register scripts
     */
    public function register_scripts() {

        // Only register scripts if Elementor is active
        if ( ! did_action( 'elementor/loaded' ) ) {
            return;
        }

        // GSAP core
        wp_register_script(
            'ecw-gsap',
            plugins_url( 'assets/js/public-gsap-umd/gsap.js', self::PLUGIN_FILE ),
            [],
            '3.14.1',
            true
        );

        // ScrollTrigger
        wp_register_script(
            'ecw-scrolltrigger',
            plugins_url( 'assets/js/public-gsap-umd/ScrollTrigger.js', self::PLUGIN_FILE ),
            [ 'ecw-gsap' ],
            '3.14.1',
            true
        );

        // SplitText
        wp_register_script(
            'ecw-splittext',
            plugins_url( 'assets/js/public-gsap-umd/SplitText.js', self::PLUGIN_FILE ),
            [ 'ecw-gsap' ],
            '3.14.1',
            true
        );

        wp_register_script(
            'ecw-flip',
            plugins_url( 'assets/js/public-gsap-umd/Flip.js', self::PLUGIN_FILE ),
            [ 'ecw-gsap' ],
            '3.14.1',
            true
        );

        // Custom plugin scripts
        wp_register_script(
            'ecw-arc-scroll-js',
            plugins_url( 'assets/js/ecw-arc-scroll.js', self::PLUGIN_FILE ),
            [ 'ecw-gsap', 'ecw-scrolltrigger' ],
            self::VERSION,
            true
        );

        wp_register_script(
            'ecw-arc-scroll-templated-js',
            plugins_url( 'assets/js/ecw-arc-scroll-templated.js', self::PLUGIN_FILE ),
            [ 'ecw-gsap', 'ecw-scrolltrigger' ],
            self::VERSION,
            true
        );

        wp_register_script(
            'ecw-hr-scroll-js',
            plugins_url( 'assets/js/ecw-hr-scroll.js', self::PLUGIN_FILE ),
            [ 'ecw-gsap', 'ecw-scrolltrigger' ],
            self::VERSION,
            true
        );
    
        wp_register_script(
            'ecw-hr-scroll-templated-js',
            plugins_url( 'assets/js/ecw-hr-scroll-templated.js', self::PLUGIN_FILE ),
            [ 'ecw-gsap', 'ecw-scrolltrigger' ],
            self::VERSION,
            true
        );

        wp_register_script(
            'ecw-fill-text-js',
            plugins_url( 'assets/js/ecw-fill-text.js', self::PLUGIN_FILE ),
            [ 'ecw-gsap', 'ecw-scrolltrigger', 'ecw-splittext' ],
            self::VERSION,
            true
        );


        wp_register_script(
            'ecw-split-text-js',
            plugins_url( 'assets/js/ecw-split-text.js', self::PLUGIN_FILE ),
            [ 'ecw-gsap', 'ecw-scrolltrigger', 'ecw-splittext' ],
            self::VERSION,
            true
        );

        wp_register_script(
            'ecw-text-on-scroll-js',
            plugins_url( 'assets/js/ecw-text-on-scroll.js', self::PLUGIN_FILE ),
            [ 'ecw-gsap', 'ecw-scrolltrigger', 'ecw-splittext' ],
            self::VERSION,
            true
        );

        wp_register_script(
            'ecw-onflip-js',
            plugins_url( 'assets/js/ecw-on-flip-scroll.js', self::PLUGIN_FILE ),
            [ 'ecw-gsap', 'ecw-scrolltrigger', 'ecw-flip' ],
            self::VERSION,
            true
        );

        wp_register_script(
            'ecw-gallery-zoom-js',
            plugins_url( 'assets/js/ecw-gallery-zoom.js', self::PLUGIN_FILE ),
            [ 'ecw-gsap', 'ecw-scrolltrigger', 'ecw-flip' ],
            self::VERSION,
            true
        );


    }

    /**
     * Register styles
     */
    public function register_styles() {

        if ( ! did_action( 'elementor/loaded' ) ) {
            return;
        }

        wp_register_style(
            'ecw-style-reset',
            plugins_url( 'assets/css/ecw-style-reset.css', self::PLUGIN_FILE ),
            [],
            self::VERSION
        );

        wp_register_style(
            'ecw-arc-scroll-css',
            plugins_url( 'assets/css/ecw-arc-scroll.css', self::PLUGIN_FILE ),
            [],
            self::VERSION
        );

        wp_register_style(
            'ecw-arc-scroll-templated-css',
            plugins_url( 'assets/css/ecw-arc-scroll-templated.css', self::PLUGIN_FILE ),
            [],
            self::VERSION
        );

        wp_register_style(
            'ecw-hr-scroll-templated-css',
            plugins_url( 'assets/css/ecw-hr-scroll-templated.css', self::PLUGIN_FILE ),
            [],
            self::VERSION
        );

        wp_register_style(
            'ecw-hr-scroll-css',
            plugins_url( 'assets/css/ecw-hr-scroll.css', self::PLUGIN_FILE ),
            [],
            self::VERSION
        );

        wp_register_style(
            'ecw-onflip-css',
            plugins_url( 'assets/css/ecw-on-flip.css', self::PLUGIN_FILE ),
            [],
            self::VERSION
        );

        wp_register_style(
            'ecw-gallery-zoom-css',
            plugins_url( 'assets/css/ecw-gallery-zoom.css', self::PLUGIN_FILE ),
            [],
            self::VERSION
        );

        wp_register_style(
            'ecw-gallery-depth-css',
            plugins_url( 'assets/css/ecw-gallery-depth.css', self::PLUGIN_FILE ),
            [],
            self::VERSION
        );


    }

    
}

// Initialize the plugin
new ECW_Elementor_Custom_Widgets();