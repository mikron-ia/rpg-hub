# RPG hub

This project is a system for role-playing game story/campaign/epic management.

## Licensing

The project's main license is `GNU General Public License v3.0` - see the `LICENSE.md` file for the text. The `yii2`
framework is `BSD 3-Clause "New" or "Revised" License`; particular libraries can have their own licenses, see
attribution section of this file for details.

## Project state

The project is being still maintained and (occasionally) developed since it is quite useful for me in its main purpose
(keeping the campaign data for my own purposes), but due to its age (started in 2016), state of the `yii2` framework
(effectively limited to maintenance), and overall effort that would be required to bring the codebase to a decent
standard (immense and not very useful for practical purposes), it is firmly in its legacy stage.

## Background

Role-playing games are a very wide concept that ranges from very simplistic systems that fit on one page up to
extremely complex systems spanning hundreds of books; see
the [Wikipedia definition](https://en.wikipedia.org/wiki/Role-playing_game) for details. What they do have in common,
though, is the presence of a story - one can play a game without mechanics, but even a simple dungeon crawl is going to
have at some story. Managing this story, its cast, threads, and mechanical components is the main role of this project.

The project itself was originally thought of as a learning experience and a coding exercise. Unfortunately, due to
changes in the technological landscape (primarily change in popularity of frameworks and shifting design philosophies)
can no longer fulfill those goals. It is now maintained mostly for its practical uses and minor coding experiments.

## Set up instructions

1. Make sure you have the proper stack installed; the current requirements are:
    - PHP 8.2+
        - Since there are no components from 8.2 used so far, the current code will run on 8.1 as well with minimal
          tweaking -- but there is no guarantee that this behavior will be maintained
    - MySQL 5.6+ database or MariaDB equivalent
        - The current project was tested and found working up to MySQL 8.0 and MariaDB 10.6
1. If you have no composer, install it via [instructions from here](https://getcomposer.org/download/)
1. Clone the project into the desired directory
1. Run `composer install`
    - for a production deployment, add `--no-dev` option to avoid adding unnecessary libraries
1. Copy `.env.example` to `.env` and fill it with configuration data
    - Database access data is mandatory -- without that the hub will fail to start
    - Language configuration and key generators can be left on their default values -- change them only if you have a
      good reason to
    - API key must be set up to make API accessible from outside and can be ignored if API is not used
    - the URIs are needed -- without them the mailing will fail and a few redirects may not work
    - mailing data and invitation validity are optional but their lack will make inviting users via e-mail impossible
1. [optional] If you wish to add data, create the `console/migrations/data.sql` file with SQL inserts that should be
   loaded into the database; this is intended for development/test work on larger data sets and is not needed for
   normal, initial deployment of a fresh project
1. Initialise the project
    - For development: `./init --env=Development --overwrite=All`
    - For production: `./init --env=Production --overwrite=All`
1. [on an empty database] Run `./yii migrate/up`; `data.sql` will be automatically loaded if present
1. [on an empty database] Run `./yii rbac/init`; this will set up the access rights for the roles
1. [on an empty database] Run `./yii install/add-administrator` to add the administrator user
1. [optional] Set-up cron tasks in your system with the content of `scripts/`
1. Access the entry points as needed:
    - `backend/web` for the content management page
    - `frontend/web` for the presentation page

## Upgrading between versions

1. Update the code base from an archive pack or a git tag
    - If you are feeling adventurous, use the `master` branch, but its content, while usually free of breaking bugs, is
      not guaranteed to work at all times; to be safe, use the latest release
1. Run `composer install`
1. Ensure your `.env` file is up to date, based on `.env.example`
1. Run migrations with `./yii migrate/up`
1. Run `./yii rbac/v*` sequentially to get up to a proper version
   - Note: there is, to date, no record on which RBAC migration was run last; running any of those "migrations" twice
      will cause an error - it will not damage anything, though, just break the execution

## Project structure

As partially mentioned at the setup instructions, the project is composed of several modules:

- `frontend` - the presentation and the only part the players should be accessing
- `backend` - the content management system, intended for Game Masters' use
- `console` - purely administrative tools that should not be commonly used except for setup or by `cron` calls
- `common` - components used by other three modules

## Basic functionalities/components

The hub allows handling of the following:

- `Epic` - the basic container, representing the campaign / epic and containing virtually everything else that is not an
  user
- `Story` - an adventure, plot, etc. - in other words, a time-limited element of the `Epic`
- `Recap` - a description of events, intended to keep the players up to date
    - It can encompass any number of sessions
- `Game` - a discrete gaming session, often associated with a `Recap`
- `Character` - a character (either a Player Character or an NPC) present in the story, most often described from the
  perspective of the players
- `CharacterSheet` - a detailed set of mechanical data on `Character`, usually in a form of numbers and traits
    - This is still a very underdeveloped component, requiring using a dedicated and undocumented data format to display
      properly
    - It is linked to a single `Character` from a list of `Character`s that have it set as their sheet
- `Group` - a group of `Character`s - a party, an organization, or anything that justifies putting a few `Character`s
  together; can have other `Groups` as members as well
- `Scenario` - a plan for events for a `Story`
    - This is the only "large" component that exists solely on the Game Master side and cannot be displayed on the
      presentation/front side
- `Article` - miscellaneous texts
- `Announcement` - news, information, and other OOC updates directed at users
- `PointInTime` - auxiliary information, used to put in-story date/time on descriptions
- `User` - as the name suggests, this is the user, i.e. person accessing the hub; no further explanation should be
  needed

All the other components serve auxiliary roles only and are not directly editable.

Use of most components is optional; for example, an `Epic` can be conceivably run with the use of `Recap`s and `Game`s
only or just with `Character` gallery. There are, of course, limits to that - for example, the `Group` functionality is
limited (but not unusable) without any `Character`s. Still, most elements can be used entirely independently of each
other.

## Attributions

What follows is a list of libraries used in the project; only those included directly are listed.

All the following libraries are licensed under `BSD-3-Clause license` or a derivative, unless stated otherwise.

- [The yii framework](https://github.com/yiisoft/yii2) is the basis of this project
    - this includes [yii2-bootstrap](https://github.com/yiisoft/yii2-bootstrap),
      [yii2-swiftmailer](https://github.com/yiisoft/yii2-swiftmailer),
      [yii2-debug](https://github.com/yiisoft/yii2-debug), and [yii2-gii](https://github.com/yiisoft/yii2-gii)
- Tools made by [Kartik Visweswaran](https://github.com/kartik-v/)
    - those include [yii2-krajee-base](https://github.com/kartik-v/yii2-krajee-base),
      [yii2-widget-select2](https://github.com/kartik-v/yii2-widget-select2),
      [yii2-password](https://github.com/kartik-v/yii2-password),
      [strength-meter](https://github.com/kartik-v/strength-meter),
      and [yii2-widget-datepicker](https://github.com/kartik-v/yii2-widget-datepicker)
- [phpunit](https://github.com/sebastianbergmann/phpunit)
- [phpdotenv](https://github.com/vlucas/phpdotenv)
