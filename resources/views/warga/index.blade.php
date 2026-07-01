@extends('layouts.app')

@section('judul', 'Data Warga')

@section('content')

<div class="wg-toolbar">
  <h2 class="wg-title">Data Warga &amp; Kartu Keluarga</h2>
  <div class="wg-actions">
    <button class="btn" id="btnTambah" type="button" onclick="kkAdd()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Tambah KK
    </button>
    <button class="btn btn-ubah" id="btnUbah" type="button" onclick="kkEdit()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
      Ubah KK
    </button>
    <button class="btn btn-hapus" id="btnHapus" type="button" onclick="kkDelete()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
      Hapus KK
    </button>
    <button class="btn btn-unduh" id="btnUnduh" type="button" onclick="exportWarga()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Unduh
    </button>
    <button class="btn btn-upload" id="btnUpload" type="button" onclick="importOpen()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
      Upload
    </button>
  </div>
</div>

{{-- ───── Modal Import Excel ───── --}}
<div class="modal-overlay" id="importModal" onclick="if(event.target===this)importClose()">
  <div class="modal-card" style="max-width:480px;padding:0;text-align:left">
    <div class="wg-head">
      <div class="wg-head-icon" style="background:var(--biru-soft);color:var(--biru)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
      </div>
      <div>
        <h3>Upload Data Warga</h3>
      </div>
    </div>

    <div style="padding:18px 20px">
      {{-- Unduh template --}}
      <div class="import-info">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>Gunakan template Excel yang sesuai agar data terbaca dengan benar.</span>
      </div>
      <a class="btn ghost" style="font-size:12px;padding:7px 13px;margin-bottom:16px;display:inline-flex"
         href="{{ asset('templates/template_import_warga.xlsx') }}" download>
        <svg class="ic" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Unduh Template Excel
      </a>

      <div id="importUploader"></div>
      <div id="importResult" style="display:none;margin-top:12px"></div>
    </div>

    <div class="wg-foot">
      <button class="mbtn ghost" type="button" onclick="importClose()">Tutup</button>
    </div>
  </div>
</div>

<div class="grid-wrap">
  <div id="gridKK"></div>
</div>

{{-- ───── Modal Form KK ───── --}}
<div class="modal-overlay" id="kkModal">
  <div class="modal-card wg-card">
    <div class="wg-head">
      <div class="wg-head-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5 12 3l9 6.5"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/></svg>
      </div>
      <div>
        <h3 id="kkModalTitle">Tambah Kartu Keluarga</h3>
      </div>
    </div>

    <form id="formKK" onsubmit="return false" class="wg-body">
      <input type="hidden" id="kk_id">

      <div class="ff2">
        <div class="ff"><label>No. KK <span class="req">*</span></label><div id="kk_no_kk"></div></div>
        <div class="ff"><label>Tgl. Daftar</label><div id="kk_tgl_daftar"></div></div>
      </div>

      <div class="ff2">
        <div class="ff"><label>Kepala Keluarga <span class="req">*</span></label><div id="kk_kepala"></div></div>
        <div class="ff">
          <label>NIK Kepala Keluarga <small class="hint">(opsional — otomatis buat data warga)</small></label>
          <div id="kk_nik_kepala"></div>
        </div>
      </div>

      <div class="ff3">
        <div class="ff"><label>Blok</label><div id="kk_blok"></div></div>
        <div class="ff"><label>No. Rumah</label><div id="kk_no_rumah"></div></div>
        <div class="ff"><label>Status Hunian <span class="req">*</span></label><div id="kk_status_tinggal"></div></div>
      </div>

      <div class="ff"><label>Alamat Lengkap</label><div id="kk_alamat"></div></div>

      <div class="ff2">
        <div class="ff"><label>RT</label><div id="kk_rt"></div></div>
        <div class="ff"><label>RW</label><div id="kk_rw"></div></div>
      </div>

      <div class="ff"><label>No. Telepon KK</label><div id="kk_no_telepon"></div></div>
      <div class="ff"><label>Keterangan</label><div id="kk_keterangan"></div></div>

      <div class="mf-switch">
        <div id="kk_aktif"></div>
        <label class="mf-switch-lbl">KK Aktif</label>
      </div>
    </form>

    <div class="wg-foot">
      <button class="mbtn ghost" type="button" onclick="kkClose()">Batal</button>
      <button class="mbtn mbtn-save" type="button" onclick="kkSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Simpan
      </button>
    </div>
  </div>
</div>

{{-- ───── Modal Form Warga ───── --}}
<div class="modal-overlay" id="wargaModal">
  <div class="modal-card wg-card wg-card-lg">
    <div class="wg-head">
      <div class="wg-head-icon" style="background:var(--biru-soft);color:var(--biru)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div>
        <h3 id="wargaModalTitle">Tambah Anggota</h3>
      </div>
    </div>

    <form id="formWarga" onsubmit="return false" class="wg-body">
      <input type="hidden" id="w_id">
      <input type="hidden" id="w_kk_id">

      <div class="kk-info-box">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5 12 3l9 6.5"/><path d="M5 10v10h14V10"/></svg>
        <div>
          <span class="kk-info-label">Kartu Keluarga</span>
          <span id="w_kk_display" class="kk-info-val">—</span>
        </div>
      </div>

      <div class="ff2">
        <div class="ff"><label>NIK <span class="req">*</span></label><div id="w_nik"></div></div>
        <div class="ff"><label>Nama Lengkap <span class="req">*</span></label><div id="w_nama"></div></div>
      </div>

      <div class="ff3">
        <div class="ff"><label>Jenis Kelamin <span class="req">*</span></label><div id="w_jenis_kelamin"></div></div>
        <div class="ff"><label>Hubungan <span class="req">*</span></label><div id="w_hubungan"></div></div>
        <div class="ff"><label>Status Perkawinan</label><div id="w_status_perkawinan"></div></div>
      </div>

      <div class="ff2">
        <div class="ff"><label>Tempat Lahir</label><div id="w_tempat_lahir"></div></div>
        <div class="ff"><label>Tanggal Lahir</label><div id="w_tanggal_lahir"></div></div>
      </div>

      <div class="ff3">
        <div class="ff"><label>Agama</label><div id="w_agama"></div></div>
        <div class="ff"><label>Pendidikan</label><div id="w_pendidikan"></div></div>
        <div class="ff"><label>Pekerjaan</label><div id="w_pekerjaan"></div></div>
      </div>

      <div class="ff3">
        <div class="ff"><label>Status Tinggal <span class="req">*</span></label><div id="w_status_tinggal"></div></div>
        <div class="ff"><label>Status Warga <span class="req">*</span></label><div id="w_status_warga"></div></div>
        <div class="ff"><label>No. Telepon</label><div id="w_no_telepon"></div></div>
      </div>

      <div class="ff2">
        <div class="ff"><label>Tgl. Masuk</label><div id="w_tgl_masuk"></div></div>
        <div class="ff"><label>Tgl. Keluar <small class="hint">(jika pindah/meninggal)</small></label><div id="w_tgl_keluar"></div></div>
      </div>

      <div class="ff"><label>Keterangan</label><div id="w_keterangan"></div></div>

      <div class="ff">
        <label>Foto <small class="hint">(JPG/PNG, maks. 2 MB)</small></label>
        <div class="foto-upload-wrap">
          <div class="foto-preview" id="w_foto_preview">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
          <div class="foto-uploader-box">
            <div id="w_foto_uploader"></div>
            <input type="hidden" id="w_foto_path">
          </div>
        </div>
      </div>
    </form>

    <div class="wg-foot">
      <button class="mbtn ghost" type="button" onclick="wargaClose()">Batal</button>
      <button class="mbtn mbtn-save" type="button" onclick="wargaSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Simpan
      </button>
    </div>
  </div>
