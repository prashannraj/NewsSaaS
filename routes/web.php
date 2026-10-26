<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationMembershipController;
use App\Http\Controllers\OrganizationSettingsController;
use App\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::post('organizations', [OrganizationController::class, 'store'])->name('organizations.store');
    Route::get('onboarding/organizations/{organization}', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');

    Route::get('organizations/{organization}/settings', [OrganizationSettingsController::class, 'edit'])->name('organizations.settings.edit');
    Route::put('organizations/{organization}/settings', [OrganizationSettingsController::class, 'update'])->name('organizations.settings.update');

    Route::get('organizations/{organization}/invitations', [InvitationController::class, 'index'])->name('organizations.invitations.index');
    Route::post('organizations/{organization}/invitations', [InvitationController::class, 'store'])->name('organizations.invitations.store');
    Route::post('invitations/{invitation}/resend', [InvitationController::class, 'resend'])->name('invitations.resend');
    Route::delete('invitations/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');

    Route::put('organizations/{organization}/memberships/{membership}', [OrganizationMembershipController::class, 'update'])->name('organizations.memberships.update');
    Route::delete('organizations/{organization}/memberships/{membership}', [OrganizationMembershipController::class, 'destroy'])->name('organizations.memberships.destroy');
});

Route::middleware(['auth', 'password.confirm'])->group(function () {
    Route::get('auth/{provider}/link', [SocialAuthController::class, 'redirect'])->name('social.link');
    Route::delete('settings/social-accounts/{provider}', [SocialAuthController::class, 'unlink'])->name('social.unlink');
});

Route::get('invitations/{token}', [InvitationController::class, 'show'])->name('invitations.accept');
Route::post('invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitations.accept.store')->middleware('auth');

require __DIR__.'/settings.php';
