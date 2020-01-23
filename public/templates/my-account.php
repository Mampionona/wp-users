<?php if (is_user_logged_in()): ?>
    <div class="tableau-bord">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1><?= __('Tableau de bord', 'xbot17'); ?></h1>
                    <div class="row">
                        <div class="col-md-3">
                            <?php get_template_part('template-parts/sidebar'); ?>
                        </div>
                        <!-- <div class="col-md-9">
                            <div class="panel">
                                <div class="panel-body">

                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="login">
        <h1><?= __('Connexion à votre compte', 'xbot17-users'); ?></h1>
        <form method="post" id="login-form" class="login-form user-form">
            <input type="hidden" name="action" value="login_action">
            <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>

            <div class="form-group">
                <label for=""><?= __('Adresse e-mail', 'xbot17-users'); ?></label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <label for=""><?= __('Mot de passe', 'xbot17-users'); ?></label>
                <input type="password" name="mdp" class="form-control">
            </div>
            <div id="login-message"></div>
            <div class="submit">
                <input type="submit" class="submit-btn" value="<?= __('Connexion', 'xbot17-users'); ?>">
                <i class="fa fa-spinner fa-spin"></i>
            </div>
        </form>
    </div>
<?php endif; ?>
