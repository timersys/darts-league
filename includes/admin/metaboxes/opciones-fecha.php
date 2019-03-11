<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table class="form-table">

	<?php do_action( 'dartsl/metaboxes/before_source', $opts ); ?>
	<tr>
		<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'Torneo', 'dartsl' ); ?></label></th>
		<td>
			<select name="dartsl[torneo]" class="dartsl_torneo selectize" placeholder="<?php _e( 'A que torneo pertenece la fecha', 'dartsl' ); ?>">
				<?php
				$torneos = get_posts([
					'post_type'        => 'dartsl_cpt',
				]);
				foreach ( $torneos as $torneo ) : ?>
					<option value="<?php echo $torneo->ID; ?>" <?php selected(true, ( $torneo->ID == $torneo) ); ?>> <?php echo $torneo->post_title; ?></option>
				<?php endforeach; ?>
			</select>
			<p class="help-text">Selecciona el torneo</p>
		</td>
	</tr>
	<tr>
		<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'Participantes', 'dartsl' ); ?></label></th>
		<td>
			<select name="dartsl[participantes][]" class="dartsl_participantes selectize" placeholder="<?php _e( 'Elige los usuarios', 'dartsl' ); ?>" multiple="multiple">
				<?php
					$users = get_users();
					 foreach ( $users as $user ) : ?>
						<option value="<?php echo $user->user_login; ?>" <?php selected(true, in_array( $user->ID, $data['participantes'])); ?>> <?php echo $user->user_login; ?></option>
					<?php endforeach; ?>
			</select>
			<p class="help-text">Quienes participan en esta fecha <button class="button button-primary generar_llave">Generar llave</button> </p>
		</td>
	</tr>

	<tr>
		<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'Challonge', 'dartsl' ); ?></label></th>
		<td>
			<input type="text" name="dartsl[challonge_url]" value="<?= $data['challonge_ur'];?>" placeholder="Enter or generate challonge url">

		</td>
	</tr>

	<?php do_action( 'dartsl/metaboxes/after_source', $opts ); ?>

</table>