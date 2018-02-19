# Install with Composer

```bash
cd /var/www/html/

curl -OL https://github.com/gpjp-hades/Backend/releases/download/0.3/packages.json

composer create-project --repository packages.json gpjp-hades/backend hades
```

## Requirements

**PHP** 7.1+

**php_sqlite3** (most likely already present)

**libcurl** for PHP 7.10.5+

**Composer** 1.5.0+

### Change default path
If you want to move Hades from ```/hades``` to somewhere else (let's say ```/foo```) run this

```bash
cd /var/www/html/
mv hades foo

vi foo/bar/bootstrap/app.php

# find $config['path'] = "/hades";
# change it to $config['path'] = "/foo";
```

# About
> You seriously think, I'm going to describe the whole app here?
> Ok, fine...

We use the [Slim Framework](https://www.slimframework.com/) for routing and a basic structure around it.

### Here are some important folders
* **app**
  * **controller** *- contains all page controllers*
  * **middleware** *- surprise, surprise, middleware here*
  * **traits** *-just one trait for, mainly for messages Flashing*
  * auth.php *- handles user lifecycle*
  * config.php *- manages global permanent config*
  * default.conf *- should be probably moved to config.php*
  * routes.php *- **router**, quite important...*
* **bootstrap**
  * app.php *- even more important, sets Slim and its modules up*
* **db** *- database.db is created here*
  * seed.php *- seeds database*
* **logs** *- try and guess*
* **public** *- all what user can access*
  * index.php *- starts bootstrap and binds router*
* **templates** *- all HTML is here*
  * **layout** *- only to be included*

The rest you can probably figure out by yourself **:P**
