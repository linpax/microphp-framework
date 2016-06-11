<?php

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = include __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('Micro', __DIR__ . '/../');