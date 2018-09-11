<?php
/**
 * Last Order Shortcode
 *
 * Lets a user see the status of client last order.
 *
 * @package Y4sent/Shortcodes/Last_Order
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shortcode last order class.
 */
class Y4sent_Shortcode_Last_Order {

	/**
	 * Get the shortcode content.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public static function get( $atts ) {
		return WC_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	/**
	 * Output the shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public static function output( $atts ) {
		// Check cart class is loaded or abort.
		if ( is_null( WC()->cart ) ) {
			return;
		}

		$atts = shortcode_atts( array(), $atts, 'woocommerce_last_order' );

		$last_order = current( wc_get_orders( array(
			'customer' => get_current_user_id(),
			'limit'    => 1,
		) ) );

		if ( ! empty( $last_order ) ) {
			wc_get_template( 'order/order-details.php', array(
				'order_id' => $last_order->get_order_number(),
			) );
		}
	}
}
