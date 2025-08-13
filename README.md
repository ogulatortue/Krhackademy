# Guide d'installation du projet Krhackademy

Ce guide vous montrera comment installer et lancer le projet sur votre ordinateur pour y contribuer.

### Étape 0 : Prérequis - Installer les outils 🧰

Avant toute chose, assurez-vous d'avoir installé les trois outils suivants.

1.  **XAMPP** : Votre serveur local (Apache + MySQL/MariaDB + PHP).
    * [**Télécharger XAMPP**](https://www.apachefriends.org/fr/index.html) (Prenez la version avec une version récente de PHP).

2.  **Git** : L'outil pour gérer le code source et le cloner depuis GitHub.
    * [**Télécharger Git**](https://git-scm.com/downloads) (Acceptez les options par défaut lors de l'installation).

3.  **Composer** : Le gestionnaire de dépendances pour PHP.
    * [**Télécharger Composer**](https://getcomposer.org/download/) (Téléchargez et lancez `Composer-Setup.exe`).
    * **Important** : Durant l'installation, il vous demandera le chemin vers PHP. Indiquez-lui celui de XAMPP : `C:\xampp\php\php.exe`.

### Étape 1 : Cloner le projet avec VS Code 📥

Maintenant, nous allons récupérer le code depuis GitHub en utilisant l'interface de Visual Studio Code.

1.  **Copiez l'URL du dépôt** : Allez sur la page GitHub du projet et cliquez sur le bouton vert "**<> Code**", puis copiez l'URL HTTPS.
    > https://github.com/ogulatortue/Krhackademy

2.  **Clonez dans VS Code** :
    * Ouvrez VS Code. Sur l'écran d'accueil, cliquez sur "**Clone Git Repository**".
    * Une barre de saisie apparaît en haut. Collez l'URL que vous venez de copier et appuyez sur `Entrée`.
    * VS Code vous demandera ensuite où enregistrer le projet sur votre ordinateur. Choisissez le dossier `C:\xampp\htdocs\`.

3.  **Ouvrez le projet** : Une fois le téléchargement terminé, une notification apparaîtra en bas à droite. Cliquez sur "**Open**" pour ouvrir le dossier du projet dans VS Code.

### Étape 2 : Installer les dépendances 🧩

Le code est là, mais il manque les librairies du dossier `vendor`.

1.  **Ouvrez le terminal** : Dans VS Code, ouvrez un terminal intégré en appuyant sur `Ctrl` + ` ` (la touche à côté du `1`) ou via le menu `Terminal` > `New Terminal`.

2.  **Lancez l'installation** : Dans le terminal qui s'ouvre, tapez simplement cette commande et appuyez sur `Entrée`.
    ```bash
    composer install
    ```
    Composer va lire le fichier `composer.json` et installer tout le nécessaire dans un dossier `vendor` qui vient d'être créé.

### Étape 3 : Configurer l'environnement local 🔑

Il faut maintenant préparer la base de données et les identifiants de connexion.

1.  **Lancez XAMPP** : Ouvrez le panneau de contrôle de XAMPP et démarrez les modules **Apache** et **MySQL**.

2.  **Créez la base de données** :
    * Dans le panneau XAMPP, cliquez sur le bouton "**Admin**" de la ligne MySQL pour ouvrir phpMyAdmin dans votre navigateur.
    * Créez une nouvelle base de données vide et nommez-la `krhackademy`.

3.  **Créez votre fichier `.env`** :
    * Dans l'explorateur de fichiers de VS Code, vous devriez voir un fichier nommé `.env.example`.
    * Faites un clic droit dessus et choisissez "**Copier**", puis faites un clic droit dans une zone vide et choisissez "**Coller**".
    * Renommez cette nouvelle copie en `.env`.
    * Ouvrez ce fichier `.env` et remplissez-le avec vos informations locales (le mot de passe `DB_PASS` est souvent vide pour une installation par défaut de XAMPP).

### Étape 4 : C'est prêt ! ✅

Votre environnement est maintenant une copie conforme de celui du projet original. Vous pouvez ouvrir votre navigateur et vous rendre à l'adresse suivante pour voir le site fonctionner : `http://localhost/Krhackademy/`

Vous êtes prêt à développer !