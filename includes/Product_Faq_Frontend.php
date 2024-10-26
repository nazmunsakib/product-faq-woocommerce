<?php
/**
 * Product FAQ WooCommerce Plugin
 *
 * Provides an FAQ tab on WooCommerce product pages where FAQs specific to each product can be displayed.
 * Uses custom FAQs stored in post meta to enhance product details.
 *
 * @package ProductFaqWoo
 */

namespace ProductFaqWoo;

use ProductFaqWoo\Traits\Helper;

defined('ABSPATH') || exit; // Prevent direct access.

/**
 * Class Product_Faq_Frontend
 *
 * Handles frontend functionalities of the Product FAQ WooCommerce plugin, such as adding a new FAQ tab 
 * on the WooCommerce product page and displaying relevant FAQs.
 */
class Product_Faq_Frontend {
    use Helper;

    /**
     * Constructor.
     *
     * Initializes the class by adding necessary WordPress hooks.
     */
    public function __construct() {
        $this->add_hooks();
    }

    /**
     * Register WordPress hooks for the plugin.
     *
     * Adds the WooCommerce filter hook to include a custom FAQ tab in the product details.
     */
    private function add_hooks() {
        add_filter('woocommerce_product_tabs', [$this, 'add_product_faq_tab']);
    }

    /**
     * Add FAQ Tab.
     *
     * Adds a custom FAQ tab on WooCommerce product pages.
     *
     * @param array $tabs Existing WooCommerce product tabs.
     * @return array Modified product tabs with the new FAQ tab added.
     */
    public function add_product_faq_tab( $tabs ) {
        // Adds the new FAQ tab to WooCommerce product pages.
        $tabs['product_faqs_woo'] = array(
            'title'    => __('FAQs', 'product-faq-woocommerce'),
            'priority' => 50,
            'callback' => array($this, 'display_faqs'),
        );

        return $tabs;
    }

    /**
     * Display FAQs.
     *
     * Renders the FAQ content within the FAQ tab by retrieving product-specific FAQs.
     * Enqueues necessary frontend styles and scripts.
     */
    public function display_faqs() {
        global $product;
        
        // Enqueue frontend styles and scripts.
        wp_enqueue_style('pwf-frontend');
        wp_enqueue_script('pwf-frontend');
        wp_enqueue_script('pfw-global');

        $product_id = $product->get_id(); // Retrieve current product ID.
        
        // Retrieve FAQs associated with the product.
        $faqs_ids = get_post_meta($product_id, 'pfw_faq_product_ids', true) ?? [];
        $faqs = $this->get_faqs($faqs_ids);

        // Include the FAQ layout template.
        include PRODUCT_FAQ_WOO_PATH . '/views/layouts/layout-classic.php';
    }
}
