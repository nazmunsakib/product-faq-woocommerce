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


defined('ABSPATH') || die();

class Rest_API {

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
            'callback'  =>  [$this, 'get_product_faqs_callback'],
            'args'      =>  [
                'action'    =>  [
                    'validate_callback' =>  function( $param, $request, $key ){
                        return is_string( $param );
                    }
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

    public function get_product_faqs_callback( WP_REST_Request $request ){

        $data = array(
            'message'   => 'Secure data for authorized users!',
            'response'  => [],
        );

        return new WP_REST_Response($data, 200);
    }

}