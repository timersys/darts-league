<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$liga = get_post_meta(get_the_id(),'dartls_liga', ['participantes'=>[]]);
?>
<table class="form-table">

	<tr>
		<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'Liga', 'dartsl' ); ?></label></th>
		<td><p>Si es una liga, selecciona los participantes para generar los diferentes partidos.</p>
			<select name="dartsl[participantes][]" class="dartsl_participantes selectize" placeholder="<?php _e( 'Elige los usuarios', 'dartsl' ); ?>" multiple="multiple" <?= isset($torneo['comenzado'])? 'disabled':'';?>>
				<?php
					$users = get_users();
					 foreach ( $users as $user ) : ?>
						<option value="<?php echo $user->ID; ?>" <?php selected(true, in_array( $user->ID, $liga['participantes'])); ?>> <?php echo $user->display_name; ?></option>
					<?php endforeach; ?>
			</select>
			<?php if( !empty($liga['liga']) ) :?>
				<p class="help-text">
					<?= $liga['fechas'] . ' fechas creadas - <a href="'. admin_url('edit.php?post_type=dartsl_fecha_cpt&fecha_de=' . $liga['liga']) .'" target="_blank">'.$liga['liga_name'].'</a>';?>
				</p>
			<?php else: ?>
				<p class="help-text">
					<button class="components-button is-button is-default is-primary is-small generar_liga">Generar fechas</button> <span id="generar_llave_success"></span>
				</p>
			<?php endif;?>
		</td>
	</tr>
</table>
<select name="placholder_select" class="dartsl_participantes_placeholder" disabled style="display: none">
	<option value="">Selecciona uno</option>
	<?php
	$users = get_users();
	foreach ( $users as $user ) : ?>
		<option value="<?php echo $user->ID; ?>" > <?php echo $user->display_name; ?></option>
	<?php endforeach; ?>
</select>