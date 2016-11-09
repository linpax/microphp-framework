<?php /** MicroApplicationInterface */

namespace Micro\base;

use Psr\Http\Message\ResponseInterface;


interface ApplicationInterface
{
    /**
     * Send response to client
     *
     * @param ResponseInterface $response
     */
    public function send(ResponseInterface $response);

    /**
     * Terminate application
     */
    public function terminate();

    /**
     * @return ResolverInterface
     */
    public function getResolver();
}