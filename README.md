
# The Movie Review Webapp

This is the webapp used for the course Software Security TDT4237
at NTNU. Used for the first time in September 2014.
Below is the guide to fetch code and deploy it so that the app
can be browsed at [http://localhost:8080/](http://localhost:8080/).

## git

Git is a version control system used for files.
[Install git](http://git-scm.com/download).
To authenticate yourself without password, 
use [asymmetric crypto](https://help.github.com/articles/generating-ssh-keys).

One group member should clone the repo, remove .git folder and push to new repo.
Then the rest of the group members
should be added as collaborators to that repo. This way the group
can use git and github and synchronize code changes.

    ssh-keygen
    # go to github.com and create a new repo called my-new-repo. Mark it "private".
    git clone git@github.com:TDT4237/moviereviews # assumes public key installed on github
    cd moviereviews
    rm -rf .git
    git init
    git config --global user.email "you@example.com"
    git config --global user.name "Your Name"
    git add .
    git commit -m'first commit'
    git remote add origin git@github.com:<github username>/my-new-repo.git
    git push -u origin master

Windows users can use git from Git Bash, which is a terminal that
is bundled with git.

## PHP

### Windows

[install php](http://windows.php.net/download/).
Fetch the "VC11 x64 Non Thread Safe" (64 bit) or
"VC11 x86 Non Thread Safe" (32 bit) zip file.

Append the location of the PHP executable to the
[PATH environment variable](https://stackoverflow.com/questions/17727436/how-to-properly-set-php-environment-variable-to-run-commands-in-git-bash).
Restart terminal so the new PATH is sourced.

Check reach-ability of interpreter with `php -v`.

Copy `php.ini-production` to `php.ini`. Enable openssl by removing leading `;`
from `;extension=php_openssl.dll`. Set `extension_dir` to `ext`.
Enable the `php_pdo_sqlite.dll` extension.

### Linux

    apt-get install php5-cli // debian/ubuntu
    pacman -Syu php // archlinux

### OS X
If you have OS X Mavericks (10.9), then you already have all that you need.

If you have OS X Mountain Lion (10.8) or earlier, then you'll have to get PHP 5.4,
there are a few options for doing this, we'll cover HomeBrew and MacPorts:

Both:
    Install XCode (Available for free through the App Store, requires registration for download)
    Install XCode's Command Line Tools. (Should be available from within XCode's preferences)

MacPorts:
    [Installing MacPorts](https://www.macports.org/install.php)
	sudo port install php56 php56-openssl php56-sqlite
HomeBrew:
    [Installing HomeBrew](http://brew.sh)
	brew doctor
	brew tap homebrew/versions
	brew install php56

## composer

Composer is a dependency manager for PHP.
[Install composer](https://getcomposer.org/doc/00-intro.md).

    curl -sS https://getcomposer.org/installer | php

Install dependencies with `php composer.phar install`.

## Sqlite3

This is the database. It is a PHP module usually packaged as a separate
package in package managers.

    apt-get install php5-sqlite sqlite3 // debian/ubuntu
    pacman -Syu php-sqlite // archlinux

Create SQL tables and fill data with `php composer.phar run-script up`.
Inspect db with `sqlite3 app.db`.
To list all tables run `.tables`. To describe a single table by name run
 `.dump users`. For nicer layout run`.mode column` and `.headers on`.

To select users from the `users` table run

    select * from users LIMIT 10;

Delete all tables with `php composer.phar run-script down`.

## PHP's built-in HTTP server

Webapps are usually deployed with Apache or nginx. But for development
and testing there is also the built-in HTTP server. Let's use it.  
As of PHP 5.4.0, the CLI SAPI provides a 
[built-in web server](http://php.net/manual/en/features.commandline.webserver.php).

Start the built-in server by running `php -S localhost:8080 -t web web/index.php`.

The file argument is the router front end. All requests go through
the router. The -t option specifies the
DocumentRoot. Images, css, and javascript files go there.

The webapp can be browsed at [http://localhost:8080/](http://localhost:8080/).
For deployment such that the internet can reach your server run
`php -S 0.0.0.0:8080 -t web web/index.php`.

## The code base

Learn some PHP syntax with [code academy](http://www.codecademy.com/en/tracks/php).

The project is built upon a lightweight framework called
[Slim](http://docs.slimframework.com/).

The
[Twig](http://twig.sensiolabs.org/doc/templates.html)
template language is used.

Write [nice php code](http://www.phptherightway.com/).

[PHP is much better than you think](http://fabien.potencier.org/article/64/php-is-much-better-than-you-think).

## Troubleshooting and gotchas

On the course run server, we are _not_ running the PHP dev server explained here, as that is only
meant for development use. Instead we run Apache/2.4.7 (Ubuntu) PHP/5.5.9-1ubuntu4.4, where we
host the subfolder web/ as DocumentRoot. This also means that unlike the repository code, app.db resides
inside web/ on the server for the duration of Exercise 1. Those interested in exactly mimicking the
behavior of the server should start with Ubuntu Trusty to get as close to our setup as possible.

Beware that your code WILL have to run on this setup when you deliver it.

### Twig

When you access any field of a class in twig with e.g. `movie.name` it is internally
translated to `$movie->getName()`. So simply create that function.

### PHP

Subclasses do not automatically call parent constructor. Call manually with

    parent::__construct();
