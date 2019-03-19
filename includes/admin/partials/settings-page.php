<?php
/**
 * Settings page template
 * @since  1.0.0
 */


$defaults = [
	'puestos'   => [],
];
$opts     = wp_parse_args( $opts,  $defaults  );


?>
<div class="wrap geot-settings">
	<form name="geot-settings" method="post" enctype="multipart/form-data">
		<table class="form-table">

			<tr valign="top" class="geot-settings-title">
				<th colspan="2"><h3><?php _e( 'Opciones generales:', 'dartsl' ); ?></h3></th>
			</tr>
			<tr valign="top" class="">
				<th><label for="license"><?php _e( 'Puntajes', 'dartsl' ); ?></label></th>
				<td colspan="3">
					<table id="puntajes">
					<?php
					for( $i = 1; $i <= 20; $i++ ) {
						echo '<tr><td> Puesto #'.$i.' <input type="number" name="dartsl[puestos]['.$i.']" value="'.(isset($opts['puestos'][$i]) ? $opts['puestos'][$i] : 0 ).'"/></td></tr>';
					}
					?>
					</table>
				</td>
			</tr>

			<tr>
				<td><input type="submit" class="button-primary" value="<?php _e( 'Save settings', 'dartsl' ); ?>"/></td>
				<?php wp_nonce_field( 'dartsl_save_settings', 'dartsl_nonce' ); ?>
		</table>
	</form>
</div>
