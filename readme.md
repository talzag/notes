# Notes

This is a simple, browser-based means of creating and storing Notes.
We believe that a user should be able to start recording their thoughts
and ideas with a minimal amount of interaction. With this application,
you can start creating notes instantly, and worry about what you need
it for later on.

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

# Contributing

Please submit all issues and pull requests to the [tomasienrbc/notes](http://github.com/tomasienrbc/notes) repository!

## Set up the project on a local machine

1. Clone the repo to your local machine with: ``git clone https://github.com/tomasienrbc/notes``.

2. Install the project's dependencies with ``php composer.phar install`` or ``composer install``. If you don't already have composer installed, get it here: https://getcomposer.org/download/

Sometimes installing Laravel is a problem because it requires php-mcrypt and for some reason that doesn't work. Google the problem as it comes up, but often Homebrew can fix it: brew install mcrypt php56-mcrypt

3. Create your local MySQL database (use MAMP's PHPMyAdmin if you want a shortcut). Make sure the database name, username, and password match the information in app/config/database.php in the "mysql" section

4. Run the migrations with ``php artisan migrate``. If you want/need database seed data, run php artisan db:seed

5. Rename the `.env.example` file to `.env` and modify or uncomment whichever configuration settings you would like to dynamically control.

6. Start a local server with ``php artisan serve`` or set your MAMP server to serve from the "public" folder and get developing!
