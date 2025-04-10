const db = require('../../config/db'); // Connexion à la base de données

// Récupérer la liste des événements
const getEvents = (req, res) => {
  const query = `
    SELECT e.id, e.start, s.name AS stadium, 
           t1.name AS team_home, t2.name AS team_away, e.score, e.winner_id 
    FROM mainapp_event e
    LEFT JOIN mainapp_stadium s ON e.stadium_id = s.id
    LEFT JOIN mainapp_team t1 ON e.team_home_id = t1.id
    LEFT JOIN mainapp_team t2 ON e.team_away_id = t2.id
    ORDER BY e.start ASC
  `;
  db.query(query, (err, results) => {
    if (err) return res.status(500).send('Erreur lors de la récupération des événements');
    res.json(results);
  });
};

 // Ajouter un nouvel événement
const createEvent = (req, res) => {
  const { start, stadium_id, team_home_id, team_away_id, score, winner_id } = req.body;

  if (!start || !stadium_id || !team_home_id || !team_away_id) {
    return res.status(400).send('Tous les champs sont obligatoires');
  }

  const query = `
    INSERT INTO mainapp_event (start, stadium_id, team_home_id, team_away_id, score, winner_id)
    VALUES (?, ?, ?, ?, ?, ?)
  `;
  db.query(query, [start, stadium_id, team_home_id, team_away_id, score, winner_id], (err, result) => {
    if (err) return res.status(500).send('Erreur lors de l\'ajout de l\'événement');
    res.status(201).send({ message: 'Événement ajouté avec succès' });
  });
};

// Exporter les fonctions
module.exports = {
  getEvents,
  createEvent
};
