<?php

// Fonction de connexion à la base de données
function dbConnect() {
        $db = new PDO('mysql:host=localhost;dbname=miniblog', 'root', 'root');
        return $db;
}

// Fonction pour vérifier les identifiants d'un utilisateur (connexion)
function login($login, $mdp) {
    $db = dbConnect();
    $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($mdp, $user['mdp'])) {
        // Stocker les informations utilisateur dans la session
        $_SESSION['user_id'] = $user['id_utilisateur']; 
        $_SESSION['prenom'] = $user['prenom']; 
        return $user;
    }
    return false;
}

// Vérifie si l'utilisateur est admin
function isAdmin() {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1;
}

// Vérifie si l'utilisateur est connecté
function isLog() {
    return isset($_SESSION['user_id']);
}

// Fonction pour enregistrer un nouvel utilisateur
function register($nom, $prenom, $login, $mdp, $photo) {
    $db = dbConnect();

    // Vérifie si le login existe déjà
    $stmt = $db->prepare("SELECT id_utilisateur FROM utilisateurs WHERE login = ?");
    $stmt->execute([$login]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        return ['success' => false, 'error' => 'Le login est déjà utilisé.'];
    }

    $hashedPassword = password_hash($mdp, PASSWORD_BCRYPT);

    // Insertion de l'utilisateur
    $stmt = $db->prepare("INSERT INTO utilisateurs (nom, prenom, login, mdp) VALUES (?, ?, ?, ?)");
    $success = $stmt->execute([$nom, $prenom, $login, $hashedPassword]);

    if ($success) {
        // Récupérer l'utilisateur nouvellement inscrit pour établir la session
        $userId = $db->lastInsertId();

        // Gestion de la photo de profil
        if ($photo && $photo['error'] === 0) {
            $imageResult = addImage($photo, $userId);
            if ($imageResult !== true) {
                return ['success' => false, 'error' => $imageResult];
            }
        } elseif ($photo && $photo['error'] !== UPLOAD_ERR_NO_FILE) {
            return ['success' => false, 'error' => 'Erreur lors du téléchargement de la photo.'];
        }        

        // Ajouter les informations dans la session
        $_SESSION['user_id'] = $userId;
        $_SESSION['prenom'] = $prenom;

        return ['success' => true];
    }

    return ['success' => false, 'error' => 'Erreur lors de l\'inscription.'];
}

