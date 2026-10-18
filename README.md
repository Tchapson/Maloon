# Maloon

Site vitrine de conseil avec pages de présentation, domaines d'expertise, contact et demande de rendez-vous.

## Technologies et organisation

- HTML, CSS et JavaScript pour l'interface.
- `Acceuil.html` : page d'entrée (nom historique conservé pour les liens existants).
- `Rendez-vous.html`, `Js/rendezvous.js` : formulaire utilisant Firestore et une intégration Google Apps Script.
- `Php/Traitement.php` : traitement PHP distinct utilisant PHPMailer.
- `Php/PHPMailer-master/` : bibliothèque tierce, avec ses propres licences et sa documentation.

## Consulter l'interface localement

```bash
git clone https://github.com/Tchapson/Maloon.git
cd Maloon
python3 -m http.server 8000 --bind 127.0.0.1
```

Ouvrir http://127.0.0.1:8000/Acceuil.html. Ce serveur permet de consulter l'interface mais n'exécute pas PHP.

## Intégrations et précautions

Le code Google Apps Script, les règles Firestore et la configuration des services ne sont pas inclus. Le clonage seul ne fournit donc pas un service de prise de rendez-vous autonome. Ne pas utiliser de données personnelles réelles pour tester les intégrations existantes.

Le formulaire JavaScript et le traitement PHP sont deux chemins distincts. Un envoi `no-cors` ne permet pas de confirmer la réception d'un email. Un secret placé dans le JavaScript est public et ne constitue pas une authentification : la validation et les protections anti-abus doivent être assurées côté serveur. Les clés de configuration Firebase côté client ne remplacent pas les règles d'accès Firestore.

Le traitement PHP utilise les variables d'environnement serveur `SMTP_USERNAME`, `SMTP_PASSWORD` et `MAIL_TO`. Elles doivent être définies dans l'environnement de PHP ; un fichier `.env` n'est pas chargé automatiquement. Les anciens identifiants SMTP publiés doivent être révoqués si encore actifs.

## Compétences illustrées

Intégration d'un site multipage, mise en forme CSS, gestion d'un formulaire, interactions JavaScript et intégration de services externes.

Mohamed Noumory Fané. Les bibliothèques tierces conservent leurs auteurs et licences.
