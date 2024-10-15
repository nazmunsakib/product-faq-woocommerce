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
		/**
		 * Register Admin css
		 */
		wp_register_style(
			'chosen',
			PFW_ASSETS . '/admin/css/chosen.min.css',
			null,
			PFW_VERSION
		);
		wp_register_style(
			'select2',
			PFW_ASSETS . '/admin/css/select2.min.css',
			null,
			PFW_VERSION
		);

		wp_register_style(
			'pfw-admin',
			PFW_ASSETS . '/admin/css/pfw-admin.css',
			null,
			PFW_VERSION
		);

		/**
		 * Register Admin JS
		 */
		wp_register_script(
			'pfw-admin',
			PFW_ASSETS . '/admin/js/pfw-admin.js',
			['pfw-global'],
			PFW_VERSION
		);
		wp_register_script(
			'pfw-global',
			PFW_ASSETS . '/global/js/pfw-global.js',
			null,
			PFW_VERSION
		);
		wp_register_script(
			'select2',
			PFW_ASSETS . '/admin/js/select2.min.js',
			['jquery'],
			PFW_VERSION
		);
		wp_register_script(
			'chosen',
			PFW_ASSETS . '/admin/js/chosen.jquery.min.js',
			['jquery'],
			PFW_VERSION
		);

		wp_localize_script('pfw-global', 'pfwObj', array(
			'nonce' 	=> wp_create_nonce('wp_rest'),
			'api_url' 	=> esc_url_raw(rest_url()),
		));
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
			'pwf-frontend',
			PFW_ASSETS . '/frontend/css/product-faq-woo.css',
			null,
			PFW_VERSION
		);

		wp_register_script(
			'pwf-frontend',
			PFW_ASSETS . '/frontend/js/script.js',
			['pfw-global'],
			PFW_VERSION
		);

		wp_register_script(
			'pfw-global',
			PFW_ASSETS . '/global/js/pfw-global.js',
			null,
			PFW_VERSION
		);
	}

}