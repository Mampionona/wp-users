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
class Xbot17_Users_Admin
{
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
	public function __construct( $plugin_name, $version )
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action('show_user_profile', array($this, 'editUserProfile'));
		add_action('personal_options_update', array($this, 'editUserProfileUpdate'));
		add_action('edit_user_profile', array($this, 'editUserProfile') );
		add_action('edit_user_profile_update', array($this, 'editUserProfileUpdate'));
		add_filter('pays', array(__CLASS__, 'pays'), 10, 1);
		add_action('add_meta_boxes', array($this, 'initMetabox'));
		add_action('save_post', array($this, 'saveMetabox'));
		add_action('admin_menu', array($this, 'addAdminMenu'));
		add_filter('manage_users_columns', array(__CLASS__, 'usersAjouterColonnes'));
		add_filter('manage_users_custom_column', array($this, 'afficherLaDateInscription'), 1, 3);
		add_filter('localize_datetime', array($this, 'localizeDatetime'), 10, 1);
		add_action('pre_user_query', array($this, 'usersAdminColumnsOrderBy'));
		add_filter('manage_users_sortable_columns', array($this, 'usersSortableColumns'));
	}

	public function usersSortableColumns( $columns )
	{
		$sortable_columns = array(
			// meta column id => sortby value used in query
			'inscrit_le' => 'registered',
			'role' => 'role',
			// 'connecte_xbot17' => 'connecte_xbot17'
		);

		return wp_parse_args($sortable_columns, $columns);
	}

	public function usersAdminColumnsOrderBy($query)
	{
		global $pagenow;

		if (!is_admin() || 'users.php' !== $pagenow || isset($_GET['orderby'])) {
			return;
		}
		$query->query_orderby = 'ORDER BY user_registered DESC';
	}

	public static function usersAjouterColonnes($columns)
	{
		// Enlever la colonne 2FA status
		unset($columns['wfls_2fa_status']);
		$new_columns = array();
		// Ajouter la colonne téléphone à droite du nom
		foreach ($columns as $key => $title) {
			if ($key === 'email') {
				$new_columns['telephone'] = __('Téléphone', 'xbot17-users');
			}
			$new_columns[$key] = $title;
		}
		$new_columns['inscrit_le'] = __('Inscrit le', 'xbot17-users');
		$new_columns['connecte_xbot17'] = __('Compte connecté au Xbot17', 'xbot17-users');
		return $new_columns;
	}

	public function afficherLaDateInscription($val, $column, $user_id)
	{
		$user = get_userdata($user_id);

		switch ($column) {
			case 'inscrit_le':
				return apply_filters('localize_datetime', $user->user_registered);

			case 'telephone':
				$telephone = get_user_meta($user_id, 'user_telephone', true);
				if ($telephone) return $telephone;
				return '-';

			case 'connecte_xbot17':
				if (get_user_meta($user_id, 'connecte_xbot17', true)) return __('Oui', 'xbot17-users');
				return __('Non', 'xbot17-users');
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

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
	public function enqueue_scripts()
	{

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
		$user_pays = get_user_meta( $user->ID, 'user_pays', true );
		?>

		<table class="form-table" id="custom-user-meta">
			<tr>
				<th>
					<label for="telephone"><?= __( 'Téléphone', 'xbot17-users' ); ?></label>
				</th>
				<td>
					<input type="text" name="user_telephone" id="telephone" value="<?php echo esc_attr( get_user_meta( $user->ID, 'user_telephone', true ) ); ?>" class="regular-text" />
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
					<input type="text" name="user_annee_naissance" id="annee-naissance" value="<?php echo esc_attr( get_user_meta( $user->ID, 'user_annee_naissance', true ) ); ?>" class="regular-text" />
				</td>
			</tr>
		</table>
		<table class="form-table" id="cree-le">
			<tr>
				<th>
					<label><?= __( 'Inscrit le', 'xbot17-users' ); ?></label>
				</th>
				<td>
					<p><?= apply_filters('localize_datetime', $user->user_registered); ?></p>
				</td>
			</tr>
		</table>
		<table class="form-table">
			<tr>
				<th>
					<label><?= __('Le compte est-il connecté au Xbot17 ?', 'xbot17-users'); ?></label>
				</th>
				<td>
					<?php $connecte = (bool) get_user_meta($user->ID, 'connecte_xbot17', true); ?>
					<fieldset>
						<label>
							<input name="connecte_xbot17" type="radio" value="1" <?= $connecte ? 'checked' : ''; ?>>
							<span><?= __('Oui', 'xbot17-users'); ?></span>
						</label>
						<br>
						<label>
							<input name="connecte_xbot17" type="radio" value="0" <?= !$connecte ? 'checked' : ''; ?>>
							<span><?= __('Non', 'xbot17-users'); ?></span>
						</label>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php
	}

	public function localizeDatetime($datetime)
	{
		$date = new Datetime($datetime);
		return date_i18n(get_option('date_format'), $date->getTimestamp());
	}

	public function editUserProfileUpdate( $user_id )
	{
		$fields = array('user_telephone', 'user_pays', 'user_annee_naissance');

		foreach ($fields as $field) {
			update_user_meta($user_id, $field, sanitize_text_field(self::getValue($field)));
		}

		$connecte_xbot17 = (bool) self::getValue('connecte_xbot17');
		update_user_meta($user_id, 'connecte_xbot17', $connecte_xbot17);
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
		global $pagenow;

		if ($pagenow !== 'post.php') {
			return;
		}
		$is_active = self::getValue('only_for_logged_in_user');
		update_post_meta($post_id, 'only_for_logged_in_user', $is_active);
	}

	public function addAdminMenu()
	{
		add_submenu_page(
			'users.php',
			__('Devenir investisseur', 'xbot17-users'),
			__('Devenir investisseur', 'xbot17-users'),
			'manage_options',
			'devenir-investisseur',
			array($this, 'adminMenuInit')
		);
	}

	public function adminMenuInit()
	{
		if (isset($_POST['devenir_investisseur'])) {
			update_option('admin_emails', self::getValue('admin_emails'));
			update_option('email_template', self::getValue('email_template'));
			update_option('trade_en_cours', self::getValue('trade_en_cours'));
		}

		$admin_emails = get_option('admin_emails', '');
		$template = stripslashes(get_option('email_template', ''));
		?>
			<div class="wrap">
				<h1><?= __('Paramètres', 'xbot17-users'); ?></h1>
				<form method="post">
					<input type="hidden" name="devenir_investisseur" value="1">
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="admin-emails">E-mail de notification</label></th>
							<td>
								<textarea name="admin_emails" id="admin-emails" class="regular-text" rows="5"><?= $admin_emails; ?></textarea><br>
								<span>Une valeur par ligne</span>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="email-template">Modèle</label></th>
							<td><textarea name="email_template" id="email-template" class="regular-text" rows="12"><?= $template; ?></textarea></td>
						</tr>
						<tr>
							<th scope="row">Y a-t-il des trade en cours sur les comptes connectés au Xbot17?</th>
							<td>
								<?php $trade_en_cours = (bool) get_option('trade_en_cours', 0); ?>
								<fieldset>
									<label>
										<input name="trade_en_cours" type="radio" value="1" <?= $trade_en_cours ? 'checked' : ''; ?>>
										<span><?= __('Oui', 'xbot17-users'); ?></span>
									</label>
									<br>
									<label>
										<input name="trade_en_cours" type="radio" value="0" <?= !$trade_en_cours ? 'checked' : ''; ?>>
										<span><?= __('Non', 'xbot17-users'); ?></span>
									</label>
								</fieldset>
							</td>
						</tr>
					</table>
					<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?= __('Enregistrer les modifications', 'xbot17-users'); ?>"></p>
				</form>
			</div>
		<?php
	}
}
