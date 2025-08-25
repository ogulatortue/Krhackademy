<?php

$error_message = '';

if (isset($_SESSION['user_id'])) {
	header('Location: /');
	exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	$username = trim($_POST['username'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$password = $_POST['password'] ?? '';
	$password_confirm = $_POST['password_confirm'] ?? '';

	if (empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
		$error_message = 'Veuillez remplir tous les champs.';
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error_message = "Le format de l'adresse e-mail est invalide.";
	} elseif (strlen($password) < 8) {
		$error_message = 'Le mot de passe doit contenir au moins 8 caractères.';
	} elseif ($password !== $password_confirm) {
		$error_message = 'Les mots de passe ne correspondent pas.';
	} else {
		$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
		$stmt->execute([$username, $email]);

		if ($stmt->fetch()) {
			$error_message = 'Ce nom d\'utilisateur ou cette adresse e-mail est déjà utilisé(e).';
		} else {
			$password_hash = password_hash($password, PASSWORD_ARGON2ID);

			$stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
			
			if ($stmt->execute([$username, $email, $password_hash])) {
				header('Location: /login');
				exit();
			} else {
				$error_message = 'Une erreur est survenue lors de la création du compte.';
			}
		}
	}
}