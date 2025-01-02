<h2>Créer un post</h2>
<form action="index.php?action=createPost" method="POST" class="form post-form">
    <div class="form-group">
        <label for="titre">Titre :</label>
        <input type="text" name="titre" id="titre" required>
    </div>
    <div class="form-group">
        <label for="contenu">Contenu :</label>
        <textarea name="contenu" id="contenu" rows="10" required></textarea>
    </div>
    <div class="form-buttons">
        <button type="submit" class="action-button">Publier</button>
    </div>
</form>