</div>

{{-- ───── Modal Konfirmasi Hapus ───── --}}
<div class="modal-overlay" id="deleteModal" onclick="if(event.target===this)deleteClose()">
  <div class="modal-card">
    <div class="modal-ic">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
    </div>
    <h3 id="deleteTitle">Hapus Data?</h3>
    <p id="deleteMsg">Data ini akan dihapus permanen.</p>
    <div class="modal-actions">
      <button class="mbtn ghost" type="button" onclick="deleteClose()">Batal</button>
      <button class="mbtn danger" type="button" id="deleteConfirmBtn">Ya, Hapus</button>
    </div>
  </div>
</div>

@endsection

@push('styles')
<style>
.content { max-width: none; padding-bottom: 0 }

/* Toolbar */
.wg-toolbar { display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;gap:16px }
.wg-title   { font-size:24px;font-weight:800;letter-spacing:-.02em;color:var(--tinta);margin:0 }
.wg-actions { display:flex;gap:8px;align-items:center;flex-shrink:0 }

/* Grid */
.grid-wrap { background:var(--surface);border:1px solid var(--garis);overflow:auto;box-shadow:var(--shadow);height:calc(100vh - 148px) }
@media(max-width:640px){
  .wg-toolbar{flex-direction:column;align-items:stretch;gap:10px}
  .wg-title{font-size:18px}
  .wg-actions{display:flex;flex-wrap:wrap;gap:8px}
  .grid-wrap{height:calc(100vh - 200px)}
}

