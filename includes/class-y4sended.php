<?php
/**
 * Y4sended setup
 *
 * @package Y4sended
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main Y4sended Class.
 *
 * @class Y4sended
 */
final class Y4sended {

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
	 * Add sended order status in register.
	 *
	 * @param array $order_statuses Shop orders statuses.
	 * @return array
	 */
	public function register_order_status( $order_statuses ) {
		$sended = array(
			'wc-sended' => array(
				'label'                     => _x( 'Sended', 'Order status', 'y4sended' ),
				'public'                    => false,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: number of orders */
				'label_count'               => _n_noop( 'Sended <span class="count">(%s)</span>', 'Sended <span class="count">(%s)</span>', 'y4sended' ),
			),
		);
		$before         = apply_filters( 'y4sended_sended_before', 'wc-completed' );
		$order_statuses = self::array_insert_before( $order_statuses, $before, $sended );

		return $order_statuses;
	}

	/**
	 * Add sended order status in getter.
	 *
	 * @param array $order_statuses Shop orders statuses.
	 * @return array
	 */
	public function get_order_status( $order_statuses ) {
		$sended         = array(
			'wc-sended' => _x( 'Sended', 'Order status', 'y4sended' ),
		);
		$before         = apply_filters( 'y4sended_sended_before', 'wc-completed' );
		$order_statuses = self::array_insert_before( $order_statuses, $before, $sended );
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
