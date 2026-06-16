<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DownloadRequestController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Magic Link Approval — PUBLIK (tanpa login)
|--------------------------------------------------------------------------
*/
// Download approval
Route::get('/approve/download/{token}/{action}',
    [ApprovalController::class, 'showDownload'])
    ->name('approval.download.show')
    ->where('action', 'approve|reject');

Route::post('/approve/download/{token}/{action}',
    [ApprovalController::class, 'decideDownload'])
    ->name('approval.download.decide')
    ->where('action', 'approve|reject');

// Upload approval (Sangat Rahasia)
Route::get('/approve/upload/{token}/{action}',
    [ApprovalController::class, 'showUpload'])
    ->name('approval.upload.show')
    ->where('action', 'approve|reject');

Route::post('/approve/upload/{token}/{action}',
    [ApprovalController::class, 'decideUpload'])
    ->name('approval.upload.decide')
    ->where('action', 'approve|reject');

/*
|--------------------------------------------------------------------------
| Protected Routes — Arsiparis
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // ── Dashboard ─────────────────────────────────────────────────
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ── Dokumen ──────────────────────────────────────
    Route::resource('documents', DocumentController::class);
    Route::get('/documents/{document}/preview',
        [DocumentController::class, 'preview'])->name('documents.preview');
    Route::get('/documents/{document}/preview/pdf',
        [DocumentController::class, 'previewStream'])->name('documents.preview.stream');
    Route::get('/documents/{document}/download',
        [DocumentController::class, 'download'])->name('documents.download');

    // Trash & restore
    Route::get('/documents-trash',
        [DocumentController::class, 'trashed'])->name('documents.trashed');
    Route::patch('/documents/{id}/restore',
        [DocumentController::class, 'restore'])->name('documents.restore');
    Route::delete('/documents/{id}/force-delete',
        [DocumentController::class, 'forceDelete'])->name('documents.force-delete');

    // ── Download Requests ────────────────────────────
    Route::resource('download-requests', DownloadRequestController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::get('/download-requests/{downloadRequest}/download',
        [DownloadRequestController::class, 'download'])
        ->name('download-requests.download');

    Route::post('/download-requests/{downloadRequest}/resend',
        [DownloadRequestController::class, 'resend'])
        ->name('download-requests.resend');

    // ── Divisi ────────────────────────────────────────────────────
    Route::resource('divisions', \App\Http\Controllers\DivisionController::class);
    Route::patch('/divisions/{division}/toggle',
        [\App\Http\Controllers\DivisionController::class, 'toggleActive'])
        ->name('divisions.toggle');

    // ── Pengguna ──────────────────────────────────────────────────
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::patch('/users/{user}/toggle',
        [UserController::class, 'toggleActive'])->name('users.toggle');

    // ── Activity Log ──────────────────────────────────────────────
    Route::get('/activity-logs',
        [\App\Http\Controllers\ActivityLogController::class, 'index'])
        ->name('activity-logs.index');
});
