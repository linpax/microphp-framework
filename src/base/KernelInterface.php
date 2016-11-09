<?php

namespace Micro\base;


interface KernelInterface
{
    public function loadInjectorsFromCache();

    public function isCli();

    public function isDebug();

    public function getConfig();

    public function getEnvironment();

    public function getStartTime();

    public function getWebDir();

    public function getAppDir();

    public function getCacheDir();

    public function getLogDir();
}