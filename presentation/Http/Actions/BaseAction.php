<?php
declare(strict_types=1);

namespace Presentation\Http\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use JsonException;
use RuntimeException;

abstract class BaseAction
{
    private const CODE_WRONG_ARGS     = 'GEN-FUBARGS';
    private const CODE_NOT_FOUND      = 'GEN-LIKETHEWIND';
    private const CODE_INTERNAL_ERROR = 'GEN-AAAGGH';
    private const CODE_UNAUTHORIZED   = 'GEN-MAYBGTFO';
    private const CODE_FORBIDDEN      = 'GEN-GTFO';
    private const CODE_SUCCESS        = 'GEN-OK';
    private const CODE_CREATED        = 'GEN-CREATED';

    public int $statusCode = 200;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function respondWithSuccess($message = 'Ok'): JsonResponse
    {
        return $this->setStatusCode(200)
            ->respondWithArray(
                [
                    'code'      => self::CODE_SUCCESS,
                    'http_code' => 200,
                    'message'   => $message,
                ]
            );
    }

    public function respondWithArray(array $array, array $headers = []): JsonResponse
    {
        return Response::json($array, $this->statusCode, $headers);
    }

    public function respondWithMessage($message = 'Ok'): JsonResponse
    {
        return $this->respondWithArray(
            [
                'code'      => self::CODE_SUCCESS,
                'http_code' => $this->statusCode,
                'message'   => $message,
            ]
        );
    }

    public function respondWithItem(array $array, array $headers = []): JsonResponse
    {
        return Response::json($array, $this->statusCode, $headers);
    }

    public function respondWithCollection(array $array, array $headers = []): JsonResponse
    {
        return Response::json($array, $this->statusCode, $headers);
    }

    public function respondWithCreated($message = 'Created'): JsonResponse
    {
        return $this->setStatusCode(201)
            ->respondWithArray(
                [
                    'code'      => self::CODE_CREATED,
                    'http_code' => 201,
                    'message'   => $message,
                ]
            );
    }

    public function errorUnauthorized($message = 'Unauthorized'): JsonResponse
    {
        return $this->setStatusCode(401)
            ->respondWithError($message, self::CODE_UNAUTHORIZED);
    }

    public function respondWithError($message, $errorCode): JsonResponse
    {
        if ($this->statusCode === 200) {
            trigger_error(
                'You better have a really good reason for erroring on a 200...',
                E_USER_WARNING
            );
        }

        return $this->respondWithArray(
            [
                'error' => [
                    'code'      => $errorCode,
                    'http_code' => $this->statusCode,
                    'message'   => $message,
                ],
            ]
        );
    }

    public function errorForbidden($message = 'Forbidden'): JsonResponse
    {
        return $this->setStatusCode(403)
            ->respondWithError($message, self::CODE_FORBIDDEN);
    }

    public function errorWrongArgs($message = 'Wrong Arguments'): JsonResponse
    {
        return $this->setStatusCode(400)
            ->respondWithError($message, self::CODE_WRONG_ARGS);
    }

    public function errorNotFound($message = 'Resource Not Found'): JsonResponse
    {
        return $this->setStatusCode(404)
            ->respondWithError($message, self::CODE_NOT_FOUND);
    }

    public function errorWrongArgsWithJson(string $errorBag = 'Wrong Arguments'): JsonResponse
    {
        try {
            $message = json_decode(
                $errorBag,
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            return $this->setStatusCode(400)
                ->respondWithError($message, self::CODE_WRONG_ARGS);
        } catch (JsonException $previous) {
            throw new RuntimeException(
                'Failed to decode message bag: ' . var_export($errorBag, true),
                0,
                $previous
            );
        }
    }

    public function errorWrongArgsWithArray(array $errorBag = ['errors' => 'Wrong Arguments']): JsonResponse
    {
        return $this->setStatusCode(400)
            ->respondWithError(json_encode($errorBag, JSON_THROW_ON_ERROR), self::CODE_WRONG_ARGS);
    }

    public function errorInternalError($message = 'Internal Error'): JsonResponse
    {
        return $this->setStatusCode(500)
            ->respondWithError($message, self::CODE_INTERNAL_ERROR);
    }
}
