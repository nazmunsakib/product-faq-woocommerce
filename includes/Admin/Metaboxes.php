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
     * Add Metaboxes.
     *
     * @param mixed $post_type post type
     */
    public function add_meta_boxes($post_type) {
        if ( in_array( $post_type, $this->post_types ) ) {
            add_meta_box(
                'product_faq_meta_settings',
                esc_html__( 'Product FAQ Panel', 'faq-for-woocommerce' ),
                array( $this, 'render_meta_box' ),
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
    public function render_meta_box( $post ) {
            // Get all FAQs
    $faqs = get_posts(array('post_type' =>  'product', 'numberposts' => -1));

    // Get saved FAQ IDs
    $selected_faqs = get_post_meta($post->ID, '_pfw_product_faqs', true);

    // Render the select box
    echo '<select name="product_faqs[]" multiple style="width:100%; height: 150px;">';
    foreach ($faqs as $faq) {
        $selected = in_array($faq->ID, (array) $selected_faqs) ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($faq->ID) . '" ' . $selected . '>' . esc_html($faq->post_title) . '</option>';
    }
    echo '</select>';
        ?>
        
        <div class="pfw-metaboxes-wrapper woocommerce">
            <table class="form-table pfw-admin-form-table">
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

        // Verify the nonce and autosave
        if (!isset($_POST['pfw_product_faqs']) || defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Save the selected FAQ IDs
        $selected_faqs = array_map('intval', $_POST['pfw_product_faqs']);
        update_post_meta($post_id, '_pfw_product_faqs', $selected_faqs);

    }

}
