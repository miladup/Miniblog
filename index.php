<?php
require __DIR__ . '/app/models/model.php'; 
session_start();
$action = $_GET['action'] ?? 'home';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet"/>
    <title>Miniblog | Mila</title>
</head>
<body class="page-container">
    <header class="navbar">
    <nav>
    <div class="navbar-center">
        <a href="index.php?action=home" class="<?= $action === 'home' ? 'active' : '' ?>">Accueil</a>
        <a href="index.php?action=allPosts" class="<?= $action === 'allPosts' ? 'active' : '' ?>">Archives</a>
        <?php if (isAdmin()): ?>
            <a href="index.php?action=addPost" class="<?= $action === 'addPost' ? 'active' : '' ?>">Poster</a>
            <a href="index.php?action=showUsers" class="<?= $action === 'showUsers' ? 'active' : '' ?>">Utilisateurs</a>
        <?php endif; ?>
    </div>
    <div class="navbar-right">
    <?php if (!isLog()): ?>
            <a href="index.php?action=login" class="<?= $action === 'login' ? 'active' : '' ?>">Connexion</a>
        <?php endif; ?>
        <?php if (isLog()): ?>
            <a href="index.php?action=profile" class="<?= $action === 'profile' ? 'active' : '' ?>">Profil</a>
            <a href="index.php?action=logout">Déconnexion</a>
        <?php endif; ?>
    </div>
</nav>
</header>
<main class="content">
<?php

