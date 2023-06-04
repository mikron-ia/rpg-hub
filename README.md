# RPG hub

A system for role-playing game story/campaign/epic (names vary from system to system) management. This is an attempt on
an integrated system that would be a help in that process.

## Background

[Role-playing games](https://en.wikipedia.org/wiki/Role-playing_game) are a very wide category, ranging from very
simplistic systems to extremely complex mechanical solutions. What they do have in common, though, is the story - you
can play a game without mechanics, but even a primitive dungeon crawl is going to have some story. Managing this story,
its cast, threads, and - if present - mechanical components - is the role of this project.

## Set up instruction

1. Make sure you have the proper stack installed; the current requirements are:
    * PHP 7.2+
    * MySQL 5.6+ database or MariaDB equivalent
1. If you have no composer, install it via instructions from [here](https://getcomposer.org/download/)
1. Clone the project to the desired directory
1. Run `composer install`
    * for a production deployment, add `--no-dev` option to avoid adding unnecessary libraries
1. Copy `.env.example` to `.env` and fill it with configuration data
    * Database access is mandatory; without it, the hub will fail to start
    * Language configuration and key generators can be left with their default valuea
        * change them only if you have a good reason to
    * API key must be set up to make API accessible from outside
    * URIs are needed - without them mailing will fail, and a few redirects may not work
    * mailing data and invitation validity are optional, but their lack will make inviting users impossible
1. [optional] If you wish to add data, create the `console/migrations/data.sql` file with SQL inserts that should be
   loaded into the database; this is intended for development/test work on larger data sets and is not needed for
   normal, initial deployment of a fresh project
1. Initialise the project
    * For development: `./init --env=Development --overwrite=All`
    * For production: `./init --env=Production --overwrite=All`
1. [on empty database] Run `./yii migrate/up`; `data.sql` will be automatically loaded if present
1. [on empty database] Run `./yii rbac/init`; this will set up the access rights for the roles
1. [on empty database] Run `./yii install/add-administrator` to add the administrator user
1. [optional] Set-up cron tasks with content of `scripts/`
1. Access the entry points as needed:
    * `backend/web` for the content management page
    * `frontend/web` for the presentation page

## Upgrading between versions

1. Update the code base from an archive pack or a git tag
    * If you are feeling adventurous, use the `master` branch, but its content is **not** guaranteed to work at all
      times
1. Run `composer install`
1. Ensure your `.env` file is up to date, based on `.env.example`
1. Run migrations with `./yii migrate/up`
1. Run `./yii rbac/v*` sequentially to get up to a proper version
    * Note: there is, to date, no record on which RBAC migration was ran last; running something twice will likely cause
      an error

## Project structure

As partially mentioned at the setup instructions, the project is composed of several modules:

* `frontend` - the presentation and the only part the players should be accessing
* `backend` - the content management system, intended for Game Masters' use
* `console` - purely administrative tools that should not be commonly used except for setup or by `cron` calls
* `common` - components used by other three modules

## Basic functionalities/components

The hub allows handling of the following:

* `Epic` - the basic campaign, containing virtually everything else that is not an user
* `Story` - an adventure, plot, etc. - in other words, a time-limited element of the `Epic`
* `Recap` - a description of events, intended to keep the players up to date
    * It can encompass from one to any number of sessions
* `Game` - a discrete gaming session, often associated with a `Recap`
* `Character` - a character (either a Player Character or an NPC) present in the story, most often described from the
  perspective of the players
* `CharacterSheet` - a detailed set of data on `Character`, usually in a form of numbers and traits
    * This is still a very underdeveloped component, requiring using a dedicated and undocumented data format to display
      properly
    * It is linked to a single `Character`
* `Group` - a group of `Character`s - a party, an organization, or anything that justifies putting a few `Character`s
  together; can have other `Groups` as members as well
* `Scenario` - a plan for events for a `Story`
    * This is the only "large" component that exists solely on the Game Master side and cannot be displayed on the
      presentation/front side
* `Article` - miscellaneous texts
* `PointInTime` - auxiliary information, used to put in-story date/time on descriptions
* `User` - as the name suggests, this is the user, i.e. person accessing the hub; no further explanation should be
  needed

Other components serve only auxiliary roles and are not directly editable.

Use of most components is optional; for example, an `Epic` can be conceivably run with use of `Recap`s and `Game`s only
- or just with `Character` gallery. There are, of course, limits to that - for example,`Group` functionality is limited
without any `Character`s. Still, most elements can be used entirely independently of each other.