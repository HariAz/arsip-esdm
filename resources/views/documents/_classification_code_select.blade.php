{{--
    Partial: dropdown kode klasifikasi arsip
    Berdasarkan Kepmen ESDM No. 167 K/04/MEM/2020
    Params: $selectedCode (string, nilai yang sudah dipilih)
--}}
@php
$groups = [
    '— I. RUMPUN FASILITATIF —' => [
        'HK (HUKUM)' => [
            'HK.01' => 'HK.01 — Peraturan Perundang-Undangan',
            'HK.02' => 'HK.02 — Bantuan Hukum / Kasus Hukum',
            'HK.03' => 'HK.03 — Perjanjian Dalam Negeri',
            'HK.04' => 'HK.04 — Perjanjian Luar Negeri',
            'HK.05' => 'HK.05 — Nota Kesepahaman (MoU)',
        ],
        'PR (PERENCANAAN)' => [
            'PR.01' => 'PR.01 — Rencana Kerja & Strategis (Renstra/RKT)',
            'PR.02' => 'PR.02 — Program Kerja',
            'PR.03' => 'PR.03 — Kerja Sama Antar Lembaga',
            'PR.04' => 'PR.04 — Laporan Akuntabilitas (LAKIP)',
        ],
        'OT (ORGANISASI & TATA LAKSANA)' => [
            'OT.01' => 'OT.01 — Kelembagaan / Struktur Organisasi',
            'OT.02' => 'OT.02 — Tata Laksana / SOP',
            'OT.03' => 'OT.03 — Analisis Jabatan & Beban Kerja',
        ],
        'KP (KEPEGAWAIAN)' => [
            'KP.01' => 'KP.01 — Pengadaan & Pengangkatan Pegawai',
            'KP.02' => 'KP.02 — Mutasi Jabatan / Kepangkatan',
            'KP.03' => 'KP.03 — Pengembangan Pegawai (Diklat)',
            'KP.04' => 'KP.04 — Penilaian Kinerja (SKP)',
            'KP.05' => 'KP.05 — Kesejahteraan & Penghargaan',
            'KP.06' => 'KP.06 — Disiplin Pegawai (Cuti, Sanksi)',
        ],
        'KU (KEUANGAN)' => [
            'KU.01' => 'KU.01 — Penganggaran (DIPA, RKA-KL)',
            'KU.02' => 'KU.02 — Perbendaharaan & Pendapatan',
            'KU.03' => 'KU.03 — Pelaksanaan Anggaran (SPM, SP2D)',
            'KU.04' => 'KU.04 — Akuntansi & Pelaporan Keuangan',
        ],
        'PL (PERLENGKAPAN / LOGISTIK)' => [
            'PL.01' => 'PL.01 — Pengadaan Barang/Jasa (BMN)',
            'PL.02' => 'PL.02 — Penyimpanan & Distribusi',
            'PL.03' => 'PL.03 — Inventarisasi & Pemeliharaan Aset',
            'PL.04' => 'PL.04 — Penghapusan Barang Milik Negara',
        ],
        'TU (TATA USAHA & KEARSIPAN)' => [
            'TU.01' => 'TU.01 — Tata Persuratan / Naskah Dinas',
            'TU.02' => 'TU.02 — Pengelolaan Arsip (Penyusutan)',
            'TU.03' => 'TU.03 — Rumah Tangga & Protokol',
        ],
        'HM (HUBUNGAN MASYARAKAT)' => [
            'HM.01' => 'HM.01 — Layanan Informasi / PPID',
            'HM.02' => 'HM.02 — Hubungan Media / Siaran Pers',
            'HM.03' => 'HM.03 — Pengaduan Masyarakat',
            'HM.04' => 'HM.04 — Publikasi & Dokumentasi',
        ],
        'TI (TEKNOLOGI INFORMASI)' => [
            'TI.01' => 'TI.01 — Pengembangan Sistem Aplikasi',
            'TI.02' => 'TI.02 — Pengelolaan Jaringan & Infrastruktur',
            'TI.03' => 'TI.03 — Keamanan Data & Informasi',
        ],
        'PS (PENGAWASAN)' => [
            'PS.01' => 'PS.01 — Audit / Pemeriksaan Internal',
            'PS.02' => 'PS.02 — Laporan Hasil Pemeriksaan (LHP)',
            'PS.03' => 'PS.03 — Tindak Lanjut Hasil Pemeriksaan (TLHP)',
        ],
    ],
    '— II. RUMPUN SUBSTANTIF —' => [
        'MG (MINYAK DAN GAS BUMI)' => [
            'MG.01' => 'MG.01 — Kebijakan & Program Sektor Migas',
            'MG.02' => 'MG.02 — Kegiatan Usaha Hulu Migas',
            'MG.03' => 'MG.03 — Kegiatan Usaha Hilir Migas',
            'MG.04' => 'MG.04 — Infrastruktur & Keselamatan Migas',
        ],
        'MB (MINERAL DAN BATUBARA)' => [
            'MB.01' => 'MB.01 — Wilayah Pertambangan & Informasi Minerba',
            'MB.02' => 'MB.02 — Pengusahaan Mineral (IUP)',
            'MB.03' => 'MB.03 — Pengusahaan Batubara',
            'MB.04' => 'MB.04 — Teknik & Lingkungan Minerba',
            'MB.05' => 'MB.05 — Penerimaan Negara (PNBP) Sektor Minerba',
        ],
        'TL (TENAGA LISTRIK)' => [
            'TL.01' => 'TL.01 — Pembinaan & Pengusahaan Ketenagalistrikan',
            'TL.02' => 'TL.02 — Teknik & Lingkungan Ketenagalistrikan',
            'TL.03' => 'TL.03 — Tarif & Subsidi Listrik',
        ],
        'RE (ENERGI BARU TERBARUKAN)' => [
            'RE.01' => 'RE.01 — Pengusahaan Bioenergi, Surya, Angin, Air',
            'RE.02' => 'RE.02 — Panas Bumi (Geothermal)',
            'RE.03' => 'RE.03 — Konservasi & Efisiensi Energi',
        ],
        'GL (GEOLOGI)' => [
            'GL.01' => 'GL.01 — Pemetaan & Survei Geologi',
            'GL.02' => 'GL.02 — Sumber Daya Mineral, Batubara & Panas Bumi',
            'GL.03' => 'GL.03 — Air Tanah & Geologi Lingkungan',
            'GL.04' => 'GL.04 — Mitigasi Bencana Geologi',
            'GL.05' => 'GL.05 — Pengujian Laboratorium Teknis',
        ],
    ],
];
@endphp

<select
    name="document_classification_code"
    class="form-select font-monospace @error('document_classification_code') is-invalid @enderror"
    style="font-size:13px;"
>
    <option value="">— Pilih Kode Klasifikasi —</option>
    @foreach($groups as $rumpunLabel => $subGroups)
        <option disabled style="font-weight:700; color:#6b7280; background:#f3f4f6;">{{ $rumpunLabel }}</option>
        @foreach($subGroups as $groupName => $codes)
            <optgroup label="{{ $groupName }}">
                @foreach($codes as $code => $label)
                    <option value="{{ $code }}"
                        {{ $selectedCode === $code ? 'selected' : '' }}
                        style="font-family: monospace;">
                        {{ $label }}
                    </option>
                @endforeach
            </optgroup>
        @endforeach
    @endforeach
</select>
