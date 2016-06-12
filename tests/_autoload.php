<?php

/**
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 */

require __DIR__ . '/../base/Autoload.php';

spl_autoload_register(['\Micro\Base\Autoload', 'loader'], true, false);

\Micro\Base\Autoload::setAlias('Micro', __DIR__ . '/..');
