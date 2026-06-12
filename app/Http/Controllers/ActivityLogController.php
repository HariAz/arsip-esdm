<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user', 'document'])
            ->orderByDesc('created_at');

        // Filter aksi
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter tanggal dari
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // Filter tanggal sampai
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter kata kunci (description / actor)
        if ($request->filled('search')) {
            $kw = $request->search;
            $query->where(function ($q) use ($kw) {
                $q->where('description', 'LIKE', "%{$kw}%")
                  ->orWhere('actor_name', 'LIKE', "%{$kw}%")
                  ->orWhere('actor_email', 'LIKE', "%{$kw}%");
            });
        }

        $logs  = $query->paginate(25)->withQueryString();
        $users = \App\Models\User::orderBy('name')->get();

        // Daftar aksi unik untuk dropdown filter
        $actions = ActivityLog::selectRaw('DISTINCT action')
            ->orderBy('action')
            ->pluck('action');

        return view('activity-logs.index', compact('logs', 'users', 'actions'));
    }
}
