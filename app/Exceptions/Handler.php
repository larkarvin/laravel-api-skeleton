<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e, $request) {
            
            if ($request->wantsJson()) {   //add Accept: application/json in request
                return $this->handleApiException($request, $e);
            } 
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'End Point not found.'
                ], 404);
            }
        });
    }

    private function handleApiException($request, $exception)
    {
        $exception = $this->prepareException($exception);
        
        if ($exception instanceof \Illuminate\Http\Exception\HttpResponseException) {
            $exception = $this->apiResponse($exception, 500);
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {    
            $exception = $this->apiResponse($exception, 401);
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            return $this->apiResponse($exception, 422);
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
            return $this->apiResponse($exception, 401);
        }

        return $this->apiResponse($exception);
    }

    private function apiResponse($exception, $forceHttpStatus = NULL)
    {
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }

        if(!empty($forceHttpStatus)){
            $statusCode = $forceHttpStatus;
        }

        $response = [];
        switch ($statusCode) {
            case 401:
                $response['message'] = 'Unauthorized';
                break;
            case 403:
                $response['message'] = 'Forbidden';
                break;
            case 404:
                $response['message'] = 'Resource Not Found';
                break;
            case 405:
                $response['message'] = 'Method Not Allowed';
                break;
            case 422:
                $response['message'] = $exception->getMessage();
                $response['errors'] = $exception->errors();
                break;
            default:
                $response['message'] = ($statusCode == 500) ?  $exception->getMessage()  : 'Whoops, looks like something went wrong';
                break;
        }

        if (config('app.debug')) {
            // dd($exception->getCode());
            if(method_exists($exception, 'getLine')){
                $response['line'] = $exception->getLine() ?? '';
            }
            if(method_exists($exception, 'getFile')){
                $response['file'] = $exception->getFile() ?? '';
            }
            if(method_exists($exception, 'getTrace')){
                $response['trace'] = $exception->getTrace() ?? '';
            }
            if(method_exists($exception, 'getCode')){
                $response['code'] = $exception->getCode() ?? '';
            }
        }

        $response['status'] = $statusCode;

        return response()->json($response, $statusCode);
    }
}
