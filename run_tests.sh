#!/bin/sh

# @link https://github.com/linpax/microphp-framework
# @copyright Copyright (c) 2013 Oleg Lunegov
# @license https://github.com/linpax/microphp-framework/blob/master/LICENSE

cd tests
phpunit --colors --bootstrap=_autoload.php .
cd ..
