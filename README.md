### Projet AHEAD
## Installation du projet
### Obligatoire
1) Installer les dépendences PHP avec la commande: composer install (à la racine du projet)
2) Installer les dépendences Javascript avec la commande: npm install (ou yarn install)
2) Avoir installé une base de données, mysql, mariadb, etc.
3) Créer un fichier .env.local contenant les identifiants de connexion à la base de données. Par exemple les informations de connexion pour une base de données en locale seraient: DATABASE_URL="mysql://root:@127.0.0.1:3306/ahead?serverVersion=8.0.32&charset=utf8mb4"
4) Pour créer la base de données, se placer à la racine du projet et executer la commande: php bin/console d:d:c
5) Executer les migrations avec la commande: php bin/console d:m:m
6) Executer les fixtures pour avoir un jeu de données avec la commande: php bin/console d:f:l
7) Compiler le Javascript avec la commande npm run watch (ou yarn watch)
8) Lancer le serveur local avec la commande: symfony serve (Le TLS peut être requis pour OAuth2 de Google: symfony server:ca:install)
### Optionnel
Il est possible d'installer OAuth2 afin de se connecter directement avec son compte Google. Pour cela il faut ajouter dans le fichier .env.local précédemment crée les paramètres suivantes:

OAUTH_GOOGLE_ID=xxxxxx.apps.googleusercontent.com
OAUTH_GOOGLE_SECRET=xxxxxx

Ces paramètres sont récupérables à l'adresse https://developers.google.com/identity/openid-connect/openid-connect et en cliquant sur le lien "Credentials page".  
1) Maintenant, vous devez cliquer sur "Créer des identifiants" et choisir "ID client OAuth". 
2) Choisir "Application Web" comme type d'application, et donner un nom (Ahead par exemple).
Il faut par la suite paramétrer les URI de redirection autorisées en ajoutant https://127.0.0.1:8000/connect/google/check.
3) Une fois l'ajout du client OAuth terminé, vous devez ajouter un utilisateur de test dans l'onglet à gauche "Ecran de consentement OAuth". Cliquez sur "Add Users" et saisissez l'adresse de votre compte gmail.

## Livrables

### Support de présentation
TODO

### Vidéo de présentation
TODO

### Cahier des charges
- Se trouve à la racine du projet dans le dossier "documents".

### Kanban/Gantt
- Se trouve à la racine du projet dans le dossier "documents/gestion_de_projet".

### Maquette
- [Lien vers la maquette](https://www.canva.com/design/DAGS4RGKyuA/DvIejtJ1hC-v5E_jo9-s7Q/edit?utm_content=DAGS4RGKyuA&utm_campaign=designshare&utm_medium=link2&utm_source=sharebutton)

## Pistes d'amélioration pour l'application
1) Ajouter des synonymes des maladies/symtômes pour simplifier la recherche de l'utilisateur. Par exemple "maux de tête", "mal de tête", "douleur à la tête", etc.
2) Tester l'application en profondeur avec l'application NVDA, qui donne des informations avec une voix synthétique et/ou en Braille pour les personnes mal/non voyantes.
3) Pouvoir ajouter de nouvelles maladies si elles n'existent pas dans la liste. Si la maladie est recensée par 5 utilisateurs différents, elle est ajoutée à la base de données. L'écriture de la maladie peut être différente (Covid, covid19, Covid-19, etc), il faudra donc une IA qui rassemblera ces mots en un seul.
4) Avoir un compte "Pro" pour les professionnels de santé, afin d'avoir des informations vérifiées.
5) Terminer la gestion des cookies.