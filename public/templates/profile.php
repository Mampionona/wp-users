<?php
    $user = wp_get_current_user();
    // var_dump($user);
    // var_dump(wp_check_password( 'wp_set_auth_cookie', $user->data->user_pass, $user->ID ));
?>

<div class="register edit-profile">
    <form method="post" id="register-form" class="register-form user-form">
        <input type="hidden" name="action" value="edit_profil_action">
        <?php wp_nonce_field( 'xbot17security', 'security' ); ?>
        <div class="form-group">
            <label for=""><?= __('Nom', 'xbot17-users'); ?></label>
            <input type="text" name="nom" class="form-control" value="<?= $user->user_lastname; ?>">
        </div>
        <div class="form-group">
            <label for=""><?= __('Prénom', 'xbot17-users'); ?></label>
            <input type="text" name="prenom" class="form-control" value="<?= $user->user_firstname; ?>">
        </div>
        <div class="form-group">
            <label for=""><?= __('Téléphone', 'xbot17-users'); ?></label>
            <input type="telephone" name="telephone" class="form-control" value="<?= get_user_meta($user->ID, 'user_telephone', true); ?>">
        </div>
        <div class="form-group">
            <label for=""><?= __('Pays', 'xbot17-users'); ?></label>
            <select name="pays" class="form-control">
                <?php foreach (apply_filters('pays', array()) as $pays): ?>
                    <?php
                        $attr_selected = '';
                        if ($pays === get_user_meta($user->ID, 'user_pays', true)) {
                            $attr_selected = 'selected';
                        }
                    ?>
                    <option <?= $attr_selected; ?> value="<?= $pays; ?>"><?= $pays; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for=""><?= __('Année de naissance', 'xbot17-users'); ?></label>
            <input type="number" name="annee_naissance" class="form-control" min="1900" max="2100" value="<?= get_user_meta($user->ID, 'user_annee_naissance', true); ?>">
        </div>
        <p><strong><?= __('Modifier mon mot de passe', 'xbot17-users'); ?></strong></p>
        <div class="form-group">
            <label class="optional-label"><?= __('Mot de passe actuel', 'xbot17-users'); ?></label>
            <input type="password" name="mdp_actuel" class="form-control" autocomplete="new-password" value="kkkk">
        </div>
        <div class="form-group">
            <label class="optional-label" for=""><?= __('Nouveau mot de passe', 'xbot17-users'); ?></label>
            <input type="password" name="mdp" id="mdp" class="form-control" placeholder="<?= __('8 caractères min.', 'xbot17-users'); ?>" autocomplete="new-password">
        </div>
        <div class="form-group">
            <label class="optional-label" for=""><?= __('Confirmer le nouveau mot de passe', 'xbot17-users'); ?></label>
            <input type="password" name="confirmation_mdp" class="form-control" autocomplete="new-password">
        </div>
        <p class="mention">(*) <?= __('Champ obligatoire', 'xbot17-users'); ?></>
        <div id="login-message" style="display: none"></div>
        <div class="submit">
            <input type="submit" class="submit-btn" value="<?= __('Enregistrer les modifications', 'xbot17-users'); ?>">
            <i class="fa fa-spinner fa-spin"></i>
        </div>
    </form>
</div>

<div id="inscription-ok" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="inscription-ok-label" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="inscription-ok-label"><?= __('Félicitations !', 'xbot17-users'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __('Vos informations ont été enregistrées.', 'xbot17-users'); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
    </div>
</div>
