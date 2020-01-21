<?php if (is_user_logged_in()): ?>
    <p>tableau de bord</p>
<?php else: ?>
    <div class="login">
        <h1><?= __('Connexion à votre compte', 'xbot17-users'); ?></h1>
        <form method="post" id="login-form" class="login-form user-form">
            <input type="hidden" name="action" value="login_action">
            <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>

            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="<?= __('Adresse e-mail', 'xbot17-users'); ?>">
            </div>
            <div class="form-group">
                <input type="password" name="mdp" class="form-control" placeholder="<?= __('Mot de passe', 'xbot17-users'); ?>">
            </div>
            <div id="login-message"></div>
            <div class="submit">
                <input type="submit" class="submit-btn btn-block" value="<?= __('Connexion', 'xbot17-users'); ?>">
            </div>
        </form>
    </div>
<?php endif; ?>
