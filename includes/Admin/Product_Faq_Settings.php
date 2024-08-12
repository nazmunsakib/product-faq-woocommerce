<?php
/**
 * Plugin Main Class
 *
 * @package ProductFaqWoo
 */
namespace ProductFaqWoo;

defined('ABSPATH') || die();

class Product_Faq_Settings {

	/**
	 * Class constructor.
	 */
	public function __construct() {
        $this->add_hooks();
	}

	private function add_hooks() {
        add_action('init', [$this, 'register_post_type']);
	}

    public function register_post_type(){

    }

}