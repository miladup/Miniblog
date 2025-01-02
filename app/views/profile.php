<div class="profile-container" id="profile-display" class="centered-container">
<h2 class="profile-title">Mon profil</h2>
    <!-- Affichage de la photo -->
    <?php
    $allowedExtensions = ['jpg', 'png', 'gif', 'webp'];
    $photoPath = null;

    foreach ($allowedExtensions as $ext) {
        $path = __DIR__ . "/../../medias/pdp/{$_SESSION['user_id']}.$ext";
        if (file_exists($path)) {
            $photoPath = "medias/pdp/{$_SESSION['user_id']}.$ext";
            break;
        }
    }
    ?>

    <?php if (isset($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <!-- Affichage de l'image -->
    <?php if ($photoPath): ?>
    <img src="<?= htmlspecialchars($photoPath . '?' . time()); ?>" class="pdp pdp-150" alt="Photo de profil">
    <?php else: ?>
        <img src="medias/pdp/default.jpg" alt="Photo de profil" class="pdp pdp-150">
    <?php endif; ?>

    <!-- Affichage des informations utilisateur -->
    <div class="profile-info">
        <p>Prénom : <span id="prenom-display"><?= htmlspecialchars($user['prenom']); ?></span></p>
        <p>Nom : <span id="nom-display"><?= htmlspecialchars($user['nom']); ?></span></p>
        <p>Login : <span id="login-display"><?= htmlspecialchars($user['login']); ?></span></p>
    </div>
    <div class="profile-buttons">
        <button class="action-button" onclick="editProfile()">Modifier</button>
    </div>
</div>

<!-- Formulaire de modification (caché par défaut) -->
<form id="profile-edit-form" class="profile-edit-form" action="index.php?action=updateProfile" method="POST" enctype="multipart/form-data" style="display: none;" class="form centered-container">
    <label class="photo" for="photo">Photo :</label>
    <input type="file" name="photo" id="photo">

    <p>Prénom : <input type="text" name="prenom" id="prenom-input" value="<?= htmlspecialchars($user['prenom']); ?>"></p>
    <p>Nom : <input type="text" name="nom" id="nom-input" value="<?= htmlspecialchars($user['nom']); ?>"></p>
    <p>Login : <input type="text" name="login" id="login-input" value="<?= htmlspecialchars($user['login']); ?>"></p>

    <button class="action-button" type="submit">Enregistrer</button>
    <button class="action-button" type="button" onclick="cancelEdit()">Annuler</button>
</form>

<script src="script.js"></script>