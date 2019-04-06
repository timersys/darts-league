<?php
/**
 * Class DartsL_Cpt will handle all stuff related to custom post type
 * @since 1.0.0
 */
class DartsL_Cpt {

	/**
	 * DartsL_Cpt constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_cpt' ] );
		add_filter( 'the_content', [ $this, 'add_torneo_info'] );
	}

	/**
	 * Register custom post types
	 * @since     1.0.0
	 * @return void
	 */
	public function register_cpt() {

		$labels = [
			'name'               => 'Darts League v' . DARTSL_VERSION,
			'singular_name'      => _x( 'Torneo', 'post type singular name', 'dartsl' ),
			'menu_name'          => _x( 'Torneo', 'admin menu', 'dartsl' ),
			'name_admin_bar'     => _x( 'Torneo', 'add new on admin bar', 'dartsl' ),
			'add_new'            => _x( 'Add New', 'Torneo', 'dartsl' ),
			'add_new_item'       => __( 'Add New Torneo', 'dartsl' ),
			'new_item'           => __( 'New Torneo', 'dartsl' ),
			'edit_item'          => __( 'Edit Torneo', 'dartsl' ),
			'view_item'          => __( 'View Torneo', 'dartsl' ),
			'all_items'          => __( 'Torneo', 'dartsl' ),
			'search_items'       => __( 'Search Torneo', 'dartsl' ),
			'parent_item_colon'  => __( 'Parent Torneo:', 'dartsl' ),
			'not_found'          => __( 'No Torneo found.', 'dartsl' ),
			'not_found_in_trash' => __( 'No Torneo found in Trash.', 'dartsl' ),
		];

		$args = [
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'query_var'           => true,
			'exclude_from_search' => false,
			'rewrite'             => [ 'slug' => 'torneo' ],
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

		register_post_type( 'dartsl_cpt', $args );

	}

	/**
	 * Añade info del torneo al post
	 * @param $content
	 *
	 * @return string
	 */
	public function add_torneo_info($content){
		global $wpdb;
		if( 'dartsl_cpt' != get_post_type() )
			return $content;


		// partidos
		$fechas = $wpdb->get_col( $wpdb->prepare("SELECT ID FROM {$wpdb->prefix}posts p LEFT JOIN $wpdb->postmeta pm ON p.ID = pm.post_id  WHERE meta_key = 'torneo_id' AND meta_value = %d", get_the_id() ) );
		$partidos = $wpdb->get_results( "SELECT fecha_id, player1_id, player1_score,player1_co, player1_avg, player2_id, player2_score, player2_co, player2_avg, user1.display_name as player1_name, user2.display_name as player2_name, p.post_title, p.post_name as slug FROM {$wpdb->prefix}dartsl_matches LEFT JOIN $wpdb->users user1 ON user1.ID = player1_id LEFT JOIN $wpdb->users user2 ON user2.ID = player2_id LEFT JOIN $wpdb->posts p ON p.ID = fecha_id WHERE fecha_id IN ('".implode("','",$fechas)."')"   );

		// posiciones fecha
		$posiciones = $wpdb->get_results( $wpdb->prepare(
			"SELECT (SELECT SUM(points) as points FROM wp_dartsl_ranks dr WHERE torneo_id = %d AND dr.user_id = userid ) as points,
jugador, userid, SUM(Win) As ganados, SUM(Loss) as perdidos, SUM(Draw) as empatados, SUM(score) as lf, SUM(lc) as lc, AVG(darts_avg) as avg, MAX(co) as co,
 (SUM(score) - SUM(lc)) as dif
FROM
( SELECT  dm.torneo_id, user1.display_name as jugador, player1_id as userid,
     CASE WHEN player1_score > player2_score THEN 1 ELSE 0 END as Win, 
     CASE WHEN player1_score < player2_score THEN 1 ELSE 0 END as Loss, 
     CASE WHEN player1_score = player2_score THEN 1 ELSE 0 END as Draw, 
     player1_co AS co,
     player1_avg as darts_avg,
	 player1_score as score,
 player2_score as lc
  FROM {$wpdb->prefix}dartsl_matches dm
  LEFT JOIN {$wpdb->prefix}users user1 ON user1.ID = player1_id
  UNION ALL
  SELECT  dm2.torneo_id, user2.display_name as jugador,  player2_id as userid,
     CASE WHEN player2_score > player1_score THEN 1 ELSE 0 END as Win, 
     CASE WHEN player2_score < player1_score THEN 1 ELSE 0 END as Loss, 
     CASE WHEN player2_score = player1_score THEN 1 ELSE 0 END as Draw, 
     player2_co AS co,
     player2_avg as darts_avg,
 	player2_score as score,
  player1_score as lc
  FROM {$wpdb->prefix}dartsl_matches dm2
  LEFT JOIN {$wpdb->prefix}users user2 ON user2.ID = player2_id
) t
  WHERE torneo_id = %d
GROUP BY jugador
ORDER By points DESC, dif DESC", get_the_id(),get_the_id(),get_the_id(),get_the_id(),get_the_id() ));


		ob_start();
		?>
		<h2>Tabla de posiciones del torneo <?php the_title();?></a></h2>
		<table id="posiciones">
			<thead>
			<tr>
				<th>Nombre</th><th>G</th><th>E</th><th>P</th><th>LF</th><th>LC</th><th>Dif.</th><th>CO</th><th>AVG</th><th>Pts</th>
			</tr>
			</thead>
			<?php
			$opts = get_option('dartsl_settings');
			$puestos = $opts['puestos'];
			if( !empty($posiciones) ) {
				foreach ($posiciones as $i => $pos) {
					echo '<tr><td>'.$pos->jugador.'</td><td>'.$pos->ganados.'</td><td>'.$pos->empatados.'</td><td>'.$pos->perdidos.'</td><td>'.$pos->lf.'</td><td>'.$pos->lc.'</td><td>'.$pos->dif.'</td><td class="maximo_co">'.$pos->co.'</td><td class="maximo_avg">'.number_format($pos->avg,2).'</td><td>'.$pos->points.'</td></tr>';
				}
			}
			?>
		</table>
		<h2>Fechas jugadas</h2>
		<table id="partidos">
			<?php
			$fecha_id = -1;
			if( !empty($partidos) ) {
				foreach ($partidos as $partido) {
					$partido = (array) $partido;
					if($partido['fecha_id'] != $fecha_id) {
						echo '<tr><td><h3><a href="'.site_url('fecha/'.$partido['slug']).'" target="_blank" >'.$partido['post_title'].'</a></h3></td></tr>';
					}
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

					$fecha_id = $partido['fecha_id'];
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

new DartsL_Cpt();