/* Buttons */
.btn-ubah  { background:var(--emas) !important;color:#fff !important }
.btn-ubah:hover  { filter:brightness(1.08) }
.btn-hapus { background:var(--stempel) !important;color:#fff !important }
.btn-hapus:hover { filter:brightness(1.08) }
.btn-unduh  { background:var(--biru) !important;color:#fff !important }
.btn-unduh:hover  { filter:brightness(1.08) }
.btn-upload { background:#059669 !important;color:#fff !important }
.btn-upload:hover { filter:brightness(1.08) }
.import-info { display:flex;align-items:flex-start;gap:8px;padding:10px 12px;background:var(--biru-soft);border-radius:8px;font-size:12.5px;color:#1a3d52;margin-bottom:12px }
.import-info svg { flex:0 0 15px;margin-top:1px }
.btn[disabled] { opacity:.45;cursor:not-allowed;pointer-events:none }

/* DevExtreme grid styling */
#gridKK, #gridKK .dx-widget, .dx-master-detail-cell .dx-widget { font-family:'Plus Jakarta Sans',system-ui,sans-serif }
#gridKK .dx-datagrid { border:none;color:var(--tinta) }
#gridKK .dx-datagrid-headers { background:var(--kertas-2);border-bottom:1px solid var(--garis) }
#gridKK .dx-datagrid-headers .dx-header-row > td { font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:13px 14px;border:none }
#gridKK .dx-datagrid-rowsview { border-top:none }
#gridKK .dx-data-row > td { padding:11px 14px;font-size:13.5px;color:var(--tinta);border-bottom:1px solid var(--garis);border-right:none;vertical-align:middle }
#gridKK .dx-datagrid-rowsview .dx-data-row:hover > td { background:#F0F4F1 !important }
#gridKK .dx-datagrid-rowsview .dx-row-focused > td,
#gridKK .dx-datagrid-rowsview .dx-selection.dx-row > td { background:var(--daun-pucat) !important;color:#155234 !important;border-bottom-color:#cfe1d6 !important }
/* Column lines */
#gridKK .dx-datagrid .dx-column-lines > td { border-right:1px solid var(--garis) }
/* Alternating rows */
#gridKK .dx-datagrid-rowsview .dx-row-alt > td { background:#FAFAF7 }
#gridKK .dx-pager { background:var(--kertas);border-top:1px solid var(--garis);padding:10px 14px }
#gridKK .dx-pager .dx-page,.dx-pager .dx-page-size { border-radius:7px;font-size:12.5px }
#gridKK .dx-pager .dx-pages .dx-selection,.dx-pager .dx-page-sizes .dx-selection { background:var(--hutan);color:#fff }
#gridKK .dx-pager .dx-info { color:var(--redup);font-size:12.5px }
#gridKK .dx-toolbar { display:none }

/* Master-detail panel */
.dx-master-detail-cell { padding:0 !important;background:var(--kertas) !important }
.detail-panel { padding:16px 20px }
.detail-toolbar { display:flex;align-items:center;justify-content:space-between;margin-bottom:12px }
.detail-toolbar h4 { font-size:13.5px;font-weight:700;color:var(--tinta);margin:0 }
.detail-actions { display:flex;gap:8px }
.btn-sm { font-size:12px;padding:6px 12px;border-radius:8px }

/* Warga sub-grid */
.warga-grid .dx-datagrid { border:1px solid var(--garis) }
.warga-grid .dx-datagrid-headers .dx-header-row > td { font-size:10px;padding:9px 12px }
.warga-grid .dx-data-row > td { padding:8px 12px;font-size:13px }

/* Cell chips */
.gc-blok { display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;padding:2px 10px;border-radius:20px;background:var(--biru-soft);color:#1a3d52 }
.gc-jiwa { display:inline-flex;align-items:center;justify-content:center;min-width:28px;height:28px;padding:0 6px;border-radius:7px;background:var(--kertas-2);border:1px solid var(--garis);font-size:12.5px;font-weight:700;color:var(--redup) }
.gc-hunian { font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px }
.gc-hunian.milik  { background:var(--daun-pucat);color:#14532D }
.gc-hunian.sewa   { background:var(--emas-soft);color:#6b4f04 }
.gc-hunian.numpang{ background:var(--kertas-2);color:var(--redup);border:1px solid var(--garis) }
.gc-pill { display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:700;padding:3px 11px;border-radius:20px }
.gc-pill::before { content:"";width:6px;height:6px;border-radius:50% }
.gc-pill.on  { background:var(--daun-pucat);color:#1B5E3F }.gc-pill.on::before  { background:#2D6A4F }
.gc-pill.off { background:var(--stempel-soft);color:#9A3422 }.gc-pill.off::before { background:var(--stempel) }
.gc-sw-aktif  { background:var(--daun-pucat);color:#14532D }
.gc-sw-pindah { background:var(--emas-soft);color:#6b4f04 }
.gc-sw-meninggal { background:var(--kertas-2);color:var(--redup);border:1px solid var(--garis) }
.gc-hub { font-size:11px;color:var(--redup);font-weight:600 }
.gc-nik { font-family:'IBM Plex Mono',monospace;font-size:11.5px;color:var(--redup) }
.gc-mono { font-family:'IBM Plex Mono',monospace;font-size:12px;color:var(--redup) }

/* Modal */
.wg-card    { max-width:580px;width:100%;padding:0;text-align:left }
.wg-card-lg { max-width:720px }
.wg-head    { display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--garis) }
.wg-head-icon { flex:0 0 34px;width:34px;height:34px;border-radius:9px;background:var(--daun-pucat);color:var(--daun);display:flex;align-items:center;justify-content:center }
.wg-head-icon svg { width:17px;height:17px }
.wg-head h3 { font-size:15px;font-weight:800;margin:0 0 1px }
.wg-body    { padding:18px 20px;display:flex;flex-direction:column;gap:0;max-height:70vh;overflow-y:auto }
.kk-info-box { display:flex;align-items:center;gap:10px;padding:10px 13px;background:var(--daun-pucat);border:1px solid #cfe1d6;border-radius:9px;margin-bottom:14px }
.kk-info-box svg { flex:0 0 14px;color:var(--daun) }
.kk-info-label { display:block;font-size:10.5px;font-weight:700;color:var(--daun);letter-spacing:.04em;text-transform:uppercase }
.kk-info-val { display:block;font-size:13.5px;font-weight:700;color:var(--hutan) }
.ff  { margin-bottom:13px;display:flex;flex-direction:column }
.ff > label { font-size:12px;font-weight:700;margin-bottom:5px;color:var(--redup) }
.ff2 { display:grid;grid-template-columns:1fr 1fr;gap:12px }
.ff3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px }
.req { color:var(--stempel) }
.hint { font-size:11px;color:#9aa89f;font-weight:500 }
.mf-switch { display:flex;align-items:center;gap:8px;margin-top:4px }
.wg-foot { display:flex;gap:10px;justify-content:flex-end;padding:12px 20px;border-top:1px solid var(--garis);background:var(--kertas);border-radius:0 0 14px 14px }
.wg-foot .mbtn { flex:0 0 auto; min-width:90px }
.wg-foot .mbtn.ghost { background:var(--stempel-soft);color:var(--stempel);border:1.5px solid #f5c6bb }
.wg-foot .mbtn.ghost:hover { background:#fde5e0 }
.mbtn-save { display:inline-flex;align-items:center;gap:7px;background:var(--hutan);color:#fff;font-weight:700;font-size:13px;padding:8px 18px;border-radius:10px;border:none;cursor:pointer;transition:.14s }
.mbtn-save:hover { background:var(--hutan-2) }
.mbtn-save svg { width:14px;height:14px }

/* Foto upload */
.foto-upload-wrap { display:flex;align-items:flex-start;gap:14px }
.foto-preview {
  flex:0 0 72px;width:72px;height:72px;border-radius:12px;
  background:var(--kertas-2);border:1.5px solid var(--garis);
  display:flex;align-items:center;justify-content:center;
  overflow:hidden;color:var(--redup);
}
.foto-preview img { width:100%;height:100%;object-fit:cover }
.foto-uploader-box { flex:1 }
.foto-hint { font-size:11.5px;color:var(--redup);margin-top:5px }

/* Icon action buttons di sub-grid */
.wg-ic-btn {
  width:28px;height:28px;border-radius:7px;border:none;cursor:pointer;
  display:inline-flex;align-items:center;justify-content:center;
  transition:.13s;flex:0 0 28px;
}
.wg-ic-btn svg { width:13px;height:13px }
.wg-ic-edit  { background:var(--emas-soft);color:#7a5c00 }
.wg-ic-edit:hover  { background:var(--emas);color:#fff }
.wg-ic-off   { background:#FEF3C7;color:#92400E }
.wg-ic-off:hover   { background:#F59E0B;color:#fff }
.wg-ic-del   { background:var(--stempel-soft);color:var(--stempel) }
.wg-ic-del:hover   { background:var(--stempel);color:#fff }

/* Avatar di sub-grid */
.wg-av {
  width:30px;height:30px;border-radius:8px;object-fit:cover;
  border:1.5px solid var(--garis);margin-right:8px;vertical-align:middle;flex:0 0 30px;
}
.wg-av-placeholder {
  width:30px;height:30px;border-radius:8px;
  background:var(--daun-pucat);color:var(--hutan);
  display:inline-flex;align-items:center;justify-content:center;
  font-size:11px;font-weight:800;margin-right:8px;flex:0 0 30px;vertical-align:middle;
}

@media (max-width:600px) { .ff2,.ff3 { grid-template-columns:1fr } .wg-card { margin:12px } }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/devextreme/exceljs.min.js') }}"></script>
<script src="{{ asset('js/devextreme/FileSaver.min.js') }}"></script>
<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

const urlKKList      = "{{ route('warga.kk.list') }}";
const urlKKSave      = "{{ route('warga.kk.save') }}";
const urlKKDelete    = "{{ route('warga.kk.delete') }}";
const urlWargaList   = "{{ url('warga/anggota') }}";
const urlWargaSave    = "{{ route('warga.save') }}";
const urlWargaDelete  = "{{ route('warga.delete') }}";
const urlUploadFoto   = "{{ route('warga.upload-foto') }}";
const urlImport       = "{{ route('warga.import') }}";

const OPT_STATUS_TINGGAL_KK = [
  { id:'milik',   label:'Milik Sendiri' },
  { id:'sewa',    label:'Sewa / Kontrak' },
  { id:'numpang', label:'Numpang' },
];
const OPT_STATUS_TINGGAL_WARGA = [
  { id:'tetap',   label:'Tetap' },
  { id:'kontrak', label:'Kontrak' },
  { id:'kos',     label:'Kos' },
  { id:'numpang', label:'Numpang' },
];
const OPT_HUBUNGAN = [
  { id:'kepala_keluarga', label:'Kepala Keluarga' },
  { id:'istri',    label:'Istri' },
  { id:'suami',    label:'Suami' },
  { id:'anak',     label:'Anak' },
  { id:'orang_tua',label:'Orang Tua' },
  { id:'mertua',   label:'Mertua' },
  { id:'menantu',  label:'Menantu' },
  { id:'cucu',     label:'Cucu' },
  { id:'lainnya',  label:'Lainnya' },
];
const OPT_STATUS_KAWIN = [
  { id:'belum_kawin', label:'Belum Kawin' },
  { id:'kawin',       label:'Kawin' },
  { id:'cerai_hidup', label:'Cerai Hidup' },
  { id:'cerai_mati',  label:'Cerai Mati' },
];
const OPT_AGAMA = ['Islam','Kristen Protestan','Kristen Katolik','Hindu','Buddha','Khonghucu','Lainnya'];
const OPT_PENDIDIKAN = ['Tidak Sekolah','SD','SMP','SMA/SMK','D1','D2','D3','S1','S2','S3'];
const OPT_STATUS_WARGA = [
  { id:'aktif',     label:'Aktif' },
  { id:'pindah',    label:'Pindah' },
  { id:'meninggal', label:'Meninggal' },
];

const HUNIAN_CLS = { milik:'milik', sewa:'sewa', numpang:'numpang' };
const SW_CLS     = { aktif:'gc-sw-aktif', pindah:'gc-sw-pindah', meninggal:'gc-sw-meninggal' };
const SW_LBL     = { aktif:'Aktif', pindah:'Pindah', meninggal:'Meninggal' };

let grid, focusedRow = null;

// ─── Init ─────────────────────────────────────────────────────────────
$(function() {
  var p = window.__perms || {};
  if (!p.add)    document.getElementById('btnTambah').disabled = true;
  if (!p.edit)   document.getElementById('btnUbah').disabled   = true;
  if (!p.delete) document.getElementById('btnHapus').disabled  = true;
  if (!p.export) document.getElementById('btnUnduh').disabled  = true;
  if (!p.upload) document.getElementById('btnUpload').disabled = true;

  grid = $("#gridKK").dxDataGrid({
    dataSource: new DevExpress.data.CustomStore({ key:"id", load: () => $.getJSON(urlKKList) }),
    showBorders: false, showColumnLines: true, showRowLines: true,
    rowAlternationEnabled: true, width:"100%", height:"100%",
    columnAutoWidth: true, wordWrapEnabled: false,
    scrolling: { useNative: true, showScrollbar: 'always', mode: 'standard' },
    headerFilter: { visible: true },
    paging: { pageSize: 50 },
    pager: { visible:true, displayMode:"compact", showPageSizeSelector:true, allowedPageSizes:[25,50,"all"], showInfo:true, showNavigationButtons:true },
    focusedRowEnabled: true,
    onFocusedRowChanged: e => { focusedRow = e.row ? e.row.data : null; },
    masterDetail: {
      enabled: true,
      template: function(container, info) {
        buildDetailPanel(container, info.data);
      }
    },
    columns: [
      {
        caption:"No.", width:52, alignment:"center",
        allowFiltering:false, allowSorting:false,
        cellTemplate: function(c, o) {
          var idx = o.component.pageIndex() * o.component.pageSize() + o.rowIndex + 1;
          $('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(idx).appendTo(c);
        }
      },
      {
        dataField:"alamat_singkat", caption:"Blok / No.", width:110,
        cellTemplate: function(c, o) {
          if (!o.value) { $('<span style="color:var(--garis-2)">—</span>').appendTo(c); return; }
          $('<span class="gc-blok">').text(o.value).appendTo(c);
        }
      },
      {
        dataField:"kepala_keluarga", caption:"Kepala Keluarga", width:200,
        cellTemplate: function(c, o) {
          var wrap = $('<div>');
          $('<div style="font-weight:600;font-size:13.5px">').text(o.value || '—').appendTo(wrap);
          $('<div class="gc-nik">').text(o.row.data.no_kk || '').appendTo(wrap);
          wrap.appendTo(c);
        }
      },
      {
        dataField:"status_tinggal", caption:"Hunian", width:105,
        cellTemplate: function(c, o) {
          var map = { milik:'Milik Sendiri', sewa:'Sewa/Kontrak', numpang:'Numpang' };
          var v   = o.value || '';
          $('<span class="gc-hunian '+(HUNIAN_CLS[v]||'numpang')+'">').text(map[v]||v).appendTo(c);
        }
      },
      {
        dataField:"rt", caption:"RT/RW", width:85, alignment:"center",
        cellTemplate: function(c, o) {
          var rt = o.value || '—';
          var rw = o.row.data.rw || '—';
          $('<span style="font-size:12.5px;font-weight:600">').text(rt + ' / ' + rw).appendTo(c);
        }
      },
      {
        dataField:"alamat", caption:"Alamat", width:180,
        cellTemplate: function(c, o) {
          $('<span style="font-size:12.5px;color:var(--redup)">').text(o.value || '—').appendTo(c);
        }
      },
      {
        dataField:"no_telepon", caption:"No. Telepon", width:130,
        cellTemplate: function(c, o) {
          if (!o.value) { $('<span style="color:var(--garis-2)">—</span>').appendTo(c); return; }
          $('<span class="gc-mono">').text(o.value).appendTo(c);
        }
      },
      {
        dataField:"jumlah_jiwa", caption:"Jiwa", width:65, alignment:"center",
        cellTemplate: function(c, o) {
          $('<span class="gc-jiwa">').text(o.value || 0).appendTo(c);
        }
      },
      {
        dataField:"tgl_daftar", caption:"Tgl. Daftar", width:105, alignment:"center",
        cellTemplate: function(c, o) {
          $('<span style="font-size:12.5px">').text(o.value || '—').appendTo(c);
        }
      },
      {
        dataField:"aktif", caption:"Status", width:100, alignment:"center",
        cellTemplate: function(c, o) {
          var on = (o.value === true || o.value === 1);
          $('<span class="gc-pill '+(on?'on':'off')+'">').text(on?'Aktif':'Nonaktif').appendTo(c);
        }
      }
    ]
  }).dxDataGrid("instance");

  initKKEditors();
  initWargaEditors();
});

// ─── Detail panel (warga per KK) ──────────────────────────────────────
function buildDetailPanel(container, kk) {
  var panel = $('<div class="detail-panel">');

  var toolbar = $('<div class="detail-toolbar">');
  $('<h4>').text('Anggota Keluarga — ' + (kk.kepala_keluarga || '')).appendTo(toolbar);

  var actions = $('<div class="detail-actions">');
  $('<button class="btn btn-sm" type="button">').html(
    '<svg class="ic" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Tambah Anggota'
  ).on('click', function() { wargaAdd(kk.id, kk.no_kk, kk.kepala_keluarga); }).appendTo(actions);
  actions.appendTo(toolbar);
  toolbar.appendTo(panel);

  var subGrid = $('<div class="warga-grid">');
  subGrid.appendTo(panel);
  panel.appendTo(container);

  var subGridRef = null; // referensi instance sub-grid

  subGrid.dxDataGrid({
    dataSource: new DevExpress.data.CustomStore({
      key: "id",
      load: () => $.getJSON(urlWargaList + '/' + kk.id)
    }),
    showBorders: true, showColumnLines: false, showRowLines: true,
    rowAlternationEnabled: false, width: "100%",
    focusedRowEnabled: true,
    paging: { enabled: false },
    onInitialized: function(e) { subGridRef = e.component; },
    columns: [
      { caption:"No.", width:45, alignment:"center", allowFiltering:false, allowSorting:false,
        cellTemplate: function(c,o) { $('<span style="font-size:11.5px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c); }
      },
      { dataField:"nama", caption:"Nama", minWidth:170, cellTemplate: function(c,o) {
          var wrap = $('<div style="display:flex;align-items:center">');
          if (o.row.data.foto_url) {
            $('<img class="wg-av">').attr('src', o.row.data.foto_url).appendTo(wrap);
          } else {
            var init = (o.value||'').split(' ').map(function(w){return w[0]||'';}).slice(0,2).join('').toUpperCase();
            $('<span class="wg-av-placeholder">').text(init).appendTo(wrap);
          }
          var info = $('<div>');
          $('<div style="font-weight:600;font-size:13px">').text(o.value).appendTo(info);
          $('<div class="gc-nik">').text(o.row.data.nik || '').appendTo(info);
          info.appendTo(wrap);
          wrap.appendTo(c);
      }},
      { dataField:"hubungan", caption:"Hubungan", width:140, cellTemplate: function(c,o) {
          var map = { kepala_keluarga:'Kepala Keluarga',istri:'Istri',suami:'Suami',anak:'Anak',orang_tua:'Orang Tua',mertua:'Mertua',menantu:'Menantu',cucu:'Cucu',lainnya:'Lainnya' };
          $('<span class="gc-hub">').text(map[o.value] || o.value || '—').appendTo(c);
      }},
      { dataField:"jenis_kelamin", caption:"L/P", width:55, alignment:"center", cellTemplate: function(c,o) {
          $('<span style="font-weight:700;color:'+(o.value==='L'?'var(--biru)':'var(--stempel)')+'">').text(o.value||'—').appendTo(c);
      }},
      { dataField:"umur", caption:"Umur", width:65, alignment:"center", cellTemplate: function(c,o) {
          $('<span>').text(o.value != null ? o.value + ' th' : '—').appendTo(c);
      }},
      { dataField:"pekerjaan", caption:"Pekerjaan", minWidth:120, cellTemplate: function(c,o) {
          $('<span style="font-size:12.5px;color:var(--redup)">').text(o.value || '—').appendTo(c);
      }},
      { dataField:"status_tinggal", caption:"Tinggal", width:90, cellTemplate: function(c,o) {
          var map = { tetap:'Tetap',kontrak:'Kontrak',kos:'Kos',numpang:'Numpang' };
          $('<span style="font-size:12px;color:var(--redup)">').text(map[o.value]||o.value||'—').appendTo(c);
      }},
      { dataField:"status_warga", caption:"Status", width:100, cellTemplate: function(c,o) {
          var v = o.value || 'aktif';
          $('<span class="gc-hunian '+(SW_CLS[v]||'')+'">').text(SW_LBL[v]||v).appendTo(c);
      }},
      { dataField:"no_telepon", caption:"Telepon", width:120, cellTemplate: function(c,o) {
          $('<span class="gc-mono">').text(o.value || '—').appendTo(c);
      }},
      {
        caption:"Aksi", width:105, alignment:"center", allowFiltering:false, allowSorting:false,
        cellTemplate: function(c, o) {
          var d = o.row.data;
          var wrap = $('<div style="display:flex;gap:4px;justify-content:center">');
          // Ubah
          $('<button class="wg-ic-btn wg-ic-edit" type="button" title="Ubah data anggota"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg></button>')
            .on('click', function() { wargaEdit(d, kk.no_kk, kk.kepala_keluarga); }).appendTo(wrap);
          // Nonaktif (soft delete)
          $('<button class="wg-ic-btn wg-ic-off" type="button" title="Nonaktifkan (pindah/meninggal)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg></button>')
            .on('click', function() { wargaNonaktif(d, subGridRef); }).appendTo(wrap);
          // Hapus permanen
          $('<button class="wg-ic-btn wg-ic-del" type="button" title="Hapus permanen (koreksi data salah)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg></button>')
            .on('click', function() { wargaDeleteConfirm(d, subGridRef); }).appendTo(wrap);
          wrap.appendTo(c);
        }
      }
    ]
  });
}

// ─── Init editors KK ──────────────────────────────────────────────────
function initKKEditors() {
  $("#kk_no_kk").dxTextBox({ placeholder:"16 digit No. KK", maxLength:16 });
  $("#kk_kepala").dxTextBox({ placeholder:"Nama kepala keluarga" });
  $("#kk_nik_kepala").dxTextBox({ placeholder:"16 digit NIK (opsional — otomatis buat data warga)", maxLength:16 });
  $("#kk_blok").dxTextBox({ placeholder:"cth. Blok A" });
  $("#kk_no_rumah").dxTextBox({ placeholder:"cth. 12" });
  $("#kk_alamat").dxTextArea({ placeholder:"Alamat lengkap", height:70, autoResizeEnabled:true });
  $("#kk_rt").dxTextBox({ placeholder:"001" });
  $("#kk_rw").dxTextBox({ placeholder:"015" });
  $("#kk_no_telepon").dxTextBox({ placeholder:"cth. 08xx" });
  $("#kk_tgl_daftar").dxDateBox({ type:"date", displayFormat:"dd/MM/yyyy", placeholder:"dd/mm/yyyy", showClearButton:true });
  $("#kk_keterangan").dxTextArea({ placeholder:"Keterangan tambahan...", height:60, autoResizeEnabled:true });
  $("#kk_aktif").dxSwitch({ value:true });
  $("#kk_status_tinggal").dxSelectBox({
    dataSource: OPT_STATUS_TINGGAL_KK, valueExpr:"id", displayExpr:"label",
    value:"milik", placeholder:"— Pilih —"
  });
}

// ─── Init editors Warga ────────────────────────────────────────────────
function initWargaEditors() {
  $("#w_nik").dxTextBox({ placeholder:"16 digit NIK", maxLength:16 });
  $("#w_nama").dxTextBox({ placeholder:"Nama lengkap sesuai KTP" });
  $("#w_tempat_lahir").dxTextBox({ placeholder:"cth. Jakarta" });
  $("#w_tanggal_lahir").dxDateBox({ type:"date", displayFormat:"dd/MM/yyyy", showClearButton:true });
  $("#w_pekerjaan").dxTextBox({ placeholder:"cth. Wiraswasta" });
  $("#w_no_telepon").dxTextBox({ placeholder:"cth. 0812xxxx" });
  $("#w_tgl_masuk").dxDateBox({ type:"date", displayFormat:"dd/MM/yyyy", showClearButton:true });
  $("#w_tgl_keluar").dxDateBox({ type:"date", displayFormat:"dd/MM/yyyy", showClearButton:true });
  $("#w_keterangan").dxTextArea({ placeholder:"Keterangan...", height:60, autoResizeEnabled:true });

  $("#w_foto_uploader").dxFileUploader({
    uploadUrl: urlUploadFoto,
    uploadHeaders: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
    name: 'foto',
    uploadMode: 'instantly',
    allowedFileExtensions: ['.jpg','.jpeg','.png','.webp'],
    maxFileSize: 2097152,
    multiple: false,
    accept: 'image/*',
    labelText: 'atau seret foto ke sini',
    selectButtonText: 'Pilih Foto',
    showFileList: false,
    onUploaded: function(e) {
      var res = JSON.parse(e.request.responseText);
      if (res.ok) {
        $("#w_foto_path").val(res.path);
        $("#w_foto_preview").html('<img src="' + res.url + '">');
      }
    },
    onUploadError: function() {
      DevExpress.ui.notify("Gagal upload foto. Pastikan format JPG/PNG dan ukuran < 2MB","error",3000);
    }
  });

  $("#w_jenis_kelamin").dxSelectBox({ dataSource:[{id:'L',label:'Laki-laki'},{id:'P',label:'Perempuan'}], valueExpr:"id", displayExpr:"label", placeholder:"— Pilih —" });
  $("#w_hubungan").dxSelectBox({ dataSource:OPT_HUBUNGAN, valueExpr:"id", displayExpr:"label", placeholder:"— Pilih —" });
  $("#w_status_perkawinan").dxSelectBox({ dataSource:OPT_STATUS_KAWIN, valueExpr:"id", displayExpr:"label", value:"belum_kawin" });
  $("#w_agama").dxSelectBox({ dataSource:OPT_AGAMA, showClearButton:true, placeholder:"— Pilih —", acceptCustomValue:true });
  $("#w_pendidikan").dxSelectBox({ dataSource:OPT_PENDIDIKAN, showClearButton:true, placeholder:"— Pilih —", acceptCustomValue:true });
  $("#w_status_tinggal").dxSelectBox({ dataSource:OPT_STATUS_TINGGAL_WARGA, valueExpr:"id", displayExpr:"label", value:"tetap" });
  $("#w_status_warga").dxSelectBox({ dataSource:OPT_STATUS_WARGA, valueExpr:"id", displayExpr:"label", value:"aktif" });
}

// ─── KK CRUD ──────────────────────────────────────────────────────────
function kkAdd() {
  $("#kk_id").val('');
  $("#kkModalTitle").text('Tambah Kartu Keluarga');
  $("#kkModalSub").text('Lengkapi data kepala keluarga dan rumah');
  $("#kk_no_kk").dxTextBox("instance").option("value","");
  $("#kk_kepala").dxTextBox("instance").option("value","");
  $("#kk_nik_kepala").dxTextBox("instance").option("value","");
  $("#kk_nik_kepala").closest(".ff2").show(); // tampilkan kembali saat tambah baru
  $("#kk_blok").dxTextBox("instance").option("value","");
  $("#kk_no_rumah").dxTextBox("instance").option("value","");
  $("#kk_alamat").dxTextArea("instance").option("value","");
  $("#kk_rt").dxTextBox("instance").option("value","");
  $("#kk_rw").dxTextBox("instance").option("value","");
  $("#kk_no_telepon").dxTextBox("instance").option("value","");
  $("#kk_tgl_daftar").dxDateBox("instance").option("value",null);
  $("#kk_keterangan").dxTextArea("instance").option("value","");
  $("#kk_status_tinggal").dxSelectBox("instance").option("value","milik");
  $("#kk_aktif").dxSwitch("instance").option("value",true);
  kkOpen();
}

function kkEdit() {
  if (!focusedRow) { DevExpress.ui.notify("Pilih baris KK terlebih dahulu","warning",2500); return; }
  var d = focusedRow;
  $("#kk_id").val(d.id);
  $("#kkModalTitle").text('Ubah Kartu Keluarga');
  $("#kkModalSub").text('Mengubah: ' + d.kepala_keluarga);
  // Sembunyikan field NIK saat mode ubah (NIK sudah ada di data warga)
  $("#kk_nik_kepala").dxTextBox("instance").option("value","");
  $("#kk_nik_kepala").closest(".ff2").hide();
  $("#kk_no_kk").dxTextBox("instance").option("value", d.no_kk || "");
  $("#kk_kepala").dxTextBox("instance").option("value", d.kepala_keluarga || "");
  $("#kk_blok").dxTextBox("instance").option("value", d.blok || "");
  $("#kk_no_rumah").dxTextBox("instance").option("value", d.no_rumah || "");
  $("#kk_alamat").dxTextArea("instance").option("value", d.alamat || "");
  $("#kk_rt").dxTextBox("instance").option("value", d.rt || "");
  $("#kk_rw").dxTextBox("instance").option("value", d.rw || "");
  $("#kk_no_telepon").dxTextBox("instance").option("value", d.no_telepon || "");
  $("#kk_tgl_daftar").dxDateBox("instance").option("value", d.tgl_daftar ? new Date(d.tgl_daftar.split('/').reverse().join('-')) : null);
  $("#kk_keterangan").dxTextArea("instance").option("value", d.keterangan || "");
  $("#kk_status_tinggal").dxSelectBox("instance").option("value", d.status_tinggal || "milik");
  $("#kk_aktif").dxSwitch("instance").option("value", d.aktif === true || d.aktif === 1);
  kkOpen();
}

function fmtDate(d) {
  if (!d) return '';
  var dt = new Date(d);
  return dt.getFullYear() + '-' + String(dt.getMonth()+1).padStart(2,'0') + '-' + String(dt.getDate()).padStart(2,'0');
}

function kkSave() {
  var data = {
    id:             $("#kk_id").val(),
    no_kk:          $("#kk_no_kk").dxTextBox("instance").option("value"),
    kepala_keluarga:$("#kk_kepala").dxTextBox("instance").option("value"),
    blok:           $("#kk_blok").dxTextBox("instance").option("value"),
    no_rumah:       $("#kk_no_rumah").dxTextBox("instance").option("value"),
    alamat:         $("#kk_alamat").dxTextArea("instance").option("value"),
    rt:             $("#kk_rt").dxTextBox("instance").option("value"),
    rw:             $("#kk_rw").dxTextBox("instance").option("value"),
    no_telepon:     $("#kk_no_telepon").dxTextBox("instance").option("value"),
    tgl_daftar:     fmtDate($("#kk_tgl_daftar").dxDateBox("instance").option("value")),
    keterangan:     $("#kk_keterangan").dxTextArea("instance").option("value"),
    status_tinggal: $("#kk_status_tinggal").dxSelectBox("instance").option("value"),
    aktif:          $("#kk_aktif").dxSwitch("instance").option("value") ? 1 : 0,
    nik_kepala:     $("#kk_nik_kepala").dxTextBox("instance").option("value") || '',
  };
  if (!data.no_kk)          { DevExpress.ui.notify("No. KK wajib diisi","error",2500); return; }
  if (!data.kepala_keluarga){ DevExpress.ui.notify("Nama kepala keluarga wajib diisi","error",2500); return; }

  $.ajax({ url:urlKKSave, type:"POST", data:data })
    .done(function(r) { DevExpress.ui.notify(r.message,"success",2500); kkClose(); grid.refresh(); })
    .fail(function(xhr) {
      var m = xhr.responseJSON?.errors ? Object.values(xhr.responseJSON.errors).flat().join(' ') : (xhr.responseJSON?.message || "Gagal menyimpan");
      DevExpress.ui.notify(m,"error",3500);
    });
}

function kkDelete() {
  if (!focusedRow) { DevExpress.ui.notify("Pilih baris KK terlebih dahulu","warning",2500); return; }
  var d = focusedRow;
  $("#deleteTitle").text("Hapus KK?");
  $("#deleteMsg").text('KK "' + d.kepala_keluarga + '" akan dihapus. KK harus kosong (0 anggota) untuk bisa dihapus.');
  $("#deleteConfirmBtn").off('click').on('click', function() {
    $.ajax({ url:urlKKDelete, type:"POST", data:{ id:d.id } })
      .done(function(r) { DevExpress.ui.notify(r.message,"success",2500); deleteClose(); focusedRow=null; grid.refresh(); })
      .fail(function(xhr) { DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal menghapus","error",3000); deleteClose(); });
  });
  deleteOpen();
}

// ─── Warga CRUD ───────────────────────────────────────────────────────
function wargaAdd(kkId, noKk, kkNama) {
  $("#w_id").val(''); $("#w_kk_id").val(kkId);
  $("#wargaModalTitle").text('Tambah Anggota');
  $("#wargaModalSub").text('Isi data anggota keluarga');
  $("#w_kk_display").text('No. KK ' + noKk + ' — ' + kkNama);
  $("#w_nik").dxTextBox("instance").option("value","");
  $("#w_nama").dxTextBox("instance").option("value","");
  $("#w_jenis_kelamin").dxSelectBox("instance").option("value",null);
  $("#w_hubungan").dxSelectBox("instance").option("value",null);
  $("#w_status_perkawinan").dxSelectBox("instance").option("value","belum_kawin");
  $("#w_tempat_lahir").dxTextBox("instance").option("value","");
  $("#w_tanggal_lahir").dxDateBox("instance").option("value",null);
  $("#w_agama").dxSelectBox("instance").option("value",null);
  $("#w_pendidikan").dxSelectBox("instance").option("value",null);
  $("#w_pekerjaan").dxTextBox("instance").option("value","");
  $("#w_no_telepon").dxTextBox("instance").option("value","");
  $("#w_status_tinggal").dxSelectBox("instance").option("value","tetap");
  $("#w_status_warga").dxSelectBox("instance").option("value","aktif");
  $("#w_tgl_masuk").dxDateBox("instance").option("value",null);
  $("#w_tgl_keluar").dxDateBox("instance").option("value",null);
  $("#w_keterangan").dxTextArea("instance").option("value","");
  $("#w_foto_path").val('');
  $("#w_foto_preview").html('<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>');
  wargaOpen();
}

function wargaEdit(d, noKk, kkNama) {
  $("#w_id").val(d.id); $("#w_kk_id").val(d.kartu_keluarga_id);
  $("#wargaModalTitle").text('Ubah Anggota');
  $("#wargaModalSub").text('Mengubah: ' + d.nama);
  $("#w_kk_display").text('No. KK ' + (noKk || '—') + (kkNama ? ' — ' + kkNama : ''));
  $("#w_nik").dxTextBox("instance").option("value", d.nik || "");
  $("#w_nama").dxTextBox("instance").option("value", d.nama || "");
  $("#w_jenis_kelamin").dxSelectBox("instance").option("value", d.jenis_kelamin || null);
  $("#w_hubungan").dxSelectBox("instance").option("value", d.hubungan || null);
  $("#w_status_perkawinan").dxSelectBox("instance").option("value", d.status_perkawinan || "belum_kawin");
  $("#w_tempat_lahir").dxTextBox("instance").option("value", d.tempat_lahir || "");
  $("#w_tanggal_lahir").dxDateBox("instance").option("value", d.tanggal_lahir ? new Date(d.tanggal_lahir.split('/').reverse().join('-')) : null);
  $("#w_agama").dxSelectBox("instance").option("value", d.agama || null);
  $("#w_pendidikan").dxSelectBox("instance").option("value", d.pendidikan || null);
  $("#w_pekerjaan").dxTextBox("instance").option("value", d.pekerjaan || "");
  $("#w_no_telepon").dxTextBox("instance").option("value", d.no_telepon || "");
  $("#w_status_tinggal").dxSelectBox("instance").option("value", d.status_tinggal || "tetap");
  $("#w_status_warga").dxSelectBox("instance").option("value", d.status_warga || "aktif");
  $("#w_tgl_masuk").dxDateBox("instance").option("value", d.tgl_masuk ? new Date(d.tgl_masuk.split('/').reverse().join('-')) : null);
  $("#w_tgl_keluar").dxDateBox("instance").option("value", d.tgl_keluar ? new Date(d.tgl_keluar.split('/').reverse().join('-')) : null);
  $("#w_keterangan").dxTextArea("instance").option("value", d.keterangan || "");
  $("#w_foto_path").val(d.foto || '');
  if (d.foto_url) {
    $("#w_foto_preview").html('<img src="' + d.foto_url + '">');
  } else {
    $("#w_foto_preview").html('<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>');
  }
  wargaOpen();
}

function wargaSave() {
  var tglLahir  = $("#w_tanggal_lahir").dxDateBox("instance").option("value");
  var tglMasuk  = $("#w_tgl_masuk").dxDateBox("instance").option("value");
  var tglKeluar = $("#w_tgl_keluar").dxDateBox("instance").option("value");

  var data = {
    id:                 $("#w_id").val(),
    kartu_keluarga_id:  $("#w_kk_id").val(),
    nik:                $("#w_nik").dxTextBox("instance").option("value"),
    nama:               $("#w_nama").dxTextBox("instance").option("value"),
    jenis_kelamin:      $("#w_jenis_kelamin").dxSelectBox("instance").option("value"),
    hubungan:           $("#w_hubungan").dxSelectBox("instance").option("value"),
    status_perkawinan:  $("#w_status_perkawinan").dxSelectBox("instance").option("value"),
    tempat_lahir:       $("#w_tempat_lahir").dxTextBox("instance").option("value"),
    tanggal_lahir:      fmtDate(tglLahir),
    agama:              $("#w_agama").dxSelectBox("instance").option("value"),
    pendidikan:         $("#w_pendidikan").dxSelectBox("instance").option("value"),
    pekerjaan:          $("#w_pekerjaan").dxTextBox("instance").option("value"),
    no_telepon:         $("#w_no_telepon").dxTextBox("instance").option("value"),
    status_tinggal:     $("#w_status_tinggal").dxSelectBox("instance").option("value"),
    status_warga:       $("#w_status_warga").dxSelectBox("instance").option("value"),
    tgl_masuk:          fmtDate(tglMasuk),
    tgl_keluar:         fmtDate(tglKeluar),
    keterangan:         $("#w_keterangan").dxTextArea("instance").option("value"),
    foto:               $("#w_foto_path").val() || '',
  };

  if (!data.nik)  { DevExpress.ui.notify("NIK wajib diisi","error",2500); return; }
  if (!data.nama) { DevExpress.ui.notify("Nama wajib diisi","error",2500); return; }
  if (!data.jenis_kelamin) { DevExpress.ui.notify("Jenis kelamin wajib dipilih","error",2500); return; }
  if (!data.hubungan)      { DevExpress.ui.notify("Hubungan wajib dipilih","error",2500); return; }

  $.ajax({ url:urlWargaSave, type:"POST", data:data })
    .done(function(r) {
      DevExpress.ui.notify(r.message,"success",2500);
      wargaClose();
      grid.refresh();
    })
    .fail(function(xhr) {
      var m = xhr.responseJSON?.errors ? Object.values(xhr.responseJSON.errors).flat().join(' ') : (xhr.responseJSON?.message || "Gagal menyimpan");
      DevExpress.ui.notify(m,"error",3500);
    });
}

// Soft delete: tandai pindah/meninggal tanpa hapus data
function wargaNonaktif(d, subGridInst) {
  $("#deleteTitle").text("Nonaktifkan Anggota?");
  $("#deleteMsg").text('"' + d.nama + '" akan ditandai sebagai Pindah/Meninggal (soft delete). Data tetap tersimpan untuk historis.');
  $("#deleteConfirmBtn").off('click').on('click', function() {
    $.ajax({ url:urlWargaDelete, type:"POST", data:{ id:d.id, force:0 } })
      .done(function(r) {
        DevExpress.ui.notify(r.message,"success",2500);
        deleteClose();
        if (subGridInst) subGridInst.refresh();
        else grid.refresh();
      })
      .fail(function() { DevExpress.ui.notify("Gagal","error",3000); });
  });
  deleteOpen();
}

// Hard delete: hapus permanen (untuk kesalahan input data)
function wargaDeleteConfirm(d, subGridInst) {
  $("#deleteTitle").text("Hapus Permanen?");
  $("#deleteMsg").text('"' + d.nama + '" akan dihapus PERMANEN dari database. Gunakan hanya untuk koreksi data salah input. Tidak bisa dipulihkan!');
  $("#deleteConfirmBtn").off('click').on('click', function() {
    $.ajax({ url:urlWargaDelete, type:"POST", data:{ id:d.id, force:1 } })
      .done(function(r) {
        DevExpress.ui.notify(r.message,"success",2500);
        deleteClose();
        if (subGridInst) subGridInst.refresh();
        else grid.refresh();
      })
      .fail(function() { DevExpress.ui.notify("Gagal menghapus","error",3000); });
  });
  deleteOpen();
}

// ─── Export Excel ─────────────────────────────────────────────────────
function exportWarga() {
  $.getJSON(urlKKList, function(kkData) {
    var workbook = new ExcelJS.Workbook();

    // Sheet 1: Rekap KK
    var sheetKK = workbook.addWorksheet('Kartu Keluarga');
    sheetKK.columns = [
      { header:'No. KK', key:'no_kk', width:20 },
      { header:'Kepala Keluarga', key:'kepala_keluarga', width:25 },
      { header:'Blok/No. Rumah', key:'alamat_singkat', width:15 },
      { header:'Alamat', key:'alamat', width:35 },
      { header:'Status Hunian', key:'status_tinggal', width:15 },
      { header:'Jumlah Jiwa', key:'jumlah_jiwa', width:12 },
      { header:'Tgl. Daftar', key:'tgl_daftar', width:13 },
      { header:'Status', key:'aktif', width:10 },
    ];
    sheetKK.getRow(1).font = { bold:true, color:{ argb:'FFFFFFFF' } };
    sheetKK.getRow(1).fill = { type:'pattern', pattern:'solid', fgColor:{ argb:'FF2D6A4F' } };
    kkData.forEach(function(r, i) {
      var row = sheetKK.addRow({ ...r, aktif: r.aktif ? 'Aktif' : 'Nonaktif' });
      if (i % 2 === 0) row.fill = { type:'pattern', pattern:'solid', fgColor:{ argb:'FFF2EFE6' } };
    });

    // Sheet 2: Semua Warga
    var sheetWarga = workbook.addWorksheet('Semua Warga');
    sheetWarga.columns = [
      { header:'NIK', key:'nik', width:18 },
      { header:'Nama', key:'nama', width:25 },
      { header:'No. KK', key:'no_kk', width:20 },
      { header:'Kepala KK', key:'kepala', width:22 },
      { header:'L/P', key:'jenis_kelamin', width:6 },
      { header:'Umur', key:'umur', width:7 },
      { header:'Hubungan', key:'hubungan', width:15 },
      { header:'Agama', key:'agama', width:15 },
      { header:'Pekerjaan', key:'pekerjaan', width:20 },
      { header:'Status Tinggal', key:'status_tinggal', width:15 },
      { header:'Status Warga', key:'status_warga', width:13 },
      { header:'No. Telepon', key:'no_telepon', width:16 },
    ];
    sheetWarga.getRow(1).font = { bold:true, color:{ argb:'FFFFFFFF' } };
    sheetWarga.getRow(1).fill = { type:'pattern', pattern:'solid', fgColor:{ argb:'FF2C5C7A' } };

    var fetchAll = kkData.map(function(kk) {
      return $.getJSON(urlWargaList + '/' + kk.id).then(function(wargaList) {
        wargaList.forEach(function(w, i) {
          var row = sheetWarga.addRow({
            nik: w.nik, nama: w.nama, no_kk: kk.no_kk,
            kepala: kk.kepala_keluarga, jenis_kelamin: w.jenis_kelamin,
            umur: w.umur, hubungan: w.hubungan, agama: w.agama,
            pekerjaan: w.pekerjaan, status_tinggal: w.status_tinggal,
            status_warga: w.status_warga, no_telepon: w.no_telepon,
          });
          if (i % 2 === 0) row.fill = { type:'pattern', pattern:'solid', fgColor:{ argb:'FFE2ECF1' } };
        });
      });
    });

    $.when.apply($, fetchAll).then(function() {
      workbook.xlsx.writeBuffer().then(function(buffer) {
        var now = new Date();
        var tgl = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getDate()).padStart(2,'0');
        saveAs(new Blob([buffer], { type:'application/octet-stream' }), 'DataWarga_' + tgl + '.xlsx');
      });
    });
  });
}

// ─── Modal helpers ─────────────────────────────────────────────────────
// ─── Import Excel ─────────────────────────────────────────────────────
function importOpen() {
  $("#importResult").hide().html('');
  document.getElementById('importModal').classList.add('show');

  // Init uploader (hanya sekali)
  if (!$("#importUploader").data('dx-was-initialized')) {
    $("#importUploader").dxFileUploader({
      uploadUrl: urlImport,
      uploadHeaders: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      name: 'file',
      uploadMode: 'useButtons',
      allowedFileExtensions: ['.xlsx'],
      maxFileSize: 5242880, // 5 MB
      selectButtonText: 'Pilih File Excel',
      uploadButtonText: 'Mulai Upload',
      labelText: 'atau seret file .xlsx ke sini',
      onUploaded: function(e) {
        var res = JSON.parse(e.request.responseText);
        var html = '';
        if (res.ok) {
          html = '<div style="padding:10px 12px;background:var(--daun-pucat);border-radius:8px;font-size:13px;color:#14532D">'
            + '<strong>Berhasil!</strong> ' + res.message + '</div>';
          grid.refresh();
        } else {
          html = '<div style="padding:10px 12px;background:var(--stempel-soft);border-radius:8px;font-size:12.5px;color:#9A3422">'
            + '<strong>Gagal:</strong> ' + (res.message || 'Terjadi kesalahan') + '</div>';
          if (res.errors && res.errors.length) {
            html += '<ul style="margin-top:8px;padding-left:16px;font-size:12px;color:#9A3422">';
            res.errors.forEach(function(e) { html += '<li>' + e + '</li>'; });
            html += '</ul>';
          }
        }
        $("#importResult").html(html).show();
      },
      onUploadError: function(e) {
        var msg = 'Upload gagal.';
        try {
          var r = JSON.parse(e.request.responseText);
          if (r.message) msg = r.message;
        } catch(ex) {
          if (e.request.status === 419) msg = 'Token keamanan expired. Silakan refresh halaman lalu coba lagi.';
          else if (e.request.status === 422) msg = 'File tidak valid. Pastikan format .xlsx dan ukuran < 5 MB.';
          else msg = 'Upload gagal (HTTP ' + e.request.status + '). ' + (e.request.responseText || '');
        }
        $("#importResult").html('<div style="padding:10px 12px;background:var(--stempel-soft);border-radius:8px;font-size:12.5px;color:#9A3422">' + msg + '</div>').show();
      }
    });
  }
}

function importClose() {
  document.getElementById('importModal').classList.remove('show');
}

function kkOpen()     { document.getElementById('kkModal').classList.add('show'); }
function kkClose()    { document.getElementById('kkModal').classList.remove('show'); }
function wargaOpen()  { document.getElementById('wargaModal').classList.add('show'); }
function wargaClose() { document.getElementById('wargaModal').classList.remove('show'); }
function deleteOpen() { document.getElementById('deleteModal').classList.add('show'); }
function deleteClose(){ document.getElementById('deleteModal').classList.remove('show'); }

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') { kkClose(); wargaClose(); deleteClose(); importClose(); }
});
</script>
@endpush
