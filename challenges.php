<?php
require 'back-end/session_check.php'; // LE VIDEUR EST À L'ENTRÉE !
$currentPage = 'challenges'; // Variable pour le style du lien actif
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Relevez nos challenges de cybersécurité pour débutants. Mettez vos compétences en hacking éthique à l'épreuve avec le club Krhacken.">
    <meta name="keywords" content="Challenges, CTF, Débutant, Web Hacking, OSINT, Pwned, Krhacken, Esisar, cybersécurité, cryptographie, forensique, stéganographie, classement">
    <meta name="author" content="BRUCHON Hugo">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kr[HACK]ademy - Challenges de Cybersécurité pour Débutants</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <link rel="stylesheet" href="./css/style.css"/>
    <link rel="stylesheet" href="./css/header-footer.css"/>
    <link rel="stylesheet" href="./css/shared-components.css"/>
    <link rel="icon" type="image/png" href="./images/logo_krhacken_r.ico" />
</head>
<body>

    <?php require 'header.php'; ?>

    <main class="main">
        <?php
        // Connexion à la BDD pour récupérer les challenges
        require 'back-end/db_connect.php';

        $challengesByCategory = [];
        try {
            $stmt = $pdo->query("SELECT * FROM challenges ORDER BY category, points"); 
            while ($row = $stmt->fetch()) {
                $challengesByCategory[$row['category']][] = $row;
            }
        } catch (PDOException $e) {
            echo "<p style='text-align: center; color: red;'>Erreur : Impossible de charger les challenges depuis la base de données.</p>";
        }

        // Boucle sur les catégories et les challenges pour générer le HTML
        if (!empty($challengesByCategory)) {
            foreach ($challengesByCategory as $category => $challenges) {
                echo '<section class="category-section">';
                echo '    <h2 class="main-title category-title">' . htmlspecialchars($category) . '</h2>';
                echo '    <div class="box-container">';
                
                foreach ($challenges as $challenge) {
                    $challengeId = htmlspecialchars($challenge['challenge_id_str']);
                    $challengeTitle = htmlspecialchars($challenge['title']);
                    $challengePoints = htmlspecialchars($challenge['points']);
                    $challengeIcon = htmlspecialchars($challenge['icon_class'] ?? 'fa-flag');
                    
                    // Déterminer la classe de difficulté en fonction des points
                    $difficultyClass = 'difficulty-easy';
                    if ($challengePoints >= 50 && $challengePoints < 100) {
                        $difficultyClass = 'difficulty-medium';
                    } elseif ($challengePoints >= 100) {
                        $difficultyClass = 'difficulty-hard';
                    }

                    echo <<<HTML
                    <a href="./challenges/{$challengeId}.html" class="card" data-challenge-id="{$challengeId}">
                        <i class="fas {$challengeIcon} card-icon" aria-hidden="true"></i>
                        <div class="card-text-content">
                            <div class="card-title-wrapper">
                                <h4>{$challengeTitle}</h4>
                                <span class="difficulty-tag {$difficultyClass}">{$challengePoints} <i class="fas fa-flag"></i></span>
                            </div>
                        </div>
                        <div class="check-box">
                            <i class="fas fa-times-circle checkbox-icon not-completed" aria-hidden="true"></i>
                            <i class="fas fa-check-circle checkbox-icon completed" aria-hidden="true"></i>
                        </div>
                    </a>
HTML;
                }
                
                echo '    </div>';
                echo '</section>';
            }
        } else {
            echo "<p style='text-align: center;'>Aucun challenge n'est disponible pour le moment. La base de données est peut-être vide.</p>";
        }
        ?>

        <section class="category-section">
            <h2 class="main-title category-title">Pwned (Exploitation Binaire)</h2>
            <div class="box-container">
                <a href="#" class="card" data-challenge-id="pwned-placeholder" style="cursor: not-allowed; opacity: 0.7;">
                    <i class="fas fa-terminal card-icon" aria-hidden="true"></i>
                    <div class="card-text-content">
                        <div class="card-title-wrapper">
                            <h4>Challenges à venir...</h4>
                        </div>
                        <p>De nouveaux challenges d'exploitation binaire seront bientôt disponibles.</p>
                    </div>
                    <div class="check-box">
                        <i class="fas fa-lock checkbox-icon" aria-hidden="true"></i>
                    </div>
                </a>
            </div>
        </section>

        <div id="no-results-message" class="no-results" style="display: none;">
            <p>Aucun challenge ne correspond à votre recherche.</p>
        </div>
    </main>

    <?php require 'footer.php'; ?>

    <script src="./js/progress-tracker.js" defer></script>
    <script src="./js/header.js" defer></script>
    <script src="./js/search.js" defer></script>
</body>
</html>