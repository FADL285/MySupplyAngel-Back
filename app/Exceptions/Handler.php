<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Throwable $exception)
    {
        if ($this->isHttpException($exception) && ! $request->wantsJson()) {
            $code = $exception->getStatusCode();
            switch ($code) {
                case '404':
                    return response()->view('dashboard.error.404_notauth', [], 404);
                    break;
                case '403':
                    return response()->view('dashboard.error.403', [], 403);
                    break;
                default:
                    abort(404);
                    break;
            }
        }

        if ($this->isHttpException($exception) && $request->wantsJson()) {
            $code = $exception->getStatusCode();
            switch ($code) {
                case '404':
                    return response()->json(['status' => false,'message' => 'الصفحة غير موجودة', 'data' => null], 404);
                    break;
                case '403':
                    return response()->json(['status' => false,'message' => 'ليس لديك صلاحيات الدخول', 'data' => null], 403);
                    break;
                default:
                    return response()->json(['status' => false,'message' => 'الصفحة غير موجودة', 'data' => null], 403);
                    break;
            }
        }

        if ($exception instanceof MaintenanceModeException) {
            return response()->view('dashboard.error.503')->header('Content-Type', 'text/html; charset=utf-8');
        }

        if ($exception instanceof ModelNotFoundException && auth()->check() && in_array(auth()->user()->user_type,['admin','superadmin']) && ! $request->ajax() && ! $request->wantsJson()) {
            return response()->view('dashboard.error.404', [], 404);
        }

        if ($exception instanceof ModelNotFoundException && $request->wantsJson()) {
          return response()->json(['status' => false,'message' => "لم يتم العثور علي بيانات",'data'=> null ],404);
        }

        if ($exception instanceof AuthenticationException && $request->wantsJson()) {
            return response()->json(['status' => false,'message' => 'قم بتسجيل الدخول أولا' , 'data' => null ],401);
        }

        return parent::render($request, $exception);
    }
}
