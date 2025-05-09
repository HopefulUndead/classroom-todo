# image personnalisée
FROM mysql:latest

# Définir les variables d'environnement pour MySQL
# possibilité d'utiliser .env.local afin d'avoir des variables et d'assurer d'autant + la sécurité des données
ENV MYSQL_ROOT_PASSWORD=root
ENV MYSQL_DATABASE=db_schema

ENV MYSQL_USER=user
ENV MYSQL_PASSWORD=password

# Exposer le port 3306 !! PORT VIRTUEL, :x
EXPOSE 3306
