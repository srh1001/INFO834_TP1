# INFO834 TP1

Lien github : https://github.com/srh1001/INFO834_TP1.git   
  
  
## Rappel :
L'objectif de ce TP était de créer une page web permettant à des utilisateurs de se connecter.  
Les données des comptes utilisateurs sont stockées sur une base de données SQL.  
En parralèle, il nous fallait stocker le timestamp de connexion de chaque compte dans une base de données Redis.  
Enfin, une contrainte doit être respectée : le nombre de connexion pour un compte ne doit pas dépasser une certaine limite au cours des 10 dernières minutes par exemple.  

## Conception et implémentation :  
#### Pages php :
Du côté web, nous avons une page de connexion (*login.php*) qui permet de se connecter avec un login et un mot de passe, ou de se créer un compte (*register.php*). Une fois connecté, on arrive sur la page *connected.php* qui permet juste de vérifier que la connexion a bien eu lieu.

#### SQL :
La base de données SQL stocke donc ces informations (login et mot de passe), en plus du prenom et nom de l'utilisateur. Dans mon cas je ne renseigne pas ces champs, car je ne les ai pas ajoutés au formulaire lors de la création d'un compte étant donné que ce n'est pas la problématique du TP. On pourrait d'ailleurs imaginer un schéma de DB plus complet côté phpMyAdmin, etc. Mais là encore ce n'est pas la problématique du TP.  

#### Redis :
Sur Redis, les timestamps de connexions sont stockés sur la base de données 0 en utilisant pour clé "*connexion:login*" ou *login* est celui de l'utilisateur, et en utilisant pour valeur une liste Redis contenant les timestamps de connexion.

#### Lien entre php et python :
Au moment de la connexion, le fichier *login.php* exécute le fichier *connexion_redis.py*, lequel permet de vérifier la contrainte du nombre limite de connexions au cours des 10 dernières minutes, et d'ajouter ou non un nouveau timestamp de connexion pour l'utilisateur en question dans la BD Redis.  
Le fichier *login.php* récupère grâce à la fonction *exec*, le dernier *print* réalisé dans le script python. En l'occurence, ces prints sont des codes erreurs / de réussite (par exemple code erreur 500 si limite de connexions dépassée, erreur 300 pour toute autre erreur et code 200 si réussite). Le fichier login.php va donc contrôler le contenu du dernier print pour savoir s'il faut ou non réaliser la connexion.  
(Remarque : le contenu du dernier print étant ce qui nous intéresse, c'est pour cela que dans le script python on voit chaque fois un print avant un return).  

#### Lien entre python et redis :
Comme cela a été dit, le fichier *connexion_redis.py* permet de vérifier la contrainte du nombre limite de connexions au cours des 10 dernières minutes, et d'ajouter ou non un nouveau timestamp de connexion pour l'utilisateur en question dans la BD Redis. Pour cela, il contient une fonction *add_connexion(login)* et une fonction *check_connexion_limit(login, n_connexions_limit, n_minutes)*.  
Dans cette deuxième fonction, *n_connexions_limit* correspond au nombre de connexions maximum qu'on souhaite autoriser au cours des n dernières minutes (*n_minutes*).  
Le principe derrère cette fonction est de prendre par exemple la 10ème avant dernière connexion grâce à la commande *LRANGE* de Redis, puis de vérifier que son timestamp n'est pas dans la limite de temps donnée.  
Enfin, si la limite du nombre de connexions autorisées est respectée, on ajoute le nouveau timestamp de connexion pour l'utilisateur en question avec la commande Redis *RPUSH*, qui permet d'ajouter une nouvelle donnée à la fin d'une liste.
  

## Configuration :  

#### Python : 
Installer la librairie Redis sur votre environnement python.  
Récupérer le chemin d'installation de votre python (fichier *python.exe*).  
Modifier le fichier *EtuServices/login.php* à la ligne 26 en mettant votre chemin vers votre *python.exe* dans la variable *$pythonBinPath* .  
  
#### Lancer redis :
Après avoir installer redis, lancer un serveur sur le port par défaut 6379 : *redis-server --port 6379* .   
Lancer ensuite le client sur ce même port : *redis-cli -p 6379* .  
  
#### XAMPP pour phpMyAdmin :
Lancer phpMyAdmin sur XAMPP, rendez-vous à http://localhost/phpmyadmin/index.php?route=/
et créer une nouvelle base de données de votre choix puis y importer le fichier *sql_db/sql_dump/backup.sql* du github.  
  
#### XAMPP pour php :
Dans votre répertoire d'installation xampp, se placer dans le dossier *htdocs* et y mettre le dossier EtuServices du github.  
Ouvrir la page http://localhost/EtuServices/login.php .  
  

## Utilisation :  
Vous pouvez maintenant créer votre compte depuis login.php ou utiliser un compte existant (login: login1, mot de passe: testmdp) puis vous connecter.  
  
Vous pouvez modifier les contraintes sur le nombre limite de connexions au cours des n dernières minutes dans le fichier connexion_redis.py à la ligne 39.  
  
Vous pouvez vérifier l'ajout des données dans Redis depuis le terminal du client avec les commandes suivantes par exemple :  
 - *SCAN 0*  
 - *LRANGE connexion:votreLogin 0 -1*  
  

