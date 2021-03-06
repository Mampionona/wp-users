<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Xbot17_Users
 * @subpackage Xbot17_Users/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Xbot17_Users
 * @subpackage Xbot17_Users/public
 * @author     Mampionona <mmampionona@gmail.com>
 */

class Xbot17_Users_Public
{
	const ANNEE_MIN = 1900;
	const ANNEE_MAX = 2100;

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

	private $user;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_shortcode('register', array(__CLASS__, 'register'));
		add_shortcode('my_account', array(__CLASS__, 'myAccount'));
		add_shortcode('profile', array($this, 'showProfile'));

		add_action('template_redirect', array($this, 'activateUser'));
		add_action('template_redirect', array($this, 'verifyAuth'));
		add_action('nouvel_investisseur', array($this, 'notifyAdmin'));
		add_action('nouvel_investisseur', array($this, 'notifyClient'));
		add_action('wp', array($this, 'hideAdminbar'));

		add_action('wp_ajax_nopriv_register_action', array($this, 'handleRegister'));
		add_action('wp_ajax_nopriv_login_action', array($this, 'handleLogin'));
		add_action('wp_ajax_edit_profil_action', array($this, 'updateProfile'));

		add_filter('translated_post_link', array(__CLASS__, 'translatedPostLink'), 10, 1);
	}

	public function updateProfile()
	{
		$user_id = get_current_user_id();
		$prenom = self::getValue('prenom');
		$nom = self::getValue('nom');
		$telephone = self::getValue('telephone');
		$pays = self::getValue('pays');
		$annee = self::getValue('annee_naissance');
		$mdp = self::getValue('mdp');
		$confirmation_mdp = self::getValue('confirmation_mdp');
		$mdp_actuel = self::getValue('mdp_actuel');

		// Valider les données
		if (
			empty($nom)
			|| empty($prenom)
			|| empty($telephone)
			|| empty($pays)
			|| empty($annee)
		) wp_send_json_error(array(
			'message' => __('Veuillez compléter les champs obligatoires.', 'xbot17-users')
		));
		elseif (!empty($mdp) && mb_strlen($mdp) < 8) wp_send_json_error(array(
			'message' => __('Mot de passe trop court.', 'xbot17-users')
		));
		elseif (!empty($mdp) && $mdp !== $confirmation_mdp) wp_send_json_error(array(
			'message' => __('Les mots de passe ne correspondent pas.', 'xbot17-users')
		));
		elseif ($annee < self::ANNEE_MIN || $annee > self::ANNEE_MAX) wp_send_json_error(array(
			'message' => __('Année de naissance invalide.', 'xbot17-users')
		));

		$userdata = array(
			'ID' => $user_id,
			'first_name' => sanitize_text_field($prenom),
			'last_name' => sanitize_text_field($nom)
		);

		$user_id = wp_update_user($userdata);

		update_user_meta($user_id, 'user_telephone', sanitize_text_field($telephone));
		update_user_meta($user_id, 'user_pays', sanitize_text_field($pays));
		update_user_meta($user_id, 'user_annee_naissance', sanitize_text_field($annee));

		if (is_wp_error($user_id)) {
			wp_send_json_error(array('message' => $user_id->get_error_message()));
		}

		if (!empty($mdp)) {
			// verifier le mot de passe actuel
			if (!wp_check_password($mdp_actuel, $this->user($user_id)->data->user_pass, $user_id)) {
				wp_send_json_error(array(
					'message' => __('Votre mot de passe est incorrect.', 'xbot17-users')
				));
			}
			// mettre à jour le mot de passe
			wp_set_password($mdp, $user_id);
			// Sets the authentication cookies based on user ID.
			wp_set_auth_cookie($user_id, 1, is_ssl());
		}

		wp_send_json_success(array('message' => 'updated'));
	}

	public function showProfile()
	{
		return self::loadTemplate('profile.php');
	}

	public function hideAdminbar()
	{
		// esorina ny admin-bar rehefa investisseur
		if (current_user_can('subscriber')) {
			add_filter('show_admin_bar', '__return_false');
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/xbot17-users-public.css');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/xbot17-users-public.js', array( 'jquery' ), $this->version, false );

		$mon_compte = 13;

		wp_localize_script( $this->plugin_name, 'xbot17_users', array(
			'ajaxurl' => add_query_arg('lang', ICL_LANGUAGE_CODE, admin_url('admin-ajax.php')),
			'redirect_uri' => apply_filters('translated_post_link', $mon_compte)
		));
	}

	public static function register($atts)
	{
		return self::loadTemplate('register.php');
	}

	public function handleRegister()
	{
		self::checkNonce();

		$nom = sanitize_text_field(self::getValue('nom'));
		$prenom = sanitize_text_field(self::getValue('prenom'));
		$email = sanitize_text_field(self::getValue('email'));
		$telephone = sanitize_text_field(self::getValue('telephone'));
		$pays = sanitize_text_field(self::getValue('pays'));
		$annee_naissance = (int) self::getValue('annee_naissance');
		$mdp = self::getValue('mdp');
		$confirmation_mdp = self::getValue('confirmation_mdp');

		if (
			empty($nom)
			|| empty($prenom)
			|| empty($email)
			|| empty($telephone)
			|| empty($pays)
			|| empty($annee_naissance)
			|| empty($mdp)
			|| empty($confirmation_mdp)
		) {
			wp_send_json_error(array(
				'message' => __('Tous les champs sont obligatoires.', 'xbot17-users')
			));
		} elseif ($annee_naissance < self::ANNEE_MIN || $annee_naissance > self::ANNEE_MAX) {
			wp_send_json_error(array(
				'message' => __('Année de naissance invalide.', 'xbot17-users')
			));
		} elseif (mb_strlen($mdp) < 8) {
			wp_send_json_error(array(
				'message' => __('Mot de passe trop court.', 'xbot17-users')
			));
		} elseif ($mdp !== $confirmation_mdp) {
			wp_send_json_error(array(
				'message' => __('Les mots de passe ne correspondent pas.', 'xbot17-users')
			));
		}

		$new_user = array(
			'user_pass' =>  sanitize_text_field($mdp),
			'user_login' => $email,
			'user_email' => $email,
			'first_name' => $prenom,
			'last_name' => $nom,
			// 'role' => 'pending',
			'role' => 'subscriber'
		);

		$user_id = wp_insert_user($new_user);
		if ($user_id && !is_wp_error($user_id)) {
			update_user_meta($user_id, 'user_pays', $pays);
			update_user_meta($user_id, 'user_telephone', $telephone);
			update_user_meta($user_id, 'user_annee_naissance', $annee_naissance);

			// $code = sha1( $user_id . time() );
			// $mon_compte = 13;
			// $link = apply_filters('translated_post_link', $mon_compte);
			// $activation_link = add_query_arg( array(
			// 	'activate_account' => true,
			// 	'key' => $code,
			// 	'user' => $user_id
			// ), $link);
			// $subject = __('Activer votre compte Xbot17', 'xbot17-users');
			// $message = sprintf(
			// 	__('Bonjour %s,<br><br>Pour activer votre compte Xbot17, merci de cliquer sur le lien ci-dessous:<br><br>%s<br><br>Bien cordialement', 'xbot17-users'),
			// 	$prenom,
			// 	'<a href="' . $activation_link . '">' . $activation_link . '</a>'
			// );

			// envoyer le lien d'activation
			// self::sendNotification($email, $subject, $message);
			// add_user_meta($user_id, 'has_to_be_activated', $code, true);
			do_action('nouvel_investisseur', $user_id);
			// wp_send_json_success(array(
			// 	'message' => __('Vos informations ont été enregistrées. Vous recevrez un email pour l\'activation de votre compte.', 'xbot17-users'),
			// 	'user_id' => $user_id
			// ));
			wp_send_json_success(array(
				'message' => __('Vos informations ont été enregistrées. Vous pouvez maintenant accéder à votre compte.', 'xbot17-users'),
				// 'user_id' => $user_id
			));
		}

		wp_send_json_error(array('message' => $user_id->get_error_message()));
	}

	public static function sendNotification($to, $subject, $message, $headers = array())
	{
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		return wp_mail($to, $subject, $message, $headers);
	}

	public static function myAccount()
	{
		return self::loadTemplate('my-account.php');
	}

	// Load template file
	public static function loadTemplate($template)
	{
		$template = plugin_dir_path(__FILE__) . 'templates/' . $template;
		if (is_file($template)) {
			ob_start();
			load_template($template);
			return ob_get_clean();
		}

		return '';
	}

	public function handleLogin()
	{
		self::checkNonce();

		$info = array();
		$info['user_login'] = sanitize_text_field(self::getValue('email'));
		$info['user_password'] = sanitize_text_field(self::getValue('mdp'));
		$info['remember'] = true;

		$this->checkIfUserIsActivated($info['user_login'], $info['user_password']);

		$user_signon = wp_signon( $info, false );

		if (is_wp_error($user_signon)) {
			wp_send_json_error(array(
				'loggedin' => false,
				'message' => __('L\'adresse email et le mot de passe que vous avez entrés ne correspondent pas à ceux présents dans nos fichiers. Veuillez vérifier et réessayer.', 'xbot17-users')
			));
		}

		wp_send_json_success(array('loggedin' => true));
	}

	public static function getValue($key)
	{
		if (isset($_POST[$key])) return $_POST[$key];
		return '';
	}

	/**
	 * Verify nonce
	 */
	public static function checkNonce()
	{
		check_ajax_referer( 'xbot17security', 'security' );
	}

	/**
	 * Check user activation on login
	 */
	public function checkIfUserIsActivated($username, $password)
	{
		$username = sanitize_user($username);
		$password = trim($password);

		$user = apply_filters('authenticate', null, $username, $password);

		if ($user && get_user_meta( $user->ID, 'has_to_be_activated', true )) {
			wp_send_json_error(array(
				'message' => __('Votre compte n\'est pas encore activé.', 'xbot17-users')
			));
		}
	}

	/**
	 * Activate user
	 */
	public function activateUser()
	{
		if ((bool) filter_input( INPUT_GET, 'activate_account' )) {
			$user_id = filter_input(INPUT_GET, 'user', FILTER_VALIDATE_INT, array(
				'options' => array('min_range' => 1)
			));
			if ( $user_id ) {
				// get user meta activation hash field
				$code = get_user_meta( $user_id, 'has_to_be_activated', true );
				if ( $code === filter_input( INPUT_GET, 'key' ) ) {
					delete_user_meta( $user_id, 'has_to_be_activated' );
					$userdata = array(
						'ID' => $user_id,
						'role' => 'subscriber'
					);
					$user_id = wp_update_user($userdata);
				}
			}
		}
	}

	public static function getTranslatedPostID($post_id)
	{
		if (function_exists('icl_object_id')) {
			return icl_object_id($post_id, 'page', false, ICL_LANGUAGE_CODE);
		}
		return $post_id;
	}

	public static function translatedPostLink($post_id)
	{
		return get_the_permalink(self::getTranslatedPostID($post_id));
	}

	public function notifyAdmin($new_user_id)
	{
		$admin_emails = get_option('admin_emails', '');

		if (empty($admin_emails)) {
			return;
		}

		$user = $this->user($new_user_id);

		if (!$user) {
			return;
		}

		$site_name = get_bloginfo('name');
		$search = array(
			'##SITE##',
			'##NOM##',
			'##PRENOM##',
			'##EMAIL##',
			'##TELEPHONE##',
			'##PAYS##',
			'##ANNEENAISSANCE##'
		);
		$replace = array(
			$site_name,
			$user->last_name,
			$user->first_name,
			$user->user_email,
			get_user_meta($new_user_id, 'user_telephone', true),
			get_user_meta($new_user_id, 'user_pays', true),
			get_user_meta($new_user_id, 'user_annee_naissance', true)
		);

		$subject = sprintf(__('[%s] Inscription d\'un nouvel utilisateur', 'xbot17-users'), $site_name);
		$message = stripslashes(get_option('email_template', ''));
		$message = str_replace($search, $replace, $message);
		$message = wpautop($message);
		$admin_emails = preg_split('/\n/', $admin_emails);

		foreach ($admin_emails as $email) {
			if (!empty($email)) {
				self::sendNotification($email, $subject, $message);
			}
		}
	}

	public function notifyClient($user_id)
	{
		$user = $this->user($user_id);
		$sujet = sprintf(__('[%s] Inscription', 'xbot17-users'), get_bloginfo('name'));
		$message = sprintf(__('Bonjour %s, <br><br>Vos informations ont été enregistrées. <br><br>Vous pouvez désormais accéder à votre compte sur le site Xbot17.', 'xbot17-users'), $user->first_name);

		self::sendNotification($user->user_email, $sujet, $message);
	}

	private function user($user_id)
	{
		return get_userdata($user_id);
	}

	public function verifyAuth()
	{
		global $post;

		$is_protected = (bool) get_post_meta($post->ID, 'only_for_logged_in_user', true);
		if ($is_protected && !is_user_logged_in()) {
			$redirect_uri = apply_filters('translated_post_link', 13);
			wp_redirect($redirect_uri);
			exit;
		}
	}
}
