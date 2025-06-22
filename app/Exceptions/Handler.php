<?php

namespace App\Exceptions;

use App\Traits\JsonResponseAPI;
use ErrorException;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandlerBase;

class Handler extends ExceptionHandlerBase
{
    use JsonResponseAPI;

//    public function __construct(Container $container)
//    {
//        parent::__construct($container);
//    }

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            Log::error($e);
        });

        $this->renderable(function (Throwable $e) {
            if ($e instanceof MissingAbilityException) {
                return $this->handleMissingAbilityException($e);
            }
            if ($e instanceof GuzzleException) {
                return $this->guzzleException($e);
            }
            if ($e instanceof ModelNotFoundException) {
                return $this->modelNotFoundException($e);
            }
            if ($e instanceof NotFoundHttpException) {
                return $this->notFoundException($e);
            }
            if ($e instanceof AuthenticationException) {
                return $this->authenticationException($e);
            }
            if ($e instanceof QueryException) {
                return $this->queryException($e);
            }
            if ($e instanceof RuntimeException) {
                return $this->handleRuntimeException($e);
            }
            if ($e instanceof InvalidArgumentException) {
                return $this->handleInvalidArgumentException($e);
            }
            if ($e instanceof ErrorException) {
                return $this->handleErrorException($e);
            }


        });
    }


    private function guzzleException(GuzzleException $e): JsonResponse
    {
        return $this->errorResponse(
            'Server Error: Issue communicating with third-party',
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    private function modelNotFoundException(ModelNotFoundException $e): JsonResponse
    {
        return $this->errorResponse(
            'Requested Entity Not Found',
            Response::HTTP_NOT_FOUND
        );
    }

    private function authenticationException(AuthenticationException $e): JsonResponse
    {
        return $this->errorResponse(
            $e->getMessage(),
            Response::HTTP_UNAUTHORIZED
        );
    }

    private function notFoundException(NotFoundHttpException $e): JsonResponse
    {
        return $this->errorResponse(
            'This URL seems to be on a coffee break.',
            Response::HTTP_NOT_FOUND
        );
    }

    private function tooManyRequestsHttpException(TooManyRequestsHttpException $e): JsonResponse
    {
        return $this->errorResponse(
            'Too many attempts made. Kindly try again later',
            Response::HTTP_TOO_MANY_REQUESTS
        );
    }

    private function queryException(QueryException $e): JsonResponse
    {
        Log::error('Database Query Exception: ', [$e]);

        if (!app()->environment('production')) {
            return $this->errorResponse(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST
            );
        }
        return $this->errorResponse(
            "We couldn't process your request right now.",
            Response::HTTP_BAD_REQUEST
        );
    }

    private function handleRuntimeException(RuntimeException $e): JsonResponse
    {
        Log::error('Runtime Exception: ', [$e]);

        if (!app()->environment('production')) {
            return $this->errorResponse(
                $e,
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        return $this->errorResponse(
            "Something went wrong",
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    private function handleInvalidArgumentException(InvalidArgumentException $e): JsonResponse
    {
        Log::error('Invalid Argument Exception: ', [$e]);

        if (!app()->environment('production')) {
            return $this->errorResponse(
                $e,
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        return $this->errorResponse(
            "Something went wrong",
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    private function handleErrorException(ErrorException $e): JsonResponse
    {
        Log::error('Error Exception: ', [$e]);

        if (!app()->environment('production')) {
            return $this->errorResponse(
                $e,
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        return $this->errorResponse(
            "An error occurred while processing your request.",
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    private function handleAccessDeniedException(AccessDeniedHttpException $e): JsonResponse
    {
        Log::error('Error Exception: ', [$e]);

        return $this->errorResponse(
            "Permission denied.",
            Response::HTTP_FORBIDDEN
        );
    }

    private function handleMissingAbilityException(MissingAbilityException $e): JsonResponse
    {
        Log::error('Error Exception: ', [$e]);

        return $this->errorResponse(
            "You are not permitted to carry out this action",
            Response::HTTP_FORBIDDEN
        );
    }
}
