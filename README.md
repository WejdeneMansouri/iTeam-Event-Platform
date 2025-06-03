# 🎓 iTeam University Event Platform

Une plateforme web de gestion d'événements développée pour iTeam University. Elle permet une interaction fluide entre les administrateurs et les étudiants autour de la création, gestion et participation à des événements universitaires.

---

## 🚀 Fonctionnalités principales

### 👨‍💼 Administrateur :
- Gérer les événements :
  - Créer, modifier, supprimer des événements
  - Ajouter un événement directement depuis le **calendrier interactif**
- Gérer les propositions d’événements :
  - **Accepter ou refuser** les propositions envoyées par les étudiants (avec image)
- Gérer les participants :
  - Accepter ou rejeter les **pré-inscriptions**
- Gérer les utilisateurs :
  - Ajouter des comptes étudiants et administrateurs
  - Génération automatique d’un mot de passe
  - Envoi automatique d’un email contenant les identifiants
- Notifications :
  - Reçoit une **notification** lorsqu’un étudiant s’est préinscrit ou a proposé un événement

### 🎓 Étudiant :
- Consulter la liste des événements à venir
- Effectuer une **pré-inscription** à un événement
- Recevoir un **email de confirmation** lors de la pré-inscription
- Être notifié par email lors de l’acceptation ou du refus
- Modifier son profil personnel (avec photo de profil)
- Donner un **avis** sur les événements auxquels il a participé
- Publier des **images de l’événement**
- **Proposer un événement** avec **image illustrant l'événement**

---

## 📬 Notifications par email

- Lors de la création d’un compte étudiant ou admin → un email avec identifiants est envoyé.
- Lors de la pré-inscription à un événement → un email de confirmation est envoyé.
- Lors de l'acceptation ou du rejet d'une participation → un email de réponse est envoyé.
- Lorsqu’un étudiant propose un événement → une **notification est envoyée à l’administrateur**.

---

## 📸 Gestion des images

- Chaque **utilisateur** (admin ou étudiant) a sa **propre photo de profil**
- Les **images liées aux événements** (propositions, publications des étudiants, etc.) sont enregistrées dans le dossier `uploads/`

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
├── admin/
├── student/
├── events/
├── config/
├── src/
├── uploads/
├── login.php
├── logout.php
├── logo.png
└── README.md
