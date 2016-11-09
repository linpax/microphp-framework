<?php

namespace Micro\Cli;

use Micro\base\Application;
use Micro\Base\Exception;
use Micro\Cli\Consoles\DefaultConsoleCommand;
use Micro\Web\ResponseInjector;
use Psr\Http\Message\ResponseInterface;


// всё что нужно для CLI
class CliApplication extends Application
{
    /**
     * @return mixed
     */
    public function getResolver()
    {
        return new CliResolver;
    }

    /**
     * Run application with error
     *
     * @param \Exception $e
     * @return ResponseInterface
     * @throws \InvalidArgumentException|\RuntimeException|Exception
     */
    protected function doException(\Exception $e)
    {
        $command = new DefaultConsoleCommand();
        $command->data = '"Error #' . $e->getCode() . ' - ' . $e->getMessage() . '"';
        $command->execute();

        $response = (new ResponseInjector)->build();
        $response = $response->withHeader('status', (string)(int)$command->result); // TODO: hack for select cli stream

        $stream = $response->getBody();
        $stream->write($command->message);

        return $response->withBody($stream);
    }
}