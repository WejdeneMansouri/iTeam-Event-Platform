# iTeam-Event-Platform# 🎓 iTeam University Event Platform

Une plateforme web de gestion d'événements développée pour iTeam University. Elle permet une interaction fluide entre les administrateurs et les étudiants autour de la création, gestion et participation à des événements universitaires.

---

## 🚀 Fonctionnalités principales

### 👨‍💼 Administrateur :
- Gérer les événements :
  - Créer, modifier, supprimer des événements
- Gérer les participants :
  - Accepter ou rejeter les pré-inscriptions
- Gérer les utilisateurs :
  - Ajouter des comptes étudiants et administrateurs
  - Génération automatique d’un mot de passe
  - Envoi automatique d’un email contenant les identifiants

### 🎓 Étudiant :
- Consulter la liste des événements à venir
- Effectuer une **pré-inscription** à un événement
- Recevoir un **email de confirmation** lors de la pré-inscription
- Être notifié par email lors de l’acceptation ou du refus
- Modifier son profil personnel
- Donner un avis sur les événements auxquels il a participé
- Publier des **images de l’événement**

---

## 📬 Notifications par email

- Lors de la création d’un compte étudiant ou admin → un email avec identifiants est envoyé.
- Lors de la pré-inscription à un événement → un email de confirmation est envoyé.
- Lors de l'acceptation ou du rejet d'une participation → un email de réponse est envoyé.

---

## 🛠️ Technologies utilisées

- **Frontend** : HTML, CSS
- **Backend** : PHP (sans framework)
- **Base de données** : MySQL (via phpMyAdmin)
- **Serveur local** : XAMPP

---

## 📁 Structure du projet

```bash
event-platform/
│
├── admin/                  # Espace administration (événements, utilisateurs, participants)
├── student/                # Espace étudiant (profil, événements, avis, images)
├── events/                 # Traitement des validations, suppressions
├── config/                 # Fichier de connexion à la base de données
├── src/                    # Librairies externes (email, PHPMailer, etc.)
├── login.php               # Page de connexion (admin / étudiant)
├── logout.php              # Déconnexion
└── README.md               # Description du projet
