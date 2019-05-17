<?php
/**
 * Class DartsL_Fecha_Cpt will handle all stuff related to custom post type
 * @since 1.0.0
 */
class DartsL_Fecha_Cpt {

	/**
	 * DartsL_Fecha_Cpt constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_cpt' ] );
		add_action( 'add_meta_boxes_dartsl_fecha_cpt', [ $this, 'add_meta_boxes' ], 99 );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts' ] );
		add_action( 'save_post_dartsl_fecha_cpt', [ $this, 'save_meta_options' ] );
		add_filter( 'the_content', [ $this, 'add_fecha_into_content' ] );
		#add_action( 'manage_dartsl_fecha_cpt_posts_custom_column', [ $this, 'set_custom_cpt_values' ], 10, 2 );
		#add_filter( 'wp_insert_post_data', [ $this, 'modify_post_name' ], 10, 2 );
	}

	/**
	 * Register custom post types
	 * @since     1.0.0
	 * @return void
	 */
	public function register_cpt() {



		$labels = [
			'name'               => 'Fechas',
			'singular_name'      => _x( 'Fecha', 'post type singular name', 'dartsl' ),
			'menu_name'          => _x( 'Fecha', 'admin menu', 'dartsl' ),
			'name_admin_bar'     => _x( 'Fecha', 'add new on admin bar', 'dartsl' ),
			'add_new'            => _x( 'Add New', 'Fecha', 'dartsl' ),
			'add_new_item'       => __( 'Add New Fecha', 'dartsl' ),
			'new_item'           => __( 'New Fecha', 'dartsl' ),
			'edit_item'          => __( 'Edit Fecha', 'dartsl' ),
			'view_item'          => __( 'View Fecha', 'dartsl' ),
			'all_items'          => __( 'Fecha', 'dartsl' ),
			'search_items'       => __( 'Search Fecha', 'dartsl' ),
			'parent_item_colon'  => __( 'Parent Fecha:', 'dartsl' ),
			'not_found'          => __( 'No League found.', 'dartsl' ),
			'not_found_in_trash' => __( 'No League found in Trash.', 'dartsl' ),
		];

		$args = [
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'query_var'           => true,
			'exclude_from_search' => false,
			'rewrite'             => [ 'slug' => 'fecha' ],
			'capability_type'     => 'post',
			'capabilities'        => [
				'publish_posts'       => apply_filters( 'dartsl_settings_page_roles', 'manage_options' ),
				'edit_posts'          => apply_filters( 'dartsl_settings_page_roles', 'manage_options' ),
				'edit_others_posts'   => apply_filters( 'dartsl_settings_page_roles', 'manage_options' ),
				'delete_posts'        => apply_filters( 'dartsl_settings_page_roles', 'manage_options' ),
				'delete_others_posts' => apply_filters( 'dartsl_settings_page_roles', 'manage_options' ),
				'read_private_posts'  => apply_filters( 'dartsl_settings_page_roles', 'manage_options' ),
				'edit_post'           => apply_filters( 'dartsl_settings_page_roles', 'manage_options' ),
				'delete_post'         => apply_filters( 'dartsl_settings_page_roles', 'manage_options' ),
				'read_post'           => apply_filters( 'dartsl_settings_page_roles', 'manage_options' ),
			],
			'has_archive'         => true,
			'hierarchical'        => false,
			'menu_position'       => 10,
			'supports'            => [ 'title', 'editor','custom-fields','revisions' ],
			'show_in_rest'	=> true
		];

		register_post_type( 'dartsl_fecha_cpt', $args );
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Liga/Torneo', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Liga/Torneo', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Buscar Liga/Torneos', 'textdomain' ),
			'all_items'         => __( 'Todos los tipos', 'textdomain' ),
			'parent_item'       => __( 'Parent Liga/Torneo', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Liga/Torneo:', 'textdomain' ),
			'edit_item'         => __( 'Edit Liga/Torneo', 'textdomain' ),
			'update_item'       => __( 'Update Liga/Torneo', 'textdomain' ),
			'add_new_item'      => __( 'Add New Liga/Torneo', 'textdomain' ),
			'new_item_name'     => __( 'New Liga/Torneo Name', 'textdomain' ),
			'menu_name'         => __( 'Liga/Torneo', 'textdomain' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'fecha-de' ),
		);

		register_taxonomy( 'fecha_de', array( 'dartsl_fecha_cpt' ), $args );
	}

