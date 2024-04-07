<?php

namespace App\Exceptions;

use Throwable;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Laravel\Sanctum\Exceptions\UnauthenticatedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Auth\AuthenticationException;

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


    public function render($request, Throwable $exception)
    {
        // dd($exception->getMessage(),$exception instanceof AccessDeniedHttpException, $exception instanceof MissingAbilityException,$exception instanceof UnauthenticatedException,$exception instanceof UnauthorizedHttpException,$exception instanceof AuthenticationException);
        // Sanctum Ability Exception handling
        if (($exception instanceof MissingAbilityException || $exception instanceof AccessDeniedHttpException) && $exception->getMessage() === "Invalid ability provided.") {
            return response()->json(['status' => false, 'error' => 'Invalid ability provided.', 'message' => 'Invalid ability provided.', 'data' => []], 403);
        }

        // Sanctum Unthenticated Exception handling
        if ($exception instanceof UnauthenticatedException || $exception instanceof UnauthorizedHttpException) {
            return response()->json(['status' => false, 'error' => 'Unauthenticated.', 'message' => 'Unauthenticated.'], 401);
        }
    
        return parent::render($request, $exception);
    }

}
