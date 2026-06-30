@extends('layouts.app')
@section('judul','Dashboard')
@section('content')

@php
  if (!function_exists('rp')) {
    function rp($n) { return 'Rp ' . number_format((float)$n, 0, ',', '.'); }
  }
  if (!function_exists('rpShort')) {
    function rpShort($n) {
      $n = (float)$n;
      if ($n >= 1000000) return number_format($n/1000000, 1, ',', '.') . ' jt';
      if ($n >= 1000)    return number_format($n/1000, 0, ',', '.') . ' rb';
      return (int)$n;
    }
  }
@endphp

{{-- Greeting + Filter Periode --}}
<div class="db-greeting">
  <div>
    <h1 class="db-title">Selamat datang, {{ $user->name }}</h1>
  </div>
  @if($isPengurus)
  <form method="GET" action="{{ route('dashboard') }}" class="db-periode-form">
    <label class="db-periode-label">Periode</label>
    <select name="periode" class="db-periode-select" onchange="this.form.submit()">
      @foreach($periodeOptions as $opt)
        <option value="{{ $opt['value'] }}" {{ $opt['value'] === $periodeInput ? 'selected' : '' }}>
          {{ $opt['label'] }}
        </option>
      @endforeach
    </select>
  </form>
  @endif
</div>

@if($isPengurus)

{{-- Stat cards baris 1 --}}
<div class="db-stats">
  <div class="db-stat db-stat-emas">
    <div class="db-stat-lbl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg> Saldo Kas RT</div>
    <div class="db-stat-val">{{ rp($saldoKas) }}</div>
    <div class="db-stat-sub"><span class="db-up">+{{ rp($kasmasukBulan) }}</span> masuk &nbsp;<span class="db-down">-{{ rp($kaskeluarBulan) }}</span> keluar bulan ini</div>
  </div>
  <div class="db-stat">
    <div class="db-stat-lbl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/></svg> Warga Aktif</div>
    <div class="db-stat-val">{{ number_format($jumlahWarga) }} <small>jiwa</small></div>
    <div class="db-stat-sub">{{ number_format($jumlahKK) }} Kartu Keluarga terdaftar</div>
  </div>
  <div class="db-stat db-stat-stempel">
    <div class="db-stat-lbl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg> Tunggakan Iuran</div>
    <div class="db-stat-val">{{ $tunggakanKK }} <small>KK</small></div>
    <div class="db-stat-sub">
      @if($tunggakanNominal > 0)
        <span class="db-down">{{ rp($tunggakanNominal) }}</span> belum terbayar
      @else
        <span style="color:#2D6A4F;font-weight:700">✓ Semua lunas bulan ini</span>
      @endif
    </div>
  </div>
  <div class="db-stat db-stat-biru">
    <div class="db-stat-lbl"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V9l7-5 7 5v12"/><path d="M9 21v-6h6v6"/></svg> Booking Fasilitas</div>
    <div class="db-stat-val">{{ $bookingMendatang }} <small>agenda</small></div>
    <div class="db-stat-sub">
      @if($bookingBerikutnya)
        Terdekat: {{ $bookingBerikutnya->tanggal_mulai?->locale('id')->isoFormat('D MMM') }} — {{ $bookingBerikutnya->nama_acara }}
      @else
        Tidak ada booking mendatang
      @endif
    </div>
  </div>
</div>

{{-- Baris 2: Iuran bulan ini + Pembayaran + Kas (1 baris 3 kolom) --}}
@php
  $lunasJml = $iuranBulan['lunas']->jumlah ?? 0;
  $sebaJml  = $iuranBulan['sebagian']->jumlah ?? 0;
  $belumJml = $iuranBulan['belum']->jumlah ?? 0;
  $totalJml = $lunasJml + $sebaJml + $belumJml;
  $pctLunas = $totalJml > 0 ? round($lunasJml/$totalJml*100) : 0;
  $pctSeba  = $totalJml > 0 ? round($sebaJml/$totalJml*100) : 0;
  $pctBelum = $totalJml > 0 ? round($belumJml/$totalJml*100) : 0;
@endphp

