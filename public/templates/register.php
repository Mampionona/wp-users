<div class="register">
    <form method="post" id="register-form" class="register-form user-form">
        <input type="hidden" name="action" value="register_action">
        <?php wp_nonce_field( 'xbot17security', 'security' ); ?>
        <div class="form-group">
            <label for=""><?= __('Nom', 'xbot17-users'); ?></label>
            <input type="text" name="nom" class="form-control">
        </div>
        <div class="form-group">
            <label for=""><?= __('Prénom', 'xbot17-users'); ?></label>
            <input type="text" name="prenom" class="form-control">
        </div>
        <div class="form-group">
            <label for=""><?= __('Adresse e-mail', 'xbot17-users'); ?></label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="form-group">
            <label for=""><?= __('Téléphone', 'xbot17-users'); ?></label>
            <input type="telephone" name="telephone" class="form-control">
        </div>
        <div class="form-group">
            <label for=""><?= __('Pays', 'xbot17-users'); ?></label>
            <select name="pays" class="form-control">
                <?php foreach (apply_filters('pays', array()) as $pays): ?>
                    <option value="<?= $pays; ?>"><?= $pays; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for=""><?= __('Année de naissance', 'xbot17-users'); ?></label>
            <input type="number" name="annee_naissance" class="form-control" min="1900">
        </div>
        <div class="form-group">
            <label for=""><?= __('Mot de passe', 'xbot17-users'); ?></label>
            <input type="password" name="mdp" id="mdp" class="form-control" placeholder="<?= __('8 caractères min.', 'xbot17-users'); ?>">
        </div>
        <div class="form-group">
            <label for=""><?= __('Confirmer le mot de passe', 'xbot17-users'); ?></label>
            <input type="password" name="confirmation_mdp" class="form-control">
        </div>
        <p class="mention">(*) <?= __('Champ obligatoire', 'xbot17-users'); ?></>
        <div id="login-message" style="display: none"></div>
        <div class="submit">
            <input type="submit" class="submit-btn" value="<?= __('Créer le compte', 'xbot17-users'); ?>">
            <i class="fa fa-spinner fa-spin"></i>
        </div>
    </form>
</div>
