<?php
/**
 * Plugin Enqueue Assets.
 *
 * @package ProductFaqWoo
 */
namespace ProductFaqWoo;

defined('ABSPATH') || die();

class Enqueue {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action('admin_enqueue_scripts', array( $this, 'admin_assets' ), 100 );
        add_action('wp_enqueue_scripts', array( $this, 'frontend_assets' ), 100 );
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * Frontend assets handler
	 *
	 * @return void
	 */
	public function admin_assets() {
		wp_register_style(
			'database-crud-operations',
			PRODUCT_FAQ_WOO_ASSETS . '/css/style.css',
			null,
			PRODUCT_FAQ_WOO_VERSION
		);
	}

    /**
	 * Enqueue frontend assets.
	 *
	 * Frontend assets handler
	 *
	 * @return void
	 */
	public function frontend_assets() {
		wp_register_style(
			'product-faq-woo-frontend',
			PRODUCT_FAQ_WOO_ASSETS . '/frontend/css/product-faq-woo.css',
			null,
			PRODUCT_FAQ_WOO_VERSION
		);

		wp_register_script(
			'product-faq-woo-frontend-script',
			PRODUCT_FAQ_WOO_ASSETS . '/frontend/js/script.js',
			null,
			PRODUCT_FAQ_WOO_VERSION
		);
	}

}