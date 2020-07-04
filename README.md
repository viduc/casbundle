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

l'installation se fait via composer:

`composer require jasig/phpcas`
`composer require viduc/casbundle`