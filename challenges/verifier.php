<?php

$flags = [
    "integrity_key" => "A95C1ADBC819FC5FC82EB862B67D910E0B38260C525DBF1211A26E0E0D2F79F6",
    "autre_challenge_id" => "HASH_DE_L_AUTRE_CHALLENGE",
    "encore_un_autre_id" => "UN_AUTRE_HASH"
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $challengeId = $_POST['challenge_id'];
    $userFlag = strtoupper(trim($_POST['flag']));

    if (isset($flags[$challengeId]) && $userFlag === $flags[$challengeId]) {
        echo "<script>
                alert('Félicitations ! Flag correct ! ✅');
                window.history.back();
              </script>";
    } else {
        echo "<script>
                alert('Incorrect. Essayez encore. ❌');
                window.history.back();
              </script>";
    }
} else {
    echo "Veuillez soumettre le formulaire.";
}

?>