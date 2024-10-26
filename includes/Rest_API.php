<?php
/**
 * REST API Class for Product FAQ WooCommerce Plugin
 *
 * Registers custom REST API endpoints for managing FAQs associated with WooCommerce products.
 * Provides endpoints to add or remove FAQs from a product and retrieve associated FAQs.
 *
 * @package ProductFaqWoo
 */

namespace ProductFaqWoo;

use WP_REST_Request;
use WP_Error;
use WP_REST_Response;
use ProductFaqWoo\Traits\Helper;

defined('ABSPATH') || exit; // Prevent direct access.

/**
 * Class Rest_API
 *
 * Handles the REST API routes and callbacks for managing product FAQs.
 */
class Rest_API {
    use Helper;

    /**
     * Constructor.
     *
     * Initializes the class and registers the REST API routes.
     */
    public function __construct() {
        $this->add_hooks();
    }

    /**
     * Register REST API hooks.
     *
     * Adds WordPress action to register custom API routes.
     */
    private function add_hooks() {
        add_action('rest_api_init', [$this, 'custom_api_route']);
    }

    /**
     * Register custom API routes for FAQ management.
     *
     * Creates two REST API routes:
     * - /product-faq-woocommerce/v1/set-faq: To add or remove FAQs for a product.
     * - /product-faq-woocommerce/v1/get-faq: To retrieve FAQs associated with a product.
     */
    public function custom_api_route() {
        // Route for adding/removing an FAQ to/from a product.
        register_rest_route('product-faq-woocommerce/v1', '/set-faq', array(
            'methods'   => 'POST',
            'callback'  => [$this, 'set_faq_callback'],
            'args'      => [
                'action' => [
                    'validate_callback' => function($param, $request, $key) {
                        return is_string($param);
                    },
                    'required' => false
                ]
            ],
            'permission_callback' => function(WP_REST_Request $request) {
                $nonce = $request->get_header('X-WP-Nonce');

                // Verify nonce for security.
                if (!wp_verify_nonce($nonce, 'wp_rest')) {
                    return new WP_Error('rest_forbidden', esc_html__('Invalid nonce'), ['status' => 403]);
                }

                // Check if user is logged in.
                if (!is_user_logged_in()) {
                    return new WP_Error('rest_forbidden', esc_html__('You do not have permission to access this resource.'), ['status' => 403]);
                }

                return true;
            }
        ));

        // Route for retrieving FAQs associated with a product.
        register_rest_route('product-faq-woocommerce/v1', '/get-faq', array(
            'methods'   => 'POST',
            'callback'  => [$this, 'get_faqs_callback'],
            'args'      => [
                'action' => [
                    'validate_callback' => function($param, $request, $key) {
                        return is_string($param);
                    },
                    'required' => false
                ]
            ],
            'permission_callback' => function(WP_REST_Request $request) {
                $nonce = $request->get_header('X-WP-Nonce');

                if (!wp_verify_nonce($nonce, 'wp_rest')) {
                    return new WP_Error('rest_forbidden', esc_html__('Invalid nonce'), ['status' => 403]);
                }

                if (!is_user_logged_in()) {
                    return new WP_Error('rest_forbidden', esc_html__('You do not have permission to access this resource.'), ['status' => 403]);
                }

                return true;
            }
        ));
    }

    /**
     * Set FAQ Callback.
     *
     * Adds or removes an FAQ from a product based on the action provided in the request.
     *
     * @param WP_REST_Request $request The incoming REST API request.
     * @return WP_REST_Response A response with success message and updated FAQ data.
     */
    public function set_faq_callback(WP_REST_Request $request) {
        $params     = $request->get_params();
        $product_id = isset($params['product_id']) ? intval($params['product_id']) : 0;
        $action     = isset($params['action']) ? $params['action'] : false;
        $faq_id     = isset($params['faq_id']) ? intval($params['faq_id']) : 0;
        $faq_data   = [];

        if (!$product_id || !$faq_id || !$action) {
            return new WP_REST_Response(['message' => 'Undefined Product ID or FAQ ID', 'success' => false], 403);
        }

        // Retrieve existing FAQ IDs for the product.
        $faqs_ids = get_post_meta($product_id, 'pfw_faq_product_ids', true);
        $faqs_ids = is_array($faqs_ids) ? $faqs_ids : [];

        // Perform action based on the request.
        switch($action) {
            case "add":
                if (!in_array($faq_id, $faqs_ids)) {
                    array_push($faqs_ids, $faq_id);
                    update_post_meta($product_id, 'pfw_faq_product_ids', $faqs_ids);
                }
                $message = 'FAQ ID has been successfully added!';
                break;

            case "remove":
                if (in_array($faq_id, $faqs_ids)) {
                    $key = array_search($faq_id, $faqs_ids);

                    if ($key !== false) {
                        unset($faqs_ids[$key]);
                    }

                    update_post_meta($product_id, 'pfw_faq_product_ids', $faqs_ids);
                }
                $message = 'FAQ ID has been successfully removed!';
                break;
        }

        // Retrieve updated FAQ data for the response.
        $faq_posts = $this->get_faqs($faqs_ids);
        foreach ($faq_posts as $faq) {
            $faq_data[] = [
                'id'      => $faq->ID,
                'title'   => esc_html($faq->post_title),
                'content' => esc_html($faq->post_content),
                'link'    => get_permalink($faq->ID),
            ];
        }

        $data = [
            'message'   => $message,
            'faq_posts' => $faq_data
        ];

        return new WP_REST_Response($data, 200);
    }

    /**
     * Get FAQs Callback.
     *
     * Retrieves FAQs associated with a specified product.
     *
     * @param WP_REST_Request $request The incoming REST API request.
     * @return WP_REST_Response A response with the list of FAQs for the specified product.
     */
    public function get_faqs_callback(WP_REST_Request $request) {
        $params     = $request->get_params();
        $product_id = isset($params['product_id']) ? intval($params['product_id']) : 0;
        $faq_data   = [];

        if (!$product_id) {
            return new WP_REST_Response(['message' => 'Undefined Product ID', 'success' => false], 403);
        }

        // Retrieve FAQ IDs associated with the product.
        $faqs_ids = get_post_meta($product_id, 'pfw_faq_product_ids', true);
        $faq_posts = $this->get_faqs($faqs_ids);

        // Prepare response data with FAQ titles.
        foreach ($faq_posts as $faq) {
            $faq_data[] = [
                'id'    => $faq->ID,
                'title' => esc_html($faq->post_title),
            ];
        }

        $data = [
            'message'   => 'Get All FAQs by Product ID',
            'faq_posts' => $faq_data
        ];

        return new WP_REST_Response($data, 200);
    }
}
