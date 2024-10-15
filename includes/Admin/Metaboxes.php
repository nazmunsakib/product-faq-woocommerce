<?php
/**
 * Metaboxes
 *
 * @package ProductFaqWoo
 */
namespace ProductFaqWoo\Admin;
use ProductFaqWoo\Traits\Helper;

defined('ABSPATH') || die();

/**
 * Metaboxes Class.
 */
class Metaboxes{

    use Helper;

    private $post_types;

    /**
     * Hook in tabs.
     */
    public function __construct(){
        add_action( 'add_meta_boxes', array( $this, 'add' ) );
        add_action( 'save_post', array( $this, 'save' ) );

        $this->post_types = ['product_faq'];
    }

    /**
     * Add Metaboxes.
     *
     * @param mixed $post_type post type
     */
    public function add($post_type) {
        if ( in_array( $post_type, $this->post_types ) ) {
            add_meta_box(
                'product_faq_meta_settings',
                esc_html__( 'Product FAQ Settings', 'faq-for-woocommerce' ),
                array( $this, 'render' ),
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
    public function render( $post ) {
        wp_enqueue_style('pfw-admin');

        /**
         * Retrieve saved meta value
         */
        $selected_faqs  = get_post_meta($post->ID, 'pfw_product_ids', true);
        $selected_faqs  = is_array($selected_faqs) ? $selected_faqs : [];

        $products = get_posts(
            array(
                'post_type'     =>  'product',
                'fields'        => 'ids',
                'numberposts'   => -1,
            )
        );

        $output  = '';
        $output .= '<div class="pfw-faq-metabox-wrapper">';
        $output .= '<label for="pfw_product_select">Choose Products:</label>';
        $output .= '<select id="pfw_product_select" name="pfw_product_ids[]" multiple="multiple">';
        
        foreach ( $products as $product_id ) {
            $product_title  = get_the_title( post: $product_id );
            $selected   = in_array($product_id, $selected_faqs) ? 'selected' : '';
            $output .= '<option value="'.esc_attr($product_id).'" '.esc_attr($selected).'>'.esc_html($product_title).'</option>';
        }

        $output .= '</select>';
        $output .= '</div>';

        echo $output;
    }


    /**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save( $post_id ) {

        /**
         * Verify the nonce and autosave
         */
        if (!isset($_POST['pfw_product_ids']) || defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        /**
         * Retrieve existing faq meta values
         */
        $old_faq_meta = get_post_meta($post_id, 'pfw_product_ids', true);
        $old_faq_meta = is_array($old_faq_meta) ? $old_faq_meta : [];

        /**
         * Get the new values from the form submission
         */
        $new_faq_meta = $_POST['pfw_product_ids'];

        /**
         * Merge the old values with the new values
         */
        $merged_faq_meta = array_unique(array_merge($old_faq_meta, $new_faq_meta));

        /**
         * Save the selected FAQ IDs
         */
        update_post_meta($post_id, 'pfw_product_ids', $merged_faq_meta);

        foreach( $merged_faq_meta as $product_id ){
            /**
             * Retrieve existing product meta values
             */
            $old_product_meta = get_post_meta($product_id, 'pfw_faq_ids', true);
            $old_product_meta = is_array($old_product_meta) ? $old_product_meta : [];

            array_push($old_product_meta, $post_id);

            $new_product_meta = array_unique($old_product_meta);

            update_post_meta( $product_id, 'pfw_faq_ids', meta_value: $new_product_meta );
        }
    }
}
