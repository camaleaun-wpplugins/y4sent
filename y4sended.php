<?php
/**
 * Plugin Name:     WooCommerce Sended Status
 * Plugin URI:      https://github.com/camaleaun/y4sended
 * Description:     WooCommerce sended status to order
 * Author:          Yogh
 * Author URI:      https://www.yogh.com.br
 * Text Domain:     y4sended
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         Y4sended
 */

defined( 'ABSPATH' ) || die(); // Exit if accessed directly.

// Include the main Y4sended class.
if ( ! class_exists( 'Y4sended' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-y4sended.php';
}

new Y4sended();
