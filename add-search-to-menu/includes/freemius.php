<?php

/**
 * Loads Freemius SDK
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exits if accessed directly.
}
// Creates a helper function for easy Freemius SDK access.
function is_fs() {
    global $is_fs;
    if ( !isset( $is_fs ) ) {
        // Activate multisite network integration.
        if ( !defined( 'WP_FS__PRODUCT_2086_MULTISITE' ) ) {
            define( 'WP_FS__PRODUCT_2086_MULTISITE', true );
        }
        // Include Freemius SDK.
        require_once dirname( __FILE__ ) . '/freemius/start.php';
        $is_fs = fs_dynamic_init( array(
            'id'              => '2086',
            'slug'            => 'add-search-to-menu',
            'type'            => 'plugin',
            'public_key'      => 'pk_e05b040b84ff5014d0f0955127743',
            'is_premium'      => false,
            'premium_suffix'  => '',
            'has_addons'      => false,
            'has_paid_plans'  => true,
            'has_affiliation' => 'selected',
            'menu'            => array(
                'slug'        => 'ivory-search',
                'first-path'  => 'plugins.php',
                'support'     => false,
                'affiliation' => false,
            ),
            'is_live'         => true,
        ) );
    }
    return $is_fs;
}

// Init Freemius.
is_fs();
// Signal that SDK was initiated.
do_action( 'is_fs_loaded' );
is_fs()->add_filter( 'plugin_icon', function () {
    return IS_PLUGIN_DIR . '/admin/assets/logo.png';
} );
// Disable affiliate notice
is_fs()->add_filter( 'show_affiliate_program_notice', '__return_false' );
/**
 * Drop index table after uninstall.
 * When this hook is executed, the plugin files no longer exists.
 * So we can't call IS_Index_Model::uninstall() method.
 * 
 * @since 5.0
 */
function is_index_uninstall() {
    global $wpdb;
    $is_index_table = $wpdb->prefix . 'is_inverted_index';
    $sql = $wpdb->prepare( "SHOW TABLES LIKE %s;", $is_index_table );
    if ( $wpdb->get_var( $sql ) === $is_index_table ) {
        $sql = "DROP TABLE {$is_index_table};";
        $wpdb->query( $sql );
    }
}

is_fs()->add_action( 'after_uninstall', 'is_index_uninstall' );