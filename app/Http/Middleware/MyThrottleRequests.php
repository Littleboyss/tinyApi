<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Packages\Utils\Drive\MyRateLimiter;
use Symfony\Component\HttpFoundation\Response;

class MyThrottleRequests
{
    /**
     * The rate limiter instance.
     *
     * @var \Packages\Utils\Drive\MyRateLimiter
     */
    protected $limiter;

    /**
     * Create a new request throttler.
     *
     * @param   \Packages\Utils\Drive\MyRateLimiter $limiter
     */
    public function __construct(MyRateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  int $maxAttempts ; 次数
     * @param  int $decaySeconds ;秒数
     * @return mixed
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decaySeconds = 60)
    {
        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts, $decaySeconds)) {
            return $this->buildResponse($key, $maxAttempts);
        }

        $this->limiter->hit($key, $decaySeconds);

        $response = $next($request);

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    /**
     * Resolve request signature.
     *
     * @param  \Illuminate\Http\Request $request
     * @return string
     */
    protected function resolveRequestSignature($request)
    {
//        return $request->fingerprint();
        return $this->fingerprint($request);   //重新封装fingerprint方法
    }

    /**
     * Resolve request signature.
     *
     * @param  \Illuminate\Http\Request $request
     * @return string
     */
    public function fingerprint($request)
    {
        if (!$route = $request->route()) {
            throw new \RuntimeException('Unable to generate fingerprint. Route unavailable.');
        }

        $ip = Auth::id() ?? $request->ip();     //如果存在员工id就取员工id,否则取ip

        return sha1(implode('|', array_merge(
            $route->methods(), [$route->domain(), $route->uri(), $ip]
        )));
    }

    /**
     * Create a 'too many attempts' response.
     *
     * @param  string $key
     * @param  int $maxAttempts
     * @return \Illuminate\Http\Response
     */
    protected function buildResponse($key, $maxAttempts)
    {
//        $message = json_encode([
//            'ret' => 4029,
//            'result' => 4029,
//            'res_info' => 'Too many attempts, please slow down the request.'
//        ]);
//
//        $response = new Response($message, 429);

        $message = json_encode([
            'ret' => 0,
            'result' => 0,
            'res_info' => []
        ]);

        $response = new Response($message, 200);

        $retryAfter = $this->limiter->availableIn($key);

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );
    }

    /**
     * Add the limit header information to the given response.
     *
     * @param  \Symfony\Component\HttpFoundation\Response $response
     * @param  int $maxAttempts
     * @param  int $remainingAttempts
     * @param  int|null $retryAfter
     * @return \Illuminate\Http\Response
     */
    protected function addHeaders(Response $response, $maxAttempts, $remainingAttempts, $retryAfter = null)
    {
        $headers = [
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ];

        if (!is_null($retryAfter)) {
            $headers['Retry-After'] = $retryAfter;
            $headers['Content-Type'] = 'application/json';
        }

        $response->headers->add($headers);

        return $response;
    }

    /**
     * Calculate the number of remaining attempts.
     *
     * @param  string $key
     * @param  int $maxAttempts
     * @param  int|null $retryAfter
     * @return int
     */
    protected function calculateRemainingAttempts($key, $maxAttempts, $retryAfter = null)
    {
        if (!is_null($retryAfter)) {
            return 0;
        }

        return $this->limiter->retriesLeft($key, $maxAttempts);
    }
}