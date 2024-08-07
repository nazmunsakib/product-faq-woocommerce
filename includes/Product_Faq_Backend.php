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

        /**
         * Post Type: Product FAQs.
         */
        $labels = [
            "name" => esc_html__( "Product FAQs", "product-faq-woocommerce" ),
            "singular_name" => esc_html__( "Product FAQ", "product-faq-woocommerce" ),
            "add_new" => esc_html__( "Add FAQ", "product-faq-woocommerce" ),
            "add_new_item"  => esc_html__( "Add New FAQ", "product-faq-woocommerce" ),
        ];
    
        $args = [
            "label"=> esc_html__( "Product FAQs", "product-faq-woocommerce" ),
            "labels" => $labels,
            "description" => "",
            "public" => true,
            "publicly_queryable" => true,
            "show_ui" => true,
            "show_in_rest" => true,
            "rest_base" => "",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "rest_namespace" => "wp/v2",
            "has_archive" => false,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "delete_with_user" => false,
            "exclude_from_search" => false,
            "capability_type" => "post",
            "map_meta_cap" => true,
            "hierarchical" => false,
            "can_export" => false,
            "rewrite" => [ "slug" => "product_faq", "with_front" => true ],
            "query_var" => true,
            "supports" => [ "title", "editor", "thumbnail" ],
            "show_in_graphql" => false,
        ];
    
        register_post_type( "product_faq", $args );
    }

}