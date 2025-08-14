<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <a href="./index.php"><img src="./images/logo_krhacken_invert2.webp" alt="Logo Krhackademy" width="40" height="40"/></a>
    <h1 class="title"> <a href="./index.php">Kr[HACK]ademy</a> </h1>
    <nav class="nav-bar" aria-label="Navigation principale">
        <ul id="nav-links-desktop">
            <li class="<?= ($currentPage === 'index') ? 'active' : '' ?>"><a href="./index.php">ACCUEIL</a></li>
            <li class="<?= ($currentPage === 'lessons') ? 'active' : '' ?>"><a href="./lessons.php">LEÇONS</a></li>
            <li class="<?= ($currentPage === 'challenges') ? 'active' : '' ?>"><a href="./challenges.php">CHALLENGES</a></li>
            <li class="<?= ($currentPage === 'scenarios') ? 'active' : '' ?>"><a href="./scenarios.php">SCÉNARIOS</a></li>
        </ul>
    </nav>
    <button class="fas fa-bars" aria-label="Ouvrir le menu de navigation" aria-expanded="false" aria-controls="mobile-nav-menu"></button>
    
    <?php if ($currentPage !== 'index'): ?>
        <button id="search-toggle-btn" class="fas fa-search" aria-label="Ouvrir la barre de recherche" aria-expanded="false" aria-controls="filter-controls"></button>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['user_id'])): ?>
        <button id="leaderboard-toggle-btn" class="fas fa-trophy" aria-label="Ouvrir le classement" aria-expanded="false" aria-controls="leaderboard-menu"></button>
    <?php endif; ?>
    
    <button id="profile-toggle-btn" class="fas fa-user-circle" aria-label="Ouvrir le menu du profil"></button>
    
</header>

<section class="profile-panel" id="profile-menu" aria-hidden="true">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="profile-header">
            <i class="fas fa-user-circle panel-icon"></i>
            <span class="profile-username"><?= htmlspecialchars($_SESSION['username'] ?? 'Utilisateur') ?></span>
            <button id="close-profile-btn" class="fas fa-times" aria-label="Fermer le menu du profil"></button>
        </div>
        <ul class="profile-options">
            <li><a href="#"><i class="fas fa-user fa-fw"></i> Mon Profil</a></li>
            <li><a href="#"><i class="fas fa-cog fa-fw"></i> Paramètres</a></li>
            <li><a href="./logout.php"><i class="fas fa-sign-out-alt fa-fw"></i> Déconnexion</a></li>
        </ul>
    <?php else: ?>
        <div class="profile-header">
            <i class="fas fa-user-circle panel-icon"></i>
            <span class="profile-username">Visiteur</span>
            <button id="close-profile-btn" class="fas fa-times" aria-label="Fermer le menu du profil"></button>
        </div>
        <ul class="profile-options">
            <li><a href="./login.php"><i class="fas fa-sign-in-alt fa-fw"></i> Se connecter</a></li>
            <li><a href="./register.php"><i class="fas fa-user-plus fa-fw"></i> S'inscrire</a></li>
        </ul>
    <?php endif; ?>
</section>