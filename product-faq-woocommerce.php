<?php
/**
 * Plugin Name: Product FAQs for WooCommerce
 * Plugin URI: https://nazmunsakib.co/
 * Description: WooCommerce Product FAQs plugin!
 * Version: 1.0.0
 * Author: Nazmun Sakib
 * Author URI: https://nazmunsakib.com
 * License: GPL2
 * Text Domain: product-faq-woocommerce
 * Domain Path: /languages
 * 
 * WP Requirement & Test
 * Requires at least: 4.4
 * Tested up to: 6.5
 * Requires PHP: 5.6
 * 
 * WC Requirement & Test
 * WC requires at least: 3.2
 * WC tested up to: 7.9
 * 
 *  @package ProductFaqWoo
 */


defined('ABSPATH') || die();

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Main class for Dynamic Event Management plugin.
 */
final class Product_FAQ_Woocommerce {

    /**
     * The single instance of the class.
     *
     * @var Product_FAQ_Woocommerce|null
     */
    private static $instance = null;

    /**
     * Plugin version.
     *
     * @var string
     */
    private static $version = '1.0.0';

    /**
     * Constructor.
     *
     * Initializes the class and hooks necessary actions.
     */
    private function __construct() {
        $this->define_constants();
        $this->add_hooks();
    }

    /**
     * Returns the single instance of the class.
     *
     * @return Product_FAQ_Woocommerce The single instance of the class.
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Defines plugin constants.
     */
    private function define_constants() {
        define( 'PFW_VERSION', self::$version );
        define( 'PRODUCT_FAQ_WOO_FILE', __FILE__ );
        define( 'PRODUCT_FAQ_WOO_PATH', __DIR__ );
        define( 'PRODUCT_FAQ_WOO_URL', plugins_url( '', PRODUCT_FAQ_WOO_FILE ) );
        define( 'PFW_ASSETS', PRODUCT_FAQ_WOO_URL . '/assets' );
    }

    /**
     * Adds hooks.
     */
    private function add_hooks() {
        add_action( 'init', array( $this, 'load_textdomain' ) );
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    /**
     * Initializes the plugin.
     */
    public function init() {
        ProductFaqWoo\Product_Faq_Woo_Main::init();
    }

    /**
     * Loads the plugin's text domain for localization.
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'product-faq-woocommerce', false, dirname( plugin_basename( PRODUCT_FAQ_WOO_FILE ) ) . '/languages' );
    }

}

/**
 * Initializes the Product_FAQ_Woocommerce class.
 *
 * @return Product_FAQ_Woocommerce
 */
function product_faq_woocommerce() {
    return Product_FAQ_Woocommerce::instance();
}

// Initialize the plugin.
product_faq_woocommerce();
