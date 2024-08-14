<?php
/**
 * Plugin Main Class
 *
 * @package ProductFaqWoo
 */
namespace ProductFaqWoo;
use ProductFaqWoo\Traits\Helper;

defined('ABSPATH') || die();

class Product_Faq_Frontend {

    use Helper;

	/**
	 * Class constructor.
	 */
	public function __construct() {
        $this->add_hooks();
	}

	private function add_hooks() {
        add_filter( 'woocommerce_product_tabs', [$this, 'add_product_faq_tab' ] );
	}

    public function add_product_faq_tab( $tabs ){

        // Adds the new tab
        $tabs['product_faqs_woo'] = array(
            'title'    => __('FAQs', 'product-faq-woocommerce' ),
            'priority' => '50',
            'callback' => array($this, 'display_faqs'),
        );
    
        return $tabs;
    }

    public function display_faqs() {
        wp_enqueue_style('product-faq-woo-frontend');
        wp_enqueue_script('product-faq-woo-frontend-script');

        global $product;
        $id     = $product->get_id();
        $faqs   = $this->faqs_by_product_id($id);

        include PRODUCT_FAQ_WOO_PATH . '/views/layouts/layout-classic.php';
    }

}