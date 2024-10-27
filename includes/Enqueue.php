<?php
/**
 * Plugin Enqueue Assets
 *
 * Handles the registration and enqueuing of both frontend and admin assets 
 * for the Product FAQ WooCommerce plugin.
 *
 * @package ProductFaqWoo
 */

namespace ProductFaqWoo;

defined('ABSPATH') || exit; // Prevent direct access.

/**
 * Class Enqueue
 *
 * Manages the enqueueing of CSS and JS files for the admin and frontend.
 */
class Enqueue {

    /**
     * Constructor.
     *
     * Initializes the class and hooks into WordPress to enqueue admin and frontend assets.
     */
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'admin_assets'), 100);
        add_action('wp_enqueue_scripts', array($this, 'frontend_assets'), 100);
    }

    /**
     * Enqueue Admin Assets.
     *
     * Registers and enqueues styles and scripts for the admin dashboard.
     * Styles and scripts are specific to the Product FAQ WooCommerce plugin’s admin interface.
     *
     * @return void
     */
    public function admin_assets() {
        // Register admin CSS files.
        wp_register_style(
            'chosen',
            PFW_ASSETS . '/admin/css/chosen.min.css',
            null,
            PFW_VERSION
        );
        wp_register_style(
            'pfw-multi-select',
            PFW_ASSETS . '/admin/css/multi-select.css',
            null,
            PFW_VERSION
        );
        wp_register_style(
            'pfw-admin',
            PFW_ASSETS . '/admin/css/pfw-admin.css',
            null,
            PFW_VERSION
        );

        // Register admin JavaScript files.
        wp_register_script(
            'pfw-admin',
            PFW_ASSETS . '/admin/js/pfw-admin.js',
            ['pfw-global', 'pfw-multi-select'],
            PFW_VERSION,
            true
        );
        wp_register_script(
            'pfw-global',
            PFW_ASSETS . '/global/js/pfw-global.js',
            null,
            PFW_VERSION,
            true
        );
        wp_register_script(
            'pfw-multi-select',
            PFW_ASSETS . '/admin/js/multi-select.js',
            null,
            PFW_VERSION,
            true
        );

        // Localize the global script with data accessible in JavaScript.
        wp_localize_script('pfw-global', 'pfwObj', array(
            'nonce'   => wp_create_nonce('wp_rest'), // Nonce for security.
            'api_url' => esc_url_raw(rest_url()),    // Base URL for REST API calls.
        ));
    }

    /**
     * Enqueue Frontend Assets.
     *
     * Registers and enqueues styles and scripts for the frontend of the site.
     * Ensures Product FAQ WooCommerce plugin assets are available on product pages.
     *
     * @return void
     */
    public function frontend_assets() {
        // Register frontend CSS file.
        wp_register_style(
            'pwf-frontend',
            PFW_ASSETS . '/frontend/css/product-faq-woo.css',
            null,
            PFW_VERSION
        );

        // Register frontend JavaScript files.
        wp_register_script(
            'pwf-frontend',
            PFW_ASSETS . '/frontend/js/script.js',
            ['pfw-global'], // Sets 'pfw-global' as a dependency.
            PFW_VERSION,
            true
        );
        wp_register_script(
            'pfw-global',
            PFW_ASSETS . '/global/js/pfw-global.js',
            null,
            PFW_VERSION,
            true
        );
    }
}
