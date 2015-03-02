# Notes

This is a simple, browser-based means of creating and storing Notes.
We believe that a user should be able to start recording their thoughts
and ideas with a minimal amount of interaction. With this application,
you can start creating notes instantly, and worry about what you need
it for later on.

# Contributing

## Set up the Project

1. Clone the repo to your local machine with: ``git clone https://github.com/tomasienrbc/notes``.

2. Install the project's dependencies with ``php composer.phar install`` or ``composer install``. If you don't already have composer installed, get it here: https://getcomposer.org/download/

3. Create your local MySQL database, and run the migrations with ``php artisan migrate``.

4. Start a local server with ``php artisan serve`` and get developing!

# Core Features

## Current Features

- Create a note
- View all notes
- Edit notes
- Make notes public/private 

## Desired Features

- vanity/custom urls for public notes
- Auto-saving
- Group/real-time editing (maybe - I prefer to use extensions for this though but if someone can tackle easily it would be cool)

# Extensions

## Current Extensions

- **Google Doc:** turn any note into a google doc (in development)
    - Sync a google account
    - command + g => choose an account => receive back link to the doc. Magic!

- **Slack:** turn any note into a google doc (in development)
    - Post a formatted version of a blank slate in slack w/link. 
    - Ideally "team" permissions for everyone in your slack org to edit the doc would be pretty interesting

## Desired Extensions

- **To-do lists:** markdown lists become interactive to-do lists 
    - Click to cross things off
    - Ideally schedule reminders and other to-do list-y things
- **Blogging:** export to various blogging/micro-blogging platforma
    - Wordpress
    - Twitter
    - Tumblr (if possible)
    