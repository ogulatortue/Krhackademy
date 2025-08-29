<?php
// Ce script est destiné à être exécuté depuis la ligne de commande, pas par un navigateur.
require_once dirname(__DIR__) . '/bootstrap.php';

echo "Début du traitement de la file d'attente des e-mails...\n";

$mailer = new MailerService();

// 1. Sélectionner les e-mails en attente
$stmt = $pdo->prepare("SELECT * FROM email_queue WHERE status = 'pending' LIMIT 10"); // On en traite 10 à la fois
$stmt->execute();
$emails = $stmt->fetchAll();

if (empty($emails)) {
    echo "Aucun e-mail à envoyer.\n";
    exit();
}

foreach ($emails as $email) {
    echo "Envoi de l'e-mail à " . $email['recipient'] . "... ";
    
    try {
        // 2. Tenter d'envoyer l'e-mail
        $mailer->sendEmail($email['recipient'], $email['subject'], $email['body']);
        
        // 3. Mettre à jour le statut à 'sent' en cas de succès
        $updateStmt = $pdo->prepare("UPDATE email_queue SET status = 'sent' WHERE id = ?");
        $updateStmt->execute([$email['id']]);
        echo "OK.\n";

    } catch (Exception $e) {
        // 4. Mettre à jour le statut à 'failed' en cas d'échec
        $updateStmt = $pdo->prepare("UPDATE email_queue SET status = 'failed' WHERE id = ?");
        $updateStmt->execute([$email['id']]);
        echo "ÉCHEC (" . $e->getMessage() . ").\n";
    }
}

echo "Traitement terminé.\n";