<div class="db-iuran-card" style="margin-bottom:14px">
  <div class="db-section-title">Iuran {{ $bulan }}</div>
  <div class="db-iuran-bars">
    <div class="db-iuran-row"><span class="db-ib-label">Lunas</span><div class="db-ib-bar"><div class="db-ib-fill db-ib-lunas" style="width:{{ $pctLunas }}%"></div></div><span class="db-ib-num">{{ $lunasJml }} KK</span></div>
    <div class="db-iuran-row"><span class="db-ib-label">Sebagian</span><div class="db-ib-bar"><div class="db-ib-fill db-ib-sebagian" style="width:{{ $pctSeba }}%"></div></div><span class="db-ib-num">{{ $sebaJml }} KK</span></div>
    <div class="db-iuran-row"><span class="db-ib-label">Belum</span><div class="db-ib-bar"><div class="db-ib-fill db-ib-belum" style="width:{{ $pctBelum }}%"></div></div><span class="db-ib-num">{{ $belumJml }} KK</span></div>
    @if($totalJml === 0)<p style="font-size:12.5px;color:var(--redup);text-align:center;padding:8px 0">Belum ada tagihan</p>@endif
  </div>
  @if($piutangAktif > 0)
  <div style="margin-top:14px;padding-top:12px;border-top:1px solid var(--garis)">
    <div style="font-size:11px;font-weight:700;color:var(--redup);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Piutang Aktif</div>
    <div style="font-size:18px;font-weight:800;color:var(--stempel)">{{ $piutangAktif }} orang</div>
    <div style="font-size:11.5px;color:var(--redup)">{{ rp($piutangTotal) }} belum kembali</div>
  </div>
  @endif
</div>

{{-- Cashflow chart --}}
<div class="db-card" style="margin-top:16px">
  <div class="db-card-head">
    <h3><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg> Cashflow 6 Bulan Terakhir</h3>
  </div>
  <div class="db-cashflow">
    <div class="db-cf-legend">
      <span><i class="db-dot db-dot-masuk"></i> Masuk</span>
      <span><i class="db-dot db-dot-keluar"></i> Keluar</span>
    </div>
    <div class="db-cf-chart">
      @php $maxVal = 1; foreach($cashflow as $_cf){ $v = max($_cf['masuk'],$_cf['keluar']); if($v>$maxVal)$maxVal=$v; } @endphp
      @foreach($cashflow as $cf)
      @php $kasUrl = route('kas.index') . '?bulan=' . $cf['bulan']; @endphp
      <div class="db-cf-col">
        <div class="db-cf-amounts">
          @if($cf['masuk'] > 0)<span class="db-cf-amt-masuk">{{ rpShort($cf['masuk']) }}</span>@endif
          @if($cf['keluar'] > 0)<span class="db-cf-amt-keluar">{{ rpShort($cf['keluar']) }}</span>@endif
        </div>
        <div class="db-cf-bars">
          <a href="{{ $kasUrl }}&tipe=masuk" class="db-cf-bar db-cf-masuk db-cf-click"
             style="height:{{ round($cf['masuk']/$maxVal*100) }}%"
             title="Masuk {{ $cf['label'] }}: {{ rp($cf['masuk']) }}"></a>
          <a href="{{ $kasUrl }}&tipe=keluar" class="db-cf-bar db-cf-keluar db-cf-click"
             style="height:{{ round($cf['keluar']/$maxVal*100) }}%"
             title="Keluar {{ $cf['label'] }}: {{ rp($cf['keluar']) }}"></a>
        </div>
        <div class="db-cf-label">{{ $cf['label'] }}</div>
      </div>
      @endforeach
    </div>
  </div>
</div>

@else
<div class="db-welcome-card">
  <div class="db-wc-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9.5 12 3l9 6.5"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/></svg></div>
  <h2>Selamat datang, {{ $user->name }}!</h2>
  <p>Gunakan menu di sebelah kiri untuk mengakses fitur yang tersedia.</p>
</div>
@endif

@endsection

@push('styles')
<style>
.content{max-width:none;padding-bottom:32px}

