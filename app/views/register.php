<form action="index.php?action=register" method="POST" enctype="multipart/form-data" class="form">
    <legend class="form-legend">Inscription</legend>

    <div class="form-group">
        <input type="text" name="nom" id="nom" class="form-input" placeholder=" " value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '' ?>" required>
        <label for="nom" class="form-label">Nom :</label>
    </div>

    <div class="form-group">
        <input type="text" name="prenom" id="prenom" class="form-input" placeholder=" " value="<?= isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : '' ?>" required>
        <label for="prenom" class="form-label">Prénom :</label>
    </div>

    <div class="form-group">
        <input type="text" name="login" id="login" class="form-input" placeholder=" " value="<?= isset($_POST['login']) ? htmlspecialchars($_POST['login']) : '' ?>" required>
        <label for="login" class="form-label">Nom d'utilisateur :</label>
    </div>

    <div class="form-group">
        <input type="password" name="mdp" id="mdp" class="form-input" placeholder=" " required>
        <label for="mdp" class="form-label">Mot de passe :</label>
        <span id="toggle-mdp" class="eye-icon">
            <img src="medias/eye.svg" alt="Afficher mot de passe">
        </span>
    </div>

    <div class="form-group">
        <input type="password" name="confirm_mdp" id="confirm_mdp" class="form-input" placeholder=" " required>
        <label for="confirm_mdp" class="form-label">Confirmer le mot de passe :</label>
        <span id="toggle-confirm_mdp" class="eye-icon">
            <img src="medias/eye.svg" alt="Afficher mot de passe">
        </span>
    </div>

    <div class="form-group">
        <input type="file" name="photo" id="photo" class="form-input form-photo" accept="image/*">
        <label for="photo" class="form-label">Photo de profil :</label>
    </div><br>

    <div class="form-group">
        <button type="submit" class="form-button">S'inscrire</button>
    </div>

    <div>
        <span>Déjà propriétaire d'un compte ? </span>
        <a href="index.php?action=login">Connexion</a>
    </div>
</form>

