# RPG hub
A system for role-playing game story/campaign/epic (names vary from system to system) management. This is an attempt on an integrated system. It is already functional, but a bit bare.

## Background
[Role-playing games](https://en.wikipedia.org/wiki/Role-playing_game) are a very wide category, ranging from very simplistic systems to extremely complex mechanical solutions. What they do have in common, though, is the story - you can play a game without mechanics, but even a primitive dungeon crawl is going to have some story. Managing this story, its cast, threads, and - if present - mechanical components - is the role of this project.

## Set up instruction
1. If you have no composer, install it via [https://getcomposer.org/download/](https://getcomposer.org/download/)
1. If you have no composer asset plugin, install it via `composer global require "fxp/composer-asset-plugin"`
1. Clone the project
1. Run `composer install` or your system equivalent (on linux, it usually boils down to `php composer.phar install`; for production, add `--no-dev` option to avoid unnecessary libraries
1. Copy `.env.example` to `.env` and fill it with configuration data
    1. Database data are mandatory; without them, the hub will fail to start
    1. Language data and key generators can be left as they are, or can be customised as desired
    1. API key must be set up to make API accessible from outside
1. If you wish to add data, create `console/migrations/data.sql` file with SQL inserts be loaded into the database; this is intended for tests on larger data sets and is not needed for normal, initial deployment
1. Initialise the project
    1. For tests: `./init --env=Development --overwrite=All`
    1. For production: `./init --env=Production --overwrite=All`
1. Run `./yii migrate/up` if this is a fresh database installation; `data.sql` will be automatically loaded if present; skip this step if you connect to existing, configured database
1. Run `./yii rbac/init` if this is fresh database installation to set up rights
1. Run `./yii rbac/set-administrator` if this is fresh database installation to set up rights; this will give administrator rights to user with ID == 1.
1. Enjoy the hub! Entry points are:
    1. `backend/web` for back end
    1. `frontend/web` for front end
