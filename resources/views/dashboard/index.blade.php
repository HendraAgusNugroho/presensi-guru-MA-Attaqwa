@extends('layouts.app')
@section('title','Dashboard')
@section('meta_description', 'Dashboard presensi guru MA Attaqwa — ringkasan kehadiran, statistik, dan scan terbaru hari ini.')

@push('styles')
<style>
.super-admin-banner {
    background: linear-gradient(135deg, #b91c1c, #dc2626);
    border-radius: 14px; padding: 16px 20px; color: #fff;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px; margin-bottom: 18px;
}
.school-welcome {
    background: linear-gradient(135deg, hsl(145,60%,18%), hsl(145,55%,28%));
    border-radius: 14px; padding: 18px 22px; color: #fff;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px; margin-bottom: 0;
    height: 100%;
    min-height: 100%;
}
.school-welcome h2 { font-size: 1.15rem; font-weight: 800; margin-bottom: 3px; }
.school-welcome p  { font-size: .85rem; opacity: .85; }
.school-welcome .date-badge {
    background: rgba(255,255,255,.15); padding: 8px 16px; border-radius: 10px;
    font-size: .85rem; font-weight: 700; text-align: center;
    border: 1px solid rgba(255,255,255,.2); white-space: nowrap;
    flex-shrink: 0;
}
.school-welcome .date-badge .day { font-size: 1.4rem; font-weight: 900; display: block; }

.dashboard-hero {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
    margin-bottom: 20px;
}
@media (min-width: 900px) {
    .dashboard-hero {
        grid-template-columns: 1.15fr 1fr;
        align-items: stretch;
    }
}

.greeting-card {
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    width: 100%;
    height: 100%;
    min-height: 100%;
    border: 1.5px solid;
}
.greeting-card__left {
    display: flex;
    align-items: center;
    gap: 14px;
    flex: 1;
    min-width: 0;
}
.greeting-card__icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.greeting-card__icon i { font-size: 1.1rem; }
.greeting-card__content { flex: 1; min-width: 0; }
.greeting-card__title { font-weight: 800; font-size: .92rem; line-height: 1.35; }
.greeting-card__sub { font-size: .82rem; color: #475569; margin-top: 4px; line-height: 1.45; }
.greeting-card__aside {
    flex-shrink: 0;
    text-align: center;
    padding: 10px 14px;
    border-radius: 10px;
    background: rgba(255,255,255,.6);
    border: 1px solid rgba(0,0,0,.06);
    min-width: 72px;
}
.greeting-card__time {
    font-size: 1.25rem;
    font-weight: 800;
    display: block;
    line-height: 1.1;
    font-variant-numeric: tabular-nums;
}
.greeting-card__tz {
    font-size: .68rem;
    font-weight: 700;
    opacity: .75;
    letter-spacing: .04em;
    text-transform: uppercase;
}
.greeting-card--pagi { background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-color: #fde68a; }
.greeting-card--pagi .greeting-card__icon { background: rgba(217, 119, 6, .12); }
.greeting-card--pagi .greeting-card__icon i,
.greeting-card--pagi .greeting-card__title,
.greeting-card--pagi .greeting-card__time { color: #d97706; }
.greeting-card--siang { background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-color: #bae6fd; }
.greeting-card--siang .greeting-card__icon { background: rgba(2, 132, 199, .12); }
.greeting-card--siang .greeting-card__icon i,
.greeting-card--siang .greeting-card__title,
.greeting-card--siang .greeting-card__time { color: #0284c7; }
.greeting-card--sore { background: linear-gradient(135deg, #faf5ff 0%, #ede9fe 100%); border-color: #ddd6fe; }
.greeting-card--sore .greeting-card__icon { background: rgba(124, 58, 237, .12); }
.greeting-card--sore .greeting-card__icon i,
.greeting-card--sore .greeting-card__title,
.greeting-card--sore .greeting-card__time { color: #7c3aed; }
.greeting-card--malam { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-color: #bfdbfe; }
.greeting-card--malam .greeting-card__icon { background: rgba(30, 64, 175, .12); }
.greeting-card--malam .greeting-card__icon i,
.greeting-card--malam .greeting-card__title,
.greeting-card--malam .greeting-card__time { color: #1e40af; }

@media (max-width: 599px) {
    .greeting-card {
        flex-wrap: wrap;
        align-items: flex-start;
    }
    .greeting-card__aside {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 8px 12px;
    }
    .greeting-card__time { font-size: 1.1rem; }
}
</style>
@endpush

@section('content')

{{-- Super Admin Banner --}}
@if(auth()->user()->isSuperAdmin())
<div class="super-admin-banner">
    <div style="display:flex;align-items:center;gap:14px">
        <div style="width:44px;height:44px;background:rgba(255,255,255,.15);border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <i class="fas fa-shield-halved" style="font-size:1.2rem"></i>
        </div>
        <div>
            <div style="font-weight:800;font-size:1rem">Mode Super Admin Aktif</div>
            <div style="font-size:.8rem;opacity:.85">Anda memiliki akses penuh ke seluruh sistem</div>
        </div>
    </div>
    <a href="{{ route('pengguna.index') }}"
       style="background:rgba(255,255,255,.15);color:#fff;padding:8px 16px;border-radius:8px;
              text-decoration:none;font-weight:700;font-size:.85rem;border:1px solid rgba(255,255,255,.3);
              display:flex;align-items:center;gap:6px;">
        <i class="fas fa-users-gear"></i> Kelola Pengguna
    </a>
</div>
@endif

{{-- Welcome + Greeting --}}
<div class="dashboard-hero">
<div class="school-welcome">
    <div style="display:flex;align-items:center;gap:16px;flex:1;min-width:0">
        <div style="width:52px;height:52px;background:hsl(48,96%,53%);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(0,0,0,.2);">
            <i class="fas fa-mosque" style="color:hsl(145,60%,18%);font-size:1.2rem" aria-hidden="true"></i>
        </div>
        <div style="min-width:0">
            <h2>Selamat datang, {{ auth()->user()->name }}!</h2>
            <p>YPIA Daarul Mu'min — Madrasah Aliyah Attaqwa Benda Tangerang</p>
        </div>
    </div>
    <div class="date-badge">
        <span class="day">{{ $today->format('d') }}</span>
        {{ $today->isoFormat('MMMM Y') }}<br>
        <span style="font-size:.75rem;opacity:.8">{{ $today->isoFormat('dddd') }}</span>
    </div>
</div>

{{-- Greeting Otomatis Berdasarkan Waktu --}}
@php
    $jam = (int) now()->format('H');
    if ($jam >= 5 && $jam < 12) {
        $greetClass = 'greeting-card--pagi';
        $greetIcon  = 'fa-sun';
        $greetText  = 'Selamat Pagi';
        $greetSub   = 'Semoga aktivitas mengajar hari ini berjalan lancar dan penuh berkah.';
    } elseif ($jam >= 12 && $jam < 15) {
        $greetClass = 'greeting-card--siang';
        $greetIcon  = 'fa-cloud-sun';
        $greetText  = 'Selamat Siang';
        $greetSub   = 'Tetap semangat dalam menjalankan aktivitas di sekolah.';
    } elseif ($jam >= 15 && $jam < 18) {
        $greetClass = 'greeting-card--sore';
        $greetIcon  = 'fa-cloud-sun-rain';
        $greetText  = 'Selamat Sore';
        $greetSub   = 'Terima kasih atas dedikasi Anda hari ini untuk pendidikan.';
    } else {
        $greetClass = 'greeting-card--malam';
        $greetIcon  = 'fa-moon';
        $greetText  = 'Selamat Malam';
        $greetSub   = 'Jangan lupa menjaga kesehatan dan istirahat yang cukup.';
    }
@endphp
<div class="greeting-card {{ $greetClass }}">
    <div class="greeting-card__left">
        <div class="greeting-card__icon">
            <i class="fas {{ $greetIcon }}" aria-hidden="true"></i>
        </div>
        <div class="greeting-card__content">
            <div class="greeting-card__title">{{ $greetText }}, {{ auth()->user()->name }}!</div>
            <div class="greeting-card__sub">{{ $greetSub }}</div>
        </div>
    </div>
    <div class="greeting-card__aside" aria-label="Waktu saat ini">
        <span class="greeting-card__time">{{ now()->format('H:i') }}</span>
        <span class="greeting-card__tz">WIB</span>
    </div>
</div>
</div>

{{-- Stat Cards --}}
<div class="stats-grid">
    <div class="stat-card total">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-label">Total Guru Aktif</div>
        <div class="stat-value">{{ $totalGuru }}</div>
    </div>
    <div class="stat-card hadir">
        <div class="stat-icon"><i class="fas fa-user-check"></i></div>
        <div class="stat-label">Hadir Hari Ini</div>
        <div class="stat-value">{{ $hadirHariIni }}</div>
    </div>
    <div class="stat-card telat">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-label">Terlambat</div>
        <div class="stat-value">{{ $telatHariIni }}</div>
    </div>
    <div class="stat-card absen">
        <div class="stat-icon"><i class="fas fa-user-xmark"></i></div>
        <div class="stat-label">Tidak Hadir</div>
        <div class="stat-value">{{ $tidakHadir }}</div>
    </div>
    <div class="stat-card izin">
        <div class="stat-icon"><i class="fas fa-file-circle-check"></i></div>
        <div class="stat-label">Izin</div>
        <div class="stat-value">{{ $izinHariIni }}</div>
    </div>
    <div class="stat-card sakit">
        <div class="stat-icon"><i class="fas fa-kit-medical"></i></div>
        <div class="stat-label">Sakit</div>
        <div class="stat-value">{{ $sakitHariIni }}</div>
    </div>
</div>

{{-- Charts --}}
<div class="card-grid-2">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-line" style="color:hsl(145,60%,28%)"></i> Presensi 7 Hari Terakhir</h3>
        </div>
        <div style="position:relative;height:220px;">
            <canvas id="chartPresensi"></canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-pie" style="color:hsl(48,96%,50%)"></i> Status Hari Ini</h3>
        </div>
        <div style="position:relative;height:220px;">
            <canvas id="chartStatus"></canvas>
        </div>
    </div>
</div>

{{-- Tabel Scan Terbaru & Guru Terlambat --}}
<div class="card-grid-2">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-clock-rotate-left" style="color:hsl(145,60%,28%)"></i> Scan Terbaru</h3>
            <a href="{{ route('presensi.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Nama Guru</th><th>Jam Masuk</th><th>Status</th></tr>
                </thead>
                <tbody>
                @forelse($riwayatScan as $p)
                <tr>
                    <td>
                        <strong>{{ $p->guru->nama ?? '-' }}</strong><br>
                        <small style="color:#5a7a5a;font-size:.78rem">{{ $p->guru->id_pengguna ?? '' }}</small>
                    </td>
                    <td style="font-weight:600;color:hsl(145,60%,28%)">{{ $p->jam_masuk ?? '-' }}</td>
                    <td>
                        <span class="badge badge-{{ str_replace('_','-',$p->status) }}">
                            {{ strtoupper(str_replace('_',' ',$p->status)) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3">
                        <div class="empty-state" style="padding:24px">
                            <i class="fas fa-calendar-xmark"></i>
                            Belum ada scan hari ini
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-bell" style="color:hsl(48,96%,50%)"></i> Guru Terlambat Hari Ini</h3>
            @if(count($guruTelat) > 0)
                <span class="badge" style="background:#fef9c3;color:#a16207">{{ count($guruTelat) }} guru</span>
            @endif
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Nama Guru</th><th>Jam Masuk</th><th>Telat</th></tr>
                </thead>
                <tbody>
                @forelse($guruTelat as $p)
                <tr>
                    <td><strong>{{ $p->guru->nama ?? '-' }}</strong></td>
                    <td style="color:#f59e0b;font-weight:600">{{ $p->jam_masuk }}</td>
                    <td>
                        <span style="background:#fef9c3;color:#92400e;padding:3px 10px;border-radius:99px;font-size:.8rem;font-weight:700">
                            +{{ $p->menit_telat }} mnt
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3">
                        <div class="empty-state" style="padding:24px">
                            <i class="fas fa-circle-check" style="color:#22c55e"></i>
                            <span style="color:#22c55e;font-weight:600">Tidak ada guru terlambat</span>
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Jadwal Aktif --}}
@if($jadwal)
@php $toleransiMenit = $jadwal->toleransiMenit(); @endphp
<div class="card" style="background:linear-gradient(135deg,hsl(145,60%,96%),hsl(48,96%,96%));border:1px solid #d4e8d4">
    <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
        <div style="width:44px;height:44px;background:hsl(145,60%,28%);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas fa-clock" style="color:#fff;font-size:1.1rem"></i>
        </div>
        <div>
            <div style="font-weight:800;color:hsl(145,60%,18%);margin-bottom:2px">
                Jadwal Aktif: {{ $jadwal->nama_jadwal }}
            </div>
            <div style="font-size:.85rem;color:hsl(145,60%,30%);display:flex;gap:16px;flex-wrap:wrap">
                <span><i class="fas fa-right-to-bracket"></i> Masuk: <strong>{{ $jadwal->jam_masuk }}</strong></span>
                <span><i class="fas fa-hourglass-half"></i> Toleransi: <strong>{{ $toleransiMenit }} menit</strong></span>
                <span><i class="fas fa-right-from-bracket"></i> Pulang: <strong>{{ $jadwal->jam_pulang }}</strong></span>
            </div>
        </div>
        <div style="margin-left:auto;display:flex;gap:8px;align-items:center">
            @if(auth()->user()->isSuperAdmin())
            <button type="button"
                    onclick="openModalEditJadwal()"
                    class="btn btn-sm"
                    aria-haspopup="dialog"
                    aria-controls="modal-edit-jadwal"
                    style="background:hsl(145,60%,28%);color:#fff;border:none;cursor:pointer;display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;font-size:.85rem;font-weight:700">
                <i class="fas fa-pen-to-square"></i> Edit Jadwal
            </button>
            @endif
            <a href="{{ route('laporan.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-chart-bar"></i> Lihat Laporan
            </a>
        </div>
    </div>
</div>
@endif

@if(auth()->user()->isSuperAdmin() && $jadwal)
<div id="modal-edit-jadwal"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-edit-jadwal-title"
     aria-hidden="true"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;
            align-items:center;justify-content:center;padding:16px">
    <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:420px;
                box-shadow:0 20px 60px rgba(0,0,0,.25)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
            <h3 id="modal-edit-jadwal-title" style="font-weight:800;color:hsl(145,60%,18%);font-size:1rem;margin:0">
                <i class="fas fa-clock" style="color:hsl(145,60%,35%);margin-right:8px" aria-hidden="true"></i>
                Edit Jadwal Aktif
            </h3>
            <button type="button" onclick="closeModalEditJadwal()"
                    aria-label="Tutup dialog"
                    style="background:none;border:none;font-size:1.3rem;cursor:pointer;color:#64748b">
                &times;
            </button>
        </div>
        <form method="POST" action="{{ route('jadwal_masuk.update_dashboard', $jadwal) }}">
            @csrf
            @method('PATCH')
            <div style="margin-bottom:14px">
                <label style="font-size:.85rem;font-weight:600;color:#374151;display:block;margin-bottom:4px">Jam Masuk</label>
                <input type="time" name="jam_masuk" class="form-control"
                       value="{{ \Carbon\Carbon::parse($jadwal->jam_masuk)->format('H:i') }}" required>
            </div>
            <div style="margin-bottom:14px">
                <label style="font-size:.85rem;font-weight:600;color:#374151;display:block;margin-bottom:4px">Jam Pulang</label>
                <input type="time" name="jam_pulang" class="form-control"
                       value="{{ \Carbon\Carbon::parse($jadwal->jam_pulang)->format('H:i') }}" required>
            </div>
            <div style="margin-bottom:20px">
                <label style="font-size:.85rem;font-weight:600;color:#374151;display:block;margin-bottom:4px">Toleransi Keterlambatan (menit)</label>
                <input type="number" name="batas_toleransi" class="form-control"
                       value="{{ $toleransiMenit }}" min="0" max="120" required>
            </div>
            <div style="display:flex;gap:10px">
                <button type="submit" class="btn btn-primary" style="flex:1">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <button type="button"
                        onclick="closeModalEditJadwal()"
                        class="btn btn-secondary">Batal</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js" defer crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="application/json" id="grafik-data-json">{!! json_encode($grafikData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
<script type="application/json" id="status-chart-data-json">{!! json_encode([$hadirHariIni, $telatHariIni, $tidakHadir, $izinHariIni, $sakitHariIni], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
<script>
function openModalEditJadwal() {
    var modal = document.getElementById('modal-edit-jadwal');
    if (!modal) return;
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
}
function closeModalEditJadwal() {
    var modal = document.getElementById('modal-edit-jadwal');
    if (!modal) return;
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
}
document.getElementById('modal-edit-jadwal')
    ?.addEventListener('click', function(e) {
        if (e.target === this) closeModalEditJadwal();
    });

function initDashboardCharts() {
    if (typeof Chart === 'undefined') return;
    var grafikNode = document.getElementById('grafik-data-json');
    var statusNode = document.getElementById('status-chart-data-json');
    if (!grafikNode || !statusNode) return;

    var grafikData = JSON.parse(grafikNode.textContent);
    var statusChartData = JSON.parse(statusNode.textContent);
    var labels = grafikData.map(function(d) { return d.tanggal; });

new Chart(document.getElementById('chartPresensi'), {
    type: 'line',
    data: {
        labels,
        datasets: [
            {
                label: 'Hadir',
                data: grafikData.map(d => d.hadir),
                borderColor: 'hsl(145,60%,35%)',
                backgroundColor: 'hsla(145,60%,35%,.12)',
                tension: .4, fill: true, pointRadius: 4, pointHoverRadius: 6,
            },
            {
                label: 'Terlambat',
                data: grafikData.map(d => d.telat),
                borderColor: 'hsl(48,96%,45%)',
                backgroundColor: 'hsla(48,96%,45%,.1)',
                tension: .4, fill: true, pointRadius: 4, pointHoverRadius: 6,
            },
            {
                label: 'Tidak Hadir',
                data: grafikData.map(d => d.tidak_hadir),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239,68,68,.08)',
                tension: .4, fill: true, pointRadius: 4, pointHoverRadius: 6,
            },
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 14 } } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

new Chart(document.getElementById('chartStatus'), {
    type: 'doughnut',
    data: {
        labels: ['Hadir', 'Terlambat', 'Tidak Hadir', 'Izin', 'Sakit'],
        datasets: [{
            data: statusChartData,
            backgroundColor: ['hsl(145,60%,35%)', 'hsl(48,96%,50%)', '#ef4444', '#3b82f6', '#f97316'],
            borderWidth: 0,
            hoverOffset: 8,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 14 } }
        }
    }
});
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDashboardCharts);
} else {
    initDashboardCharts();
}
</script>
@endpush
