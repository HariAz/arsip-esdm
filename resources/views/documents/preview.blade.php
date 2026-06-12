@extends('layouts.app')

@section('title', 'Preview — ' . $document->title)
@section('page-title', 'Preview Dokumen')

@section('content')

{{-- ── HEADER DOKUMEN ── --}}
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        <div>
            <div class="fw-semibold" style="font-size:15px; color:var(--esdm-navy);">
                {{ $document->title }}
            </div>
            <div class="text-muted" style="font-size:12px;">
                {{ $document->document_number }}
                &nbsp;·&nbsp;
                <span class="badge badge-{{ str_replace('_','-',$document->classification) }}">
                    {{ $document->classification_label }}
                </span>
                &nbsp;·&nbsp;
                {{ $document->file_name }}
                &nbsp;·&nbsp;
                {{ $document->file_size_formatted }}
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button onclick="toggleFullscreen()" class="btn btn-outline-secondary btn-sm" id="btn-fullscreen" title="Layar penuh">
            <i class="bi bi-fullscreen" id="icon-fullscreen"></i>
        </button>
        @if($document->isFreeDownload())
        <a href="{{ route('documents.download', $document) }}" class="btn btn-success btn-sm">
            <i class="bi bi-download me-1"></i>Download
        </a>
        @endif
    </div>
</div>


{{-- ── VIEWER WRAPPER ── --}}
{{-- Wrapper outer: fixed height, clips overflow --}}
<div id="preview-card" class="card p-0" style="height:78vh; overflow:hidden; border-radius:8px; background:#525659;">
    <iframe
        id="pdf-frame"
        src="{{ route('documents.preview.stream', $document) }}#toolbar=0&navpanes=0"
        style="width:100%; height:78vh; border:none; display:block;"
        title="Preview: {{ $document->title }}"
    ></iframe>
</div>

{{-- Fallback jika browser tidak support inline PDF --}}
<div id="pdf-fallback" class="d-none mt-3">
    <div class="alert alert-warning d-flex gap-2">
        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
        <div>
            Browser Anda tidak dapat menampilkan PDF secara inline.
            @if($document->isFreeDownload())
                <a href="{{ route('documents.download', $document) }}" class="fw-semibold">
                    Klik di sini untuk mendownload file.
                </a>
            @else
                Silakan gunakan Chrome atau Edge untuk preview PDF.
            @endif
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .badge-sangat-rahasia { background:#fee2e2; color:#7f1d1d; }
    .badge-rahasia        { background:#fef3c7; color:#92400e; }
    .badge-terbatas       { background:#dbeafe; color:#1e3a8a; }
    .badge-biasa          { background:#d1fae5; color:#065f46; }

    /* Fullscreen mode */
    #preview-card.fullscreen-mode {
        position: fixed !important;
        inset: 0;
        z-index: 1050;
        height: 100vh !important;
        border-radius: 0 !important;
    }
    #preview-card.fullscreen-mode #pdf-frame {
        height: 100vh !important;
    }
</style>
@endpush

@push('scripts')
<script>
    const frame = document.getElementById('pdf-frame');

    // Deteksi jika iframe gagal load PDF (browser tidak support)
    frame.addEventListener('load', function () {
        try {
            if (frame.contentDocument && frame.contentDocument.body &&
                frame.contentDocument.body.innerHTML === '') {
                document.getElementById('pdf-fallback').classList.remove('d-none');
            }
        } catch (e) { /* PDF tampil normal via stream */ }
    });

    // Toggle fullscreen
    function toggleFullscreen() {
        const card = document.getElementById('preview-card');
        const icon = document.getElementById('icon-fullscreen');
        const btn  = document.getElementById('btn-fullscreen');
        const isFs = card.classList.toggle('fullscreen-mode');

        icon.className = isFs ? 'bi bi-fullscreen-exit' : 'bi bi-fullscreen';
        btn.title      = isFs ? 'Keluar layar penuh' : 'Layar penuh';
    }

    // ESC untuk keluar fullscreen
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && document.getElementById('preview-card').classList.contains('fullscreen-mode')) {
            toggleFullscreen();
        }
    });
</script>
@endpush
