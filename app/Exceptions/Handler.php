<?php

namespace App\Exceptions;

use App\Exceptions\Freenom\InvalidConfigException;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

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
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception) && app()->environment('production')) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        $status_code = config('exception_code.' . get_class($exception), method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500);

        if ($exception instanceof InvalidConfigException) {
            $result = [
                'code'        => $status_code,
                'message'     => $exception->getMessage(),
                'server_time' => time(),
                'data'        => []
            ];

            return response()->json($result, $status_code);
        } elseif ($exception instanceof ValidationException) {
            $result['code'] = $status_code;
            $result['data'] = [];
            foreach ($exception->validator->errors()->toArray() as $field => $errors) {
                foreach ($errors as $error) {
                    $result['data'][] = [
                        'field'   => $field,
                        'content' => $error,
                    ];
                }
            }

            return response()->json($result, $status_code);
        }

        return parent::render($request, $exception);
    }
}
