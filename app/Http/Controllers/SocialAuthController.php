<?php

namespace App\Http\Controllers;

use App\Models\SocialAuthAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    protected function allowedProviders(): array
    {
        return config('oauth.providers', ['google']);
    }

    protected function validateProvider(string $provider): void
    {
        abort_unless(in_array($provider, $this->allowedProviders(), true), 404);
    }

    public function redirect(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors(['oauth' => __('Unable to authenticate with :provider.', ['provider' => $provider])]);
        }

        $providerUserId = (string) $socialUser->getId();
        $email = $socialUser->getEmail();
        $emailVerified = $this->providerEmailVerified($provider, $socialUser);
        $token = $socialUser->token ?? null;
        $refreshToken = $socialUser->refreshToken ?? null;
        $expiresIn = $socialUser->expiresIn ?? null;

        $existingAccount = SocialAuthAccount::where('provider', $provider)
            ->where('provider_user_id', $providerUserId)
            ->first();

        // Authenticated linking flow.
        if (Auth::check()) {
            $user = Auth::user();

            if ($existingAccount && $existingAccount->user_id !== $user->id) {
                return redirect()->route('settings.security.edit')
                    ->withErrors(['oauth' => __('This :provider account is already linked to another user.', ['provider' => $provider])]);
            }

            if (! $existingAccount) {
                SocialAuthAccount::create([
                    'user_id' => $user->id,
                    'provider' => $provider,
                    'provider_user_id' => $providerUserId,
                    'token' => $token,
                    'refresh_token' => $refreshToken,
                    'expires_at' => $expiresIn ? now()->addSeconds($expiresIn) : null,
                ]);
            } else {
                $existingAccount->update([
                    'token' => $token,
                    'refresh_token' => $refreshToken,
                    'expires_at' => $expiresIn ? now()->addSeconds($expiresIn) : null,
                ]);
            }

            return redirect()->route('settings.security.edit')
                ->with('status', 'social-account-linked');
        }

        // Existing linked account: log in.
        if ($existingAccount) {
            $user = $existingAccount->user;
            if (! $user || ! $user->isActive()) {
                return redirect()->route('login')->withErrors(['oauth' => __('This account is suspended or unavailable.')]);
            }
            Auth::login($user, true);
            $existingAccount->update([
                'token' => $token,
                'refresh_token' => $refreshToken,
                'expires_at' => $expiresIn ? now()->addSeconds($expiresIn) : null,
            ]);

            return redirect()->intended(route('dashboard'));
        }

        // Email-based linking only when provider confirms verification.
        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                if (! $emailVerified) {
                    return redirect()->route('login')
                        ->withErrors(['oauth' => __('The OAuth provider did not verify this email address. Link the account after logging in.')]);
                }
                if (! $user->isActive()) {
                    return redirect()->route('login')->withErrors(['oauth' => __('This account is suspended or unavailable.')]);
                }
                SocialAuthAccount::create([
                    'user_id' => $user->id,
                    'provider' => $provider,
                    'provider_user_id' => $providerUserId,
                    'token' => $token,
                    'refresh_token' => $refreshToken,
                    'expires_at' => $expiresIn ? now()->addSeconds($expiresIn) : null,
                ]);
                if (! $user->email_verified_at && $emailVerified) {
                    $user->forceFill(['email_verified_at' => now()])->save();
                }
                Auth::login($user, true);

                return redirect()->intended(route('dashboard'));
            }
        }

        // Registration via OAuth.
        if (! config('oauth.allow_registration', true)) {
            return redirect()->route('login')->withErrors(['oauth' => __('Registration via :provider is disabled.', ['provider' => $provider])]);
        }

        if (! $email) {
            return redirect()->route('login')->withErrors(['oauth' => __('The OAuth provider did not return an email address.')]);
        }

        $user = User::create([
            'public_id' => (string) Str::ulid(),
            'name' => $socialUser->getName() ?: $email,
            'email' => $email,
            'password' => Hash::make(Str::random(40)),
            'status' => 'active',
            'locale' => 'en',
            'timezone' => 'UTC',
            'email_verified_at' => $emailVerified ? now() : null,
        ]);

        SocialAuthAccount::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_user_id' => $providerUserId,
            'token' => $token,
            'refresh_token' => $refreshToken,
            'expires_at' => $expiresIn ? now()->addSeconds($expiresIn) : null,
        ]);

        Auth::login($user, true);

        return redirect()->intended(route('dashboard'));
    }

    public function unlink(Request $request, string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        $account = SocialAuthAccount::where('user_id', $request->user()->id)
            ->where('provider', $provider)
            ->first();

        if ($account) {
            $account->delete();
        }

        return redirect()->route('settings.security.edit')
            ->with('status', 'social-account-unlinked');
    }

    protected function providerEmailVerified(string $provider, $socialUser): bool
    {
        $raw = $socialUser->getRaw() ?? [];

        foreach (['email_verified', 'verified_email', 'emailVerified'] as $key) {
            if (array_key_exists($key, $raw)) {
                $value = $raw[$key];

                return $value === true || $value === 1 || $value === 'true' || $value === '1';
            }
        }

        // Google's Socialite user has a verified email helper in some versions.
        if (method_exists($socialUser, 'getEmailVerified') && $socialUser->getEmailVerified()) {
            return true;
        }

        return false;
    }
}
