import sys
import redis
from datetime import datetime

# Codes d'erreur
ERROR_TOO_MANY_CONNECTIONS = 500
ERROR = 300
SUCCESS = 200

def add_connexion(login):
    """
    Ajoute les données de connexion à Redis pour un utilisateur donné.

    Parameters:
    - login (str): Le nom d'utilisateur pour lequel ajouter les données de connexion.

    Returns:
    - int: Code de retour indiquant le succès ou l'échec de l'opération.
           SUCCESS (200) : Opération réussie.
           ERROR (300) : Erreur lors de l'ajout des données à Redis.
    """
    try:
        # Connexion à Redis
        r = redis.StrictRedis(host='localhost', port=6379, db=0)

        # Ajouter le timestamp actuel à la liste "connexion:login"
        timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        r.rpush(f"connexion:{login}", timestamp)

        print(SUCCESS)
        return SUCCESS
    
    except Exception as e:
        print(e)
        print(ERROR)
        return ERROR


def check_connection_limit(login, n_connexions_limit=10, n_minutes=10):
    """
    Vérifie si le nombre de connexions pour un utilisateur dépasse la limite spécifiée
    et si la n-ème avant dernière connexion remonte à plus de la limite de minutes fixée.

    Parameters:
    - login (str): Le nom d'utilisateur pour lequel vérifier la limite de connexions.
    - n_connexions_limit (int): Limite du nombre de connexions autorisées.
    - n_minutes (int): Limite de minutes pour la vérification temporelle.

    Returns:
    - int: Code de retour indiquant le succès ou l'échec de la vérification.
           SUCCESS (200) : Limite non atteinte.
           ERROR_TOO_MANY_CONNECTIONS (500) : Limite de connexions atteinte.
           ERROR (300) : Erreur lors de la vérification.
    """
    try:
        # Connexion à Redis
        r = redis.StrictRedis(host='localhost', port=6379, db=0)

        # Récupérer les derniers timestamps des connexions récentes
        last_n_connexions = r.lrange(f"connexion:{login}", -n_connexions_limit, -1)
        
        # Vérifier que la n avant dernière connexion était il y a plus de la limite fixée de minutes
        oldest_connexion_str = last_n_connexions[0].decode()
        oldest_connexion_unix = int(datetime.timestamp(datetime.strptime(oldest_connexion_str, "%Y-%m-%d %H:%M:%S")))
        current_timestamp_unix = int(datetime.now().timestamp())

        if (current_timestamp_unix - oldest_connexion_unix) <= n_minutes * 60:
            print(ERROR_TOO_MANY_CONNECTIONS)
            return ERROR_TOO_MANY_CONNECTIONS
        else:
            print(SUCCESS)
            return SUCCESS

    except Exception as e:
        print(e)
        print(ERROR) 
        return ERROR
    
# Récupérer le login depuis les arguments de la ligne de commande
if __name__ == "__main__":

    # On vérifie qu'il y a bien un login renseigné :
    if len(sys.argv) > 1:
        login = sys.argv[1]

        if check_connection_limit(login) == SUCCESS:
           add_connexion(login)

    else:
        print("Erreur : pas de login renseigné.")
