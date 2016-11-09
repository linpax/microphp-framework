<?php

namespace Micro\Mvc;

use Micro\Base\Application;
use Micro\Base\Injector;
use Micro\base\ResolverInterface;
use Micro\Web\ResponseInjector;
use Psr\Http\Message\ResponseInterface;

// всё что нужно для MVC
class MvcApplication extends Application
{
    /**
     * @return ResolverInterface
     */
    public function getResolver()
    {
        return new MvcResolver;
    }

    /**
     * @param ResponseInterface $response
     */
    public function send(ResponseInterface $response)
    {
        header('HTTP/' . $response->getProtocolVersion() . ' ' . $response->getStatusCode() . ' ' . $response->getReasonPhrase());

        foreach ($response->getHeaders() as $header => $values) {
            header($header . ': ' . implode(', ', $values));
        }

        parent::send($response);
    }

    /**
     * Do exception
     *
     * @access private
     *
     * @param \Exception $e Exception
     *
     * @return ResponseInterface
     * @throws \Micro\Base\Exception
     */
    protected function doException(\Exception $e)
    {
        $output = (new ResponseInjector)->build();

        $errorController = (new Injector)->param('errorController');
        $errorAction = (new Injector)->param('errorAction');

        if (!$errorController || !$errorAction) { // render SAPI error not configured
            $stream = $output->getBody();
            $stream->write('Option `errorController` or `errorAction` not configured');

            return $output->withBody($stream);
        }

        // Render SAPI error
        $_POST['error'] = $e;

        $controller = $errorController;

        /** @var \Micro\Mvc\Controllers\IController $result */
        $result = new $controller(false);
        $result = $result->action($errorAction);

        if ($result instanceof ResponseInterface) {
            return $result;
        }

        $stream = $output->getBody();
        $stream->write((string)$result);

        return $output->withBody($stream);
    }
}