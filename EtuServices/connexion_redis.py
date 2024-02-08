import sys
import redis
from datetime import datetime


# Fonction pour ajouter des données de connexion à Redis
def add_connexion(login):
    # Connexion à Redis
    r = redis.StrictRedis(host='localhost', port=6379, db=0)
    try:
        # Ajouter le login à l'ensemble "logins"
        #r.sadd("logins", login)

        # Ajouter le timestamp actuel à la liste "timestamps:login"
        timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        r.rpush(f"connexion:{login}", timestamp)

        print(f"Données de connexion ajoutées pour {login}")
        return True
    except Exception as e:
        print(f"Erreur lors de l'ajout des données pour {login}: {str(e)}")
        return False

# Récupérer le login depuis les arguments de la ligne de commande
if len(sys.argv)>1:
    login = sys.argv[1]
    add_connexion(login)
    print("Test")
    
# Test indiv
add_connexion("userTest")
add_connexion("userTest1")