<?php
/**
 * Plugin Main Class
 *
 * @package ProductFaqWoo
 */
namespace ProductFaqWoo;

use ProductFaqWoo\Enqueue;
use ProductFaqWoo\Product_Faq_Frontend;
use ProductFaqWoo\Admin\Metaboxes;
use ProductFaqWoo\Admin\Product_Faq_Backend;

defined('ABSPATH') || die();

class Product_Faq_Woo_Main {

    /**
     * Instance
     * 
     * @var Product_Faq_Woo_Main
     */
    private static $instance = null;

	/**
	 * Class constructor.
	 * Private to enforce singleton pattern.
	 */
	private function __construct() {
        // Include dependencies and initiate them
        $this->includes();
	}

    /**
     * Initialize the main plugin class using singleton pattern.
     * 
     * @return Product_Faq_Woo_Main
     */
    public static function init(){
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Includes all necessary classes.
     */
	private function includes() {
        // Initialize required classes
        new Enqueue();
        new Product_Faq_Frontend();
        new Product_Faq_Backend();
        new Rest_API();
        new Metaboxes();
	}
}
