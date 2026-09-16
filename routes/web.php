<?php

use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\DocumentVersionController;
use App\Http\Controllers\Admin\DocumentPageController;
use App\Http\Controllers\Admin\VariableController;
use App\Http\Controllers\DocumentGenerationController;
use App\Http\Controllers\Admin\GeneratedDocumentController;
use App\Http\Controllers\MyDocumentsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques — génération de documents
|--------------------------------------------------------------------------
*/

Route::get('generer', [DocumentGenerationController::class, 'countries'])
    ->name('generate.countries');

Route::get('generer/{country}/documents', [DocumentGenerationController::class, 'documents'])
    ->name('generate.documents');

Route::get('generer/{country}/{documentType}/formulaire', [DocumentGenerationController::class, 'form'])
    ->name('generate.form');

Route::post('generer/{documentType}/soumettre', [DocumentGenerationController::class, 'submit'])
    ->middleware('throttle:6,1')
    ->name('generate.submit');

Route::get('generer/succes/{generatedDocument}', [DocumentGenerationController::class, 'success'])
    ->name('generate.success');

Route::get('generer/telecharger/{generatedDocument}/{format?}', [DocumentGenerationController::class, 'download'])
    ->where('format', 'pdf|image')
    ->name('generate.download');
    Route::get('/documents/{generatedDocument}/preview', [DocumentGenerationController::class, 'preview'])->name('generate.preview');

Route::post('/documents/{generatedDocument}/pay', [PaymentController::class, 'pay'])
    ->middleware('auth')
    ->name('generate.pay');
Route::post('/webhooks/geniuspay', [PaymentController::class, 'webhook'])->name('webhooks.geniuspay');

Route::redirect('/', '/generer');
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.submit');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('mes-documents', [MyDocumentsController::class, 'index'])
    ->middleware('auth')
    ->name('my-documents.index');

    Route::get('cgu', function () {
    return view('public.cgu');
})->name('cgu');

Route::get('inscription', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('inscription', [RegisterController::class, 'register'])->name('register.submit');


Route::middleware('auth')->group(function () {
    Route::get('email/verifier', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('email/verifier/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verifier/renvoyer', [VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.resend');
});
/*
|--------------------------------------------------------------------------
| Routes Admin — Étapes 2, 3 & 4
|--------------------------------------------------------------------------
| Middleware 'admin' à créer (voir note en bas de fichier).
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin']) // adapte selon ton middleware réel
    ->group(function () {

        Route::resource('countries', CountryController::class);

        Route::resource('document-types', DocumentTypeController::class);

        Route::get('document-types/{documentType}/versions', [DocumentVersionController::class, 'index'])
            ->name('document-versions.index');

        Route::get('document-types/{documentType}/versions/create', [DocumentVersionController::class, 'create'])
            ->name('document-versions.create');

        Route::post('document-types/{documentType}/versions', [DocumentVersionController::class, 'store'])
            ->name('document-versions.store');

        Route::get('document-versions/{documentVersion}/edit', [DocumentVersionController::class, 'edit'])
            ->name('document-versions.edit');
        Route::get('document-versions/{documentVersion}', [DocumentVersionController::class, 'show'])
            ->name('document-versions.show');

        Route::put('document-versions/{documentVersion}', [DocumentVersionController::class, 'update'])
            ->name('document-versions.update');

        Route::post('document-versions/{documentVersion}/publish', [DocumentVersionController::class, 'publish'])
            ->name('document-versions.publish');

        Route::post('document-versions/{documentVersion}/duplicate', [DocumentVersionController::class, 'duplicate'])
            ->name('document-versions.duplicate');

        Route::delete('document-versions/{documentVersion}', [DocumentVersionController::class, 'destroy'])
            ->name('document-versions.destroy');

        Route::post('document-versions/{documentVersion}/pages', [DocumentPageController::class, 'store'])
            ->name('document-versions.pages.store');

        Route::delete('document-versions/{documentVersion}/pages/{documentPage}', [DocumentPageController::class, 'destroy'])
            ->name('document-versions.pages.destroy');

        Route::get('document-versions/{documentVersion}/pages/{documentPage}/canvas', [DocumentPageController::class, 'canvas'])
            ->name('document-versions.pages.canvas');
            Route::get('document-versions/{documentVersion}/pages/{documentPage}/preview', [DocumentPageController::class, 'preview'])
            ->name('document-pages.preview');

        Route::post('pages/{documentPage}/variables', [VariableController::class, 'store'])
            ->name('variables.store');

        Route::put('variables/{variable}', [VariableController::class, 'update'])
            ->name('variables.update');

        Route::delete('variables/{variable}', [VariableController::class, 'destroy'])
            ->name('variables.destroy');

            Route::get('generated-documents', [GeneratedDocumentController::class, 'index'])
    ->name('generated-documents.index');


    Route::get('versions', [DocumentVersionController::class, 'all'])
            ->name('versions.index');

        Route::get('variables', [VariableController::class, 'index'])
            ->name('variables.index');

        Route::get('settings', [SettingController::class, 'edit'])
            ->name('settings.edit');

        Route::put('settings', [SettingController::class, 'update'])
            ->name('settings.update');

        Route::resource('users', UserController::class)->except(['show']);
    });
