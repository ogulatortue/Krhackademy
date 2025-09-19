<?php

require_once __DIR__ . '/../bootstrap.php';

require_page_login();

$userService = new User($pdo);
$challengeService = new ChallengeService($pdo); 
$currentUserId = $_SESSION['user_id'];
$errors = [];
$success = '';

$backUrl = $_SESSION['settings_return_to'] ?? '/'; 

if (isset($_SERVER['HTTP_REFERER'])) {
    $refererPath = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
    if ($refererPath !== '/settings') {
        $backUrl = htmlspecialchars($_SERVER['HTTP_REFERER']);
        $_SESSION['settings_return_to'] = $backUrl;
    }
}

function getAvailableImages(string $directoryPath, string $webPath): array
{
    $allowedBaseDir = realpath(ROOT_PATH . '/public/images');
    if (!$allowedBaseDir || strpos(realpath($directoryPath), $allowedBaseDir) !== 0) {
        return [];
    }
    $allowedExtensions = ['webp'];
    $files = glob($directoryPath . '*.{' . implode(',', $allowedExtensions) . '}', GLOB_BRACE);
    
    $imageList = [];
    if ($files) {
        foreach ($files as $file) {
            $imageList[] = $webPath . basename($file);
        }
    }
    return $imageList;
}

$lockedAvatars = [
    '/images/avatars/bds.webp',
    '/images/avatars/BRUCHON.webp',
    '/images/avatars/buffon.webp',
    '/images/avatars/cie.webp',
    '/images/avatars/cre.webp',
    '/images/avatars/discord.webp',
    '/images/avatars/first.webp',
    '/images/avatars/flag.webp',
    '/images/avatars/guigui.webp',
    '/images/avatars/lowres.webp',
    '/images/avatars/musisar.webp',
    '/images/avatars/smart.webp',
    '/images/avatars/hely-copter.webp',
    '/images/avatars/bazzys3.webp',
];
$lockedBanners = [];

$challengeRewards = [
    21 => '/images/avatars/discord.webp', // Chall 21 -> Pdp Discord
];

foreach ($challengeRewards as $challengeId => $rewardPath) {
    if ($challengeService->hasCompletedChallenge($currentUserId, $challengeId)) {
        $keyAvatar = array_search($rewardPath, $lockedAvatars);
        if ($keyAvatar !== false) {
            unset($lockedAvatars[$keyAvatar]);
        }
        $keyBanner = array_search($rewardPath, $lockedBanners);
        if ($keyBanner !== false) {
            unset($lockedBanners[$keyBanner]);
        }
    }
}



$allAvatars = getAvailableImages(ROOT_PATH . '/public/images/avatars/', '/images/avatars/');
$allBanners = getAvailableImages(ROOT_PATH . '/public/images/banners/', '/images/banners/');

$unlockedAvatarsList = [];
$lockedAvatarsList = [];
foreach ($allAvatars as $avatar) {
    if (in_array($avatar, $lockedAvatars)) {
        $lockedAvatarsList[] = $avatar;
    } else {
        $unlockedAvatarsList[] = $avatar;
    }
}
$availableAvatars = array_merge($unlockedAvatarsList, $lockedAvatarsList);

$unlockedBannersList = [];
$lockedBannersList = [];
foreach ($allBanners as $banner) {
    if (in_array($banner, $lockedBanners)) {
        $lockedBannersList[] = $banner;
    } else {
        $unlockedBannersList[] = $banner;
    }
}
$availableBanners = array_merge($unlockedBannersList, $lockedBannersList);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();

if (isset($_POST['update_customization'])) {
    $avatarUrl = $_POST['avatar_url'] ?? null;
    $avatarBgColor = $_POST['avatar_bg_color'] ?? null;
    $bannerUrl = $_POST['banner_url'] ?? null;
    $bannerBgColor = $_POST['banner_bg_color'] ?? null;
    $validationErrors = [];

    if (!in_array($avatarUrl, $availableAvatars) || in_array($avatarUrl, $lockedAvatars)) {
        $validationErrors[] = "L'avatar sélectionné n'est pas valide ou est bloqué.";
    }
    if (!in_array($bannerUrl, $availableBanners) || in_array($bannerUrl, $lockedBanners)) {
        $validationErrors[] = "La bannière sélectionnée n'est pas valide ou est bloquée.";
    }
    if (!preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $avatarBgColor)) {
        $validationErrors[] = "La couleur de l'avatar n'est pas valide.";
    }
    if (!preg_match('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/', $bannerBgColor)) {
        $validationErrors[] = "La couleur de la bannière n'est pas valide.";
    }

    if (empty($validationErrors)) {
        if ($userService->updateProfileAppearance($currentUserId, $avatarUrl, $avatarBgColor, $bannerUrl, $bannerBgColor)) {
            $_SESSION['flash_message'] = [
                'type'    => 'success',
                'title'   => 'Profil mis à jour',
                'message' => 'Vos préférences ont été enregistrées avec succès.'
            ];

            $_SESSION['avatar_url'] = $avatarUrl;
            $_SESSION['avatar_bg_color'] = $avatarBgColor;


        } else {
            $_SESSION['flash_message'] = [
                'type'    => 'error',
                'title'   => 'Erreur Serveur',
                'message' => 'Impossible de sauvegarder vos préférences. Veuillez réessayer.'
            ];
        }
    } else {
        $_SESSION['flash_message'] = [
            'type'    => 'error',
            'title'   => 'Données invalides',
            'message' => 'Certains des choix soumis ne sont pas valides. Veuillez corriger.'
        ];
    }
}
    
    if (isset($_POST['action']) && $_POST['action'] === 'update_account') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (empty($username) || empty($email)) {
            $errors[] = "Le pseudo et l'e-mail ne peuvent pas être vides.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse e-mail n'est pas valide.";
        } else {
            if ($userService->updateAccountInfo($currentUserId, $username, $email)) {
                $_SESSION['username'] = $username; 
                $_SESSION['flash_message'] = [
                    'type'    => 'success',
                    'title'   => 'Profil mis à jour',
                    'message' => 'Vos informations ont été mises à jour avec succès.'
                ];
            } else {
                $errors[] = "Une erreur est survenue lors de la mise à jour.";
            }
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $user = $userService->findById($currentUserId);

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $errors[] = "Tous les champs de mot de passe sont requis.";
        } elseif (!password_verify($currentPassword, $user['password_hash'])) {
            $errors[] = "Le mot de passe actuel est incorrect.";
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = "Les nouveaux mots de passe ne correspondent pas.";
        } elseif (strlen($newPassword) < 8) {
            $errors[] = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
        } else {
            if ($userService->updatePassword($currentUserId, $newPassword)) {
                $success = "Votre mot de passe a été changé avec succès.";
            } else {
                $errors[] = "Une erreur est survenue lors du changement de mot de passe.";
            }
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'delete_account') {
        if ($userService->deleteUser($currentUserId)) {
            header('Location: /logout');
            exit();
        } else {
            $errors[] = "Impossible de supprimer le compte. Veuillez réessayer.";
        }
    }
}

$user = $userService->findById($currentUserId);
if (!$user) {
    header('Location: /logout');
    exit();
}
$currentUser = $user;