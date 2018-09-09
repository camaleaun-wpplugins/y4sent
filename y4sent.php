<?php
/**
 * Plugin Name:     WooCommerce Sent Status
 * Plugin URI:      https://github.com/camaleaun/y4sent
 * Description:     WooCommerce sent status to order
 * Author:          Yogh
 * Author URI:      https://www.yogh.com.br
 * Text Domain:     y4sent
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         Y4sent
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed directly.

// Define Y4SENT_PLUGIN_FILE.
if ( ! defined( 'Y4SENT_PLUGIN_FILE' ) ) {
	define( 'Y4SENT_PLUGIN_FILE', __FILE__ );
}

// Include the main Y4sent class.
if ( ! class_exists( 'Y4sent' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-y4sent.php';
}

new Y4sent();
