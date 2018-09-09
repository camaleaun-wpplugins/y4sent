<?php
/**
 * Class WC_Email_Customer_Sent_Order file.
 *
 * @package Y4sent\Emails
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'Y4sent_Email_Customer_Sent_Order', false ) ) :

	/**
	 * Customer Sent Order Email.
	 *
	 * Order sent emails are sent to the customer when the order is marked sent and usual indicates that the order has been shipped.
	 *
	 * @class       WC_Email_Customer_Sent_Order
	 * @version     2.0.0
	 * @package     Y4sent/Classes/Emails
	 * @extends     WC_Email
	 */
	class Y4sent_Email_Customer_Sent_Order extends WC_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'customer_sent_order';
			$this->customer_email = true;
			$this->title          = __( 'Sent order', 'y4sent' );
			$this->description    = __( 'Order sent emails are sent to customers when their orders are marked sent and usually indicate that their orders have been shipped.', 'y4sent' );
			$this->template_html  = 'emails/customer-sent-order.php';
			$this->template_plain = 'emails/plain/customer-sent-order.php';
			$this->placeholders   = array(
				'{site_title}'   => $this->get_blogname(),
				'{order_date}'   => '',
				'{order_number}' => '',
			);

			// Triggers for this email.
			add_action( 'y4sent_order_status_sent_notification', array( $this, 'trigger' ), 10, 2 );

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param int            $order_id The order ID.
		 * @param WC_Order|false $order Order object.
		 */
		public function trigger( $order_id, $order = false ) {
			$this->setup_locale();

			if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
				$order = wc_get_order( $order_id );
			}

			if ( is_a( $order, 'WC_Order' ) ) {
				$this->object                         = $order;
				$this->recipient                      = $this->object->get_billing_email();
				$this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
				$this->placeholders['{order_number}'] = $this->object->get_order_number();
			}

			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}

		/**
		 * Get email subject.
		 *
		 * @return string
		 */
		public function get_default_subject() {
			return __( 'Your {site_title} order from {order_date} was sent', 'y4sent' );
		}

		/**
		 * Get email heading.
		 *
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'Your order was sent', 'y4sent' );
		}

		/**
		 * Get content html.
		 *
		 * @access public
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html(
				$this->template_html, array(
					'order'         => $this->object,
					'email_heading' => $this->get_heading(),
					'sent_to_admin' => false,
					'plain_text'    => false,
					'email'         => $this,
				)
			);
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_plain, array(
					'order'         => $this->object,
					'email_heading' => $this->get_heading(),
					'sent_to_admin' => false,
					'plain_text'    => true,
					'email'         => $this,
				)
			);
		}

		/**
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'    => array(
					'title'   => __( 'Enable/Disable', 'y4sent' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'y4sent' ),
					'default' => 'yes',
				),
				'subject'    => array(
					'title'       => __( 'Subject', 'y4sent' ),
					'type'        => 'text',
					'desc_tip'    => true,
					/* translators: %s: list of placeholders */
					'description' => sprintf( __( 'Available placeholders: %s', 'y4sent' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'    => array(
					'title'       => __( 'Email heading', 'y4sent' ),
					'type'        => 'text',
					'desc_tip'    => true,
					/* translators: %s: list of placeholders */
					'description' => sprintf( __( 'Available placeholders: %s', 'y4sent' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'email_type' => array(
					'title'       => __( 'Email type', 'y4sent' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'y4sent' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}
	}

endif;

return new Y4sent_Email_Customer_Sent_Order();
