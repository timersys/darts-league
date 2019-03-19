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
			'rewrite'             => [ 'slug' => 'darts-league' ],
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

}

new DartsL_Cpt();