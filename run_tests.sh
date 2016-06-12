#!/usr/bin/bash
cd tests
phpunit --colors --bootstrap=_autoload.php .
cd ..
