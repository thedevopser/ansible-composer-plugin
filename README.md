## Ansible Composer Plugin

Ce plugin composer permet de mettre en place rapidement une recette Ansible dans un projet en legacy ou sous Symfony.

[![Build_Status](https://app.travis-ci.com/thedevopser/ansible-composer-plugin.svg?branch=main
)](https://app.travis-ci.com/thedevopser/ansible-composer-plugin.svg?branch=main
)

## Pré-requis

- PHP 7.2 ou supérieur
- Symfony 5.x
- composer v2.x

### Installation

```bash
composer require thedevopser/ansible-composer-plugin --dev

# Indiquer votre type de projet, par défaut symfony
Sélectionner votre type de projet
  [0] legacy
  [1] symfony
 > 1
Vous avez choisi : symfony
Installation complete.
```

En cas de soucis, vous pouvez réinstaller les fichiers avec la commande 
```bash
composer thedevopser:ansible:install --force
```

### Adapter les fichiers 

Une fois l'installation terminée, vous aurez à la racine de votre projet un dossier `ansible` qui contiendra au minimum deux fichiers : 

- hosts.yml
- symfony.yml ou legacy.yml

Vous devrez dupliquer les fichiers hosts par le nombre de serveur sur lesquels vous voulez déployer (ie qualif.yml, dev.yml, prod.yml)

#### fichier hosts.yml

```yaml
all:
  hosts:
    webserver:
      root_path: # a remplir avec le chemin de votre application sur le serveur (ie /var/www/html/app_a)
      ansible_user: # utilisateur utilisé pour le déploiement ssh sur votre machine de destination
      ansible_host: # adresse ip ou FDQN de votre serveur de destination
```

#### fichier deploy-xxx

```yaml
# les seules modifications utiles sont sur l'option 

rsync_opts:
  - "--verbose"
  - "--exclude=.git"
  - "--exclude=.idea"
  - "--exclude=ansible"
  
# Ajouter en suivant les dossiers / fichiers que vous souhaiter exclure de la synchronisation des sources sous la forme --exclude=xxx
```

## Utiliser les playbook

Vous avez deux manières d'utiliser les playbook

- en local (avec ansible installé sur votre machine)
- sur un CI/CD

#### local

Il suffit de lancer la commande suivante 

```bash
ansible-playbook -i ansible/host.yml ansible/deploy-playbook-xxx.yml
```

#### CI/CD

Exemple pour gitlab sur une application Symfony

```yaml
stages:
  - tests
  - deploy

tests:
  stage: tests
  variables:
    GIT_STRATEGY: fetch
  script:
    - composer install
    - yarn install
    - yarn run build
    - bin/phpunit

deploy_qualif:
  stage: deploy
  variables:
    GIT_STRATEGY: fetch
  script:
    - composer install
    - yarn install
    - yarn run build
    - ansible-playbook -i ansible/host.yml ansible/deploy-playbook-symfony.yml
  only:
    - qualif


```

Exemple pour gitlab sur une application legacy

```yaml
stages:
  - deploy

deploy_qualif:
  stage: deploy
  variables:
    GIT_STRATEGY: fetch
  script:
    - ansible-playbook -i ansible/host.yml ansible/deploy-playbook-legacy.yml
  only:
    - qualif
```
