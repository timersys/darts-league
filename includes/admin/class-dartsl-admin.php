<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://timersys.com
 * @since      1.0.0
 *
 * @package    Dartsl
 * @subpackage Dartsl/admin
 */


/**
 * @subpackage Dartsl/admin
 * @author     Damian Logghe <damian@timersys.com>
 */
class Dartsl_Admin {

	private $view;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {


		add_filter( 'plugin_action_links_' . DARTSL_PLUGIN_HOOK, [ $this, 'add_action_links' ] );

		add_action( 'admin_menu', [ $this, 'add_settings_menu' ], 8 );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		// Check what page we are on.
		$page = isset( $_GET['page'] ) ? $_GET['page'] : '';

		// Only load if we are actually on the settings page.
		if ( 'dartsl-opts' === $page ) {
			// trigger settings save
			add_action( 'admin_init', [ $this, 'save_settings' ] );

			// Determine the current active settings tab.
			$this->view = isset( $_GET['view'] ) ? esc_html( $_GET['view'] ) : 'general';

			// add settings panels
			add_action('dartsl/settings_general_panel', [ $this, 'general_panel'] );

		}

	}

	/**
	 * Add menu for Settings page of the plugin
	 * @since  1.0.0
	 * @return  void
	 */
	public function add_settings_menu() {

		add_menu_page( 'Darts League', 'Darts League', 'manage_options' , 'dartsl-opts',[
			$this,
			'settings_page'
		], 'dashicons-admin-site' );

	}

	/**
	 * Return registered settings tabs.
	 *
	 * @return array
	 */
	public function get_tabs() {

		$tabs = [
			'general' => [
				'name'   => esc_html__( 'General', 'dartsl' ),
			],
		];

		return $tabs;
	}

	/**
	 * Output tab navigation area.
	 */
	public function tabs() {

		$tabs = $this->get_tabs();

		echo '<ul class="geot-admin-tabs">';
		foreach ( $tabs as $id => $tab ) {

			$active = $id === $this->view ? 'active' : '';
			$name   = $tab['name'];
			$link   = add_query_arg( 'view', $id, admin_url( 'admin.php?page=dartsl-opts' ) );
			echo '<li><a href="' . esc_url_raw( $link ) . '" class="' . esc_attr( $active ) . '">' . esc_html( $name ) . '</a></li>';
		}
		echo '</ul>';
	}

	/**
	 * Settings page for plugin
	 * @since 1.0.0
	 */
	public function settings_page() {
		?>
		<h2>Dardos Argentina</h2>
		<div id="geot-settings" class="wrap geot-admin-wrap">
		<?php $this->tabs(); ?>
		<?php do_action("dartsl/settings_{$this->view}_panel") ?>
		</div><?php
	}

	/**
	 * Build the general_panel for the plugin settings page.
	 *
	 * @since 1.0.0
	 */
	public function general_panel() {
		$opts = get_option('dartsl_settings');
		include dirname( __FILE__ ) . '/partials/settings-page.php';
	}

	/**
	 * Save the settings page
	 * @since 1.0.0
	 * @return void
	 */
	public function save_settings() {

		if ( isset( $_POST['dartsl_nonce'] ) && wp_verify_nonce( $_POST['dartsl_nonce'], 'dartsl_save_settings' ) ) {
			$settings =  $_POST['dartsl'] ;
			update_option( 'dartsl_settings', $settings );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if( isset($_GET['page']) && $_GET['page'] == 'dartsl-opts' )
			wp_enqueue_style( 'dartsl-admin', DARTSL_PLUGIN_URL . 'includes/assets/css/admin.css', DARTSL_VERSION );
	}


	/**
	 * Register direct access link
	 *
	 * @since    1.0.0
	 *
	 * @param $links
	 *
	 * @return    Array
	 */
	public function add_action_links( $links ) {

		return array_merge(
			[
				'settings' => '<a href="' . admin_url( 'admin.php?page=dartsl-opts' ) . '">' . __( 'Settings', 'dartsl' ) . '</a>',
			],
			$links
		);

	}

}

new Dartsl_Admin();