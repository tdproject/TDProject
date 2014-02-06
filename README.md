# About

TDProject is a tool originially designed for time and for 
project management purposes. During the time the tool has
been extended with a Jasper Reports interface and a PEAR
channel server.

# Installation

Download and extract ```tdproject-1.1.tar.gz``` file from our
GitHub releases page. To do this, open your command line and 
enter:

```
$ curl -O https://github.com/tdproject/TDProject/releases/download/1.1/tdproject-1.1.tar.gz
$ tar xvfz tdproject-latest.tgz
```

After that initialize the internal PEAR repository with:

```
$ cd tdproject-{VERSION}
$ chmod +x bin/webapp
$ bin/webapp setup
```

The final step is to create the datase schema, what can be done on 
commandline only. Prior running the installation script you have to
create the database itself. There you can open a command line and
type:

```
$ mysql -uroot -p{ROOTPASSWORD}
mysql> create database tdproject;
mysql> grant all on tdproject.* to "{DB_USER}"@"localhost" identified by "{DB_PASSWORD}";
mysql> flush privileges;
mysql> quit;
```

TDProject will be delivered with an ```install.php``` that allows you 
to specify your database credentials. The following parameters *has* to 
be specified: 

* ```method```:		the setup method ```install``` or ```update```
* ```db_name```:	the database name to use
* ```db_host```:	the database host to use
* ```db_user```:	the database user to use
* ```db_pass```:	the database password to use
* ```db_charset```:	the database charset to use

```
$ php -f install.php -- --method=install --db_host=127.0.0.1 --db_charset=utf8 --db_name={DB_NAME} --db_user={DB_USER} --db_pass={DB_PASSWORD}
```

After installation you can open a browser, enter the URL you've
installed TDProject, e. g. ```http://127.0.0.1/tdproject``` and 
login as ```admin``` or ```guest```. Both accounts has 
```tdproject``` as default password.
