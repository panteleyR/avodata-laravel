<?php

namespace App\Exceptions;

use GuzzleHttp\Client;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
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
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
//        return parent::render($request, $exception);
        $rendered = parent::render($request, $exception);
        Log::info('app.requests', [
            'request' => $request->all(),
            'url' => $request->fullUrl(),
            'method' => $request->method()
        ]);

//        telegram
        $tg = new Client(['base_uri' => 'https://api.telegram.org/bot1412335892:AAEc1upGP0PCev1BhQVnJsIVB65fY_QYShc/']);
        $tgMessage = $exception->getMessage() ? $exception->getMessage() : get_class($exception);
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

        return response()->json([
            'error' => [
                'code' => $rendered->getStatusCode(),
                'message' => $exception->getMessage(),
            ]
        ], $rendered->getStatusCode());
    }
}
