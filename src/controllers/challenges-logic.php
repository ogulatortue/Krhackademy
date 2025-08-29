<?php
$challengeService = new ChallengeService($pdo);
$userId = $_SESSION['user_id'] ?? null;
$challenges = $challengeService->findAllSorted($userId);