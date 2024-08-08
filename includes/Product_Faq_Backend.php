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
        add_action('woocommerce_product_data_panels', [$this, 'faq_tab_data_panels']);
        add_filter('woocommerce_product_data_tabs',  [$this,'faq_data_tab'] );
        add_action('init', [$this, 'register_post_type']);
	}

    public function faq_tab_data_panels() {
        ?>
        <div id="ffw_product_data" class="panel ffw_options_panel woocommerce_options_panel hidden">
            <?php
            echo "sakib";
            if ( isset($_GET['post']) && ! empty($_GET['post']) ) {
                $post_id = sanitize_text_field( wp_unslash($_GET['post']) );
                $faq_post_ids = get_post_meta($post_id, 'ffw_product_faq_post_ids', true);
                $faq_post_ids = !empty($faq_post_ids) ? $faq_post_ids : [];
                $product_faqs_data = wp_json_encode($faq_post_ids);
                $faq_posts = ffw_get_faqs_post_list();

                var_dump($faq_post_ids);
                ?>
                    <?php include FFW_FILE_DIR . '/views/faq-woocommerce-modal-form.php'; ?>
                    <div class="ffw-product-loader">
                        <div class="ffw-product-loader-overlay">
                            <span class="spinner is-active"></span>
                        </div>
                    </div>
                    <div class="ffw-product-form-header" id="ffw-product-form-header">
                        <?php do_action( 'before_faq_woocommerce_product_options' ); ?>
                        <div class="ffw-sortable-options-wrapper">
                            <div class="ffw-product-form-heading">
                                <?php echo sprintf('<h3 class="ffw-option-header-title">%s</h3>', esc_html__('FAQ List', 'faq-for-woocommerce')); ?>
                                <?php 
                                echo sprintf(
                                    '<p>%s <span class="ffw-note">%s</span></p>', 
                                    esc_html__('Manage current product FAQs here.', 'faq-for-woocommerce'), 
                                    esc_html__('These controls will work if only products are assigned to FAQs instead of product categories.', 'faq-for-woocommerce')
                                ); 
                                ?>
                            </div>
                            <div class="ffw-sortable-options-header">
                                <select class="ffw_search" id="ffw_search">
                                    <option value=""><?php esc_html_e('Select a FAQ', 'faq-for-woocommerce'); ?></option>
                                    <?php
                                    if( $faq_posts ) {
                                        foreach($faq_posts as $post) {
                                            echo sprintf('<option value="%s">%s</option>', esc_html($post->ID), esc_html($post->post_title));
                                        }
                                    }
                                    ?>
    
                                </select>
                                <div class="ffw-option-buttons">
                                    <?php echo sprintf('<button class="ffw-add-new ffw-options-header-btn">%s</button>', esc_html__('Quick Add', 'faq-for-woocommerce')); ?>
                                    <?php echo sprintf('<button class="ffw-delete-all ffw-options-header-btn" id="ffw-delete-all-faq">%s</button>', esc_html__('Delete All', 'faq-for-woocommerce')); ?>
                                </div>
                                <input type="hidden" id="ffw_products" value='<?php echo esc_html($product_faqs_data); ?>'>
                                <input type="hidden" id="ffw_product_page_id" value="<?php echo isset($_GET['post']) ? esc_html($_GET['post']) : ''; ?>">
                            </div>
                            <div class="ffw-body">
                                <?php
                                ffw_get_option_panel_body($_GET['post']);
                                ?>
                            </div>
                        </div>
    
                        <?php do_action( 'after_faq_woocommerce_product_options' ); ?>
                    </div>
                <?php
            }else {
                echo sprintf('<div class="ffw-product-publish-msg">%s</div>', esc_html__("Please publish the product first to insert the faqs", "faq-for-woocommerce"));
            }
            ?>
        </div>
        <?php
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