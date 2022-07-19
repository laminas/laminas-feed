<?php

declare(strict_types=1);

namespace Laminas\Feed\PubSubHubbub;

use Laminas\Http\PhpEnvironment\Response;

interface CallbackInterface
{
    /**
     * Handle any callback from a Hub Server responding to a subscription or
     * unsubscription request. This should be the Hub Server confirming the
     * the request prior to taking action on it.
     *
     * @param null|array $httpData GET/POST data if available and not in $_GET/POST
     * @param bool $sendResponseNow Whether to send response now or when asked
     */
    public function handle(?array $httpData = null, $sendResponseNow = false);

    /**
     * Send the response, including all headers.
     * If you wish to handle this via Laminas\Mvc\Controller, use the getter methods
     * to retrieve any data needed to be set on your HTTP Response object, or
     * simply give this object the HTTP Response instance to work with for you!
     *
     * @return void
     */
    public function sendResponse();

    /**
     * An instance of a class handling Http Responses. This is implemented in
     * Laminas\Feed\Pubsubhubbub\HttpResponse which shares an unenforced interface with
     * (i.e. not inherited from) Laminas\Feed\Pubsubhubbub\AbstractCallback.
     *
     * @param HttpResponse|Response $httpResponse
     */
    public function setHttpResponse($httpResponse);

    /**
     * An instance of a class handling Http Responses. This is implemented in
     * Laminas\Feed\Pubsubhubbub\HttpResponse which shares an unenforced interface with
     * (i.e. not inherited from) Laminas\Feed\Pubsubhubbub\AbstractCallback.
     *
     * @return HttpResponse|Response
     */
    public function getHttpResponse();
}
