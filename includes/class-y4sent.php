<?php
/**
 * Y4sent setup
 *
 * @package Y4sent
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main Y4sent Class.
 *
 * @class Y4sent
 */
final class Y4sent {

	/**
	 * Constructor for class. Hooks in methods.
	 */
	public function __construct() {
		if ( self::is_wc_active() ) {
			// @codingStandardsIgnoreStart
			add_filter(
				'woocommerce_register_shop_order_post_statuses',
				array( $this, 'register_order_status' )
			);
			add_filter(
				'wc_order_statuses',
				array( $this, 'get_order_status' )
			);
			// @codingStandardsIgnoreEnd
		}
	}

	/**
	 * Verify if WooCommerce plugin is active.
	 *
	 * @return bool
	 */
	public static function is_wc_active() {
		$check = false;

		$active_plugins = (array) get_option( 'active_plugins', array() );

		/**
		 * Apply not WooCommerce prefixed filter.
		 */
		$active_plugins = apply_filters( 'active_plugins', $active_plugins ); // @codingStandardsIgnoreLine

		if ( in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) ) {
			$check = true;
		}

		return $check;
	}

	/**
	 * Add sent order status in register.
	 *
	 * @param array $order_statuses Shop orders statuses.
	 * @return array
	 */
	public function register_order_status( $order_statuses ) {
		$sent           = array(
			'wc-sent' => array(
				'label'                     => _x( 'Sent', 'Order status', 'y4sent' ),
				'public'                    => false,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: number of orders */
				'label_count'               => _n_noop( 'Sent <span class="count">(%s)</span>', 'Sent <span class="count">(%s)</span>', 'y4sent' ),
			),
		);
		$before         = apply_filters( 'y4sent_sent_before', 'wc-completed' );
		$order_statuses = self::array_insert_before( $order_statuses, $before, $sent );

		return $order_statuses;
	}

	/**
	 * Add sent order status in getter.
	 *
	 * @param array $order_statuses Shop orders statuses.
	 * @return array
	 */
	public function get_order_status( $order_statuses ) {
		$sent           = array(
			'wc-sent' => _x( 'Sent', 'Order status', 'y4sent' ),
		);
		$before         = apply_filters( 'y4sent_sent_before', 'wc-completed' );
		$order_statuses = self::array_insert_before( $order_statuses, $before, $sent );
		return $order_statuses;
	}

	/**
	 * Insert array before an key.
	 *
	 * @param array  $array Array to receive element.
	 * @param string $key Key word or number after new one.
	 * @param array  $insert Array with your own keys to be inserted.
	 * @return array
	 */
	public static function array_insert_before( $array, $key, $insert ) {
		$pos   = array_search( $key, array_keys( $array ), true );
		$count = count( $array );
		if ( $key ) {
			$head = array_slice( $array, 0, $pos + 1 );
			if ( $count >= $pos ) {
				$tail  = array_slice( $array, $pos + 1, $count - 1, true );
				$array = array_merge( $head, $insert, $tail );
			} else {
				$array = array_merge( $head, $insert );
			}
		} else {
			$array = array_merge( $array, $insert );
		}
		return $array;
	}
}
