Viduc/CasBundle
=======

CasBundle est un bundle pour Symfony 5. Il permet l'authentification via un
serveur SSO (cas) de façon très simple. Il enbarque également un système de
switch utilisateur afin de pouvoir prendre possession d'un compte utilisateur
sans connaitre ses identifiants (fonctionnalité 'EnTantQue')


[![Build Status](https://api.travis-ci.com/viduc/casbundle.svg)](https://travis-ci.com/viduc/casbundle)


LICENSE
-------

Copyright [2020] [Tristan FLeury]

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

PREREQUIS
---------

Ce bundle fonctionne avec une version minimale de symfony 5.1 et une version de
php 7.2.
Pour fonctionner, ce bundle nécessite également l'installation de la libraire
jasig/phpcas

INSTALLATION
------------

L'installation du bundle se fait via composer:

`composer require viduc/casbundle`

CONFIGURATION
-------------
- **Configuration de la partie SSO (CAS):**
	1. créer un fichier _cas.yaml_ dans le dossier config/packages de votre application
	2. ajouter ces deux lignes à votre fichier cas.yaml:
        ```yaml
            cas:
              host: '%env(CAS_HOST)%'
        ```
    3. éditer le fichier .env et ajouter le paramètre suivant:
        ```
            CAS_HOST=mon.serveur.cas.fr
        ```
	    ou mon.serveur.cas.fr est l'url de votre serveur sso
	4. *Class User:* par défaut le bundle est configuré pour utiliser sa class CasUser.php.
	Il est cependant conseillé de créer votre propre class utilisateur en étendant la class CasUser.php
	(ou au minimum en implémentatnt l'interface UserInterface). Si vous n'avez pas de class déjà existante,
	créez votre class dans le dosser Security par exemple (créer le dossier si il n'existe pas) et étendez la class CasUser.php:
        ```php
        <?php
        namespace App\Security;
        
        use Viduc\CasBundle\Security\CasUser;
        
        class User extends CasUser
        {
        
        }
        ```
	
	5. *Class UserProvider*: la class UserProvider sert à charger l'objet utilisateur
	lorsque l'authentification est réussie.
	Par défaut le système utilisera la class CasUser en renseignant le login
	récupérer par le cas et en attribuant les roles *user* et *entantque*.
	Pour implémenter votre logique de chargement d'un utilisateur,
	vous devez créer une class UserProvider.php dans le dossier Security
	(créer le dossier si il n'existe pas) situé dans src (App).
	Cette class doit étendre la class Viduc/CasBundle/Security/UserProvider.php.
	Vous devrez ensuite surcharger la méthode:
	`chargerUtilisateurParSonLogin($username)`
	Cette méthode prend en paramètre le username (login) renvoyer par le cas
	et doit retourner un objet utilisateur (implémentant au minimum UserInterface):
        ```php
        <?php
        
        namespace App\Security;
        
        use Viduc\CasBundle\Security\UserProvider;
        use App\Security\User;
        
        class UserPersoProvider extends UserProvider
        {
            public function chargerUtilisateurParSonLogin($username)
            {
                $user = new User();
                $user->setUsername($username);
                $user->setRoles(['ROLE_USER']);
        
                return $user;
            }
        }
        ```
	C'est dans cette méthode que vous implémenterez votre propre logique de chargement utilisateur.
	Vous pouvez récupérer votre utilisateur depuis votre base de données, depuis
	un référentiel externe (ldap) ou tout autre système. Si vous souhaitez lever
	une exception (par exemple si l'utilisateur est autorisé par votre SSO mais
	n'est pas connu de l'application), utilisez de préférence les exceptions ETQ_NonAutoriseException ou
	ETQ_UtilisateurNonTrouveException (use Viduc\CasBundle\Exception\ETQ_NonAutoriseException; ou
    use Viduc\CasBundle\Exception\ETQ_UtilisateurNonTrouveException;).
	
	6. *Security.yaml* : Vous pouvez maintenant modifier le fichier security.yaml
	pour prendre en compte l'authentification et le chargement de l'utilisateur.
	Ouvrez le fichier et modifier le comme ceci:

	```yaml
        security:
            role_hierarchy:
                ROLE_ADMIN: [ROLE_USER]
            providers:
                viduc_provider:
                    id: Viduc\CasBundle\Security\UserPersoProvider
            firewalls:
                dev:
                    pattern: ^/(_(profiler|wdt)|css|images|js)/
                    security: false
                main:
                    anonymous: lazy
                    logout: ~
                    guard:
                        authenticators:
                            - viduc.cas_authenticator

        access_control:
            - { path: ^/test, roles: ROLE_USER }
    ```

	Modifier le provider en renseignant votre class précédemment créée:

	```yaml
		providers:
			viduc_provider:
				id: App\Security\UserProvider
	```
    7. *Test*: Si vous voulez tester rapidement le fonctionnement de l'authentification CAS, ajouter au fichier routes.yaml situtué dans votre dossier config cette ligne:
	```yaml
    _cas:
      resource: "@CasBundle/Resources/config/routes.yaml"
    ```
	Accédez ensuite à l'url de votre application suivit de "/cas" (par exemple: http://monapli/cas). Vous serez alors redirigé automatiquement vers votre serveur SSO pour être authentifié. Une fois logué, vous serez redirigez vers une page du bundle (Connexion En Tant Que)