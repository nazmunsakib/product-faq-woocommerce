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
        add_filter('woocommerce_product_tabs', [$this, 'add_product_faq_tab']);
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
        global $product;
        wp_enqueue_style('pwf-frontend');
        wp_enqueue_script('pwf-frontend');
        wp_enqueue_script('pfw-global');

        $product_id = $product->get_id();
        $faqs_ids   = get_post_meta( $product_id, 'pfw_faq_ids', true ) ?? [];
        $faqs       = $this->get_faqs($faqs_ids);

        include PRODUCT_FAQ_WOO_PATH . '/views/layouts/layout-classic.php';
    }

}