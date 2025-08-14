<?php
require 'back-end/db_connect.php';

// 1. Récupérer l'ID depuis l'URL (?id=...)
$lessonId = $_GET['id'] ?? null;
if (!$lessonId) {
    header("Location: lessons.php"); // Rediriger si pas d'ID
    exit();
}

// 2. Récupérer les infos de la leçon de manière sécurisée
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE lesson_id_str = ?");
$stmt->execute([$lessonId]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

// 3. Gérer le cas où la leçon n'existe pas
if (!$lesson) {
    http_response_code(404);
    echo "<h1>404 - Leçon non trouvée</h1>";
    echo "<a href='lessons.php'>Retour à la liste des leçons</a>";
    exit();
}

// 4. Déterminer la classe CSS pour la difficulté
$difficultyClass = 'difficulty-easy';
switch ($lesson['difficulty']) {
    case 'Initié': $difficultyClass = 'difficulty-medium'; break;
    case 'Confirmé': $difficultyClass = 'difficulty-hard'; break;
    case 'Expert': $difficultyClass = 'difficulty-expert'; break;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Kr[HACK]ademy - <?php echo htmlspecialchars($lesson['title']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($lesson['description']); ?>">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="./images/logo_krhacken_r.ico" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <link rel="stylesheet" href="./css/style.css"/>
    <link rel="stylesheet" href="./css/header-footer.css"/>
    <link rel="stylesheet" href="./css/content-page.css"/>
</head>
<body>

    <?php require 'header.php'; ?>

    <main class="content-container page-lesson" data-content-id="<?php echo htmlspecialchars($lesson['lesson_id_str']); ?>">
        <?php
            // On inclut le fichier de contenu correspondant à la leçon
            $contentFile = './lessons-content/' . $lesson['lesson_id_str'] . '.html';
            if (file_exists($contentFile)) {
                include $contentFile;
            } else {
                echo '<article class="content-wrapper"><p style="text-align:center; color:red;">Le fichier de contenu pour cette leçon est manquant.</p></article>';
            }
        ?>
    </main>

    <?php require 'footer.php'; ?>
    
    <script src="./js/progress-tracker.js" defer></script> 
    <script src="./js/header.js" defer></script>
</body>
</html>