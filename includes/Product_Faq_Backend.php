<?php
/**
 * Plugin Main Class
 *
 * @package ProductFaqWoo
 */
namespace ProductFaqWoo;

defined('ABSPATH') || die();

class Product_Faq_Backend {

	/**
	 * Class constructor.
	 */
	public function __construct() {
        $this->add_hooks();
	}

	private function add_hooks() {
        add_action( 'woocommerce_product_data_panels', [$this, 'faq_tab_data_panels' ] );
        add_filter('woocommerce_product_data_tabs',  [$this,'faq_data_tab'] );
        add_action('init', [$this, 'register_post_type']);
	}

    public function faq_tab_data_panels() {

    }

    public function faq_data_tab( $tabs ) {
        $tabs['product_faq_woocommerce'] = array(
            'label'    => 'FAQs',
            'target'   => 'product_faq_data',
            'priority' => 100,
        );
        
        return $tabs;
    }

    public function register_post_type(){

    }

}