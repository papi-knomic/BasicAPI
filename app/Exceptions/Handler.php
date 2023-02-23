<?php

namespace App\Exceptions;

use App\Traits\Response;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function(Exception $e, $request) {
            if ($e instanceof NotFoundHttpException) {
                return Response::errorResponse('The specified resource cannot be  found!.', 404);
            }
            if ($e instanceof ModelNotFoundException) {
                $model = $e->getModel();
                return Response::errorResponse("$model cannot be  found!.", 404);
            }
            if ($e instanceof  AccessDeniedHttpException) {
                return Response::errorResponse('You are not authorised to do this');
            }
            if ($e instanceof  HttpException) {
                $message = $e->getMessage();
                return Response::errorResponse($message);
            }
            if ($e instanceof QueryException) {
                $message = $e->getMessage();
                return Response::errorResponse($message);
            }
        });

        $this->reportable(function (Throwable $e) {
        });


    }
}
