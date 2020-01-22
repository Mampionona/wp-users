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
class Xbot17_Users_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// register
		add_shortcode('register', array(__CLASS__, 'register'));
		add_action('wp_ajax_nopriv_register_action', array($this, 'handleRegister'));
		add_action('wp_ajax_register_action', array($this, 'handleRegister'));
		// login
		add_shortcode('my_account', array(__CLASS__, 'myAccount'));
		add_action('wp_ajax_nopriv_login_action', array($this, 'handleLogin'));
		add_action('wp_ajax_login_action', array($this, 'handleLogin'));

		// activate user
		add_action('template_redirect', array($this, 'activateUser'));
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

		wp_localize_script( $this->plugin_name, 'xbot17_users', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
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

		if ($mdp !== $confirmation_mdp) {
			wp_send_json_error(array(
				'registered' => false,
				'message' => __('Les mots de passe ne correspondent pas.', 'xbot17-users')
			));
		}

		$default_newuser = array(
			'user_pass' =>  sanitize_text_field($mdp),
			'user_login' => $email,
			'user_email' => $email,
			'first_name' => $prenom,
			'last_name' => $nom,
			'role' => 'pending'
		);

		$user_id = wp_insert_user($default_newuser);
		if ( $user_id && !is_wp_error( $user_id ) ) {
			update_user_meta($user_id, 'user_pays', $pays);
			update_user_meta($user_id, 'user_telephone', $telephone);
			update_user_meta($user_id, 'user_annee_naissance', $annee_naissance);

			$code = sha1( $user_id . time() );
			$mon_compte = 13;
			$link = get_the_permalink(self::getTranslatedPostID($mon_compte));
			$activation_link = add_query_arg( array(
				'activate_account' => true,
				'key' => $code,
				'user' => $user_id
			), $link);
			add_user_meta($user_id, 'has_to_be_activated', $code, true);
			if (wp_mail($email, 'ACTIVATION SUBJECT', 'CONGRATS BLA BLA BLA. HERE IS YOUR ACTIVATION LINK: ' . $activation_link)) {
				wp_send_json_success(array(
					'registered' => true,
					'message' => __('Vos informations ont été enregistrées. Vous recevrez un email pour l\'activation de votre compte.', 'xbot17-users')
				));
			}
			wp_send_json_error(array(
				'registered' => false,
				'message' => __('Un problème s\'est survenu lors de l\'envoi de l\'email d\'activation de votre compte.', 'xbot17-users')
			));
		}

		wp_send_json_error(array(
			'registered' => false,
			'message' => $user_id->get_error_message()
		));
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

		// Nonce is checked, get the POST data and sign user on
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
		check_ajax_referer( 'ajax-login-nonce', 'security' );
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
				'message' => __('Vous n\'avez pas encore activé votre compte.', 'xbot17-users')
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
}
