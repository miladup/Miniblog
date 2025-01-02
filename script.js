function editUser(userId) {
    // Cache l'affichage normal et montre le formulaire
    document.querySelector(`#user-${userId} .user-display`).style.display = 'none';
    document.querySelector(`#edit-form-${userId}`).style.display = 'block';
}

function cancelEditUser(userId) {
    // Montre l'affichage normal et cache le formulaire
    document.querySelector(`#user-${userId} .user-display`).style.display = 'flex';
    document.querySelector(`#edit-form-${userId}`).style.display = 'none';
}

function editPost(postId) {
    document.querySelector(`#post-${postId} .post-display`).style.display = 'none';
    document.querySelector(`#edit-post-form-${postId}`).style.display = 'block';
}

function cancelEditPost(postId) {
    document.querySelector(`#post-${postId} .post-display`).style.display = 'block';
    document.querySelector(`#edit-post-form-${postId}`).style.display = 'none';
}

function editComment(commentId) {
    // Cache l'affichage normal du commentaire
    const commentDisplay = document.querySelector(`#comment-${commentId} .comment-display`);
    const editForm = document.getElementById(`edit-comment-form-${commentId}`);

    if (commentDisplay && editForm) {
        commentDisplay.style.display = 'none';
        editForm.style.display = 'block';
    }
}

function cancelEditComment(commentId) {
    // Remet l'affichage normal 
    const commentDisplay = document.querySelector(`#comment-${commentId} .comment-display`);
    const editForm = document.getElementById(`edit-comment-form-${commentId}`);

    if (commentDisplay && editForm) {
        commentDisplay.style.display = 'block';
        editForm.style.display = 'none';
    }
}

function editProfile() {
    document.getElementById('profile-display').style.display = 'none';
    document.getElementById('profile-edit-form').style.display = 'block';
}

function cancelEdit() {
    document.getElementById('profile-display').style.display = 'block';
    document.getElementById('profile-edit-form').style.display = 'none';
}

function toggleComments(postId) {
    const commentsSection = document.getElementById(`comments-${postId}`);
    const isVisible = commentsSection.style.display === 'block';
    commentsSection.style.display = isVisible ? 'none' : 'block';
}

function keepCommentsOpen(postId) {
    const commentsSection = document.getElementById(`comments-${postId}`);
    if (commentsSection) {
        commentsSection.style.display = 'block';
    }
}

// Commentaires ouverts si le paramètre openPost est présent
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const openPostId = urlParams.get('openPost');
    if (openPostId) {
        keepCommentsOpen(openPostId);
    }
});

// fonction pour valider le mdp (meme 2 fois)
function validatePasswords() {
    const mdp = document.getElementById('mdp').value;
    const confirmMdp = document.getElementById('confirm_mdp').value;

    if (mdp !== confirmMdp) {
        alert("Les mots de passe ne correspondent pas.");
        return false;
    }

    return true;
}

// Gestion des mots de passe
function togglePasswordVisibility(passwordId, iconId) {
    const passwordField = document.getElementById(passwordId);
    const icon = document.getElementById(iconId);
    const eyeClosedIcon = 'medias/eye-closed.svg';
    const eyeIcon = 'medias/eye.svg';

    if (!passwordField || !icon) return;

    icon.addEventListener('click', function () {
        const isPassword = passwordField.type === 'password';
        passwordField.type = isPassword ? 'text' : 'password';
        icon.querySelector('img').src = isPassword ? eyeClosedIcon : eyeIcon;
    });
}

function initPasswordFeatures() {
    const passwordFields = document.querySelectorAll('[type="password"]');
    passwordFields.forEach(passwordField => {
        const toggleIcon = document.querySelector(`#toggle-${passwordField.id}`);
        if (toggleIcon) {
            togglePasswordVisibility(passwordField.id, toggleIcon.id);
        }
    });
}

document.addEventListener('DOMContentLoaded', initPasswordFeatures);