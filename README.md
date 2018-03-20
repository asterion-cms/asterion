# Asterion
*Asterion is a simple multilingual web framework designed for developers.*
*It is written mainly in PHP and built over the LAMP environment.*

<br/>

One of the main problems that web developers confront is to create completely customized multilingual websites in a short period of time. With the market **Content Management Systems (CMS)** we get a jungle of modules and plugins that had to be customized and updated all the time. We had no control over the **HTML** or **JavaScript** and we have to spend hours checking on forums to solve simple problems.

So, we created a simple tool that can let developers code freely.

The philosophy behind **Asterion** is to concentrate every resource that a website has. The idea is to use just one main template, one **CSS** file, one **JavaScript** file and only one navigation control where all the main code execution happens. This way you won't have to search in multiple folders for the correct place to change the color, position or size of a certain element.

This framework is not designed for direct customers, it is designed for web developers that are skilled in **PHP**, **HTML**, **CSS** and **MySQL**.

<br/>
<br/>
<br/>
<br/>

## Installation

Asterion works in **PHP** *(version 5.3 with the PDO-MySQL extension)* and a **MySQL** database.

You must have a proper **Apache server** running or either **MAMP** (http://www.mamp.info) or **EasyPhp** (http://www.easyphp.org) to run the framework.

<br/>
<br/>

### Installing a new project

To install a new project from scratch, follow these steps:

##### 1. Download the Asterion files and put them in your web server.

You should put all the Asterion files on your local folder or webserver in a file like:

```php
/path_to_my_server_folder/public_html/
/path_to_my_server_folder/www/
/path_to_my_server_folder/htdocs/
```

You will then have two addresses. The local one that will be accessible through a file manager:

```php
/path_to_my_server_folder/public_html/my_asterion_website/
```

And the public one that will be accessible using a web browser:

```php
http://localhost/my_asterion_website/
```

##### 2. Create a MySQL database in your server or your emulator.

We do not offer an installation script, so you'll have to create the empty database via **phpMyAdmin** or the console's mysql command. In this case we need the following information:

* The **name** of the database.
* The **username** to connect to the database.
* The **password** to connect to the database.
* The **prefix** of the tables, in case that you will use the same database for multiple websites.
* Eventually, the **port** to connect to the database, by default is 3306.

##### 3. Configure Asterion.
To configure the framework, you should open the configure file located in:

```php
path_to_my_site/base/config/config.php
```

And edit the following lines:

The **TITLE** constant defines the title of your page, it will be always editable in the future.
```php
define('TITLE', 'Base Site');
```

The **SERVER_URL** constant must point to the public base access. If you are developing it should be **http://localhost**, **http://128.0.0.1** or **http://localhost:8888** depending on your server's configuration.
```php
define('SERVER_URL', 'http://localhost');
```

The **BASE_STRING** is the name of folder where you downloaded your website.
```php
define('BASE_STRING','/asterion/');
```

The idea behind the **SERVER_URL** and the **BASE_STRING** constants is that they help to build the most important **LOCAL_URL** and **LOCAL_FILE** constants. The **LOCAL_URL** constant must point to the public website and **LOCAL_FILE** constant must point to the server’s file system, as described in the first step of the installation.
```php
define('LOCAL_URL', SERVER_URL.BASE_STRING);
define('LOCAL_FILE', $_SERVER['DOCUMENT_ROOT'].BASE_STRING);
```

The **DEBUG** constant must be true if you are developing the website. It must be false when you are ready to publish it in your web server.
```php
define('DEBUG', true);
```

We have translated our framework in English, Spanish and French but it can be easily translated to other languages in the future. You can change the value depending on the languages that you need for your website. For example, if it will be just in English just put en, if you want it in French and Spanish put fr:es.
```php
define('LANGS', 'en:es:fr');
```

The **DB_** constants are used to configure the MySQL database. Asterion needs to know the server, user, password and port to connect to the database. It also needs the database name and eventually a prefix for the tables.
```php
define('DB_SERVER', 'localhost');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASSWORD', '');
define('DB_PORT', '3306');
define('DB_PREFIX', 'ast_');
```

The **EMAIL** constant is the main email address, **Asterion** will use it to create the first user and to manage the general communications.
```php
define('EMAIL', 'info@asterion.org');
```

##### 4. Test your installation.
You should go to your browser and test the landing page that should be on:

```
http://localhost/asterion
```

Usually you should see a simple page. You also have the BackEnd administration system by default on:

```php
http://localhost/asterion/admin
```

To connect you have to use the email defined in the configuration file. The initial password is *asterion*. For security reasons you should change it on your first connection.

<br/>
<br/>

### Migration of an Asterion website to production

Asterion is very flexible when dealing with migration to a new server or to another location. First of all it doesn't encode any information in the database except for the passwords and it uses a simple data structure that is completely human readable.

To migrate an Asterion website you have to follow these steps:

##### 1. Migrate the files manually.
First of all, you should save a copy of all the files. Then, you should either upload them to the production server or to the new folder where you want your site to work.

##### 2. Migrate the database manually.
Given the fact that Asterion does not provide an installation script, you should dump the actual database and create a new one on your server to import the data. If you are just moving the location of the files you do not need this step since the connection to the database remains the same.

##### 3. Re-configure your site.
Since you migrated the site, there are some lines in the configuration file to update. Firstly, you should open the configuration file in:

```php
path_to_the_new_location_of_my_site/base/config/config.php
```

And edit the following lines:

The **SERVER_URL** constant must change to the new public access. If you are migrating to a production server you should put the correct URL here that should be something like **http://www.my-website.com**.
```php
define('SERVER_URL', 'http://www.my-website.com');
```

The **BASE_STRING** must also change to the new location. If you are in production mode you should just put a slash **/** because usually you just have one website in each domain.
```php
define('BASE_STRING','/');
```

Now that we are in a production mode, keep the **DEBUG** to false to make it cleaner for the users.
```php
define('DEBUG', false);
```

You should also change the **DB_** constants to have access to the new **MySQL** database.
```php
define('DB_SERVER', 'localhost');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASSWORD', '');
define('DB_PORT', '3306');
define('DB_PREFIX', 'ast_');
```

##### 4. Test your website.
Now, you should go to your browser and test your landing page that should be on:

```php
http://www.my-website.com
```

Usually you should see a correct copy of your website. You also have access to the **BackEnd** administration system by default:

```php
http://www.my-website.com/admin
```

<br/>
<br/>
<br/>
<br/>

## Installation troubleshoot

If you don't see the simple page or you get an error message when testing your website, you can check the common errors in this section. First of all, check for the obvious, which means that your server is running. Also check that that you can see the errors configuring your **Apache web server** or emulator.

If you don't find the error here, you should contact us directly to get a solution.

##### Solution to errors of the database connection
One of the most common errors is that the website cannot connect to the database.

In order to solve this problem, first you should check if your database is running. You should have general access through a manager like **PHPMyAdmin** or **SQLManager**.

Then, you should check if your database has access from the public address like:

```php
http://localhost/asterion
```

Some databases have blocked access from certain URLs or they need a special port. Finally, check if the following variables in the config file are correct:

```php
define('DB_SERVER', 'localhost');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASSWORD', '');
define('DB_PORT', '3306');
```

##### Solution to errors of your file system
As stated in the first installation step, the most important thing to configure are the two constants that point to your website:

```php
define('LOCAL_URL', SERVER_URL.BASE_STRING);
define('LOCAL_FILE', $_SERVER['DOCUMENT_ROOT'].BASE_STRING);
```

Both must point to the public and local work directory. If you get some errors you can force the configuration of these variables like:

```php
define('LOCAL_URL', 'http://localhost/asterion/');
define('LOCAL_FILE', '/home/path_to_my_website/public_htm/asterion/');
```

A common error is to forget the slash **/** at the end of both paths.

##### You don't see anything or you get an HTTP 500 error

If you get a complete white page or you get the **HTTP 500 Internal server error**, it means that there is something wrong with the configuration of your server. Usually this kind of errors comes from the server configuration and not from Asterion.

To solve them, first check if your server works correctly. You can create another website with just a simple **index.php** file on it and see if the server is working. If the problem concerns only the **Asterion** websites, you can try to:

* Check if the permissions on the **index.php** and the **.htaccess** files are **644**. Both are the most sensitive files to run the website, some servers tend to be very strict with security measure of those two files.
* Check if your server accepts **.htaccess** files. If it doesn't, configure it to accept this file because **Asterion** needs it to do the URL redirections.
* Check if you have the permissions on your server to do URL redirections in your **.htaccess** file. The most important lines in that file are:

```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
</IfModule>
```

Those mean that any URL on your website will be treated by the index.php file, so it is of main importance that your server or emulator can run **.htaccess** files.
