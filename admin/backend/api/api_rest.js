const express = require('express');
const mysql = require('mysql2');
const jwt = require('jsonwebtoken');
const bodyParser = require('body-parser');
const cors = require('cors');
const QRCode = require('qrcode');

const app = express();
app.use(cors());
app.use(bodyParser.json());

const SECRET_KEY = 'SECRET_KEY'; // Clé secrète pour JWT

// Configurer la connexion MySQL
const db = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: '',
   database: 'jo_project_starter',
});

db.connect(err => {
  if (err) {
    console.error('❌ Erreur de connexion à MySQL', err);
    throw err;
  }
  console.log('✅ Connecté à MySQL');
});

// Middleware pour authentification avec JWT
const authenticateToken = (req, res, next) => {
    const token = req.headers['authorization']?.split(' ')[1];
    if (!token) return res.sendStatus(401); // Si le token n'existe pas, renvoie un status 401

    jwt.verify(token, SECRET_KEY, (err, user) => {
        if (err) {
            console.error('❌ Erreur de vérification du token', err);
            return res.sendStatus(403); // Si la vérification échoue, renvoie un status 403
        }
        req.user = user;
        next();
    });
};

// Inscription utilisateur (Le hash du mot de passe est géré en PHP)
app.post('/register', (req, res) => {
  const { username, email, password } = req.body;

  if (!username || !email || !password) return res.status(400).send('Tous les champs sont obligatoires');

  const query = "INSERT INTO auth_user (username, email, password) VALUES (?, ?, SHA2(?, 256))";
  db.query(query, [username, email, password], (err, result) => {
    if (err) {
      console.error('❌ Erreur lors de l\'inscription', err);
      return res.status(500).send('Erreur serveur');
    }

    const userId = result.insertId;

    // Création du token JWT
    const token = jwt.sign({ id: userId, username, is_superuser: 0 }, SECRET_KEY, { expiresIn: '48h' });

    res.status(201).json({ token, user: { id: userId, username, email, is_superuser: 0 } });
  });
});

// Connexion utilisateur
app.post('/login', (req, res) => {
  const { email, password } = req.body;

  if (!email || !password) return res.status(400).send('Email et mot de passe requis');

  const query = "SELECT id, username, email, password, is_superuser FROM auth_user WHERE email = ? AND password = SHA2(?, 256)";
  db.query(query, [email, password], (err, results) => {
    if (err) {
      console.error('❌ Erreur lors de la connexion', err);
      return res.status(500).send('Erreur serveur');
    }
    
    if (results.length === 0) return res.status(401).send('Identifiants incorrects');

    const user = results[0];
    const token = jwt.sign({ id: user.id, username: user.username, is_superuser: user.is_superuser }, SECRET_KEY, { expiresIn: '48h' });

    res.json({ token, user });
  });
});

// Liste des événements (Matchs)
app.get('/events', (req, res) => {
  const query = `SELECT e.id, e.start, s.name AS stadium, 
           t1.name AS team_home, t2.name AS team_away, e.score, e.winner_id 
    FROM mainapp_event e
    LEFT JOIN mainapp_stadium s ON e.stadium_id = s.id
    LEFT JOIN mainapp_team t1 ON e.team_home_id = t1.id
    LEFT JOIN mainapp_team t2 ON e.team_away_id = t2.id
    ORDER BY e.start ASC`;
  
  db.query(query, (err, results) => {
    if (err) {
      console.error('❌ Erreur lors de la récupération des événements', err);
      return res.status(500).send('Erreur serveur');
    }
    res.json(results);
  });
});

// Acheter un billet (JWT obligatoire)
app.post('/tickets', authenticateToken, (req, res) => {
  const { event_id, category, price } = req.body;
  const user_id = req.user.id;

  if (!event_id || !category || !price) return res.status(400).send('Données manquantes');

  const ticketIdentifier = `event:${event_id}-user:${user_id}-category:${category}-price:${price}-${Date.now()}`;

  QRCode.toDataURL(ticketIdentifier, (err, qrCodeUrl) => {
    if (err) {
      console.error('❌ Erreur de génération du QR Code', err);
      return res.status(500).send('Erreur de génération du QR Code');
    }

    const query = "INSERT INTO mainapp_ticket (event_id, user_id, category, price, qr_code) VALUES (?, ?, ?, ?, ?)";
    db.query(query, [event_id, user_id, category, price, qrCodeUrl], (err, result) => {
      if (err) {
        console.error('❌ Erreur lors de l\'achat du billet', err);
        return res.status(500).send('Erreur serveur');
      }
      res.status(201).send({ message: 'Billet acheté avec succès', qrCodeUrl });
    });
  });
});

// Vérifier un billet (JWT obligatoire)
app.post('/scan-ticket', authenticateToken, (req, res) => {
  const { ticket_id } = req.body;
  const scanner_id = req.user.id;

  if (!ticket_id) return res.status(400).send('ID du billet requis');

  db.query("SELECT used FROM mainapp_ticket WHERE id = ?", [ticket_id], (err, results) => {
    if (err) {
      console.error('❌ Erreur lors de la vérification du billet', err);
      return res.status(500).send('Erreur serveur');
    }
    if (results.length === 0) return res.status(404).send('Billet non trouvé');
    
    if (results[0].used) return res.status(400).send('Billet déjà utilisé');

    db.query("UPDATE mainapp_ticket SET used = 1 WHERE id = ?", [ticket_id], err => {
      if (err) {
        console.error('❌ Erreur lors de la mise à jour du billet', err);
        return res.status(500).send('Erreur serveur');
      }

      db.query("INSERT INTO mainapp_ticket_scan (ticket_id, scanner_id) VALUES (?, ?)", [ticket_id, scanner_id], err => {
        if (err) {
          console.error('❌ Erreur lors de l\'enregistrement du scan', err);
          return res.status(500).send('Erreur serveur');
        }
        res.send('Billet validé avec succès');
      });
    });
  });
});

// Liste des billets d'un utilisateur (JWT obligatoire)
app.get('/my-tickets', authenticateToken, (req, res) => {
  const user_id = req.user.id;

  const query = `
    SELECT t.id AS ticket_id, t.qr_code, t.used, t.category, e.start, s.name AS stadium, 
           s.location AS stadium_location, th.name AS home_team, ta.name AS away_team
    FROM mainapp_ticket t
    JOIN mainapp_event e ON t.event_id = e.id
    JOIN mainapp_stadium s ON e.stadium_id = s.id
    LEFT JOIN mainapp_team th ON e.team_home_id = th.id
    LEFT JOIN mainapp_team ta ON e.team_away_id = ta.id
    WHERE t.user_id = ?
    ORDER BY e.start ASC
  `;
  
  db.query(query, [user_id], (err, results) => {
    if (err) {
      console.error('❌ Erreur lors de la récupération des billets', err);
      return res.status(500).json({ message: 'Erreur serveur' });
    }

    res.json(results);
  });
});

// Lancer le serveur
const PORT = 3000;
app.listen(PORT, () => {
  console.log(`🚀 Serveur Node.js démarré sur http://localhost:${PORT}`);
});
