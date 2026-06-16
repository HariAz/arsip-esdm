<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Document;
use App\Models\DownloadRequest;
use App\Models\ActivityLog;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_active'        => Document::active()->count(),
            'total_pending'       => Document::where('status', Document::STATUS_PENDING_APPROVAL)->count(),
            'total_rejected'      => Document::where('status', Document::STATUS_REJECTED)->count(),
            'total_deleted'       => Document::onlyTrashed()->count(),
            'biasa'               => Document::active()->where('classification', Document::CLASSIFICATION_BIASA)->count(),
            'terbatas'            => Document::active()->where('classification', Document::CLASSIFICATION_TERBATAS)->count(),
            'rahasia'             => Document::active()->where('classification', Document::CLASSIFICATION_RAHASIA)->count(),
            'sangat_rahasia'      => Document::active()->where('classification', Document::CLASSIFICATION_SANGAT_RAHASIA)->count(),
            'download_pending'    => DownloadRequest::where('status', 'pending')->count(),
            'download_approved'   => DownloadRequest::where('status', 'approved')->count(),
            'download_downloaded' => DownloadRequest::where('status', 'downloaded')->count(),
            'total_users'         => User::count(),
        ];

        $recentLogs = ActivityLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        $recentDocs = Document::with(['division', 'uploader'])
            ->active()
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentLogs', 'recentDocs'));
    }
}
