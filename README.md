# Projet-LP
### Procédure de clonage du projet
* Vérifier que PHP est installé et à jour avec la commande `php -v`, sinon installer la dernière version soit sur le [site php](https://www.php.net/downloads.php) soit via un serveur local type -AMP.
* Se positionner dans le futur dossier du projet.
* `git init`
* `git clone https://github.com/florian1105/Projet-LP.git`
* `cd Projet-LP`
* `git checkout dev`
* `cd siteDesLP`
* `php composer install`
* `yarn install` (disponible sur le [site officiel de yarn](https://yarnpkg.com/lang/fr/docs/install/))
* `yarn encore dev --watch`

À ce stade le projet devrait fonctionner. Vérifier avec `git -status` qu'il n'y a pas de fichiers générables dans le staging (par exemple node_modules).

Si c'est le cas, effectuer :
* Ajouter les dossiers et fichiers concernés dans le **.gitignore**.
* `git rm -r --cached .` (va supprimer tous les fichiers du dépot sans supprimer les fichiers locaux)
* `git add .` (va ajouter tous les fichiers au dépot sauf les fichiers de **.gitignore**)
* Effectuer un `git status` et vérifier qu'il n'y ait bien **que** les fichiers voulus en statut `deleted`.
* `git commit -m "Suppression des fichiers générés."`
* `git push`
