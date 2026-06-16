@php
    $statusMap = [
        'waiting'  => ['icon' => 'bi-clock',             'pill' => 'pill-waiting',  'label' => 'Menunggu'],
        'sent'     => ['icon' => 'bi-envelope-open',      'pill' => 'pill-sent',     'label' => 'Email Terkirim'],
        'approved' => ['icon' => 'bi-check-lg',           'pill' => 'pill-approved', 'label' => 'Disetujui'],
        'rejected' => ['icon' => 'bi-x-lg',              'pill' => 'pill-rejected', 'label' => 'Ditolak'],
    ];
    $st  = $step?->status ?? 'waiting';
    $cfg = $statusMap[$st] ?? $statusMap['waiting'];
    $stepLabel = match($stepNum) { 1 => 'Bagian Umum', 2 => 'Kepala Divisi', default => "Step {$stepNum}" };
@endphp

<div class="d-flex gap-3 align-items-start">
    <div class="step-badge {{ $st }}">
        <i class="bi {{ $cfg['icon'] }}"></i>
    </div>
    <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="fw-semibold" style="font-size:14px; color:var(--esdm-navy);">
                Step {{ $stepNum }} — {{ $stepLabel }}
            </span>
            <span class="step-status-pill {{ $cfg['pill'] }}">{{ $cfg['label'] }}</span>
        </div>

        @if($step)
        <div class="text-muted mt-1" style="font-size:12px;">
            <i class="bi bi-person me-1"></i>{{ $step->approver_name }}
            &nbsp;·&nbsp;
            <i class="bi bi-envelope me-1"></i>{{ $step->approver_email }}
        </div>

        @if(in_array($st, ['approved', 'rejected']) && $step->decided_at)
        <div class="mt-1" style="font-size:12px; color:#374151;">
            <i class="bi bi-calendar-check me-1 text-muted"></i>
            {{ $step->decided_at->translatedFormat('d F Y, H:i') }} WIB
        </div>
        @endif

        @if($st === 'rejected' && $step->rejection_reason)
        <div class="rejection-box">
            <strong>Alasan Penolakan:</strong> {{ $step->rejection_reason }}
        </div>
        @endif

        @if($st === 'sent' && $step->token_expires_at)
        <div class="mt-1" style="font-size:11px; color:#6b7280;">
            <i class="bi bi-alarm me-1"></i>
            Link kadaluwarsa: {{ $step->token_expires_at->translatedFormat('d F Y, H:i') }} WIB
            @if($step->isExpired())
                <span class="text-danger fw-semibold">(Sudah kadaluwarsa)</span>
            @endif
        </div>
        @endif

        @else
        <div class="text-muted mt-1" style="font-size:12px;">
            Menunggu langkah sebelumnya selesai.
        </div>
        @endif
    </div>
</div>
