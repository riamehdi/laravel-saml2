# davikingcode/laravel-saml2

Plugin SAML2 pour Laravel, utilisation pour ALEX et GARDIAN. 
Ce plugin est basé sur la librairie LightSaml pour PHP : https://www.lightsaml.com/


## Installation

Dans composer.json du projet Laravel, ajouter le repository :

``` bash
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/DaVikingCode/laravel-saml2.git"
        }
    ],
```

Puis faire le require pour lancer l'installation du plugin :

``` bash
$ composer require davikingcode/laravel-saml2
```

Pour les versions antérieures à Laravel 6.0, utiliser la version 1.0.0 du plugin.


## Mise à jour du fichier .env

Se référen à la documentation ALEX ou GARDIAN, puis ajouter au fichier .env les variables suivantes.

``` bash
SAML2_MODE=alex // mode du pugin : "alex" ou "gardian"
APP_NNA= // le numéro NNA de l'application
SAML2_INTERNE_DOMAIN=enedis.fr,enedis-grdf.fr // une liste de noms de domaines considérés comme "internes"
```

ALEX :

``` bash
SAML2_LOGOUT_URL_PROD= // url de logout de prod 
SAML2_LOGOUT_URL_DEV= // url de logout de dev
SAML2_LOGIN_URL_PROD= // url de login de prod
SAML2_LOGIN_URL_DEV= // url de login de dev
```

GARDIAN :

``` bash
GARDIAN_LOGOUT_URL_PROD= // url de logout de prod 
GARDIAN_LOGOUT_URL_DEV= // url de logout de dev
GARDIAN_LOGIN_URL_PROD= // url de login de prod
GARDIAN_LOGIN_URL_DEV= // url de login de dev
```


## Création des certificats

Utiliser OpenSSL pour créer créer le certificat et la clé publique :

``` bash
$ openssl req -new -x509 -days 3650 -nodes -sha256 -out saml.crt -keyout saml.pem
```
Renommer les fichiers créés "sp.crt" et sp.pem", et les placer dans le dossier "storage/saml2/".

3650 days = clé valable 10 ans. Plus d'information sur la création de la paire ici :
https://www.lightsaml.com/LightSAML-Core/Cookbook/How-to-generate-key-pair/


## Routes

Le plugin installe automatiquement les routes suivante :

``` bash
|        | POST     | saml2/acs      | saml2-acs      | DaVikingCode\LaravelSaml2\Controllers\LaravelSaml2Controller@acs         | web        |
|        | GET|HEAD | saml2/login    | saml2-login    | DaVikingCode\LaravelSaml2\Controllers\LaravelSaml2Controller@login       | web        |
|        | GET|HEAD | saml2/logout   | saml2-logout   | DaVikingCode\LaravelSaml2\Controllers\LaravelSaml2Controller@logout      | web        |
|        | GET|HEAD | saml2/metadata | saml2-metadata | DaVikingCode\LaravelSaml2\Controllers\LaravelSaml2Controller@getMetadata | web        |
```


## XML des Metadata

Aller sur l'url "saml2/metadata" pour accéder au XML, l'enregistrer et le fournir à l'équipe ALEX ou GARDIAN.


## Vérification de la signature SAML2

L'équipe ALEX ou GARDIAN fournit une clé publique. La renommer "idp.crt" et la placer dans le dossier "storage/saml2".

Cette clé servira à contrôler la signature de la réponse SAML2.



## Publication et modification du fichier de configuration

Si besoin de modifier la configuration du plugin, la publier :

``` bash
php artisan vendor:publish --tag=laravelsaml2.config
```

Le fichier de config sera publié dans "config/laravelsaml2.php', il peut ensuite être modifier.


## Fonctionnement général du plugin

1. Aller sur l'URL "saml2/login" pour être redirigé vers le portail de connexion ALEX ou GARDIAN.
2. Une fois l'utilisateur authentifié sur le portail, on est redirigé sur l'url "saml2/acs" en POST.
3. La signature SAML2 est vérifiée, et des informations de connexion sont récupérées.
4. La fonction de login de l'application est appelée dans le UserController.


## Le UserController

Défini dans la configuration, le controller par défaut est "App\Http\Controllers\Api\UserController".

La fonction concernée est "logUserIn($attributes)" et prend en paramètre un tableau, qui contient les attributs SAML2 reçus.

Le reste de cette fonction est propre à chaque application, et dépend de la gestion des utilisateurs prévus.



## Informations complémentaires

Pour toute information concernant SAML2, se référer à la documentation LightSaml : https://www.lightsaml.com/

Pour toute information propre à ALEX ou GARDIAN, ou pour toute information complémentaire, se référer à la documention ou à l'équipe ALEX ou GARDIAN.