	/**
	 * Load all scripts and styles
	 * @param $hook
	 */
	function load_scripts( $hook ) {

		if( 'dartsl_cpt' != get_post_type() && 'dartsl_fecha_cpt' != get_post_type() ) {
			return;
		}
		// Admin scripts
		if( is_admin() ) {
			wp_enqueue_script( 'dartsl-selectize' );
			wp_enqueue_script( 'dartsl-admin', DARTSL_PLUGIN_URL . 'includes/assets/js/dartsl.js', [
				'jquery',
				'dartsl-selectize'
			], DARTSL_VERSION, true );

			wp_localize_script( 'dartsl-admin', 'dartsl', [
				'liga_url' => admin_url('edit.php?post_type=dartsl_fecha_cpt&fecha_de=')
			]);

			wp_enqueue_style( 'dartsl-selectize' );
		}

		wp_enqueue_style( 'dartsl-admin-fecha', DARTSL_PLUGIN_URL . 'includes/assets/css/dartsl_fecha.css', DARTSL_VERSION );

	}

	/**
	 * Add custom columns to cpt
	 *
	 * @param [type] $columns [description]
	 *
	 * @since  1.2
	 * @return mixed
	 */
	function set_custom_cpt_columns( $columns ) {

		$settings 	= dartsl_settings();
		$new_column = [];

		foreach ( $columns as $key => $value ) {

			$new_column[ $key ] = $value;

			if ( $key == 'title' ) {
				$new_column['source_url']	= __( 'Destination URL', 'dartsl' );
				$new_column['shortcode']	= __( 'Shortcode', 'dartsl' );

				if( isset( $settings['opt_stats'] ) && $settings['opt_stats'] == 1 )
					$new_column['count_click']	= __( 'Total Clicks', 'dartsl' );
			}
		}

		return apply_filters( 'dartsl/manage_columns/name', $new_column, $columns );
	}


	/**
	 * Add custom values columns to cpt
	 *
	 * @param $column
	 * @param $post_id
	 *
	 * @return mixed
	 * @since  1.2
	 */
	function set_custom_cpt_values( $column, $post_id ) {

		$settings     = dartsl_settings();
		$opts         = dartsl_options( $post_id );
		$value_column = '';

		switch ( $column ) {
			case 'source_url' :
				$value_column = get_permalink($post_id);
				break;
			case 'count_click' :
				$value_column = $opts['count_click'];
				break;
			case 'shortcode' :
				$value_column = '<input type="text" value="[geo-link slug=&quot;'. esc_attr__( $opts['source_slug'] ) .'&quot; nofollow=&quot;yes&quot; noreferrer=&quot;no&quot;]...[/geo-link]" readonly />';
				break;
			default:
				$column;
		}

		echo apply_filters( 'dartsl/manage_columns/value', $value_column, $column, $post_id );
	}


	/**
	 * Register the metaboxes for our cpt
	 * @since    1.0.0
	 * @return   void
	 */
	public function add_meta_boxes() {
		global $wp_meta_boxes;

		$is_liga = get_post_meta(get_the_id(), 'is_liga', false);

			add_meta_box(
				'dartsl-participantes',
				__( 'Opciones', 'dartsl' ),
				[ $this, 'dartsl_opciones' ],
				'dartsl_fecha_cpt',
				'normal',
				'core'
			);
		if( ! $is_liga ) {
			add_meta_box(
				'dartsl-resultados',
				__( 'Resultados', 'dartsl' ),
				[ $this, 'dartsl_resultados' ],
				'dartsl_fecha_cpt',
				'normal',
				'core'
			);
		} else {

			add_meta_box(
				'dartsl-resultados',
				__( 'Partidos liga', 'dartsl' ),
				[ $this, 'dartsl_resultados_liga' ],
				'dartsl_fecha_cpt',
				'normal',
				'core'
			);
		}
	}


	/**
	 * Include the metabox view for opts
	 *
	 * @param  object $post geotrcpt post object
	 * @param  array $metabox full metabox items array
	 *
	 * @since 1.0.0
	 */
	public function dartsl_opciones( $post, $metabox ) {

		$data = get_post_meta($post->ID, 'dartls_fecha', true);
		$torneo = get_post_meta($post->ID, 'dartls_torneo',true);
		$data = wp_parse_args( $data,
			[
				'participantes' => [],
				'torneo'    => '',
			]
		);
		include DARTSL_PLUGIN_DIR . '/includes/admin/metaboxes/opciones-fecha.php';
	}

