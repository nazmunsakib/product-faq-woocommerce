<?php
/**
 * Plugin Main Class
 *
 * @package ProductFaqWoo
 */
namespace ProductFaqWoo;
use ProductFaqWoo\Enqueue;
use ProductFaqWoo\Product_Faq_Frontend;

defined('ABSPATH') || die();

class Product_Faq_Woo_Main {

    /**
     * Instance
     */
    private static $instance = null;

	/**
	 * Class constructor.
	 */
	private function __construct() {
        $this->includes();
	}

    public static function init(){
        if( self::$instance === null ){
            self::$instance = new self();
        }

        return  self::$instance;
    }

	private function includes() {
        new Enqueue();
        new Product_Faq_Frontend();
        new Product_Faq_Backend();
	}

}