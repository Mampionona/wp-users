<?php if (is_user_logged_in()): ?>
    <div class="tableau-bord">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1><?= __('Tableau de bord', 'xbot17-users'); ?></h1>
                    <div class="row">
                        <div class="col-md-3">
                            <?php get_template_part('template-parts/sidebar'); ?>
                        </div>
                        <div class="col-md-9">
                            <?php
                                $user_id = get_current_user_id();
                                $connecte_xbot17 = (bool) get_user_meta($user_id, 'connecte_xbot17', true);
                                $trade_en_cours = (bool) get_option('trade_en_cours', 0);;
                                $oui = __('Oui', 'xbot17-users');
                                $non = __('Non', 'xbot17-users');
                            ?>
                            <p>
                                <strong><?= __('Votre compte est-il connecté au Xbot17?', 'xbot17-users'); ?> </strong><br><?= $connecte_xbot17 ? $oui : $non; ?>
                            </p>
                            <p>
                                <strong><?= __('Y a-t-il des trades en cours sur les comptes connectés au Xbot17?', 'xbot17-users'); ?></strong><br><?= $trade_en_cours ? $oui : $non; ?>
                            </p>
                        </div>
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
            <?php wp_nonce_field( 'xbot17security', 'security' ); ?>

            <div class="form-group">
                <label for=""><?= __('Adresse e-mail', 'xbot17-users'); ?></label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <label for=""><?= __('Mot de passe', 'xbot17-users'); ?></label>
                <input type="password" name="mdp" class="form-control" autocomplete="new-password">
            </div>
            <div id="login-message"></div>
            <div class="submit">
                <input type="submit" class="submit-btn" value="<?= __('Connexion', 'xbot17-users'); ?>">
                <i class="fa fa-spinner fa-spin"></i>
            </div>
        </form>

        <p class="mdp-oublie"><a href="<?= apply_filters('translated_post_link', 106); ?>"><?= __('Mot de passe oublié ?', 'xbot17-users'); ?></a></p>
    </div>
<?php endif; ?>
