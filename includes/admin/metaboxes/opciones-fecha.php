<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table class="form-table">

	<tr>
		<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'Torneo', 'dartsl' ); ?></label></th>
		<td>
			<select name="dartsl[torneo]" class="dartsl_torneo selectize" placeholder="<?php _e( 'A que torneo pertenece la fecha', 'dartsl' ); ?>">
				<?php
				$torneos = get_posts([
					'post_type'        => 'dartsl_cpt',
				]);
				foreach ( $torneos as $torneo_data ) : ?>
					<option value="<?php echo $torneo_data->ID; ?>" <?php selected(true, ( $torneo_data->ID == $data['torneo']) ); ?>> <?php echo $torneo_data->post_title; ?></option>
				<?php endforeach; ?>
			</select>
			<p class="help-text">Selecciona el torneo</p>
		</td>
	</tr>
	<tr>
		<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'Participantes', 'dartsl' ); ?></label></th>
		<td>
			<select name="dartsl[participantes][]" class="dartsl_participantes selectize" placeholder="<?php _e( 'Elige los usuarios', 'dartsl' ); ?>" multiple="multiple" <?= isset($torneo['comenzado'])? 'disabled':'';?>>
				<?php
					$users = get_users();
					 foreach ( $users as $user ) : ?>
						<option value="<?php echo $user->ID; ?>" <?php selected(true, in_array( $user->ID, $data['participantes'])); ?>> <?php echo $user->display_name; ?></option>
					<?php endforeach; ?>
			</select>
			<?php if( isset($torneo['comenzado']) ) :?>
				<p class="help-text">
					Torneo ya comenzado en: <a href="https://challonge.com/<?= $torneo['challonge_url'];?>" target="_blank">https://challonge.com/<?= $torneo['challonge_url'];?></a>
				</p>
			<?php else: ?>
				<p class="help-text">
					<button class="components-button is-button is-default is-primary is-small generar_llave">Generar llave</button> <span id="generar_llave_success"></span>
				</p>
				<p class="comenzar_torneo_p" style="<?= isset($torneo['challonge_url'] ) ? '' : 'display:none';?>">
					<button class="components-button is-button is-default is-primary is-small comenzar_torneo">Comenzar torneo!</button>
				</p>
			<?php endif;?>
		</td>
	</tr>

		<tr>
			<th><label>&emsp;&emsp;&emsp;&emsp;<?php _e( 'Challonge', 'dartsl' ); ?></label></th>
			<td>
				<input type="text" name="dartsl[challonge_url]" class="regular-text challonge_url_field" value="<?= isset($torneo['challonge_url'])? $torneo['challonge_url'] : '';?>" <?= isset($torneo['comenzado']) ? 'disabled' : '';?>/>
					<p class="help-text">
						Ingresa la url de la fecha y hace click en este boton para obtener resultados de una fecha ya jugada <button class="components-button is-button is-default is-primary is-small obtener_datos">Obtener datos</button> <span id="generar_llave_success"></span>
					</p>
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