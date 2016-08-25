# RPG hub
A system for role-playing game story/campaign/epic (names vary from system to system) management. This is an attempt on an integrated system. It currently has the backend and an API; frontend is incomplete.

## Background
[Role-playing games](https://en.wikipedia.org/wiki/Role-playing_game) are a very wide category, ranging from very simplistic systems to extremely complex mechanical solutions. What they do have in common, though, is the story - you can play a game without mechanics, but even a primitive dungeon crawl is going to have some story. Managing this story, its cast, threads, and - if present - mechanical components - is the role of this project.

## Installation instruction
1. If you have no composer, install it via [https://getcomposer.org/download/](https://getcomposer.org/download/)
1. If you have no composer asset plugin, install it via `composer global require "fxp/composer-asset-plugin"`
1. Clone the project
1. Run `composer install`
1. Copy `.env.example` to `.env` and fill it with configuration data
1. Run `./yii migrate/up` if this is a fresh database installation
1. Run `./yii rbac/init` if this is fresh database installation
