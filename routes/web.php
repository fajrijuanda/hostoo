<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlanController;

// Landing Page
Route::get('/', [\App\Http\Controllers\WelcomeController::class, 'index'])->name('welcome');
Route::get('/privacy-policy', [\App\Http\Controllers\WelcomeController::class, 'privacy'])->name('privacy');

// Forgot Password Routes
Route::get('/forgot-password', [\App\Http\Controllers\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [\App\Http\Controllers\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\ResetPasswordController::class, 'reset'])->name('password.update');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Email Verification
Route::get('/verify-email', [\App\Http\Controllers\VerificationController::class, 'show'])->middleware('auth')->name('verification.notice');
Route::post('/verify-email', [\App\Http\Controllers\VerificationController::class, 'verify'])->middleware('auth')->name('verification.verify');
Route::post('/verify-email/resend', [\App\Http\Controllers\VerificationController::class, 'resend'])->middleware('auth')->name('verification.resend');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google Auth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Captcha
Route::get('/captcha/image', [\App\Http\Controllers\CaptchaController::class, 'generate'])->name('captcha.image');

// Plan Routes (Protected)
Route::get('/plan/select', [PlanController::class, 'selectPlan'])->middleware('auth')->name('plan.select');

// Dashboard Routes (Protected)
Route::middleware(['auth', 'check_subscription'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Files (Refactored)
    Route::get('/files', [\App\Http\Controllers\FileManagerController::class, 'index'])->name('dashboard.files');
    Route::post('/files/upload', [\App\Http\Controllers\FileManagerController::class, 'upload'])->name('files.upload');
    Route::post('/files/make-directory', [\App\Http\Controllers\FileManagerController::class, 'makeDirectory'])->name('files.make-directory');
    Route::post('/files/delete', [\App\Http\Controllers\FileManagerController::class, 'destroy'])->name('files.delete');
    Route::post('/files/extract', [\App\Http\Controllers\FileManagerController::class, 'extract'])->name('files.extract');
    Route::post('/files/compress', [\App\Http\Controllers\FileManagerController::class, 'compress'])->name('files.compress');
    Route::get('/files/content', [\App\Http\Controllers\FileManagerController::class, 'getContent'])->name('files.content');
    Route::post('/files/save', [\App\Http\Controllers\FileManagerController::class, 'updateContent'])->name('files.save');
    Route::post('/files/create', [\App\Http\Controllers\FileManagerController::class, 'createFile'])->name('files.create');
    
    // Databases
    Route::resource('databases', App\Http\Controllers\DatabaseController::class);
    Route::get('databases/{database}/import', [App\Http\Controllers\DatabaseController::class, 'import'])->name('databases.import');
    Route::post('databases/{database}/import', [App\Http\Controllers\DatabaseController::class, 'processImport'])->name('databases.process-import');

    // Email Accounts
    Route::resource('emails', \App\Http\Controllers\EmailController::class);

    // Subscription & PHP Manager
    Route::get('/subscription', [\App\Http\Controllers\SubscriptionPageController::class, 'index'])->name('subscription.index');
    Route::get('/php-manager', [\App\Http\Controllers\PhpManagerController::class, 'index'])->name('php.manager');
    Route::post('/php-manager', [\App\Http\Controllers\PhpManagerController::class, 'update'])->name('php.update');

    // GitHub Integration
    Route::get('/github', [\App\Http\Controllers\GithubController::class, 'index'])->name('github.index');
    Route::post('/github', [\App\Http\Controllers\GithubController::class, 'update'])->name('github.update');

    // Domain Manager
    Route::get('/domains', [\App\Http\Controllers\DomainController::class, 'index'])->name('domains.index');
    Route::post('/domains', [\App\Http\Controllers\DomainController::class, 'store'])->name('domains.store');
    Route::delete('/domains/{id}', [\App\Http\Controllers\DomainController::class, 'destroy'])->name('domains.destroy');
});

// Profile Routes (Protected but accessible without subscription)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/password', [\App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::delete('/settings', [\App\Http\Controllers\SettingsController::class, 'destroy'])->name('settings.destroy');

});


    // Admin Routes
    Route::middleware(['auth', 'is_admin'])->prefix('admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/subscriptions', [\App\Http\Controllers\AdminController::class, 'subscriptions'])->name('admin.subscriptions');
        Route::post('/subscriptions/approve/{id}', [\App\Http\Controllers\AdminController::class, 'approveSubscription'])->name('admin.subscriptions.approve');
        Route::post('/subscriptions/reject/{id}', [\App\Http\Controllers\AdminController::class, 'rejectSubscription'])->name('admin.subscriptions.reject');

        // Hosting Plans
        Route::resource('plans', \App\Http\Controllers\AdminPlanController::class)->names('admin.plans');
    });

    // GitHub Webhook (Public)
    Route::post('/git/webhook/{secret}', [\App\Http\Controllers\GithubController::class, 'webhook'])->name('github.webhook');

    // Newsletter Subscription
    Route::post('/subscribe', [\App\Http\Controllers\SubscriberController::class, 'store'])->name('subscribe');
    Route::get('/unsubscribe', [\App\Http\Controllers\SubscriberController::class, 'unsubscribe'])->name('subscribe.unsubscribe');
    Route::post('/unsubscribe', [\App\Http\Controllers\SubscriberController::class, 'destroy'])->name('subscribe.delete');
