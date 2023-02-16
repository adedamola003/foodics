<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];


    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Throwable $exception)
    {
        //handle 404 for api calls
        if ($exception instanceof ModelNotFoundException && $request->wantsJson()) {
            return response()->json(['message' => 'Not Found!'], 404);
        }

        if ($exception instanceof ThrottleRequestsException) {
            $error = ['Transaction is processing, please try again after 60 seconds'];
            return response()->json(['success' => false, 'message' => 'Transaction is processing, please try again after 60 seconds', 'data' => ['error' => $error]], 429);
        }

        if ($exception instanceof AuthenticationException) {
            $error = ['Unauthenticated'];
            return response()->json(['success' => false, 'message' => 'Unauthenticated', 'data' => ['error' => $error]], 401);
        }

        if ($exception && !config('app.debug')) {
            return response()->json([
                'success' => false,
                'message' => 'Oops something went wrong, Please try again',
                'data'=>['error'=>['Oops something went wrong, Please try again']]
            ],500);
        }

        return parent::render($request, $exception);
    }

}
