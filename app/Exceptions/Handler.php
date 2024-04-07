<?php

namespace App\Exceptions;

use Throwable;
use Laravel\Sanctum\Exceptions\MissingAbilityException; // SANCTUM:ERRORHANDLING
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException; // SANCTUM:ERRORHANDLING
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException; // SANCTUM:ERRORHANDLING
use Illuminate\Auth\AuthenticationException; // SANCTUM:ERRORHANDLING

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    // SANCTUM:ERRORHANDLING
    public function render($request, Throwable $exception)
    {
        // Sanctum Ability Exception handling
        if (($exception instanceof MissingAbilityException || $exception instanceof AccessDeniedHttpException) && $exception->getMessage() === "Invalid ability provided.") {
            return response()->json(['status' => false, 'error' => 'Invalid ability provided.', 'message' => 'Invalid ability provided.', 'data' => []], 403);
        }

        // Sanctum Unthenticated Exception handling
        if ($exception instanceof AuthenticationException || $exception instanceof UnauthorizedHttpException) {
            return response()->json(['status' => false, 'error' => 'Unauthenticated.', 'message' => 'Unauthenticated.', 'data' => []], 401);
        }

        return parent::render($request, $exception);
    }

}
