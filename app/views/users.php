<h2>Liste des utilisateurs</h2>
<ul>
    <?php foreach ($users as $user): ?>
        <div id="user-<?= $user['id_utilisateur']; ?>">
            <!-- Affichage normal -->
            <div class="user-display">
                <?php
                $allowedExtensions = ['jpg', 'png', 'gif', 'webp'];
                $photoPath = null;

                foreach ($allowedExtensions as $ext) {
                    $path = __DIR__ . "/../../medias/pdp/{$user['id_utilisateur']}.$ext";
                    if (file_exists($path)) {
                        $photoPath = "medias/pdp/{$user['id_utilisateur']}.$ext";
                        break;
                    }
                }
                ?>

                <!-- Affichage de l'image -->
                <?php if ($photoPath): ?>
                    <img src="<?= htmlspecialchars($photoPath); ?>" alt="Photo de profil" class="pdp pdp-70">
                <?php else: ?>
                    <img src="medias/pdp/default.jpg" alt="Photo de profil" class="pdp pdp-70">
                <?php endif; ?>

                <!-- Affichage des informations -->
                <p>Prénom : <span id="prenom-display-<?= $user['id_utilisateur']; ?>"><?= htmlspecialchars($user['prenom']); ?></span></p>
                <p>Nom : <span id="nom-display-<?= $user['id_utilisateur']; ?>"><?= htmlspecialchars($user['nom']); ?></span></p>
                <p>Login : <span id="login-display-<?= $user['id_utilisateur']; ?>"><?= htmlspecialchars($user['login']); ?></span></p>
                <button class="mb" onclick="editUser(<?= $user['id_utilisateur']; ?>)">Modifier</button>
                <form action="index.php?action=deleteUser&id=<?= $user['id_utilisateur']; ?>" method="POST" style="display: inline;">
                    <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</button>
                </form>
            </div>

            <!-- Formulaire de modification (caché par défaut) -->
            <form class="user-edit" id="edit-form-<?= $user['id_utilisateur']; ?>" action="index.php?action=updateUser&id=<?= $user['id_utilisateur']; ?>&redirectPage=showUsers" method="POST" enctype="multipart/form-data" style="display: none;">

            <!-- Ajout du champ pour changer l'image -->
                <label for="photo-<?= $user['id_utilisateur']; ?>">Photo :</label>
                <input type="file" name="photo" id="photo-<?= $user['id_utilisateur']; ?>">
                

                <p>Prénom : <input type="text" name="prenom" id="prenom-input-<?= $user['id_utilisateur']; ?>" value="<?= htmlspecialchars($user['prenom']); ?>"></p>
                <p>Nom : <input type="text" name="nom" id="nom-input-<?= $user['id_utilisateur']; ?>" value="<?= htmlspecialchars($user['nom']); ?>"></p>
                <p>Login : <input type="text" name="login" id="login-input-<?= $user['id_utilisateur']; ?>" value="<?= htmlspecialchars($user['login']); ?>"></p>

                <button type="submit">Enregistrer</button>
                <button type="button" onclick="cancelEditUser(<?= $user['id_utilisateur']; ?>)">Annuler</button>


            </form>
                </div>
    <?php endforeach; ?>
</ul>

<script src="script.js"></script>
