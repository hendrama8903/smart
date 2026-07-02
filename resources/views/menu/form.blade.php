@extends('layouts.app')

@section('judul', $menu->exists ? 'Ubah Menu' : 'Tambah Menu')

@section('content')
@php
  $ikonList = ['dashboard','users','heart','bill','coins','building','shield','clipboard','card','menu','settings','truck','file'];
@endphp

<div class=”page-head”>
  <div class=”eyebrow”>Pengaturan / Master Menu</div>
  <h1>{{ $menu->exists ? 'Ubah Menu' : 'Tambah Menu' }}</h1>
</div>

@if($errors->any())
  <div class="form-alert">Periksa kembali isian: {{ $errors->first() }}</div>
@endif

<div class="card card-pad fwrap">
  <form method="POST" action="{{ $menu->exists ? route('menu.update', $menu) : route('menu.store') }}">
    @csrf
    @if($menu->exists) @method('PUT') @endif

    <div class="frow">
      <label>Nama Menu <span class="req">*</span></label>
      <input type="text" name="nama" value="{{ old('nama', $menu->nama) }}" placeholder="cth. Data Warga" required>
    </div>

    <div class="fgrid">
      <div class="frow">
        <label>Menu Induk</label>
        <input type="hidden" name="parent_id" id="hParentId" value="{{ old('parent_id', $menu->parent_id) }}">
        <div id="dxParentId"></div>
        <div class="fhint">Kosongkan bila ini menu induk (level atas).</div>
      </div>

      <div class="frow">
        <label>Urutan <span class="req">*</span></label>
        <input type="number" name="urutan" value="{{ old('urutan', $menu->urutan ?? 0) }}" min="0" required>
        <div class="fhint">Makin kecil makin atas.</div>
      </div>
    </div>

    <div class="fgrid">
      <div class="frow">
        <label>Icon</label>
        <input type="hidden" name="icon" id="hIcon" value="{{ old('icon', $menu->icon) }}">
        <div id="dxIcon"></div>
      </div>

      <div class="frow">
        <label>Role yang boleh akses</label>
        <input type="text" name="roles" value="{{ old('roles', $menu->roles) }}" placeholder="admin,ketua,bendahara">
        <div class="fhint">Pisahkan dengan koma. Kosongkan = semua role.</div>
      </div>
    </div>

    <div class="fgrid">
      <div class="frow">
        <label>Controller</label>
        <input type="text" name="controller" value="{{ old('controller', $menu->controller) }}" placeholder="cth. WargaController">
        <div class="fhint">Nama controller tujuan (opsional untuk menu induk).</div>
      </div>

      <div class="frow">
        <label>Fungsi / Method</label>
        <input type="text" name="fungsi" value="{{ old('fungsi', $menu->fungsi ?? 'index') }}" placeholder="cth. index">
      </div>
    </div>

    <div class="frow">
      <label>URL Manual (opsional)</label>
      <input type="text" name="url" value="{{ old('url', $menu->url) }}" placeholder="cth. /warga  (mengganti controller@fungsi bila diisi)">
    </div>

    <div class="frow">
      <label class="switch">
        <input type="checkbox" name="aktif" value="1" @checked(old('aktif', $menu->exists ? $menu->aktif : true))>
        <span>Menu aktif (tampil di sidebar)</span>
      </label>
    </div>

    <div class="factions">
      <button class="btn" type="submit">
        <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Simpan
      </button>
      <a class="btn ghost" href="{{ route('menu.index') }}">Batal</a>
    </div>
  </form>
</div>
@endsection

@push('styles')
<style>
  .fwrap{max-width:720px}
  .frow{margin-bottom:16px}
  .frow > label{display:block;font-size:13px;font-weight:700;margin-bottom:6px;color:var(--tinta)}
  .req{color:var(--stempel)}
  .frow input[type=text],.frow input[type=number]{
    width:100%;padding:11px 13px;border:1.5px solid var(--garis2);border-radius:10px;
    font-family:inherit;font-size:14px;background:#fff;color:var(--tinta);outline:none;transition:.15s}
  .frow input:focus{border-color:var(--daun);box-shadow:0 0 0 4px rgba(45,106,79,.1)}
  .frow .dx-texteditor{border-radius:10px;font-size:14px}
  .fhint{font-size:12px;color:var(--redup);margin-top:5px}
  .fgrid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
  .factions{display:flex;gap:10px;margin-top:8px}
  .switch{display:flex;align-items:center;gap:10px;font-size:13.5px;font-weight:600;cursor:pointer}
  .switch input{width:18px;height:18px;accent-color:var(--daun)}
  .form-alert{background:#FBEAE6;border:1px solid #E9C3BA;color:#9A3422;font-size:13.5px;font-weight:600;padding:11px 14px;border-radius:11px;margin-bottom:18px}
  @media(max-width:600px){ .fgrid{grid-template-columns:1fr} }
</style>
@endpush

@push('scripts')
<script>
$(function(){
  var parents = [{ id: '', nama: '— Jadikan Menu Induk —' }]
    .concat(@json($parents->map(fn($p) => ['id' => (string)$p->id, 'nama' => $p->nama])->values()));
  var icons = [{ v: '', l: '— Tanpa icon —' }]
    .concat(@json(collect($ikonList)->map(fn($ik) => ['v' => $ik, 'l' => $ik])->values()));

  $("#dxParentId").dxSelectBox({
    dataSource  : parents,
    valueExpr   : "id",
    displayExpr : "nama",
    value       : $("#hParentId").val(),
    placeholder : "— Jadikan Menu Induk —",
    onValueChanged: function(e){ $("#hParentId").val(e.value ?? ''); }
  });

  $("#dxIcon").dxSelectBox({
    dataSource  : icons,
    valueExpr   : "v",
    displayExpr : "l",
    value       : $("#hIcon").val(),
    placeholder : "— Tanpa icon —",
    onValueChanged: function(e){ $("#hIcon").val(e.value ?? ''); }
  });
});
</script>
@endpush
