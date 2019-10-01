# gnss_webgis_client
Demo of a leaflet webGIS client to see GNSS data collected with mobile app (e.g. RTKGPS+)


git submodule add https://github.com/perliedman/leaflet-realtime.git

git submodule add https://github.com/twbs/bootstrap.git

git submodule add https://github.com/wenzhixin/bootstrap-table.git


## Step 1 - Restore DB

Use the schema_demo.sql file


## Step 2 - Add .gitignore files

credenziali.py

```
import psycopg2
conn = psycopg2.connect(dbname='XXXX', port=5432, user='XXXX', password='XXXXX', host='localhost')
```


conn.php

```
<?php 
$conn = pg_connect("host=127.0.0.1 port=5432 dbname=XXXX user=XXXX password=XXXX");
if (!$conn) {
        die('Could not connect to DB, please contact the administrator.');
}
else {
        //echo ("Connected to local DB");
}
?>
```

toke_telegram.php
```
<?php
$bot_name="XXXXX";
$token = "XXXXX";
$chatid = array("XXXX","XXXX"); // insert chat id in 
?>
```
## Step 3 Start scocket automnatically at system boot

1. you have to put the correct path in order to launch the script that turns the socket on at the system boot 
change CONF PATH

2) sudo ln -s $CONF/avvio_socket.sh

3) sudo chmod +x /etc/init.d/avvio_socket.sh

4) 
ON DEBIAN update-rc.d avvio_socket.sh defaults
ON RPM chkconfig avvio_socket.sh on
