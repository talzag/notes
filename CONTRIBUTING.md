# Contribution Guidelines

Please submit all issues and pull requests to the [tomasienrbc/notes](http://github.com/tomasienrbc/notes) repository!

## Set up the project on a local machine

1. Clone the repo to your local machine with: ``git clone https://github.com/tomasienrbc/notes``.

2. Install the project's dependencies with ``php composer.phar install`` or ``composer install``. If you don't already have composer installed, get it here: https://getcomposer.org/download/

3. Create your local MySQL database, and run the migrations with ``php artisan migrate``.

4. Rename the `.env.example` file to `.env` and modify or uncomment whichever configuration settings you would like to dynamically control.

5. Start a local server with ``php artisan serve`` and get developing!
