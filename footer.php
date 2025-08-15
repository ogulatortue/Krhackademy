<footer>
        <a href="https://krhacken.org" target="_blank" rel="noopener noreferrer"> <img src="./images/logo.webp" alt="Logo du club Krhacken"> </a>
        <a href="https://esisar.grenoble-inp.fr" target="_blank" rel="noopener noreferrer"> <img src="./images/logo_esisar.webp" alt="Logo de Grenoble-INP Esisar"> </a>
        <p class="footer-legal">
            L'ENSEMBLE DE CE SITE RELÈVE DE LA LÉGISLATION FRANÇAISE ET INTERNATIONALE SUR LE DROIT D'AUTEUR ET LA PROPRIÉTÉ INTELLECTUELLE. TOUS LES DROITS DE REPRODUCTION SONT RÉSERVÉS, Y COMPRIS POUR LES DOCUMENTS TÉLÉCHARGEABLES ET LES REPRÉSENTATIONS ICONOGRAPHIQUES ET PHOTOGRAPHIQUES. LA REPRODUCTION DE TOUT OU PARTIE DE CE SITE SUR UN SUPPORT ÉLECTRONIQUE QUEL QU'IL SOIT EST FORMELLEMENT INTERDITE SAUF AUTORISATION EXPRESSE DU DIRECTEUR DE LA PUBLICATION. CE SITE EST ÉDITÉ PAR Le club kr[HACK]en, Grenoble INP Esisar, 50 Rue Barthélémy de Laffemas, 26000 Valence, France.<br>
            Créé par Hugo BRUCHON <br>
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