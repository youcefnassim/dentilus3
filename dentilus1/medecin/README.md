Espace Médecin - Installation rapide

1) Créez la base de données MySQL :
   - Importez `medecin/install.sql` dans votre serveur MySQL.

2) Configurez la connexion :
   - Modifiez `medecin/db.php` si nécessaire (hôte, nom db, utilisateur, mot de passe).

3) Créez un utilisateur admin initial :
   - Ouvrez `medecin/create_admin.php` dans votre navigateur (une seule fois). Il créera l'utilisateur `admin` avec le mot de passe `admin123`.
   - Supprimez le fichier `create_admin.php` après usage.

4) Tester localement :
   - Depuis la racine du projet, lancez :
     php -S localhost:8000
   - Ouvrez `http://localhost:8000/index.html` puis le lien `Espace Médecin`.

5) Notes de sécurité:
   - Changez le mot de passe admin immédiatement.
   - Servez via HTTPS en production.

Utilisation avec XAMPP (guide rapide)

1) Copier le projet dans le dossier htdocs de XAMPP
- Fermez Laragon si en cours d'utilisation.
- Copiez le contenu du dossier du projet dans `C:\xampp\htdocs\dentilus` (conserver la structure).

Exemple PowerShell (adapter les chemins si besoin) :
```powershell
Robocopy "C:\Users\GAMESCASH-PC\OneDrive\Desktop\dentilus\dentilus" "C:\xampp\htdocs\dentilus" /E
```

2) Démarrer XAMPP
- Ouvrez le Control Panel XAMPP et démarrez Apache et MySQL.

3) Importer la base de données
- Ouvrez http://localhost/phpmyadmin
- Créez une base `dentilus` puis importez `medecin/install.sql` ou exécutez depuis le terminal MySQL fourni par XAMPP :
```powershell
cd C:\xampp\mysql\bin
# si root sans mot de passe
.\mysql -u root < "C:\xampp\htdocs\dentilus\medecin\install.sql"
```

4) Vérifier que PHP est interprété
- Ouvrez dans le navigateur : http://localhost/dentilus/medecin/phpinfo.php
- Si vous voyez la page d'information PHP, Apache interprète correctement PHP.

5) Configurer la connexion DB si nécessaire
- Par défaut XAMPP utilise `root` sans mot de passe. Vérifiez `medecin/db.php` :
   - $DB_HOST = '127.0.0.1';
   - $DB_NAME = 'dentilus';
   - $DB_USER = 'root';
   - $DB_PASS = '';

6) Créer l'admin initial (exécuter 1 seule fois puis supprimer)
- Ouvrez : http://localhost/dentilus/medecin/create_admin.php
- Après création, supprimez ce fichier pour la sécurité.

7) Tester le flux
- Site public : http://localhost/dentilus/index-public.html
- Interface interne : http://localhost/dentilus/index.html (slide de RDV)
- Espace Médecin (login) : http://localhost/dentilus/medecin/login.php

8) Envoi d'emails
- XAMPP n'envoie pas d'emails directement sans configuration. Pour production ou tests, soit :
   - configurer sendmail/SMTP dans `php.ini`, ou
   - intégrer PHPMailer et configurer un SMTP (recommandé). Je peux ajouter PHPMailer et un exemple de configuration SMTP si vous voulez.

9) Vérifications utiles
- Si le navigateur propose de télécharger `login.php`, assurez-vous d'accéder via http://localhost/... et non via file://.
- Vérifiez les logs Apache via XAMPP Control Panel si PHP n'est pas interprété.

Si vous voulez, j'ajoute PHPMailer et un exemple SMTP, et je peux automatiser la suppression de `create_admin.php` après exécution (script à lancer une fois). Dites-moi si vous voulez que j'ajoute PHPMailer maintenant.
 
Guide rapide : installation de PHPMailer et configuration SMTP

1) Installer Composer (si nécessaire)
- Téléchargez et installez Composer depuis https://getcomposer.org/
- Vérifiez :
```powershell
composer --version
```

2) Installer PHPMailer dans le projet
```powershell
cd C:\xampp\htdocs\dentilus
composer require phpmailer/phpmailer
```

3) Configurer SMTP dans `medecin/db.php`
- Ouvrez `medecin/db.php` et complétez :
   - `$SMTP_HOST`, `$SMTP_USER`, `$SMTP_PASS`, `$SMTP_PORT`, `$SMTP_SECURE`, `$MAIL_FROM`

Exemples de configuration

- Gmail (recommandé : App Password)
   1. Activez la validation en deux étapes sur le compte Google.
   2. Générez un "App password" (Type: Mail / Device: Autre). Google vous retourne un mot de passe à 16 caractères.
   3. Dans `medecin/db.php` :
       - `$SMTP_HOST = 'smtp.gmail.com';`
       - `$SMTP_USER = 'votre.email@gmail.com';`
       - `$SMTP_PASS = 'votre_app_password_16ch';`
       - `$SMTP_PORT = 587;`
       - `$SMTP_SECURE = 'tls';`
       - `$MAIL_FROM = 'no-reply@votredomaine.com';`

- Mailgun / SendGrid / autres fournisseurs SMTP
   - Mailgun (SMTP) : host `smtp.mailgun.org`, port 587, user = SMTP login, pass = SMTP password
   - SendGrid (SMTP) : host `smtp.sendgrid.net`, user = `apikey`, pass = `VOTRE_API_KEY`, port 587

4) Redémarrer Apache (XAMPP Control Panel) après modification.

5) Tester l'envoi d'email
- Ouvrez dans le navigateur : `http://localhost/dentilus/medecin/test_mail.php`.
- Résultat attendu : message OK si l'envoi a réussi.

6) Dépannage
- Si PHPMailer n'est pas installé, la fonction de fallback utilisera `mail()` (souvent non configuré sur Windows).
- Consultez les logs Apache / PHP via XAMPP Control Panel → Logs.
- Si Gmail refuse la connexion, vérifiez l'app password et l'accès IMAP/SMTP.

Sécurité
- Ne laissez jamais les identifiants SMTP dans un repo public. Pour production, utilisez des variables d'environnement ou un fichier de configuration sécurisé.

Souhaitez-vous que je génère également un petit fichier `medecin/.env.example` et un loader PHP pour lire ces valeurs depuis un .env ?
