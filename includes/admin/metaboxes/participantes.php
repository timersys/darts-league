<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table class="form-table">

	<?php do_action( 'dartsl/metaboxes/before_source', $opts ); ?>

	<tr>
		<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'Participantes', 'dartsl' ); ?></label></th>
		<td>
			<select name="dartsl[participantes][]" class="dartsl_participantes selectize" placeholder="<?php _e( 'Elige los usuarios', 'dartsl' ); ?>" multiple="multiple">
				<?php
					$users = get_users();
					 foreach ( $users as $user ) : ?>
						<option value="<?php echo $user->user_login; ?>" <?php selected(true, in_array( $user->ID, $data['participantes'])); ?>> <?php echo $user->username; ?></option>
					<?php endforeach; ?>
			</select>
			<p class="help-text" style="margin-top: -20px;">Quienes participan en esta fecha</p>
		</td>
	</tr>

	<?php do_action( 'dartsl/metaboxes/after_source', $opts ); ?>

</table>