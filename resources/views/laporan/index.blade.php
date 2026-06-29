@extends('layouts.app')
@section('judul','Laporan Keuangan')
@section('content')

<h2 style="font-size:22px;font-weight:800;letter-spacing:-.02em;margin-bottom:20px">Laporan Keuangan</h2>

<div class="lap-grid">

  <a href="{{ route('laporan.realisasi') }}" class="lap-card">
    <div class="lap-icon" style="background:#E0F2E9;color:#2D6A4F">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
    </div>
    <div class="lap-info">
      <div class="lap-title">Realisasi vs Rencana</div>
      <div class="lap-desc">Bandingkan target anggaran dengan realisasi pendapatan &amp; pengeluaran</div>
    </div>
    <svg class="lap-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 6 15 12 9 18"/></svg>
  </a>

  <a href="{{ route('laporan.kas') }}" class="lap-card">
    <div class="lap-icon" style="background:var(--biru-soft);color:var(--biru)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
    </div>
    <div class="lap-info">
      <div class="lap-title">Laporan Kas RT</div>
      <div class="lap-desc">Rekap transaksi kas masuk dan keluar per kategori per tahun</div>
    </div>
    <svg class="lap-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 6 15 12 9 18"/></svg>
  </a>

  <a href="{{ route('laporan.iuran') }}" class="lap-card">
    <div class="lap-icon" style="background:var(--emas-soft);color:#7a5c00">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
    </div>
    <div class="lap-info">
      <div class="lap-title">Laporan Iuran</div>
      <div class="lap-desc">Rekap pembayaran iuran bulanan per periode, termasuk tunggakan</div>
    </div>
    <svg class="lap-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 6 15 12 9 18"/></svg>
  </a>

  <a href="{{ route('laporan.warga.index') }}" class="lap-card">
    <div class="lap-icon" style="background:var(--daun-pucat);color:var(--daun)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </div>
    <div class="lap-info">
      <div class="lap-title">Laporan Warga</div>
      <div class="lap-desc">Demografi, daftar lengkap, dan mutasi warga (masuk/pindah/meninggal)</div>
    </div>
    <svg class="lap-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 6 15 12 9 18"/></svg>
  </a>

  <a href="{{ route('laporan.anggaran') }}" class="lap-card">
    <div class="lap-icon" style="background:var(--stempel-soft);color:var(--stempel)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
    </div>
    <div class="lap-info">
      <div class="lap-title">Master Anggaran</div>
      <div class="lap-desc">Input rencana pendapatan &amp; pengeluaran per tahun sebagai target</div>
    </div>
    <svg class="lap-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 6 15 12 9 18"/></svg>
  </a>

</div>

@endsection
@push('styles')
<style>
.content{max-width:900px}
.lap-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.lap-card{display:flex;align-items:center;gap:16px;background:var(--surface);border:1px solid var(--garis);border-radius:14px;padding:20px 22px;box-shadow:var(--shadow);text-decoration:none;color:var(--tinta);transition:.15s}
.lap-card:hover{border-color:var(--daun);box-shadow:0 0 0 3px rgba(45,106,79,.08),var(--shadow);transform:translateY(-1px)}
.lap-icon{width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex:0 0 46px}
.lap-icon svg{width:22px;height:22px}
.lap-info{flex:1;min-width:0}
.lap-title{font-size:15px;font-weight:700;margin-bottom:4px}
.lap-desc{font-size:12.5px;color:var(--redup);line-height:1.4}
.lap-arrow{width:18px;height:18px;color:var(--redup);flex:0 0 18px}
@media(max-width:600px){.lap-grid{grid-template-columns:1fr}}
</style>
@endpush
