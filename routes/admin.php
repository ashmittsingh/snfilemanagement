<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminReelController;
use App\Http\Controllers\Admin\AdminImageController;
use App\Http\Controllers\Admin\AdminDocumentController;

use App\Http\Controllers\Admin\AdminMediaController;

Route::prefix('admin')->group(function () {

    //for login view file

    Route::middleware(['roleRedirect'])->group(function () {
        Route::get('/login', [AdminAuthController::class, 'loginView'])->name('admin.login.view');
        Route::post('/login', [AdminAuthController::class, 'loginSubmit'])->name('admin.login.submit');

        Route::get('/forgot-password', [AdminAuthController::class, 'forgotPasswordView'])->name('admin.forgot.password.view');
        Route::post('/forgot-password', [AdminAuthController::class, 'forgotPasswordSubmit'])->name('admin.forgot.password.submit');

        Route::get('/reset-password/{token}', [AdminAuthController::class, 'resetPasswordView'])->name('admin.resetPassword.view');
        Route::post('/reset-password', [AdminAuthController::class, 'resetPasswordSubmit'])->name('admin.resetPassword.submit');
    });


    Route::middleware(['is_admin'])->group(function () {




        Route::get('/files', [AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');

        Route::get('/profile', [AdminProfileController::class, 'edit'])
            ->name('admin.profile');

        Route::post('/profile-update', [AdminProfileController::class, 'update'])
            ->name('admin.profile.update');

        Route::get('/edit-password', [AdminProfileController::class, 'editpassword'])
            ->name('admin.password');

        Route::post('/update-password', [AdminProfileController::class, 'updatepassword'])
            ->name('admin.password.update');

        Route::get('/logout', [AdminAuthController::class, 'logout'])
            ->name('admin.logout');

        Route::post('/check-admin-email', [AdminProfileController::class, 'checkEmail'])
            ->name('admin.checkEmail');

        //reels

        Route::get('/add-media', [AdminMediaController::class, 'add'])
            ->name('admin.media.add');


        Route::post('/media/check-name', [AdminMediaController::class, 'checkName'])
            ->name('admin.media.check-name');

        Route::post('/media/store', [AdminMediaController::class, 'store'])
            ->name('admin.media.store');



        Route::get(
            '/files/reel/{id}/progress',
            [AdminDashboardController::class, 'reelProgress']
        )->name('admin.reel.progress');



        Route::get(
            '/media/{directory}/edit',
            [AdminMediaController::class, 'edit']
        )->name('admin.media.edit');

        Route::put(
            '/media/{directory}',
            [AdminMediaController::class, 'update']
        )->name('admin.media.update');

        Route::delete(
            '/media/{directory}',
            [AdminDashboardController::class, 'destroy']
        )->name('admin.media.destroy');

         Route::get(
            '/media/{directory}/view',
            [AdminDashboardController::class, 'view']
        )->name('admin.media.view');
    });

});