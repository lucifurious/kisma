That's ok, the `php5enmod` tool only makes symlinks. You can do it manually:

* Make sure you have installed the `memcached` PHP extension (`sudo apt-get install php5-memcached`). This creates the `/etc/php5/mods-available/memcached.ini` file. Check it and make sure it looks kosher.

* Depending on which versions of PHP you have installed, there will be a corresponding directory created under `/etc/php5`. For `php5-cli`, the directory is `/etc/php5/cli`. For PHP FPM, the directory is `/etc/php5/fpm`, etc. Beneath each of these directories is another directory called `conf.d` (i.e. `/etc/php5/cli/conf.d`):<br/>
```
$ dir /etc/php5/
total 36K
drwxr-xr-x   7 root root 4.0K Aug 19 16:34 .
drwxr-xr-x 195 root root  12K Sep  9 13:46 ..
drwxr-xr-x   3 root root 4.0K Aug 19 13:14 cgi                                        <---- php5-cgi
drwxr-xr-x   3 root root 4.0K Aug 19 16:34 cli                                         <---- php5-cli
drwxr-xr-x   4 root root 4.0K Aug 19 16:34 fpm                                       <---- php5-fpm
drwxr-xr-x   2 root root 4.0K Aug 21 16:30 mods-available                     <---- all installed extensions
-rw-r--r--   1 root root   70 Jan  8  2014 mods-available/memcached.ini  <---- The memcached config file
```

* For each of the installed PHP5 runtimes (i.e. cgi, cli, fpm, etc.) in which you wish to have `memcached` available; create a symlink from `/etc/php5/mods-available/memcached.ini` into the `conf.d` directory of that runtime:<br/>
```
$ cd /etc/php5/fpm/conf.d
$ ln -s ../../mods-available/memcached.ini 20-memcached.ini
$ cd ../../cli/conf.d
$ ln -s ../../mods-available/memcached.ini 20-memcached.ini
```<br/>I doubt you're using CGI so I didn't show that in the example. The **20-** priority in front of the file name is inserted/managed by the `php5enmod` and `php5dismod` tools. I put the **20-** in the symlink name so it can be removed with php5dismod, should you find that it is working on your box.

* Restart your server(s):<br/>
```
$ sudo service apache2 restart
$ sudo service nginx restart
$ sudo service php5-fpm restart
```<br/>Obviously, don't restart a server you're not running. You'll get an error.

* Check if it's loaded:<br/>
```
$ php -i | grep memcached
/etc/php5/cli/conf.d/20-memcached.ini,
memcached
memcached support => enabled
libmemcached version => 1.0.18
...
```

At this point, you should be good to go. To fully ensure that the **memcached** stuff is being used, delete any caches that the DSP may have created:

```
$ sudo rm -rf /path/to/your/dsp/storage/.private/app.store /tmp/.dsp* /tmp/.dreamfactory* /tmp/.kc
```

These are all the places where the DSP caches, or has cached in the past, data.

Fire up your browser and go to your DSP, click around and close your browser. Now, look in the directories mentioned above. They shouldn't be there. If they are there, then your memcache isn't working. If they are **not** there, your memcache is working fine.

Let me know if you still need help.