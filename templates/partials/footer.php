<footer>
        <a href="https://krhacken.org" target="_blank" rel="noopener noreferrer"> <img src="./images/logo.webp" alt="Logo du club Krhacken"> </a>
        <a href="https://esisar.grenoble-inp.fr" target="_blank" rel="noopener noreferrer"> <img src="./images/logo_esisar.webp" alt="Logo de Grenoble-INP Esisar"> </a>
        <p class="footer-legal">
            Ce site a été conçu et développé par Hugo BRUCHON. Le club Kr[HACK]en est autorisé à utiliser et à publier ce site à des fins non commerciales. Tous les droits d'auteur, de reproduction et de propriété intellectuelle du code, du design et des contenus originaux restent la propriété exclusive d'Hugo BRUCHON. Toute reproduction totale ou partielle sans autorisation expresse est formellement interdite.
        </p>
    </footer>

    <?php
    // Chargeur de scripts intelligent
    if (isset($scriptsToLoad) && is_array($scriptsToLoad)) {
        foreach ($scriptsToLoad as $script) {
            echo '<script src="' . htmlspecialchars($script) . '" defer></script>' . "\n";
        }
    }
    ?>

</body>
</html>