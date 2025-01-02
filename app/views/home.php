<?php if (isset($_SESSION['prenom'])): ?>
<h1 class="welcome-title">Bienvenue <?= htmlspecialchars($_SESSION['prenom']);?>,</h1>
<?php else: ?>
<h1 class="welcome-title">Bienvenue à toi,</h1>
<?php endif; ?>

<br>
<div class="posts-container">
    <h2 class="posts-heading">Regarde mes derniers posts !</h2>
    <?php if (!empty($latestPosts)): ?>
    <?php foreach ($latestPosts as $post): ?>
    <div class="post-card" id="post-<?= $post['id_billet']; ?>">
        <div class="post-display">
            <h3 class="post-title"><?= htmlspecialchars($post['titre_billet']); ?></h3>
            <p class="post-content"><?= nl2br(htmlspecialchars($post['contenu_billet'])); ?></p>
            <small class="post-date">Publié le <?= date('d/m/Y', strtotime($post['date_billet'])); ?></small>

            <?php if (isAdmin()): ?>
            <div class="post-buttons">
                <button class="post-edit-button" onclick="editPost(<?= $post['id_billet']; ?>)">Modifier</button>
                <form action="index.php?action=deletePost&id=<?= $post['id_billet']; ?>&redirectPage=home" method="POST"
                    style="display:inline;">
                    <button type="submit" class="post-delete-button"
                        onclick="return confirm('Supprimer ce post ?');">Supprimer</button>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <form class="post-edit-form" id="edit-post-form-<?= $post['id_billet']; ?>"
            action="index.php?action=updatePost&id=<?= $post['id_billet']; ?>&redirect=home" method="POST"
            style="display:none;">
            <div class="form-group">
                <label for="title-<?= $post['id_billet']; ?>">Titre :</label>
                <input type="text" id="title-<?= $post['id_billet']; ?>" name="titre"
                    value="<?= htmlspecialchars($post['titre_billet']); ?>" required>
            </div>
            <div class="form-group">
                <label for="content-<?= $post['id_billet']; ?>">Contenu :</label>
                <textarea id="content-<?= $post['id_billet']; ?>" name="contenu" rows="5" cols="50"
                    required><?= htmlspecialchars($post['contenu_billet']); ?></textarea>
            </div>
            <div class="post-buttons">
                <button type="submit" class="post-save-button">Enregistrer</button>
                <button type="button" class="post-cancel-button"
                    onclick="cancelEditPost(<?= $post['id_billet']; ?>)">Annuler</button>
            </div>
        </form>

        <button class="comments-toggle-button button-mb" onclick="toggleComments(<?= $post['id_billet']; ?>)">Afficher les
            commentaires</button>

        <?php
$openPostId = $_GET['postId'] ?? null;
?>

        <div id="comments-<?= $post['id_billet']; ?>" class="comments-section"
            style="display: <?= ($post['id_billet'] == $openPostId) ? 'block' : 'none'; ?>;">
            <?php $comments = getComments($post['id_billet']); ?>
            <?php if (!empty($comments)): ?>
            <ul class="comments-list">
                <?php foreach ($comments as $comment): ?>
                <li class="comment" id="comment-<?= $comment['id_commentaire']; ?>">
                    <div class="comment-display">
                        <?php
                            $imagePath = "./medias/pdp/" . $comment['utilisateur_id'];
                            $imageExtensions = ['jpg', 'png', 'jpeg', 'gif', 'webp'];
                            $imageFile = null;

                            foreach ($imageExtensions as $ext) {
                                if (file_exists("$imagePath.$ext")) {
                                    $imageFile = "$imagePath.$ext";
                                    break;
                                }
                            }
                            ?>

                        <img src="<?= $imageFile ? $imageFile : './medias/pdp/default.jpg' ?>" alt="Photo de profil"
                            class="pdp pdp-40">
                        <small class="comment-author">Par
                            <?= htmlspecialchars($comment['utilisateur_login']); ?>,</small>
                        <small class="comment-date">le
                            <?= date('d/m/Y à H:i', strtotime($comment['date_commentaire'])); ?></small>
                        <p class="comment-content"><?= nl2br(htmlspecialchars($comment['contenu_commentaire'])); ?></p>
                        <?php if (isAdmin()): ?>
                        <div class="comment-buttons">
                            <button class="comment-edit-button"
                                onclick="editComment(<?= $comment['id_commentaire']; ?>)">Modifier</button>
                            <form
                                action="index.php?action=deleteComment&id=<?= $comment['id_commentaire']; ?>&redirectPage=home&postId=<?= $post['id_billet']; ?>"
                                method="POST" style="display:inline;">
                                <button type="submit" class="comment-delete-button"
                                    onclick="return confirm('Supprimer ce commentaire ?');">Supprimer</button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>

                    <form class="comment-edit-form" id="edit-comment-form-<?= $comment['id_commentaire']; ?>"
                        action="index.php?action=updateComment&id=<?= $comment['id_commentaire']; ?>&redirectPage=home&postId=<?= $post['id_billet']; ?>"
                        method="POST" style="display:none;">
                        <textarea class="comment-edit-textarea" name="contenu" rows="2" cols="50"
                            required><?= htmlspecialchars($comment['contenu_commentaire']); ?></textarea>
                        <div class="comment-buttons">
                            <button type="submit" class="comment-save-button">Enregistrer</button>
                            <button type="button" class="comment-cancel-button"
                                onclick="cancelEditComment(<?= $comment['id_commentaire']; ?>)">Annuler</button>
                        </div>
                    </form>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <p class="no-comments">Aucun commentaire pour ce post.</p>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
            <form class="comment-add-form"
                action="index.php?action=addComment&id=<?= $post['id_billet']; ?>&redirectPage=home&postId=<?= $post['id_billet'];?>"
                method="POST">
                <input type="hidden" name="redirectPage" value="home">
                <textarea class="comment-add-textarea" name="comment" rows="3" cols="50"
                    placeholder="Ajouter un commentaire..." required></textarea><br>
                <button type="submit" class="comment-add-button button-mb">Envoyer</button>
            </form>
            <?php else: ?>
            <p class="login-prompt"><a href="index.php?action=login">Connectez-vous</a> pour commenter.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<p class="no-posts">Aucun billet disponible pour le moment.</p>
<?php endif; ?>
</div>

<script src="script.js"></script>