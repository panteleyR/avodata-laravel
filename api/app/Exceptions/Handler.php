<?php

namespace App\Exceptions;

use GuzzleHttp\Client;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
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
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        $code = is_int($e->getCode()) && $e->getCode() ? $e->getCode() : 500;
        Log::info('app.requests', [
            'request' => $request->all(),
            'url' => $request->fullUrl(),
            'method' => $request->method()
        ]);

        $appCode = $code;
        if ($e instanceof ApplicationException) {
            $appCode = $e->getApplicationCode();
        }

        $response = [
            "message" => $e->getMessage(),
            "appCode" => $appCode
        ];
//        if (method_exists($e, 'render') && $response = $e->render($request)) {
//            return Router::toResponse($request, $response);
//        } elseif ($e instanceof Responsable) {
//            return $e->toResponse($request);
//        }
        $tg = new Client(['base_uri' => 'https://api.telegram.org/bot1412335892:AAEc1upGP0PCev1BhQVnJsIVB65fY_QYShc/']);
        $tgMessage = $e->getMessage() ? $e->getMessage() : get_class($e);
        $tgMessage .= "\n"
            . 'url: ' . $request->fullUrl() . "\n"
            . 'method: ' . $request->method() . "\n"
            . 'token: ' . $request->bearerToken() . "\n"
            . 'serviceURL: ' . env('APP_URL') . "\n"
            . 'serviceName: ' . env('APP_NAME');
        $tg->post('sendMessage', [
            'query' => [
                'chat_id' => 539798885,
                'text' => $tgMessage
            ]
        ]);

        if ($e instanceof ValidationException) {
            if ($e->response) {
                return $e->response;
            }

            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], $e->status);
        }
        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        }
        if ($e instanceof AuthenticationException) {
            return response()->json(['message' => $e->getMessage()], 401);
        }

        if (config('app.debug')) {
            $response['trace'] = $e->getTrace();
            $response['line'] = $e->getLine();
            $response['file'] = $e->getFile();
        }

        return response()->json($response, (int) $code);
//        return parent::render($request, $e);
    }
}
