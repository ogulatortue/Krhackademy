<?php
require 'back-end/session_check.php'; // LE VIDEUR EST À L'ENTRÉE !
$currentPage = 'lessons'; // Variable pour le style du lien actif
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Explorez nos leçons de cybersécurité pour débutants, conçues par le club Krhacken pour s'initier au hacking éthique.">
    <title>Kr[HACK]ademy - Leçons de Cybersécurité</title>

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
        // Connexion à la BDD pour récupérer le contenu
        require 'back-end/db_connect.php';

        $lessonsByCategory = [];
        try {
            $stmt = $pdo->query("SELECT * FROM lessons ORDER BY category, title"); 
            while ($row = $stmt->fetch()) {
                $lessonsByCategory[$row['category']][] = $row;
            }
        } catch (PDOException $e) {
            echo "<p style='text-align: center; color: red;'>Erreur : Impossible de charger les leçons depuis la base de données.</p>";
        }

        // Boucle sur les catégories et les leçons pour générer le HTML
        if (!empty($lessonsByCategory)) {
            foreach ($lessonsByCategory as $category => $lessons) {
                echo '<section class="category-section">';
                echo '    <h2 class="main-title category-title">' . htmlspecialchars($category) . '</h2>';
                echo '    <div class="box-container">';
                
                foreach ($lessons as $lesson) {
                    $lessonId = htmlspecialchars($lesson['lesson_id_str']);
                    $lessonTitle = htmlspecialchars($lesson['title']);
                    $lessonDifficulty = htmlspecialchars($lesson['difficulty'] ?? 'Débutant');
                    $lessonDescription = htmlspecialchars($lesson['description'] ?? 'Description à venir.');
                    $lessonIcon = htmlspecialchars($lesson['icon_class'] ?? 'fa-book');

                    echo <<<HTML
                    <a href="./lessons/{$lessonId}.html" class="card" data-lesson-id="{$lessonId}">
                        <i class="fas {$lessonIcon} card-icon" aria-hidden="true"></i>
                        <div class="card-text-content">
                            <div class="card-title-wrapper">
                                <h4>{$lessonTitle}</h4>
                                <span class="difficulty-tag difficulty-easy">{$lessonDifficulty}</span>
                            </div>
                            <p>{$lessonDescription}</p>
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
            echo "<p style='text-align: center;'>Aucune leçon n'est disponible pour le moment. Avez-vous bien exécuté le script SQL pour remplir la base de données ?</p>";
        }
        ?>

        <section class="category-section">
            <h2 class="main-title category-title">Pwned (Exploitation Binaire)</h2>
            <div class="box-container">
                <a href="#" class="card" data-lesson-id="pwned-placeholder" style="cursor: not-allowed; opacity: 0.7;">
                    <i class="fas fa-terminal card-icon" aria-hidden="true"></i>
                    <div class="card-text-content">
                        <div class="card-title-wrapper">
                            <h4>Leçons à venir...</h4>
                        </div>
                        <p>De nouvelles leçons d'exploitation binaire pour débutants seront bientôt disponibles.</p>
                    </div>
                    <div class="check-box">
                        <i class="fas fa-lock checkbox-icon" aria-hidden="true"></i>
                    </div>
                </a>
            </div>
        </section>

        <div id="no-results-message" class="no-results" style="display: none;">
            <p>Aucune leçon ne correspond à votre recherche.</p>
        </div>
    </main>

    <?php require 'footer.php'; ?>

    <script src="./js/progress-tracker.js" defer></script>
    <script src="./js/header.js" defer></script>
    <script src="./js/search.js" defer></script>
</body>
</html>