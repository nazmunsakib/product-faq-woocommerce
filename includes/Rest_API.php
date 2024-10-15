<?php
/**
 * Plugin Main Class
 *
 * @package ProductFaqWoo
 */
namespace ProductFaqWoo;
use WP_REST_Request;
use WP_Error;
use WP_REST_Response;
use ProductFaqWoo\Traits\Helper;

defined('ABSPATH') || die();

class Rest_API {

    use Helper;

	/**
	 * Class constructor.
	 */
	public function __construct() {
        $this->add_hooks();
	}

	private function add_hooks() {
        add_action('rest_api_init', [$this, 'custom_api_route']);
	}
    public function custom_api_route(){
        register_rest_route('product-faq-woocommerce/v1', '/set-faq', array(
            'methods'   =>  'POST',
            'callback'  =>  [$this, 'set_faq_callback'],
            'args'      =>  [
                'action'    =>  [
                    'validate_callback' =>  function( $param, $request, $key ){
                        return is_string( $param );
                    },
                    'required'  => false
                ]
            ],
            'permission_callback' => function( WP_REST_Request $request ) {
                $nonce = $request->get_header('X-WP-Nonce');

                if ( !wp_verify_nonce( $nonce, 'wp_rest') ) {
                    return new WP_Error('rest_forbidden', esc_html__('Invalid nonce'), ['status' => 403]);
                }
            
                if ( !is_user_logged_in() ) {
                    return new WP_Error('rest_forbidden', esc_html__('You do not have permission to access this resource.'), ['status' => 403]);
                }
            
                return true;
            }
        ));

        register_rest_route('product-faq-woocommerce/v1', '/get-faq', array(
            'methods'   =>  'POST',
            'callback'  =>  [$this, 'get_faqs_callback'],
            'args'      =>  [
                'action'    =>  [
                    'validate_callback' =>  function( $param, $request, $key ){
                        return is_string( $param );
                    },
                    'required'  => false
                ]
            ],
            'permission_callback' => function( WP_REST_Request $request ) {
                $nonce = $request->get_header('X-WP-Nonce');

                if ( !wp_verify_nonce( $nonce, 'wp_rest') ) {
                    return new WP_Error('rest_forbidden', esc_html__('Invalid nonce'), ['status' => 403]);
                }
            
                if ( !is_user_logged_in() ) {
                    return new WP_Error('rest_forbidden', esc_html__('You do not have permission to access this resource.'), ['status' => 403]);
                }
            
                return true;
            }
        ));
    }

    public function set_faq_callback( WP_REST_Request $request ) {
        $params     = $request->get_params();
        $product_id = isset( $params['product_id'] ) ? intval( $params['product_id'] ) : 0;
        $action     = isset( $params['action'] ) ?  $params['action'] : false;
        $faq_id     = isset( $params['faq_id'] ) ? intval( $params['faq_id'] ) : 0;
        $faq_data   = [];
    
        if ( !$product_id || !$faq_id || !$action) {
            return new WP_REST_Response(['message' => 'Undefined Product ID or FAQ ID', 'success' => false], 403);
        }
    
        /**
         * Get the existing IDs for the product
         */
        $faqs_ids       = get_post_meta( $product_id, 'pfw_faq_ids', true );
        $products_ids   = get_post_meta( $faq_id, 'pfw_product_ids', true );
        
        /**
         * If no FAQs are found, initialize it as an empty array
         */
        $faqs_ids       = is_array( $faqs_ids ) ? $faqs_ids : [];
        $products_ids   = is_array( $products_ids ) ? $products_ids : [];

        switch( $action ){
            case "add":
                if ( !in_array( $faq_id, $faqs_ids ) ) {
                    $faqs_ids[] = $faq_id;  // Add the FAQ ID to the array
                    update_post_meta( $product_id, 'pfw_faq_ids', $faqs_ids ); // Update the post meta with the new array
                }

                if ( !in_array( $product_id, $products_ids ) ) {
                    $products_ids[] = $product_id;  // Add the FAQ ID to the array
                    update_post_meta( $faq_id, 'pfw_product_ids', $products_ids ); // Update the post meta with the new array
                }

                $message = 'FAQ ID has been successfully added!';

                break;

            case "remove":
                if ( in_array( $faq_id, $faqs_ids ) ) {
                    $key = array_search($faq_id, $faqs_ids); // Find the index of "BMW"

                    if ($key !== false) {
                        unset($faqs_ids[$key]); // Remove the item if found
                    }
                    update_post_meta( $product_id, 'pfw_faq_ids', $faqs_ids ); // Update the post meta with the new array
                }

                if ( in_array( $product_id, $products_ids ) ) {
                    $key = array_search($product_id, $products_ids); // Find the index of "BMW"

                    if ($key !== false) {
                        unset($products_ids[$key]); // Remove the item if found
                    }
                    update_post_meta( $faq_id, 'pfw_product_ids', $products_ids ); // Update the post meta with the new array
                }

                $message = 'FAQ ID has been successfully remove!';

                break;
        }
    
        $faq_posts = $this->get_faqs($faqs_ids);

        /**
         * Prepare response data with FAQ post content
         */
        foreach ( $faq_posts as $faq ) {
            $faq_data[] = array(
                'id'      => $faq->ID,
                'title'   => esc_html( $faq->post_title ),
                'content' => esc_html( $faq->post_content ),
                'link'    => get_permalink( $faq->ID ),
            );
        }

        $data = array(
            'message'   =>  $message,
            'faq_posts' => $faq_data
        );
        
        return new WP_REST_Response( $data, 200 );
    }

    public function get_faqs_callback( WP_REST_Request $request ) {
        $params     = $request->get_params();
        $product_id = isset( $params['product_id'] ) ? intval( $params['product_id'] ) : 0;
        $faq_data   = [];
    
        if ( !$product_id ) {
            return new WP_REST_Response(['message' => 'Undefined Product ID', 'success' => false], 403);
        }
    
        /**
         * Get the existing IDs for the product
         */
        $faqs_ids   = get_post_meta( $product_id, 'pfw_faq_ids', true );
        $faq_posts  = $this->get_faqs($faqs_ids);

        /**
         * Prepare response data with FAQ post content
         */
        foreach ( $faq_posts as $faq ) {
            $faq_data[] = array(
                'id'      => $faq->ID,
                'title'   => esc_html( $faq->post_title ),
            );
        }

        $data = array(
            'message'   => 'Get All FAQ By product ID FAQ!',
            'faq_posts' => $faq_data
        );
        
        return new WP_REST_Response( $data, 200 );
    }

}