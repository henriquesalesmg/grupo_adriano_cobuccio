<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\LoginAttempt;
use Illuminate\Support\Facades\Log;

class LoginLimitMiddleware
{
    // Configurações de segurança
    const MAX_ATTEMPTS_PER_EMAIL = 5;     // Máximo 5 tentativas por email
    const MAX_ATTEMPTS_PER_IP = 10;       // Máximo 10 tentativas por IP
    const LOCKOUT_TIME_MINUTES = 30;      // Bloqueio por 30 minutos
    const ATTEMPTS_WINDOW_MINUTES = 15;   // Janela de 15 minutos para contar tentativas

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Só aplica para tentativas de login
        if ($request->isMethod('POST') && $request->is('login')) {
            $email = $request->input('email');
            $ip = $request->ip();
            $userAgent = $request->userAgent();

            // Verifica se a conta está bloqueada
            if ($this->isAccountLocked($email)) {
                $lockTime = LoginAttempt::getLockTimeRemaining($email);
                $message = 'Conta temporariamente bloqueada devido a muitas tentativas de login. ';

                if ($lockTime) {
                    $remainingMinutes = $lockTime->diffInMinutes(now());
                    $message .= "Tente novamente em {$remainingMinutes} minutos.";
                } else {
                    $message .= 'Tente novamente mais tarde.';
                }

                Log::warning('Tentativa de login em conta bloqueada', [
                    'email' => $email,
                    'ip' => $ip,
                    'user_agent' => $userAgent,
                    'lock_time_remaining' => $lockTime
                ]);

                return redirect()->back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => $message]);
            }

            // Verifica limite por IP
            if ($this->isIpLimited($ip)) {
                Log::warning('IP com muitas tentativas de login', [
                    'ip' => $ip,
                    'user_agent' => $userAgent,
                    'attempted_email' => $email
                ]);

                return redirect()->back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'Muitas tentativas de login deste IP. Tente novamente mais tarde.']);
            }

            // Adiciona dados para processamento posterior
            $request->merge([
                '_security_check' => [
                    'email' => $email,
                    'ip' => $ip,
                    'user_agent' => $userAgent,
                    'failed_attempts' => $this->getFailedAttempts($email)
                ]
            ]);
        }

        $response = $next($request);

        // Após o processamento, registra a tentativa se for login
        if ($request->isMethod('POST') && $request->is('login')) {
            $this->recordLoginAttempt($request, $response);
        }

        return $response;
    }

    /**
     * Verifica se a conta está bloqueada
     */
    protected function isAccountLocked(string $email): bool
    {
        return LoginAttempt::isAccountLocked($email);
    }

    /**
     * Verifica se o IP está limitado
     */
    protected function isIpLimited(string $ip): bool
    {
        $failedAttempts = LoginAttempt::getFailedAttemptsByIp($ip, self::ATTEMPTS_WINDOW_MINUTES);
        return $failedAttempts >= self::MAX_ATTEMPTS_PER_IP;
    }

    /**
     * Obtém o número de tentativas falhadas
     */
    protected function getFailedAttempts(string $email): int
    {
        return LoginAttempt::getFailedAttemptsCount($email, self::ATTEMPTS_WINDOW_MINUTES);
    }

    /**
     * Registra a tentativa de login
     */
    protected function recordLoginAttempt(Request $request, Response $response): void
    {
        $securityData = $request->input('_security_check');

        if (!$securityData) {
            return;
        }

        $email = $securityData['email'];
        $ip = $securityData['ip'];
        $userAgent = $securityData['user_agent'];

        // Determina se o login foi bem-sucedido baseado no redirect
        $successful = $response->isRedirect() &&
                     !str_contains($response->headers->get('Location', ''), 'login');

        // Registra a tentativa
        LoginAttempt::recordAttempt($email, $ip, $userAgent, $successful);

        if ($successful) {
            // Limpa tentativas anteriores se o login foi bem-sucedido
            LoginAttempt::clearSuccessfulAttempts($email);

            Log::info('Login bem-sucedido', [
                'email' => $email,
                'ip' => $ip
            ]);
        } else {
            // Verifica se deve bloquear a conta
            $failedAttempts = $this->getFailedAttempts($email) + 1; // +1 porque acabamos de registrar

            if ($failedAttempts >= self::MAX_ATTEMPTS_PER_EMAIL) {
                LoginAttempt::lockAccount($email, self::LOCKOUT_TIME_MINUTES);

                Log::warning('Conta bloqueada por excesso de tentativas', [
                    'email' => $email,
                    'ip' => $ip,
                    'failed_attempts' => $failedAttempts,
                    'lockout_minutes' => self::LOCKOUT_TIME_MINUTES
                ]);
            } else {
                Log::warning('Tentativa de login falhada', [
                    'email' => $email,
                    'ip' => $ip,
                    'failed_attempts' => $failedAttempts,
                    'remaining_attempts' => self::MAX_ATTEMPTS_PER_EMAIL - $failedAttempts
                ]);
            }
        }
    }
}
