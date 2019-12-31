<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\PubSubHubbub;

interface CallbackInterface
{
    /**
     * Handle any callback from a Hub Server responding to a subscription or
     * unsubscription request. This should be the Hub Server confirming the
     * the request prior to taking action on it.
     *
     * @param array $httpData GET/POST data if available and not in $_GET/POST
     * @param bool $sendResponseNow Whether to send response now or when asked
     */
    public function handle(array $httpData = null, $sendResponseNow = false);

    /**
     * Send the response, including all headers.
     * If you wish to handle this via Laminas_Controller, use the getter methods
     * to retrieve any data needed to be set on your HTTP Response object, or
     * simply give this object the HTTP Response instance to work with for you!
     *
     * @return void
     */
    public function sendResponse();

    /**
     * An instance of a class handling Http Responses. This is implemented in
     * Laminas_Feed_Pubsubhubbub_HttpResponse which shares an unenforced interface with
     * (i.e. not inherited from) Laminas_Controller_Response_Http.
     *
     * @param HttpResponse|\Laminas\Http\PhpEnvironment\Response $httpResponse
     */
    public function setHttpResponse($httpResponse);

    /**
     * An instance of a class handling Http Responses. This is implemented in
     * Laminas_Feed_Pubsubhubbub_HttpResponse which shares an unenforced interface with
     * (i.e. not inherited from) Laminas_Controller_Response_Http.
     *
     * @return HttpResponse|\Laminas\Http\PhpEnvironment\Response
     */
    public function getHttpResponse();
}
