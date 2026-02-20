<?php

namespace Mitsuki\Controller;

use Mitsuki\Http\Responses\JsonResponse;
use Mitsuki\Http\Responses\Response;

/**
 * Base controller providing a convenient response helper.
 *
 * This controller can be extended by other controllers to easily create
 * Symfony HttpFoundation responses with a body, status code, and headers.
 *
 * @author Zgeniuscoders <zgeniuscoders@gmail.com>
 */
class BaseController
{
    /**
     * Creates and returns a Symfony Response instance.
     *
     * By default, the Content-Type header is set to 'text/html',
     * which can be overridden by passing a different 'Content-Type' in $headers.
     *
     * @param mixed $body The response content (string, JSON, HTML, etc.).
     * @param int $status The HTTP status code (default: 200).
     * @param array $headers Additional HTTP headers as key-value pairs.
     *
     * @return Response A Mitsuki\Http\Responses\Response instance.
     */
    public function response($body, int $status = 200, array $headers = []): Response
    {
        return new Response($body, $status, headers: array_merge(
            ['Content-Type' => 'text/html'],
            $headers
        ));
    }

    /**
     * Creates and returns a Mitsuki JsonResponse instance.
     * * Automatically serializes the data to JSON and sets the
     * Content-Type header to 'application/json'.
     *
     * @param mixed $data The data to be encoded as JSON (array, object, etc.).
     * @param int $status The HTTP status code (default: 200).
     * @param array $headers Additional HTTP headers as key-value pairs.
     * * @return JsonResponse A Mitsuki\Http\Responses\JsonResponse instance.
     */
    public function json($data, int $status = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, headers: $headers);
    }
}
