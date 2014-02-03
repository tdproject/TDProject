# About

TDProject is a tool originially designed for time and for 
project management purposes. During the time the tool has
been extended with a Jasper Reports interface and a PEAR
channel server.

# Installation

Download and extract ```tdproject-latest.tgz``` file from our
GitHub releases page. To do this, open your command line and 
enter:

```
$ curl -O http://www.github.com/tdproject/tdproject-latest.tgz
$ tar xvfz tdproject-latest.tgz
```

After that initialize the internal PEAR repository with:

```
$ cd tdproject-{VERSION}
$ chmod +x bin/webapp
$ chmod +x appserver
$ bin/webapp setup
```

The final step is to create the datase schema, what can be done on 
commandline only. TDProject will be delivered with an ```install.php``` 
that allows you to specify your database credentials. The following
parameters *has* to be specified: 

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