	public function dartsl_resultados( $post, $metabox ) {
		$torneo = get_post_meta($post->ID, 'dartls_torneo',true);
		include DARTSL_PLUGIN_DIR . '/includes/admin/metaboxes/resultados-fecha.php';
	}
	public function dartsl_resultados_liga( $post, $metabox ) {
		$torneo = get_post_meta($post->ID, 'dartls_torneo',true);
		include DARTSL_PLUGIN_DIR . '/includes/admin/metaboxes/resultados-fecha-liga.php';
	}


	/**
	 * Saves the post meta of redirections
	 * @since 1.0.0
	 */
	function save_meta_options( $post_id ) {
		global $wpdb;
		// Verify that the nonce is set and valid.
		if ( ! isset( $_POST['dartsl_options_nonce'] ) || ! wp_verify_nonce( $_POST['dartsl_options_nonce'], 'dartsl_options' ) ) {
			return $post_id;
		}
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		// same for ajax
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return $post_id;
		}
		// same for cron
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return $post_id;
		}
		// same for posts revisions
		if ( is_int( wp_is_post_autosave( $post_id ) ) ) {
			return $post_id;
		}

		// can user edit this post?
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		update_post_meta($post_id, 'torneo_id', $_POST['dartsl']['torneo']);

		if( is_array($_POST['winner']) ) {

			// delete previous matches and insert again
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}dartsl_matches WHERE fecha_id = %d", $post_id ) );
			foreach ( $_POST['winner'] as $match_index => $winner ){
				if( $winner == 'player1' || $winner == 'player2') {
					$winner_id = $winner == 'player1' ?  $_POST['player1_id'][$match_index] :  $_POST['player2_id'][$match_index];
				} else {
					$winner_id = $winner;
				}
				$sql = "INSERT INTO {$wpdb->prefix}dartsl_matches (torneo_id, fecha_id, winner, player1_id, player1_score, player1_avg, player1_co, player2_id, player2_score, player2_avg, player2_co) VALUES (%d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d)";
				$wpdb->query( $wpdb->prepare( $sql, (int) $_POST['dartsl']['torneo'], (int) $post_id, (int) $winner_id, $_POST['player1_id'][$match_index], $_POST['player1_score'][$match_index], $_POST['player1_avg'][$match_index], $_POST['player1_co'][$match_index], $_POST['player2_id'][$match_index], $_POST['player2_score'][$match_index], $_POST['player2_avg'][$match_index], $_POST['player2_co'][$match_index] ) );

			}
			$opts = get_option('dartsl_settings');
			$puestos = $opts['puestos'];
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}dartsl_ranks WHERE fecha_id = %d", $post_id ) );
			foreach ( $_POST['ranking'] as $user_id => $rank ){
				$points = isset($puestos[$rank]) ? $puestos[$rank] : 0;
				$sql = "INSERT INTO {$wpdb->prefix}dartsl_ranks (torneo_id, fecha_id, user_id, rank, points) VALUES (%d, %d, %d, %d, %d)";
				$wpdb->query( $wpdb->prepare( $sql, (int) $_POST['dartsl']['torneo'], (int) $post_id, (int) $user_id, $rank, $points ) );

			}
		}

	}

	/**
	 * Añade info de la fecha al final del post
	 * @param $content
	 *
	 * @return string
	 */
	public function add_fecha_into_content($content) {
		global $wpdb;
		if( 'dartsl_fecha_cpt' != get_post_type() )
			return $content;

		// partidos
		$partidos = $wpdb->get_results( $wpdb->prepare("SELECT *, user1.display_name as player1_name, user2.display_name as player2_name FROM {$wpdb->prefix}dartsl_matches LEFT JOIN $wpdb->users user1 ON user1.ID = player1_id LEFT JOIN $wpdb->users user2 ON user2.ID = player2_id WHERE fecha_id = %d", get_the_id() ) );
		// posiciones fecha
		$posiciones = $wpdb->get_results( $wpdb->prepare(
			"SELECT points, rank, jugador, SUM(Win) As ganados, SUM(Loss) as perdidos, SUM(Draw) as empatados, SUM(score) as lf, SUM(lc) as lc, AVG(darts_avg) as avg, MAX(co) as co, SUM(score) - SUM(lc) as dif
FROM
( SELECT  dm.fecha_id, user1.display_name as jugador, rank, points,
     CASE WHEN player1_score > player2_score THEN 1 ELSE 0 END as Win, 
     CASE WHEN player1_score < player2_score THEN 1 ELSE 0 END as Loss, 
     CASE WHEN player1_score = player2_score THEN 1 ELSE 0 END as Draw, 
     player1_co AS co,
     player1_avg as darts_avg,
	 player1_score as score,
 player2_score as lc
  FROM {$wpdb->prefix}dartsl_matches dm
  LEFT JOIN {$wpdb->prefix}users user1 ON user1.ID = player1_id
  LEFT JOIN {$wpdb->prefix}dartsl_ranks r ON player1_id = r.user_id AND r.fecha_id = %d
  UNION ALL
  SELECT  dm.fecha_id, user2.display_name as jugador, r2.rank, r2.points,
     CASE WHEN player2_score > player1_score THEN 1 ELSE 0 END as Win, 
     CASE WHEN player2_score < player1_score THEN 1 ELSE 0 END as Loss, 
     CASE WHEN player2_score = player1_score THEN 1 ELSE 0 END as Draw, 
     player2_co AS co,
     player2_avg as darts_avg,
 	player2_score as score,
  player1_score as lc
  FROM {$wpdb->prefix}dartsl_matches dm
  LEFT JOIN {$wpdb->prefix}users user2 ON user2.ID = player2_id
  LEFT JOIN {$wpdb->prefix}dartsl_ranks r2 ON player2_id = r2.user_id AND r2.fecha_id = %d
) t
  WHERE fecha_id = %d 
GROUP BY jugador
ORDER By rank, dif DESC", get_the_id(),get_the_id(),get_the_id()));


		ob_start();
		?>
		<!--suppress ALL -->
		<h2>Puntos obtenidos en la fecha <?php the_title();?> del torneo <a href="<?= get_permalink($partidos[0]->torneo_id);?>" target="_blank"><?php echo get_the_title($partidos[0]->torneo_id);?></a></h2>
		<table id="posiciones">
			<thead>
				<tr>
					<th>Nombre</th><th>G</th><th>E</th><th>P</th><th>LF</th><th>LC</th><th>Dif.</th><th>CO</th><th>AVG</th><th>Pts</th>
				</tr>
			</thead>
			<?php

			if( !empty($posiciones) ) {
				foreach ($posiciones as $i => $pos) {
					echo '<tr><td>'.$pos->jugador.'</td><td>'.$pos->ganados.'</td><td>'.$pos->empatados.'</td><td>'.$pos->perdidos.'</td><td>'.$pos->lf.'</td><td>'.$pos->lc.'</td><td>'.$pos->dif.'</td><td class="maximo_co">'.$pos->co.'</td><td class="maximo_avg">'.number_format($pos->avg,2).'</td><td>'.$pos->points.'</td></tr>';
				}
			}
			?>
		</table>
		<h2>Partidos jugados</h2>
		<table id="partidos">
			<?php
			if( !empty($partidos) ) {
				foreach ($partidos as $partido) {
					$partido = (array) $partido;

					echo '<tr>' .
					     '<td class="left_match">' .
					     '<div class="match_name"><span>' . $partido['player1_name'] . '</span></div>' .
					     '<div class="match_avg">Avg: '.$partido['player1_avg'] . '</div>' .
					     '<div class="match_co">CO: ' . $partido['player1_co'] . '</div>' .
					     '</td>' .
					     '<td><div class="score">'. $partido['player1_score'].'</div></td>' .
					     '<td><div class="score">'. $partido['player2_score'].'</div></td>' .
					     '<td class="right_match">' .
					     '<div class="match_name"><span>' . $partido['player2_name'] . '</span> </div>' .
					     '<div class="match_avg">' . $partido['player2_avg'] . ' Avg</div>' .
					     '<div class="match_co">' . $partido['player2_co'] . ' CO</div>' .
					     '</td>' .
					     '</tr>';
				}
			}
			?>
		</table>
		<script>
            (function($){
                let cos =[];
                $('.maximo_co').each(function () {
                    cos.push(parseFloat($(this).text()))
                });
                const max_co = Math.max.apply(null, cos);
                $('.maximo_co').each(function () {
                    $(this).toggleClass('max', +$(this).text() === max_co)
                });
                let avgs =[];
                $('.maximo_avg').each(function () {
                    avgs.push(parseFloat($(this).text()))
                });
                const max_avg = Math.max.apply(null, avgs);
                $('.maximo_avg').each(function () {
                    $(this).toggleClass('max', +$(this).text() === max_avg)
                });


            })(jQuery)
		</script>
		<?php
		$html = ob_get_clean();

		return $content . PHP_EOL . $html;
	}
}

new DartsL_Fecha_Cpt();