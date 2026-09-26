<?php

namespace App\Http\Middleware;

use App\Exceptions\DesktopLicenseException;
use App\Services\Desktop\LicenseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDesktopLicense
{
    public function __construct(
        private LicenseService $licenses,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        try {
            $this->licenses->verify();
        } catch (DesktopLicenseException $exception) {
            return response()->view('desktop.blocked', [
                'message' => $exception->getMessage(),
            ], 403);
        }

        return $next($request);
    }
}
