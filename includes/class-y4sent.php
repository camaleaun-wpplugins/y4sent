<?php
/**
 * Y4sent setup
 *
 * @package Y4sent
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Main Y4sent Class.
 *
 * @class Y4sent
 */
final class Y4sent {

	/**
	 * Template path.
	 *
	 * @var string
	 */
	public $template_base;

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

			// Default template base if not declared in child constructor.
			if ( is_null( $this->template_base ) ) {
				$this->template_base = $this->plugin_path() . '/templates/';
			}

			add_filter(
				'woocommerce_email_classes',
				array( $this, 'email_class' )
			);
			add_filter(
				'woocommerce_locate_core_template',
				array( $this, 'template_file' ),
				10,
				4
			);
			add_filter(
				'woocommerce_email_actions',
				array( $this, 'email_action' )
			);
			// @codingStandardsIgnoreEnd
		}
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( Y4SENT_PLUGIN_FILE ) );
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
		$before         = apply_filters( 'y4sent_sent_statuses_before', 'wc-completed' );
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
		$before         = apply_filters( 'y4sent_sent_statuses_before', 'wc-completed' );
		$order_statuses = self::array_insert_before( $order_statuses, $before, $sent );
		return $order_statuses;
	}

	/**
	 * Init email class.
	 *
	 * @param array $emails E-mail classes.
	 * @return array
	 */
	public function email_class( $emails ) {
		$sent   = array(
			'Y4sended_Email_Customer_Sent_Order' => include 'emails/class-y4sent-email-customer-sent-order.php',
		);
		$before = apply_filters( 'y4sent_sent_emails_before', 'WC_Email_Customer_Completed_Order' );
		$emails = self::array_insert_before( $emails, $before, $sent );

		return $emails;
	}

	/**
	 * Change template file.
	 *
	 * @param string $template_file Full path of template file.
	 * @param string $template      Relative template file.
	 * @param string $template_base WooCommerce template path.
	 * @param string $id            ID of current template.
	 * @return string
	 */
	public function template_file( $template_file, $template, $template_base, $id ) {
		if ( 'customer_sent_order' === $id ) {
			$template_file = $this->template_base . $template;
		}
		return $template_file;
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

	/**
	 * Add email action.
	 *
	 * @param array $email_actions Email actions.
	 * @return array
	 */
	public function email_action( $email_actions ) {
		$email_actions[] = 'woocommerce_order_status_sent';
		return $email_actions;
	}
}
