<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use TimeHunter\LaravelGoogleReCaptchaV3\Facades\GoogleReCaptchaV3;

class VerifyGoogleRecaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $method = $request->method();

        if ($request->isMethod('post') && $request->is('login')) {
            $status = GoogleReCaptchaV3::setAction('verify')
                ->verifyResponse(
                    $request->input('g-recaptcha-response'),
                    $request->getClientIp()
                )
                ->isSuccess();

            if ($status != true) {
                return redirect(route('login'));
            };
            return $next($request);
        } else {
            return $next($request);
        }
    }
}
