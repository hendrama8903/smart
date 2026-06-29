@extends('layouts.app')
@section('judul','Laporan Warga')
@section('content')

<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
  <a href="{{ route('laporan.index') }}" style="color:var(--redup);font-size:13px">← Laporan</a>
  <h2 style="font-size:22px;font-weight:800;letter-spacing:-.02em;margin:0">Laporan Warga</h2>
</div>

<div class="lap-grid">

  <a href="{{ route('laporan.warga.demografi') }}" class="lap-card">
    <div class="lap-icon" style="background:#E0F2E9;color:#2D6A4F">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </div>
    <div class="lap-info">
      <div class="lap-title">Demografi Warga</div>
      <div class="lap-desc">Distribusi usia, jenis kelamin, agama, pendidikan, pekerjaan</div>
    </div>
    <svg class="lap-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 6 15 12 9 18"/></svg>
  </a>

  <a href="{{ route('laporan.warga.daftar') }}" class="lap-card">
    <div class="lap-icon" style="background:var(--biru-soft);color:var(--biru)">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
    </div>
    <div class="lap-info">
      <div class="lap-title">Daftar Warga Lengkap</div>
      <div class="lap-desc">Rekap seluruh data warga dengan filter gang & status, bisa export Excel</div>
    </div>
    <svg class="lap-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 6 15 12 9 18"/></svg>
  </a>

  <a href="{{ route('laporan.warga.mutasi') }}" class="lap-card">
    <div class="lap-icon" style="background:var(--emas-soft);color:#7a5c00">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
    </div>
    <div class="lap-info">
      <div class="lap-title">Mutasi Warga</div>
      <div class="lap-desc">Warga baru masuk, pindah keluar, dan meninggal per periode</div>
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
