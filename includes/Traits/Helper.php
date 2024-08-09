<?php
/**
 * Plugin Main Class
 *
 * @package ProductFaqWoo
 */
namespace ProductFaqWoo\Traits;

defined('ABSPATH') || die();

trait Helper {

    /**
     * Get faqs post list
     *
     * @since 1.3.0
     */
    public function get_faqs( $ids = false ) {
        $faqs   = [];
        $fields = $ids ? 'ids' : 'all';

        $args = array(
            'post_type'         => 'product_faq',
            'fields'            => $fields,
            'posts_per_page'    => -1,
        );

        $faqs = get_posts($args);

        return apply_filters('get_product_faqs', $faqs);
    }
}