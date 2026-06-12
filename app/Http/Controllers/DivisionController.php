<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Division;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class DivisionController extends Controller
{
    /**
     * Daftar semua divisi
     */
    public function index()
    {
        $divisions = Division::withCount('documents')
            ->orderBy('code')
            ->paginate(15);

        return view('divisions.index', compact('divisions'));
    }

    /**
     * Form tambah divisi
     */
    public function create()
    {
        return view('divisions.create');
    }

    /**
     * Simpan divisi baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                    => ['required', 'string', 'max:150'],
            'code'                    => ['required', 'string', 'max:20', 'unique:divisions,code',
                                         'regex:/^[A-Z0-9\-]+$/'],
            'head_name'               => ['required', 'string', 'max:100'],
            'head_email'              => ['required', 'email', 'max:150'],
            'head_phone'              => ['nullable', 'string', 'max:20'],
            'general_affairs_name'    => ['required', 'string', 'max:100'],
            'general_affairs_email'   => ['required', 'email', 'max:150'],
        ], [
            'name.required'                    => 'Nama divisi wajib diisi.',
            'code.required'                    => 'Kode divisi wajib diisi.',
            'code.unique'                      => 'Kode divisi sudah digunakan.',
            'code.regex'                       => 'Kode hanya boleh huruf kapital, angka, dan tanda hubung.',
            'head_name.required'               => 'Nama kepala divisi wajib diisi.',
            'head_email.required'              => 'Email kepala divisi wajib diisi.',
            'head_email.email'                 => 'Format email kepala divisi tidak valid.',
            'general_affairs_name.required'    => 'Nama Bagian Umum wajib diisi.',
            'general_affairs_email.required'   => 'Email Bagian Umum wajib diisi.',
            'general_affairs_email.email'      => 'Format email Bagian Umum tidak valid.',
        ]);

        $division = Division::create([
            'name'                  => $request->name,
            'code'                  => strtoupper($request->code),
            'head_name'             => $request->head_name,
            'head_email'            => $request->head_email,
            'head_phone'            => $request->head_phone,
            'general_affairs_name'  => $request->general_affairs_name,
            'general_affairs_email' => $request->general_affairs_email,
            'is_active'             => true,
        ]);

        ActivityLog::record(
            action: ActivityLog::ACTION_DIVISION_CREATED,
            userId: Auth::id(),
            description: "Divisi baru dibuat: {$division->name} ({$division->code})",
        );

        return redirect()->route('divisions.index')
            ->with('success', "Divisi \"{$division->name}\" berhasil ditambahkan.");
    }

    /**
     * Detail divisi
     */
    public function show(Division $division)
    {
        $division->loadCount('documents');
        $recentDocuments = $division->documents()
            ->active()
            ->orderByDesc('document_date')
            ->limit(5)
            ->get();

        return view('divisions.show', compact('division', 'recentDocuments'));
    }

    /**
     * Form edit divisi
     */
    public function edit(Division $division)
    {
        return view('divisions.edit', compact('division'));
    }

    /**
     * Update divisi
     */
    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name'                    => ['required', 'string', 'max:150'],
            'code'                    => ['required', 'string', 'max:20',
                                         'unique:divisions,code,' . $division->id,
                                         'regex:/^[A-Z0-9\-]+$/'],
            'head_name'               => ['required', 'string', 'max:100'],
            'head_email'              => ['required', 'email', 'max:150'],
            'head_phone'              => ['nullable', 'string', 'max:20'],
            'general_affairs_name'    => ['required', 'string', 'max:100'],
            'general_affairs_email'   => ['required', 'email', 'max:150'],
            'is_active'               => ['boolean'],
        ], [
            'name.required'                    => 'Nama divisi wajib diisi.',
            'code.required'                    => 'Kode divisi wajib diisi.',
            'code.unique'                      => 'Kode divisi sudah digunakan oleh divisi lain.',
            'code.regex'                       => 'Kode hanya boleh huruf kapital, angka, dan tanda hubung.',
            'head_name.required'               => 'Nama kepala divisi wajib diisi.',
            'head_email.required'              => 'Email kepala divisi wajib diisi.',
            'head_email.email'                 => 'Format email kepala divisi tidak valid.',
            'general_affairs_name.required'    => 'Nama Bagian Umum wajib diisi.',
            'general_affairs_email.required'   => 'Email Bagian Umum wajib diisi.',
            'general_affairs_email.email'      => 'Format email Bagian Umum tidak valid.',
        ]);

        $old = $division->only(['name', 'head_name', 'head_email', 'general_affairs_name', 'general_affairs_email']);

        $division->update([
            'name'                  => $request->name,
            'code'                  => strtoupper($request->code),
            'head_name'             => $request->head_name,
            'head_email'            => $request->head_email,
            'head_phone'            => $request->head_phone,
            'general_affairs_name'  => $request->general_affairs_name,
            'general_affairs_email' => $request->general_affairs_email,
            'is_active'             => $request->boolean('is_active', true),
        ]);

        ActivityLog::record(
            action: ActivityLog::ACTION_DIVISION_UPDATED,
            userId: Auth::id(),
            description: "Divisi diperbarui: {$division->name} ({$division->code})",
            metadata: ['before' => $old, 'after' => $division->only(['name', 'head_name', 'head_email', 'general_affairs_name', 'general_affairs_email'])],
        );

        return redirect()->route('divisions.index')
            ->with('success', "Divisi \"{$division->name}\" berhasil diperbarui.");
    }

    /**
     * Toggle status aktif/nonaktif
     */
    public function toggleActive(Division $division)
    {
        // Tidak boleh menonaktifkan divisi yang masih punya dokumen aktif
        if ($division->is_active && $division->documents()->active()->exists()) {
            return back()->with('error',
                "Divisi \"{$division->name}\" tidak dapat dinonaktifkan karena masih memiliki dokumen aktif.");
        }

        $division->update(['is_active' => !$division->is_active]);

        $status = $division->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::record(
            action: ActivityLog::ACTION_DIVISION_TOGGLED,
            userId: Auth::id(),
            description: "Divisi {$status}: {$division->name}",
        );

        return back()->with('success', "Divisi \"{$division->name}\" berhasil {$status}.");
    }

    /**
     * Hapus divisi (hanya jika tidak ada dokumen)
     */
    public function destroy(Division $division)
    {
        if ($division->documents()->exists()) {
            return back()->with('error',
                "Divisi \"{$division->name}\" tidak dapat dihapus karena masih memiliki dokumen terkait.");
        }

        $name = $division->name;
        $division->delete();

        ActivityLog::record(
            action: ActivityLog::ACTION_DIVISION_DELETED,
            userId: Auth::id(),
            description: "Divisi dihapus: {$name}",
        );

        return redirect()->route('divisions.index')
            ->with('success', "Divisi \"{$name}\" berhasil dihapus.");
    }
}
