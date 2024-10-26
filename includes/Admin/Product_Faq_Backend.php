<?php
/**
 * Product_Faq_Backend
 *
 * @package ProductFaqWoo
 */
namespace ProductFaqWoo\Admin;
use ProductFaqWoo\Traits\Helper;

defined('ABSPATH') || die();

class Product_Faq_Backend {

    use Helper;

	/**
	 * Class constructor.
	 */
	public function __construct() {
        $this->add_hooks();
	}

	private function add_hooks() {
        add_action('woocommerce_product_data_panels', [$this, 'pwf_tab_data_panels']);
        add_filter('woocommerce_product_data_tabs',  [$this,'pwf_data_tab'] );
        add_action('init', [$this, 'register_cpt']);
	}

    public function pwf_tab_data_panels() {
        //Load script
        wp_enqueue_script('pfw-global');
        wp_enqueue_script('pfw-multi-select');
        wp_enqueue_script('pfw-admin');

        //Load Style
        wp_enqueue_style('pfw-multi-select');
        wp_enqueue_style('pfw-admin');
        
        $product_id = ( isset( $_GET['post'] ) && !empty( isset($_GET['post'] ) ) ) ? intval( $_GET['post'] ) : 0;
        ?>
        <div id="pfw_product_data" class="panel woocommerce_options_panel hidden">
            <div class="pfw-product-loader">
                <div class="pfw-product-loader-overlay">
                    <span class="spinner is-active"></span>
                </div>
            </div>
            <?php
            if ( $product_id ) {
                $post_id            = sanitize_text_field( wp_unslash($product_id) );
                $faq_ids            = get_post_meta( $post_id, 'pfw_faq_product_ids', true );
                $selected_faqs_ids  = !empty($faq_ids) ? $faq_ids : [];
                $faqs               = $this->get_faqs();
                $selected_faqs      = $this->get_faqs($selected_faqs_ids);
                ?>
                <div id="pfw-tab-content-wrapper" class="pfw-tab-content-wrapper">
                    <div class="pfw-tab-content-inner">
                        <div class="pfw-tab-content-header">
                            <?php echo sprintf('<h3 class="ffw-option-header-title">%s</h3>', esc_html__('Frequently Asked a Question (FAQ)', 'faq-for-woocommerce')); ?>
                            <?php 
                            echo sprintf(
                                '<p>%s</p>', 
                                esc_html__('Manage current product FAQs here.', 'faq-for-woocommerce')
                            ); 
                            ?>
                        </div>
                        <div class="pfw-tab-faq-sorting">
                            <select id="pfw-faq-select" name="pfw-faq-select" data-product-id="<?php echo esc_attr( $post_id ); ?>" data-placeholder="Select FAQs" multiple>
                                <?php
                                if( $faqs ) {
                                    foreach($faqs as $faq) {
                                        $selected = in_array($faq->ID, $selected_faqs_ids) ? 'selected' : '';
                                        echo sprintf('<option value="%s" %s>%s</option>', esc_html($faq->ID), $selected, esc_html($faq->post_title));
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div id="pfw-tab-faq-list" class="pfw-tab-faq-list-wrapper">
                            <?php 
                                if( is_array($selected_faqs) && count($selected_faqs) > 0 ) :
                                    foreach ( $selected_faqs as $faq ) :
                                        ?>
                                        <div class="pfw-tab-faq-item">
                                            <div class="pfq-tab-faq">
                                                <h3 class="pfq-tab-faq-title"><?php echo esc_html($faq->post_title); ?></h3>
                                            </div>
                                        </div>
                                        <?php
                                    endforeach;
                                endif;
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            }else {
                echo sprintf('<div class="pfw-product-publish-msg">%s</div>', esc_html__("Please publish the product first to insert the faqs", "faq-for-woocommerce"));
            }
            ?>
        </div>
        <?php
    }

    public function pwf_data_tab( $tabs ) {
        $tabs['product_faq_woocommerce'] = array(
            'label'    => 'FAQs',
            'target'   => 'pfw_product_data',
            'priority' => 100,
        );
        
        return $tabs;
    }

    public function register_cpt(){

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
            "label" => esc_html__( "Product FAQs", "product-faq-woocommerce" ),
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