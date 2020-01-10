# Evaluation

There is no pull request to review, so I'm going to create this markdown file to
discuss some pros and cons and also to ask some questions which I don't
understand.


## Issues

I get:
```
You need to set up the project dependencies using the following commands:
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```
when I run `docker exec uploader /preset.sh`

When I follow those steps, I get the following error message:
```
PDOException: SQLSTATE[HY000] [2002] No such file or directory in /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/PdoAdapter.php:79
Stack trace:
#0 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/PdoAdapter.php(79): PDO->__construct()
#1 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/MysqlAdapter.php(116): Phinx\Db\Adapter\PdoAdapter->createPdoConnection()
#2 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/PdoAdapter.php(148): Phinx\Db\Adapter\MysqlAdapter->connect()
#3 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/PdoAdapter.php(212): Phinx\Db\Adapter\PdoAdapter->getConnection()
#4 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/PdoAdapter.php(231): Phinx\Db\Adapter\PdoAdapter->query()
#5 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/PdoAdapter.php(373): Phinx\Db\Adapter\PdoAdapter->fetchAll()
#6 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/PdoAdapter.php(348): Phinx\Db\Adapter\PdoAdapter->getVersionLog()
#7 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/AdapterWrapper.php(204): Phinx\Db\Adapter\PdoAdapter->getVersions()
#8 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Migration/Manager/Environment.php(278): Phinx\Db\Adapter\AdapterWrapper->getVersions()
#9 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Migration/Manager.php(303): Phinx\Migration\Manager\Environment->getVersions()
#10 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Console/Command/Migrate.php(120): Phinx\Migration\Manager->migrate()
#11 /var/www/html/uploader/project/vendor/symfony/console/Command/Command.php(255): Phinx\Console\Command\Migrate->execute()
#12 /var/www/html/uploader/project/vendor/symfony/console/Application.php(924): Symfony\Component\Console\Command\Command->run()
#13 /var/www/html/uploader/project/vendor/symfony/console/Application.php(265): Symfony\Component\Console\Application->doRunCommand()
#14 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Console/PhinxApplication.php(69): Symfony\Component\Console\Application->doRun()
#15 /var/www/html/uploader/project/vendor/symfony/console/Application.php(141): Phinx\Console\PhinxApplication->doRun()
#16 /var/www/html/uploader/project/vendor/robmorgan/phinx/bin/phinx(28): Symfony\Component\Console\Application->run()
#17 {main}

Next InvalidArgumentException: There was a problem connecting to the database: SQLSTATE[HY000] [2002] No such file or directory in /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/PdoAdapter.php:82
Stack trace:
#0 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/MysqlAdapter.php(116): Phinx\Db\Adapter\PdoAdapter->createPdoConnection()
#1 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/PdoAdapter.php(148): Phinx\Db\Adapter\MysqlAdapter->connect()
#2 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/PdoAdapter.php(212): Phinx\Db\Adapter\PdoAdapter->getConnection()
#3 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/PdoAdapter.php(231): Phinx\Db\Adapter\PdoAdapter->query()
#4 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/PdoAdapter.php(373): Phinx\Db\Adapter\PdoAdapter->fetchAll()
#5 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/PdoAdapter.php(348): Phinx\Db\Adapter\PdoAdapter->getVersionLog()
#6 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Db/Adapter/AdapterWrapper.php(204): Phinx\Db\Adapter\PdoAdapter->getVersions()
#7 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Migration/Manager/Environment.php(278): Phinx\Db\Adapter\AdapterWrapper->getVersions()
#8 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Migration/Manager.php(303): Phinx\Migration\Manager\Environment->getVersions()
#9 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Console/Command/Migrate.php(120): Phinx\Migration\Manager->migrate()
#10 /var/www/html/uploader/project/vendor/symfony/console/Command/Command.php(255): Phinx\Console\Command\Migrate->execute()
#11 /var/www/html/uploader/project/vendor/symfony/console/Application.php(924): Symfony\Component\Console\Command\Command->run()
#12 /var/www/html/uploader/project/vendor/symfony/console/Application.php(265): Symfony\Component\Console\Application->doRunCommand()
#13 /var/www/html/uploader/project/vendor/robmorgan/phinx/src/Phinx/Console/PhinxApplication.php(69): Symfony\Component\Console\Application->doRun()
#14 /var/www/html/uploader/project/vendor/symfony/console/Application.php(141): Phinx\Console\PhinxApplication->doRun()
#15 /var/www/html/uploader/project/vendor/robmorgan/phinx/bin/phinx(28): Symfony\Component\Console\Application->run()
#16 {main}
```

I can access [localhost:8000](http://localhost:8000/) but the health check on [localhost:8000/health-check](http://localhost:8000/health-check) seems broken, I get HTTP 500 error and the message: "This page isn’t working" in Chromium.

Health check on [ws.human-connection.social/health-check](https://ws.human-connection.social/health-check)
responds with 404 error.


## How it works

As far as I can tell or as far as I can assume, this service acts like a proxy
for different resolutions of an image. As a backend service you would upload the
file to the service using a token. You need the token to request a JWT and then
you use that JWT to authorize the call to `POST /file`. The image service either
stores the original file on disk or can store the file on S3 compliant object
storage. It will probably return some file information, ie. the URL which you
can save with the post or user profile. This URL starts with
`/cdn/thumbnail/by-url` and can be put behind a CDN which we have to setup
ourself.

### Open Questions
* When I look into [docker-compose.yml](./docker-compose.yml) I see `mysql` and
  `phpmyadmin`. This adds a couple of dependencies to our kubernetes setup. Why
  do we need those extra dependencies at all? Is it to keep a record of cached
  file locations?
* I cannot find the place where I can set the token which is used to acquire a
  valid JWT when I request `/auth`. How can I request a valid JWT token on
  [ws.human-connection.social/api/docs](https://ws.human-connection.social/api/docs)?
  I would like to upload a file through the swagger web interface. 
* Are requests to `/cdn/thumbnail/by-url` cached in some way? Like, are the
  thumbnails saved on disk?


## Evaluation
Comparison of the two approaches that I know:

| PHP Microservice | Hosted (Digital Ocean Spaces only) | 
|------------------|------------------------------------|
| converts images to any requested resolution | we need to agree on a fixed set of file resolutions in advance |
| we add PHP to our code base | JS only |
| we implement JWT authentication twice | S3 credentials only |
| we add extra services on kubernetes (mysql, phpmyadmin) | no self-hosted services |
| ImageMagick for image resizing | sharp uses libvips - [they claim it's faster](https://github.com/lovell/sharp#sharp) |
| we have to configure a CDN on our own | Digital Ocean's CDN out of the box|

