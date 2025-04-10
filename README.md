PROJET - JO Tickets
🧰 Compétences mobilisées
Utilisation d'un gestionnaire de versions pour le code (ex : Git)

Création d'interfaces web responsives avec HTML, CSS et Bootstrap

Programmation JavaScript pour l’interactivité des pages

Utilisation d’une API REST avec Node.js pour la gestion des données

Manipulation de base de données via une API

🏁 Contexte
Le Comité International Olympique fait de nouveau appel à vos compétences ! Cette fois, il souhaite un Proof Of Concept (POC) plus avancé pour sa compétition de football.

Objectif : créer une API centralisée pour gérer les données des matchs et des tickets, utilisée par deux applications web distinctes :

Une application pour les supporters : consulter les matchs, s'inscrire, se connecter, acheter des billets, voir leurs tickets avec QR code.

Une application pour les stadiers : scanner les billets via QR code et valider l’entrée des spectateurs.

Le projet est structuré autour de trois dossiers principaux :

/api/ : contient le projet Node.js (Express.js) avec la base de données et l'API

/supporter/ : application web des supporters en HTML, CSS, JS et Bootstrap

/scanner/ : page web dédiée à la validation des billets via QR code

🏁 Objectifs
Créer 3 applications !

① API Node.js
L’API est le cœur du projet. C’est elle qui interagit avec la base de données (par exemple avec MongoDB ou MySQL) et fournit des routes aux autres applications.

L’API doit :

Gérer les utilisateurs (inscription, connexion, sessions)

Gérer les matchs, scores et catégories de places

Gérer l’achat de billets

Générer et valider les QR codes associés à chaque billet

🔧 Tu utiliseras Express.js pour créer des endpoints REST. Par exemple :

GET /api/matchs → Liste des matchs

POST /api/login → Connexion d’un utilisateur

POST /api/tickets → Achat d’un billet

Les routes devront renvoyer des réponses au format JSON.

② Application Web Supporter
Cette application est réalisée avec HTML, CSS, JavaScript et Bootstrap. Elle permet aux supporters de :

Consulter la liste des matchs

S’inscrire et se connecter via l’API

Acheter des billets (choix de la catégorie : Silver, Gold, Platinium)

Consulter leurs billets depuis un espace personnel

Voir un QR code généré pour chaque billet

🧠 Les billets doivent être regroupés par match. Le QR Code généré contiendra l’UUID du billet, utilisé par l'application scanner pour l’identifier.

Le QR code peut être généré avec une lib JS comme qrcode.js ou une API tierce.

③ Page Scanner (Stadier)
Cette page web a pour but de scanner un QR code depuis un smartphone ou d’importer une image. Elle utilise QR Scanner pour décoder le QR Code.

Une fois scanné, l’application :

Récupère l'ID du billet

Appelle l’API GET /api/tickets/:id pour vérifier sa validité

Affiche les informations du billet (match, catégorie, nom du supporter, validité...)

💬 Communication avec l’API
Toutes les requêtes se font en JavaScript à l’aide de fetch() :

js
Copier
Modifier
fetch("http://localhost:3000/api/matchs")
  .then(response => response.json())
  .then(data => {
    // afficher les matchs
  });
Pour les requêtes POST :

js
Copier
Modifier
fetch("http://localhost:3000/api/login", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
  },
  body: JSON.stringify({ email: "user@test.com", password: "1234" }),
});
⚠️ N’oublie pas de bien gérer le CORS dans ton backend Express :

js
Copier
Modifier
const cors = require("cors");
app.use(cors({ origin: "http://localhost:5500", credentials: true }));
Et n’oublie pas d’activer la gestion des cookies/sessions si nécessaire avec des packages comme express-session.

🔐 Authentification et sécurité
Pour assurer la sécurité de l’application :

Gère les sessions utilisateurs ou utilise des tokens JWT

Protège les routes sensibles (ajout de scores, achat de tickets…)

Valide les données côté serveur

🎨 Style et UX
Utilise Bootstrap pour un design propre et responsive

Utilise la police Paris2024.ttf fournie

Structure claire, parcours utilisateur fluide et intuitif

🔍 Déploiement local
1 -Lance le serveur Node.js (npm start ou node index.js)

2-Ouvre tes fichiers HTML avec Live Server pour éviter les problèmes CORS

3-Vérifie le bon fonctionnement de l’API depuis les 2 applications

4-Teste le scanner avec un QR code valide (peut être une image dans un premier temps)

