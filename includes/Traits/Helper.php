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

        return apply_filters('get_pfw_product_faqs', $faqs);
    }

    public function faqs_by_product_id( $product_id = 0 ){

        $faq_lists  = [];
        $faq_ids    = get_post_meta( $product_id, 'pfw_product_faqs', true ) ?? [];

        

        foreach( $faq_ids as $id ) {

            $post_status = get_post_status($id);
            if( "publish" !== $post_status ) {
                continue;
            }

            $faq_lists['id']       = $id;
            $faq_lists['question'] = get_the_title($id);
            $faq_lists['answer']   = get_the_content(null, false, $id);
        }

        return apply_filters('pfw_get_product_faqs_data', $faq_lists);
    }


	function pfw_get_random_product_id_has_faq() {
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => 1,
			'fields' => 'ids',
			'meta_query' => array(
				array(
					'key' => 'ffw_product_faq_post_ids',
					'value' => '',
					'compare' => '!=',
				),
			),
			'orderby' => 'rand',
			'order' => 'ASC',
		);

		$product_ids = get_posts($args);

		return reset($product_ids);
	}

}