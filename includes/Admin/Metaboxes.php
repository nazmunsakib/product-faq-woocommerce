<?php
/**
 * Metaboxes
 *
 * @package ProductFaqWoo
 */
namespace ProductFaqWoo\Admin;

defined('ABSPATH') || die();

/**
 * Metaboxes Class.
 */
class Metaboxes{

    private $post_types;

    /**
     * Hook in tabs.
     */
    public function __construct(){
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save' ) );

        $this->post_types = ['product_faq'];
    }

    /**
     * Get product support, Specific/Global product support.
     *
     */
    public function get_product_support() {
        return apply_filters('ffw_is_global_product_support_enable', false);
    }

    /**
     * Add Metaboxes.
     *
     * @param mixed $post_type post type
     */
    public function add_meta_boxes($post_type) {
        if ( in_array( $post_type, $this->post_types ) ) {
            add_meta_box(
                'product_faq_meta_settings',
                esc_html__( 'Product FAQ Panel', 'faq-for-woocommerce' ),
                array( $this, 'meta_box_content' ),
                $post_type,
                'normal',
                'high'
            );
        }
    }

    /**
     * Meta Box content.
     *
     * @param \WP_Post $post The post object.
     */
    public function meta_box_content( $post ) {
        ?>
        <div class="pfw-metaboxes-wrapper woocommerce">
            <table class="form-table pfw-admin-form-table">
                <?php
                    do_action('ffw_metabox_content_item', $post);
                ?>
            </table>
        </div>
        <?php
    }


    /**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save( $post_id ) {

        // when nonce is not set, do nothing
        if ( ! isset( $_POST['ffw_faq_product_settings_nonce'] ) ) {
            return $post_id;
        }

        $nonce = wp_unslash($_POST['ffw_faq_product_settings_nonce']);

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'ffw_faq_product_settings' ) ) {
            return $post_id;
        }

        /*
            * If this is an autosave, our form has not been submitted,
            * so we don't want to do anything.
            */
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        // Check the user's permissions.
        if ( 'ffw' == $_POST['post_type'] ) {
            //check user access or not.
            if(!ffw_is_user_capable()) {
                return $post_id;
            }
        }

        do_action('ffw_save_faq_meta', $post_id);

        $is_global_product_support_enable = $this->get_product_support();

        if( ! $is_global_product_support_enable ) {

            $product_ids = [];
            $cat_ids = [];

            // save product ids.
            if( isset($_POST['ffw_faq_products']) && !empty($_POST['ffw_faq_products']) ) {
                // Sanitize the faqs products field value.
                $product_ids = $_POST['ffw_faq_products'];
            }

            // Reference for saved previous products ids.
            // It'll help to check if new selected product ids are removed or not.
            if(isset($_POST['ffw_faq_save_products']) && !empty($_POST['ffw_faq_save_products'])) {
                $saved_product_ids = sanitize_text_field($_POST['ffw_faq_save_products']);

                if(!empty($saved_product_ids)) {
                    $saved_product_ids = explode(',', $saved_product_ids);
                    $removed_product_ids = array_diff($saved_product_ids, $product_ids);

                    error_log('product ids');
                    error_log(print_r($product_ids, true));
                    
                    error_log('saved product ids');
                    error_log(print_r($saved_product_ids, true));

                    error_log('removed product ids');
                    error_log(print_r($removed_product_ids, true));
                }
            }

            /**
             * Let's remove faq for the removed product ids.
             * 
             * @since 1.6.0
             */
            if( isset($removed_product_ids) && is_array($removed_product_ids) && !empty($removed_product_ids) ) {
                foreach($removed_product_ids as $removed_product_id) {

                    // get product faqs
                    $faq_ids = get_post_meta($removed_product_id, 'ffw_product_faq_post_ids', true);

                    // search curret faq id to saved ids and remove if found.
                    if( !empty($faq_ids) ) {
                        $index = array_search($post_id, $faq_ids);

                        if(isset($faq_ids[$index])) {
                            unset($faq_ids[$index]);

                            // Update the meta field.
                            update_post_meta( $removed_product_id, 'ffw_product_faq_post_ids', $faq_ids );
                        }
                    }
                    
                }
            }

            /**
             * Add faq post id to the product.
             * 
             * @since 1.6.0
             */
            if( isset($product_ids) && is_array($product_ids) && !empty($product_ids) ) {
                foreach($product_ids as $product_id) {
                    $post_id    = (int) $post_id;
                    $product_id = (int) $product_id;

                    //insert faqs.
                    ffw_insert_faqs_by_product($post_id, $product_id);
                }
            }

            // save categories ids.
            if( isset($_POST['ffw_faq_categories']) && !empty($_POST['ffw_faq_categories']) ) {
                // sanitize the faqs categories field value.
                $cat_ids = $_POST['ffw_faq_categories'];
            }

            // Reference for saved previous category ids.
            // It'll help to check if new selected category ids are removed or not.
            if(isset($_POST['ffw_faq_save_categories']) && !empty($_POST['ffw_faq_save_categories'])) {
                $saved_cat_ids = sanitize_text_field($_POST['ffw_faq_save_categories']);

                if(!empty($saved_cat_ids)) {
                    $saved_cat_ids = explode(',', $saved_cat_ids);
                    $removed_cat_ids = array_diff($saved_cat_ids, $cat_ids);
                }
            }

            /**
             * Let's remove faq for the removed category ids.
             * 
             * @since 1.6.0
             */
            if( isset($removed_cat_ids) && is_array($removed_cat_ids) && !empty($removed_cat_ids) ) {
                foreach($removed_cat_ids as $removed_cat_id) {

                    // get category faqs
                    $faq_ids = get_term_meta($removed_cat_id, 'ffw_cat_faq_post_ids', true);

                    // search curret faq id to saved ids and remove if found.
                    if( !empty($faq_ids) ) {
                        $index = array_search($post_id, $faq_ids);

                        if(isset($faq_ids[$index])) {
                            unset($faq_ids[$index]);

                            // Update the meta field.
                            update_term_meta( $removed_cat_id, 'ffw_cat_faq_post_ids', $faq_ids );
                        }
                    }
                    
                }
            }

            /**
             * Add faq post id to the categories.
             * 
             * @since 1.6.0
             */
            if( isset($cat_ids) && is_array($cat_ids) && !empty($cat_ids) ) {
                foreach($cat_ids as $cat_id) {

                    // get categories faqs.
                    $faq_ids = get_term_meta($cat_id, 'ffw_cat_faq_post_ids', true);

                    // when no faqs is set, put empty array.
                    if( empty($faq_ids) ) {
                        $faq_ids = [];
                    }

                    //push the faq id.
                    array_push($faq_ids, $post_id);

                    //remove duplicate faq id.
                    $faq_ids = array_unique($faq_ids);

                    // Update the meta field.
                    update_term_meta( $cat_id, 'ffw_cat_faq_post_ids', $faq_ids );
                }
            }

        }

    }

}
