<?php

/**
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2016 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 */

require __DIR__ . '/../src/base/Autoload.php';

spl_autoload_register(['\Micro\Base\Autoload', 'loader'], true, false);

\Micro\Base\Autoload::setAlias('Micro', __DIR__ . '/../src');
\Micro\Base\Autoload::setAlias('Micro\Tests', __DIR__);
