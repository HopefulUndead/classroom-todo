#FROM httpd:latest
FROM php:8.3-apache

# permet depuis volume de pouvoir mettre à jour la conf
COPY ./apache2.conf /etc/apache2/apache2.conf

#Install necessary extensions : terminal linux , on y installe dépendances de linux
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libpq-dev \
    nano \
    npm \
    && docker-php-ext-install pdo pdo_mysql zip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

#Install Composer : gère packages/bundle de sympfonie, ici ne le réinstalle pas mais utilise celui de mon windows, composer est codé en ASM donc pas besoin de le re télécharger
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#Install Php-cs-fixer
# facultatif mais permet de reformater le codage php-cs-fixer.config contient règle de conventions, qd on execute php-cs-fixer va corriger les conventions (ex camelcase)
# 3 niveaux : green (renommage var, pas de risque de casser projet) yellow(+ de droit sur executions) red(+ d'autorisation, critique, peut casser code)
RUN composer global require friendsofphp/php-cs-fixer

# bundle javascript pr effectuer action + opti
RUN npm install jquery --save

# test unitaires
#Install PHPUnit
RUN composer global require --dev phpunit/phpunit

COPY ./php.ini /usr/local/etc/php/conf.d/xdebug.ini

#Set working directory to your application
# EXACTEMENT la mm chose que dans compose, cest la route où le code PHP va se mettre, endroit où est on quand on passe par container web
WORKDIR /var/www/classroom-todo

#Copy application code into the container
# vérification
COPY ../../ /var/www/classroom-todo

#autoriser le renommage de l'URI
# !!! important, permet d'utiliser le routage symphony : /factory/list au lieu de factory/list/index.php
# permet de ne pas avoir à spécifier fichier
# on autorise le fait de changer '
# URL = requête appellée derrière URI
# URI : http:/www.localhost/
# URL : http:/www.localhost/index.php
# => permet de cacher paramètres d'url
# on peut rendre visible paramètres : {param1} ou les cacher (pas d'acollade dans route)
RUN a2enmod rewrite

#Expose port 80
EXPOSE 80

# php est un programme linéaire, or on veut service web continu :
#Start Apache
#
CMD ["apache2-foreground"]


# pwd = WORKDIR &~ cmd = php apache2-foreground

# run = cmd de dockerfile, permet de controller environnement du container
# cmd = executer cmd dans workdir, puisqu'on est dans container php c'est php qui executer la commande

# php.ini : comment php doit t'il s'éxecuter ? info de configuration