switch ($action) {
    case 'home': 
        $latestPosts = getLatestPosts();
        require __DIR__ . '/app/views/home.php';
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = login($_POST['login'], $_POST['mdp']);
            if ($user) {
                $_SESSION['user_id'] = $user['id_utilisateur'];
                header("Location: index.php?action=home");
                exit;
            } else {
                $error = "Identifiant ou mot de passe incorrect";
            }
        }
        require __DIR__ . '/app/views/login.php';
        break;

    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $photo = $_FILES['photo'] ?? null; 
    
            // Vérifie si les mots de passe correspondent
            if ($_POST['mdp'] !== $_POST['confirm_mdp']) {
                $error = "Les mots de passe ne correspondent pas.";
            } else {
                $result = register($_POST['nom'], $_POST['prenom'], $_POST['login'], $_POST['mdp'], $photo);
            
                if ($result['success']) {
                    header("Location: index.php?action=home");
                    exit;
                } else {
                    $error = $result['error'];
                }
            }
        }
        require __DIR__ . '/app/views/register.php';
        break;
        
        
    case 'allPosts':
        $allPosts = getAllPosts();
        require __DIR__ . '/app/views/archives.php';
        break;

    case 'addPost':
        if (!isAdmin()) {
            header("Location: index.php?action=home");
            exit;
        }
        require __DIR__ . '/app/views/posts.php';
        break;
    
    case 'createPost':
        if (!isAdmin()) {
            header("Location: index.php?action=home");
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $contenu = $_POST['contenu'] ?? '';

            echo $contenu;
    
            if (!empty($titre) && !empty($contenu)) {
                $success = createPost($titre, $contenu);
                if ($success) {
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Erreur lors de l'ajout du billet.";
                    header("Location: index.php?lala");
                }
            } else {
                $error = "Veuillez remplir tous les champs.";
                header("Location: index.php?lele");
            }
        }

        require __DIR__ . '/app/views/posts.php';
        break;

    case 'addComment':
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $postId = (int)$_GET['id'];
            $userId = (int)$_SESSION['user_id'];
            $content = trim($_POST['comment']);
            
            $redirectPage = $_GET['redirectPage'] ?? 'home';
    
            if (!empty($content)) {
                $success = addComment($postId, $userId, $content);
                if ($success) {
                    // Redirige vers la page appropriée avec openPost pour garder les commentaires ouverts
                    header("Location: index.php?action={$redirectPage}&openPost={$postId}&#post-{$postId}");
                    exit;
                } else {
                    $error = "Erreur lors de l'ajout du commentaire.";
                }
            } else {
                $error = "Le contenu du commentaire ne peut pas être vide.";
            }
        }
    
        header("Location: index.php?action=home");
        break;
        

    case 'showUsers':
        if (!isset($_SESSION['user_id']) || !isAdmin()) {
            header("Location: index.php?action=home");
            exit;
        }
        $users = getAllUsers();
        require __DIR__ . '/app/views/users.php';
        break;

        case 'updateComment':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
                $commentId = (int)$_GET['id'];
                $contenu = trim($_POST['contenu']);
                $postId = $_GET['postId'] ?? null;

                $redirectPage = $_GET['redirectPage'] ?? 'home'; // Redirection par défaut vers "home"
                
                if (!empty($contenu)) {
                    $success = updateComment($commentId, $contenu);
                    if ($success) {
                        header("Location: index.php?action={$redirectPage}&openPost={$postId}&#post-{$postId}");
                        exit;
                    } else {
                        $error = "Erreur lors de la mise à jour.";
                    }
                } else {
                    $error = "Le contenu ne peut pas être vide.";
                }
            }
            break;
        
    
    case 'deleteUser':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $userId = (int)$_GET['id'];
            $success = deleteUser($userId);
            if ($success) {
                header("Location: index.php?action=showUsers");
                exit;
            } else {
                $error = "Erreur lors de la suppression.";
            }
        }
        break;
    
        case 'updatePost':
            if (!isAdmin()) {
                header("Location: index.php?action=home");
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
                $postId = (int)$_GET['id'];
                $titre = trim($_POST['titre']);
                $contenu = trim($_POST['contenu']);
        
                $redirectPage = isset($_GET['redirect']) ? $_GET['redirect'] : 'home';
        
                if (!empty($titre) && !empty($contenu)) {
                    $success = updatePost($postId, $titre, $contenu);
                    if ($success) {
                        header("Location: index.php?action={$redirectPage}&#post-{$postId}");
                        exit;
                    } else {
                        $error = "Erreur lors de la mise à jour.";
                    }
                } else {
                    $error = "Tous les champs sont requis.";
                }
            }
            break;
        
    
        case 'deletePost':
            if (!isAdmin()) {
                header("Location: index.php?action=home"); 
                exit;
            }
        
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
                $postId = (int)$_GET['id'];
                $success = deletePost($postId);

                $redirectPage = $_GET['redirectPage'] ?? 'home';
        
                if ($success) {
                    header("Location: index.php?action={$redirectPage}");
                    exit;
                } else {
                    $error = "Erreur lors de la suppression du post.";
                }
            }
            header("Location: index.php?action=home");
            break;        
    
    case 'deleteComment':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $commentId = (int)$_GET['id'];
            $postId = $_GET['postId'] ?? null;

            $redirectPage = $_GET['redirectPage'] ?? 'home';
    
            $success = deleteComment($commentId);
            if ($success) {
                header("Location: index.php?action={$redirectPage}&openPost={$postId}&#post-{$postId}");
                exit;
            } else {
                $error = "Erreur lors de la suppression.";
            }
        }
        break;
        

    case 'profile':
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit;
        }
    
        $user = getUserById($_SESSION['user_id']); 
        require __DIR__ . '/app/views/profile.php';
        break;

    case 'updateProfile':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $userId = (int)$_SESSION['user_id'];
            $prenom = trim($_POST['prenom']);
            $nom = trim($_POST['nom']);
            $login = trim($_POST['login']);
            $photo = $_FILES['photo'] ?? null;
    
            if (!empty($prenom) && !empty($nom) && !empty($login)) {
                $result = updateProfile($userId, $prenom, $nom, $login, $photo);
                if ($result === true) {
                    header("Location: index.php?action=profile");
                    exit;
                } else {
                    $error = $result; // Contient l'erreur "Le login est déjà utilisé."
                }
            } else {
                $error = "Tous les champs sont requis.";
            }
        }
        // Recharger les informations de l'utilisateur
        $user = getUserById($_SESSION['user_id']);
        require __DIR__ . '/app/views/profile.php';
        break;

        case 'updateUser':
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
                $userId = $_GET['id'];
                $prenom = trim($_POST['prenom']);
                $nom = trim($_POST['nom']);
                $login = trim($_POST['login']);
                $photo = $_FILES['photo'] ?? null;
                $redirectPage = $_GET['redirectPage'] ?? 'home';
        
                if (!empty($prenom) && !empty($nom) && !empty($login)) {
                    $result = updateUser($userId, $prenom, $nom, $login, $photo);
                    if ($result === true) {
                        $_SESSION['success_message'] = "Utilisateur mis à jour avec succès.";
                        header("Location: index.php?action={$redirectPage}");
                        exit;
                    } else {
                        $_SESSION['error_message'] = $result;
                    }
                } else {
                    $_SESSION['error_message'] = "Tous les champs sont requis.";
                }
                exit;
            }
            break;        
        
    case 'logout':
        logout();
        header("Location: index.php?action=home");
        exit;

    default:
        $latestPosts = getLatestPosts();
        require __DIR__ . '/app/views/home.php';
        break;
}
?>
</main>

<footer class="footer">
    <p>© - 2024 Blog Mila. Tous droits réservés.</p>
</footer>

<script src="script.js"></script>
</body>
</html>