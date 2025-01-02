<form action="index.php?action=login" method="POST" class="form">
    <legend class="form-legend">Connexion</legend>
    
    <div class="form-group">
        <input type="text" name="login" id="login" class="form-input" placeholder=" " required>
        <label for="login" class="form-label">Nom d'utilisateur :</label>
    </div>

    <div class="form-group">
        <input type="password" name="mdp" id="mdp" class="form-input" placeholder=" " required>
        <label for="mdp" class="form-label">Mot de passe :</label>
        <span id="toggle-mdp" class="eye-icon">
            <img src="medias/eye.svg" alt="Afficher mot de passe">
        </span>
    </div>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?><br>

    <div class="form-group">
        <button type="submit" class="form-button">Se connecter</button>
    </div>

    <div>
        <span>Pas encore de compte ? </span>
        <a href="index.php?action=register">Inscription</a>
    </div>
</form>

