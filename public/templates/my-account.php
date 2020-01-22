<?php if (is_user_logged_in()): ?>
    <?php $redirect_page_id = 13; ?>
    <?php $logout_url = wp_logout_url( apply_filters('translated_post_link', $redirect_page_id) ); ?>

    <div class="tableau-bord">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1><?= __('Tableau de bord', 'xbot17'); ?></h1>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="list-group">
                                <a class="list-group-item" href="<?= apply_filters('translated_post_link', 58); ?>"><?= __('S\'inscrire avec IronFx', 'xbot17'); ?></a>
                                <a class="list-group-item" href="<?= $logout_url; ?>"><?= __('Se déconnecter', 'xbot17'); ?></a>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="panel">
                                <div class="panel-body">
                                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repellat tempora repudiandae fugiat id atque consequatur eos voluptas nam nobis suscipit praesentium nostrum neque, eligendi pariatur dicta, quos explicabo quia. Odio?</p>
                                    <p>Nulla venenatis diam ligula. Cras magna eros, vehicula vel nibh at, lobortis varius turpis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Aliquam quis euismod leo. Mauris ac turpis consectetur, tempor sapien at, consectetur quam. Vestibulum ac nulla vitae dolor volutpat pulvinar. Vestibulum non enim egestas, posuere est nec, fermentum lorem. Vivamus ut tellus quis lacus posuere lobortis vel a lacus. In feugiat, elit vel lacinia fringilla, leo orci varius orci, id condimentum dolor nisl et ante. Fusce quam leo, pellentesque id vulputate ut, sagittis nec enim. Proin malesuada lorem rhoncus, vulputate lorem non, dignissim mauris. Maecenas commodo quam id suscipit sagittis. Sed ac interdum dolor, eget vulputate eros. Praesent ac risus nec orci lacinia aliquam et eu lorem.</p>
                                </div>
                            </div>
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
