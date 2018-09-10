<?php
/**
 * Order progress
 *
 * @author  Yogh
 * @package Y4sent/Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! $order ) {
	return;
}

?>
<ul class="y4sent-order-steps">

<?php
foreach ( $order_steps as $key => $step ) :
	$class = $key;
	if ( isset( $step['reached'] ) && $step['reached'] ) {
		$class .= ' active';
	}
	?>

	<li class="<?php echo esc_attr( $class ); ?>">
		<?php echo esc_html( $step['label'] ); ?>
	</li>

	<?php
endforeach;
?>

</ul>
