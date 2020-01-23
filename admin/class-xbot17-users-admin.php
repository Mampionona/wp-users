<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Xbot17_Users
 * @subpackage Xbot17_Users/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Xbot17_Users
 * @subpackage Xbot17_Users/admin
 * @author     Mampionona <mmampionona@gmail.com>
 */
class Xbot17_Users_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action('show_user_profile', array($this, 'editUserProfile'));
		add_action('personal_options_update', array($this, 'editUserProfileUpdate'));
		add_action('edit_user_profile', array($this, 'editUserProfile') );
		add_action('edit_user_profile_update', array($this, 'editUserProfileUpdate'));
		add_filter('pays', array(__CLASS__, 'pays'), 10, 1);
		add_action('add_meta_boxes', array($this, 'initMetabox'));
		add_action('save_post', array($this, 'saveMetabox'));
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Xbot17_Users_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Xbot17_Users_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/xbot17-users-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Xbot17_Users_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Xbot17_Users_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/xbot17-users-admin.js', array( 'jquery' ), $this->version, false );

	}

	public static function getValue($key)
	{
		if (isset($_POST[$key])) return $_POST[$key];
		return '';
	}

	public function editUserProfile( $user )
	{
		$user_pays = get_the_author_meta( 'user_pays', $user->ID );
		?>
		<table class="form-table" id="custom-user-meta">
			<tr>
				<th>
					<label for="telephone"><?= __( 'Téléphone', 'xbot17-users' ); ?></label>
				</th>
				<td>
					<input type="text" name="user_telephone" id="telephone" value="<?php echo esc_attr( get_the_author_meta( 'user_telephone', $user->ID ) ); ?>" class="regular-text" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="pays"><?= __( 'Pays', 'xbot17-users' ); ?></label>
				</th>
				<td>
					<select name="user_pays" id="pays">
						<?php foreach (apply_filters('pays', array()) as $pays): ?>
							<?php $attr_selected = ($user_pays === $pays) ? 'selected' : ''; ?>
							<option value="<?= $pays; ?>" <?= $attr_selected; ?>><?= $pays; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>
					<label for="annee-naissance"><?= __( 'Année de naissance', 'xbot17-users' ); ?></label>
				</th>
				<td>
					<input type="text" name="user_annee_naissance" id="annee-naissance" value="<?php echo esc_attr( get_the_author_meta( 'user_annee_naissance', $user->ID ) ); ?>" class="regular-text" />
				</td>
			</tr>
		</table>
		<?php
	}

	public function editUserProfileUpdate( $user_id )
	{
		$fields = array('user_telephone', 'user_pays', 'user_annee_naissance');

		foreach ($fields as $field) {
			update_user_meta($user_id, $field, sanitize_text_field(self::getValue($field)));
		}
	}

	public static function pays(array $_pays = array())
	{
		$pays = require plugin_dir_path(__DIR__) . 'includes/pays.php';
		return array_merge($pays, $_pays);
	}

	public function addMetaboxCallback($post)
	{
		$active = get_post_meta($post->ID, 'only_for_logged_in_user', true);
		?>
			<label>
				<input name="only_for_logged_in_user" type="checkbox" value="1" <?= $active ? 'checked' : ''; ?>>
				&nbsp;<?= __('Uniquement pour les utilisateurs connéctés.', 'xbot17-users'); ?>
			</label>
		<?php
	}

	public function initMetabox()
	{
		add_meta_box(
			'only_for_logged_in_user',
			__('Visibilité', 'xbot17-users'),
			array($this, 'addMetaboxCallback'),
			'page',
			'side',
			'high'
		);
	}

	public function saveMetabox($post_id)
	{
		$is_active = self::getValue('only_for_logged_in_user');
		update_post_meta($post_id, 'only_for_logged_in_user', $is_active);
	}
}
