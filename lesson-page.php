<?php
require 'back-end/db_connect.php';

$lessonId = $_GET['id'] ?? null;
if (!$lessonId) {
    header("Location: lessons.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM lessons WHERE lesson_id_str = ?");
$stmt->execute([$lessonId]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lesson) {
    http_response_code(404);
    echo "<h1>404 - Leçon non trouvée</h1>";
    echo "<a href='lessons.php'>Retour à la liste des leçons</a>";
    exit();
}

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
    <link rel="stylesheet" href="./css/content-page.css"/>
</head>
<body>

    <main class="content-container page-lesson" data-content-id="<?php echo htmlspecialchars($lesson['lesson_id_str']); ?>">
        
        <article class="content-wrapper">
            <header class="content-header">
                <i class="fas <?php echo htmlspecialchars($lesson['icon_class'] ?? 'fa-book'); ?> content-icon" aria-hidden="true"></i>
                <div class="content-title-group">
                    <h1><?php echo htmlspecialchars($lesson['title']); ?></h1>
                    <p class="content-intro">
                        <?php echo htmlspecialchars($lesson['description']); ?>
                    </p>
                </div>
                <span class="difficulty-tag <?php echo $difficultyClass; ?>">
                    <?php echo htmlspecialchars($lesson['difficulty']); ?>
                </span>
            </header>
            
            <?php
                $contentFile = './lessons-content/' . $lesson['lesson_id_str'] . '.html';
                if (file_exists($contentFile)) {
                    include $contentFile;
                } else {
                    echo '<p style="text-align:center; color:red;">Le fichier de contenu pour cette leçon est manquant.</p>';
                }
            ?>
        </article>

    </main>

    <script src="./js/progress-tracker.js" defer></script> 
</body>
</html>