// Fonction pour récupérer tous les utilisateurs
function getAllUsers() {
    $db = dbConnect();
    $stmt = $db->query("SELECT * FROM utilisateurs");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour ajouter un post
function createPost($titre, $contenu) {
    $db = dbConnect();
    $stmt = $db->prepare("INSERT INTO billets (titre_billet, contenu_billet, date_billet) VALUES (?, ?, NOW())");
    return $stmt->execute([$titre, $contenu]);
}

// Fonction pour récupérer les 3 derniers posts
function getLatestPosts() {
    $db = dbConnect();
    $stmt = $db->query("SELECT * FROM billets ORDER BY date_billet DESC LIMIT 3");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $posts;
}

// Fonction pour récupérer tous les posts
function getAllPosts() {
    $db = dbConnect();
    $stmt = $db->query("SELECT * FROM billets ORDER BY date_billet DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $posts;
}

function getComments($postId) {
    $db = dbConnect();

    $query = "
    SELECT c.id_commentaire, 
           c.contenu_commentaire, 
           c.date_commentaire,
           c.utilisateur_id,
           u.login AS utilisateur_login 
    FROM commentaires c 
    JOIN utilisateurs u ON c.utilisateur_id = u.id_utilisateur
    WHERE c.billet_id = :post_id
    ORDER BY c.date_commentaire ASC
    ";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addComment($postId, $userId, $content) {
    $db = dbConnect();

    $query = "INSERT INTO commentaires (billet_id, utilisateur_id, contenu_commentaire, date_commentaire) 
              VALUES (:post_id, :user_id, :content, NOW())";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':post_id', $postId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);

    return $stmt->execute();
}

function updateUser($id, $prenom, $nom, $login, $photo = null) {
    $db = dbConnect();

    $allowedMimeTypes = [
        'image/jpeg' => '.jpg',
        'image/png' => '.png',
        'image/gif' => '.gif',
        'image/webp' => '.webp'
    ];

    // Mise à jour des informations utilisateur
    $stmt = $db->prepare("UPDATE utilisateurs SET prenom = ?, nom = ?, login = ? WHERE id_utilisateur = ?");
    $success = $stmt->execute([$prenom, $nom, $login, $id]);

    // Gestion de la photo si elle est fournie
    if ($photo && $photo['error'] === UPLOAD_ERR_OK) {
        $fileType = mime_content_type($photo['tmp_name']);
        if (array_key_exists($fileType, $allowedMimeTypes)) {
            $extension = $allowedMimeTypes[$fileType];
            $uploadDir = __DIR__ . '/../../medias/pdp/';

            foreach (glob($uploadDir . $id . '.*') as $oldFile) {
                if (file_exists($oldFile)) {
                    unlink($oldFile); // Supprime le fichier
                }
            }

            // Déplacez la photo téléchargée
            $filePath = $uploadDir . $id . $extension;
            move_uploaded_file($photo['tmp_name'], $filePath);
        }
    }

    return $success;
}

function deleteUser($id) {
    $db = dbConnect();
    $stmt = $db->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = ?");
    return $stmt->execute([$id]);
}

function updatePost($id, $titre, $contenu) {
    $db = dbConnect();
    $stmt = $db->prepare("UPDATE billets SET titre_billet = ?, contenu_billet = ? WHERE id_billet = ?");
    return $stmt->execute([$titre, $contenu, $id]);
}

function deletePost($id) {
    $db = dbConnect();
    $stmt = $db->prepare("DELETE FROM billets WHERE id_billet = ?");
    return $stmt->execute([$id]);
}

function updateComment($id, $contenu) {
    $db = dbConnect();
    $stmt = $db->prepare("UPDATE commentaires SET contenu_commentaire = ? WHERE id_commentaire = ?");
    return $stmt->execute([$contenu, $id]);
}

function deleteComment($id) {
    $db = dbConnect();
    $stmt = $db->prepare("DELETE FROM commentaires WHERE id_commentaire = ?");
    return $stmt->execute([$id]);
}

function addImage($file, $userId) {
    $allowedMimeTypes = [
        'image/jpeg' => '.jpg',
        'image/png' => '.png',
        'image/gif' => '.gif',
        'image/webp' => '.webp',
    ];

    // Si aucun fichier n'est téléchargé, utiliser l'image par défaut
    if (empty($file['tmp_name'])) {
        $defaultImage = __DIR__ . '/../../medias/pdp/default.jpg';
        $uploadDir = __DIR__ . '/../../medias/pdp/';
        $filePath = $uploadDir . $userId . '.jpg';

        if (file_exists($defaultImage)) {
            copy($defaultImage, $filePath);
            return true;
        } else {
            return "L'image par défaut est introuvable.";
        }
    }

    // Vérifiez le type de fichier s'il est téléchargé
    $fileType = mime_content_type($file['tmp_name']);
    if (!array_key_exists($fileType, $allowedMimeTypes)) {
        return "Le fichier doit être une image de type JPEG, PNG, GIF ou WebP.";
    }

    $fileExtension = $allowedMimeTypes[$fileType];

    $uploadDir = __DIR__ . '/../../medias/pdp/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileName = $userId . $fileExtension;
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return true;
    } else {
        return "Échec du téléchargement de la photo.";
    }
}

function getUserById($userId) {
    $db = dbConnect();
    $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateProfile($id, $prenom, $nom, $login, $photo = null) {
    $db = dbConnect();

    // Vérifie si le login est déjà utilisé par un autre utilisateur
    $stmt = $db->prepare("SELECT id_utilisateur FROM utilisateurs WHERE login = ? AND id_utilisateur != ?");
    $stmt->execute([$login, $id]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        return "Le login est déjà utilisé par un autre utilisateur.";
    }

    $stmt = $db->prepare("UPDATE utilisateurs SET prenom = ?, nom = ?, login = ? WHERE id_utilisateur = ?");
    $success = $stmt->execute([$prenom, $nom, $login, $id]);

    // Si une photo est envoyée
    if ($photo && $photo['error'] === UPLOAD_ERR_OK) {
        $allowedMimeTypes = [
            'image/jpeg' => '.jpg',
            'image/png' => '.png',
            'image/gif' => '.gif',
            'image/webp' => '.webp',
        ];

        $fileType = mime_content_type($photo['tmp_name']);
        if (array_key_exists($fileType, $allowedMimeTypes)) {
            $extension = $allowedMimeTypes[$fileType];
            $uploadDir = __DIR__ . '/../../medias/pdp/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach (glob($uploadDir . $id . '.*') as $oldFile) {
                if (file_exists($oldFile)) {
                    unlink($oldFile); // Supprime le fichier
                }
            }

            // Chemin du fichier
            $filePath = $uploadDir . $id . $extension;
            move_uploaded_file($photo['tmp_name'], $filePath);
        }
    }

    return $success ? true : "Erreur lors de la mise à jour.";
}

// Fonction pour se déconnecter
function logout() {
    session_start();
    session_unset();
    session_destroy();
}

?>