.db-greeting{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:18px;gap:16px;flex-wrap:wrap}
.db-title{font-size:18px;font-weight:700;letter-spacing:-.01em;margin:0}
.db-periode-form{display:flex;flex-direction:column;gap:5px;flex-shrink:0}
.db-periode-label{font-size:11px;font-weight:700;color:var(--redup);letter-spacing:.06em;text-transform:uppercase}
.db-periode-select{padding:8px 34px 8px 14px;border:1.5px solid var(--garis-2);border-radius:10px;font-family:inherit;font-size:13.5px;font-weight:600;color:var(--tinta);background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236A7A70' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat right 12px center;appearance:none;cursor:pointer;outline:none;transition:.15s}
.db-periode-select:focus{border-color:var(--daun);box-shadow:0 0 0 3px rgba(45,106,79,.1)}

.db-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:14px}
.db-stats-2{grid-template-columns:repeat(3,1fr)}
.db-grid3{display:grid;grid-template-columns:1fr 1.4fr 1.4fr;gap:14px;margin-bottom:14px}
.db-stat{background:var(--surface);border:1px solid var(--garis);border-radius:14px;padding:14px 16px 12px;box-shadow:var(--shadow);position:relative;overflow:hidden}
.db-stat::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--daun)}
.db-stat-emas::before{background:var(--emas)}.db-stat-stempel::before{background:var(--stempel)}.db-stat-biru::before{background:var(--biru)}
.db-stat-lbl{font-size:11.5px;color:var(--redup);font-weight:600;display:flex;align-items:center;gap:6px;margin-bottom:8px}
.db-stat-lbl svg{width:14px;height:14px;color:var(--daun);flex:0 0 14px}
.db-stat-emas .db-stat-lbl svg{color:var(--emas)}.db-stat-stempel .db-stat-lbl svg{color:var(--stempel)}.db-stat-biru .db-stat-lbl svg{color:var(--biru)}
.db-stat-val{font-size:22px;font-weight:800;letter-spacing:-.02em;margin-bottom:4px}
.db-stat-val small{font-size:13px;color:var(--redup);font-weight:600}
.db-stat-sub{font-size:11px;color:var(--redup)}
.db-up{color:#2D6A4F;font-weight:700}.db-down{color:var(--stempel);font-weight:700}

.db-iuran-card{background:var(--surface);border:1px solid var(--garis);border-radius:14px;padding:18px 20px;box-shadow:var(--shadow)}
.db-section-title{font-size:12px;font-weight:700;color:var(--redup);letter-spacing:.06em;text-transform:uppercase;margin-bottom:14px}
.db-iuran-bars{display:flex;flex-direction:column;gap:10px}
.db-iuran-row{display:flex;align-items:center;gap:10px}
.db-ib-label{font-size:12px;font-weight:700;color:var(--redup);width:55px;flex-shrink:0}
.db-ib-bar{flex:1;height:10px;background:var(--kertas-2);border-radius:20px;overflow:hidden}
.db-ib-fill{height:100%;border-radius:20px;transition:width .4s ease;min-width:2px}
.db-ib-lunas{background:#2D6A4F}.db-ib-sebagian{background:var(--emas)}.db-ib-belum{background:var(--stempel)}
.db-ib-num{font-size:12px;font-weight:700;color:var(--tinta);width:45px;text-align:right;flex-shrink:0}

.db-grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px}

.db-card{background:var(--surface);border:1px solid var(--garis);border-radius:14px;overflow:hidden;box-shadow:var(--shadow)}
.db-card-head{padding:14px 18px;border-bottom:1px solid var(--garis);display:flex;align-items:center;justify-content:space-between}
.db-card-head h3{font-size:14px;font-weight:700;display:flex;align-items:center;gap:8px;color:var(--tinta)}
.db-card-head h3 svg{width:16px;height:16px;color:var(--daun)}
.db-link{font-size:12px;color:var(--daun);font-weight:700}.db-link:hover{text-decoration:underline}
.db-card-body{padding:4px 0}
.db-list-item{display:flex;align-items:center;gap:12px;padding:11px 18px;border-bottom:1px solid var(--garis)}
.db-list-item:last-child{border-bottom:none}
.db-li-av{width:34px;height:34px;border-radius:10px;background:var(--daun-pucat);color:var(--hutan);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex:0 0 34px}
.db-li-av-in{background:#E0F2E9;color:#2D6A4F}.db-li-av-out{background:var(--stempel-soft);color:var(--stempel)}
.db-li-info{flex:1;min-width:0}
.db-li-info b{display:block;font-size:13.5px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.db-li-info span{font-size:11.5px;color:var(--redup)}
.db-li-right{display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0}
.db-mono{font-family:'IBM Plex Mono',monospace;font-size:12.5px;font-weight:600}
.db-in{color:#2D6A4F}.db-out{color:var(--stempel)}
.db-pill{font-size:10.5px;font-weight:700;padding:2px 8px;border-radius:20px}
.db-pill-lunas{background:var(--daun-pucat);color:#1B5E3F}.db-pill-sebagian{background:var(--emas-soft);color:#7a5c00}
.db-empty{padding:20px;text-align:center;color:var(--redup);font-size:13px}

/* Pengumuman */
.db-pg-item{display:flex;align-items:flex-start;gap:12px;padding:12px 18px;border-bottom:1px solid var(--garis)}
.db-pg-item:last-child{border-bottom:none}
.db-pg-penting{background:#FFF8F8;border-left:3px solid var(--stempel)}
.db-pg-kat{font-size:10.5px;font-weight:700;padding:2px 9px;border-radius:20px;flex-shrink:0;margin-top:2px}
.db-pg-kat-informasi{background:var(--biru-soft);color:#1a3d52}.db-pg-kat-rapat{background:var(--daun-pucat);color:#14532D}
.db-pg-kat-kegiatan{background:var(--emas-soft);color:#7a5c00}.db-pg-kat-keuangan{background:#E8F5E9;color:#2D6A4F}
.db-pg-kat-darurat{background:var(--stempel-soft);color:#9A3422}.db-pg-kat-lainnya{background:var(--kertas-2);color:var(--redup)}
.db-pg-info{flex:1;min-width:0}
.db-pg-judul{font-size:13.5px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.db-pg-meta{font-size:11.5px;color:var(--redup);margin-top:3px}

/* Cashflow */
.db-cashflow{padding:14px 20px 14px}
.db-cf-legend{display:flex;gap:14px;font-size:12px;font-weight:600;color:var(--redup);margin-bottom:12px;justify-content:flex-end}
.db-cf-legend span{display:flex;align-items:center;gap:5px}
.db-cf-chart{display:flex;align-items:flex-end;gap:6px}
.db-cf-col{flex:1;display:flex;flex-direction:column;align-items:center;gap:4px}
.db-cf-amounts{display:flex;flex-direction:column;align-items:center;gap:1px;min-height:30px;justify-content:flex-end}
.db-cf-amt-masuk{font-size:9px;font-weight:700;color:#2D6A4F;line-height:1.3}
.db-cf-amt-keluar{font-size:9px;font-weight:700;color:var(--stempel);line-height:1.3}
.db-cf-bars{display:flex;align-items:flex-end;gap:3px;height:110px;width:100%}
.db-cf-bar{flex:1;border-radius:4px 4px 0 0;transition:height .4s ease;min-height:3px}
.db-cf-masuk{background:#2D6A4F}.db-cf-keluar{background:var(--stempel)}
.db-cf-click{display:block;text-decoration:none;cursor:pointer;transition:filter .15s,transform .15s}
.db-cf-click:hover{filter:brightness(1.2);transform:scaleY(1.04);transform-origin:bottom}
.db-cf-label{font-size:11px;color:var(--redup);font-weight:600}
.db-dot{display:inline-block;width:10px;height:10px;border-radius:3px}
.db-dot-masuk{background:#2D6A4F}.db-dot-keluar{background:var(--stempel)}

.db-welcome-card{display:flex;flex-direction:column;align-items:center;justify-content:center;height:50vh;text-align:center;gap:14px}
.db-wc-icon{width:72px;height:72px;border-radius:50%;background:var(--daun-pucat);color:var(--daun);display:flex;align-items:center;justify-content:center}
.db-wc-icon svg{width:32px;height:32px}
.db-welcome-card h2{font-size:20px;font-weight:700;color:var(--tinta)}
.db-welcome-card p{font-size:13.5px;color:var(--redup)}

@media(max-width:1100px){.db-stats{grid-template-columns:repeat(2,1fr)}.db-grid3{grid-template-columns:1fr 1fr}}
@media(max-width:700px){.db-stats{grid-template-columns:1fr 1fr}.db-grid3,.db-grid2{grid-template-columns:1fr}}
</style>
@endpush
