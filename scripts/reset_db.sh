#!/bin/bash
mysql -uroot -e 'drop database zendump;'
mysql -uroot -e 'create database zendump;'
php vendor/bin/phinx migrate -c phinx-config.php
