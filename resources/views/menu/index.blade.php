@extends('layouts.app')

@section('judul','Master Menu')

@section('content')

<div class="menu-toolbar">
  <h2 class="mt-title">Daftar Menu &amp; Sub-menu</h2>
  <div class="mt-actions">
    <button class="btn" type="button" onclick="menuAdd()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
      </svg>
      Tambah
    </button>
    <button class="btn btn-ubah" type="button" onclick="menuEdit()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>
      </svg>
      Ubah
    </button>
    <button class="btn btn-hapus" type="button" onclick="menuDelete()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
      </svg>
      Hapus
    </button>
  </div>
</div>

<div class="grid-wrap">
  <div id="gridMenu"></div>
</div>

<!-- Modal Form -->
<div class="modal-overlay" id="menuModal">
  <div class="modal-card mf-card">
    <div class="mf-head">
      <div class="mf-head-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="3" width="7" height="7" rx="1.5"/>
          <rect x="14" y="3" width="7" height="7" rx="1.5"/>
          <rect x="3" y="14" width="7" height="7" rx="1.5"/>
          <path d="M17 14v6M14 17h6"/>
        </svg>
      </div>
      <div>
        <h3 id="menuModalTitle">Tambah Menu</h3>
        <p id="menuModalSub" class="mf-sub">Lengkapi informasi menu baru</p>
      </div>
    </div>

    <form id="formMenu" onsubmit="return false" class="mf-body">
      <input type="hidden" id="m_id">

      <div class="ff2">
        <div class="ff" style="flex:2"><label>Nama Menu <span class="req">*</span></label><div id="m_nama"></div></div>
        <div class="ff"><label>Tipe <span class="req">*</span></label><div id="m_type"></div></div>
      </div>

      <div class="ff2">
        <div class="ff"><label>Menu Induk</label><div id="m_parent"></div></div>
        <div class="ff"><label>Urutan <span class="req">*</span></label><div id="m_urutan"></div></div>
      </div>

      <div class="ff">
        <label>Icon</label><div id="m_icon"></div>
      </div>

      <div class="mf-divider"><span>Routing</span></div>

      <div class="ff2">
        <div class="ff"><label>Controller</label><div id="m_controller"></div></div>
        <div class="ff"><label>Fungsi / Method</label><div id="m_fungsi"></div></div>
      </div>

      <div class="ff"><label>URL Manual <small class="hint">(opsional)</small></label><div id="m_url"></div></div>

      <div class="mf-switch">
        <div id="m_aktif"></div>
        <div>
          <strong>Menu Aktif</strong>
          <span>Tampilkan di sidebar navigasi</span>
        </div>
      </div>

    </form>

    <div class="mf-foot">
      <button class="mbtn ghost" type="button" onclick="menuClose()">Batal</button>
      <button class="mbtn mbtn-save" type="button" onclick="menuSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
          <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
        </svg>
        Simpan
      </button>
    </div>
  </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal-overlay" id="deleteModal" onclick="if(event.target===this)deleteClose()">
  <div class="modal-card">
    <div class="modal-ic">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
        <path d="M10 11v6"/><path d="M14 11v6"/>
        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
      </svg>
    </div>
    <h3>Hapus Menu?</h3>
    <p id="deleteModalMsg">Menu ini akan dihapus permanen.</p>
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

/* ── Toolbar ──────────────────────────────── */
.menu-toolbar {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 16px; gap: 12px; flex-wrap: wrap;
}
.mt-title {
  font-size: 20px; font-weight: 800; letter-spacing: -.02em; color: var(--tinta);
  margin: 0; line-height: 1.2; white-space: nowrap;
}
.mt-actions { display: flex; gap: 8px; align-items: center; flex-shrink: 0; flex-wrap: wrap }
.mt-actions .btn { min-width: 100px; justify-content: center }

@media (max-width: 640px) {
  .menu-toolbar { flex-direction: column; align-items: stretch; gap: 10px }
  .mt-title { font-size: 18px; white-space: normal }
  .mt-actions { display: flex; flex-wrap: wrap; gap: 8px }
  .grid-wrap { height: calc(100vh - 200px) }
}

/* ── Grid container ───────────────────────── */
.grid-wrap {
  background: var(--surface);
  border: 1px solid var(--garis);
  border-radius: var(--radius);
  overflow: auto;
  box-shadow: var(--shadow);
  height: calc(100vh - 150px);
}

/* ── Button variants ──────────────────────── */
.btn-ubah { background: var(--emas) !important; color: #fff !important; border: none !important }
.btn-ubah:hover { filter: brightness(1.08) }
.btn-hapus { background: var(--stempel) !important; color: #fff !important; border: none !important }
.btn-hapus:hover { filter: brightness(1.08) }

/* ── DevExtreme Grid ───────────────────────── */
#gridMenu, #gridMenu .dx-widget {
  font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
}
#gridMenu .dx-treelist { border: none; color: var(--tinta); border-radius: 0 }

/* Header */
#gridMenu .dx-treelist-headers {
  background: var(--kertas-2);
  border-bottom: 1px solid var(--garis);
}
#gridMenu .dx-treelist-headers .dx-header-row > td {
  font-size: 10.5px; letter-spacing: .08em; text-transform: uppercase;
  font-weight: 800; color: var(--redup);
  padding: 13px 14px; border: none;
}
#gridMenu .dx-treelist-headers .dx-header-row { border-bottom: none }

/* Rows */
#gridMenu .dx-treelist-rowsview { border-top: none }
#gridMenu .dx-data-row > td {
  padding: 10px 14px; font-size: 13.5px; color: var(--tinta);
  border-bottom: 1px solid var(--garis); border-right: none;
  vertical-align: middle;
}
#gridMenu .dx-treelist-rowsview .dx-data-row:hover > td { background: #FBFAF5 }

/* Selected row */
#gridMenu .dx-treelist-rowsview .dx-row-focused > td,
#gridMenu .dx-treelist-rowsview .dx-selection.dx-row > td {
  background: var(--daun-pucat) !important;
  color: #155234 !important;
  border-bottom-color: #cfe1d6 !important;
}
#gridMenu .dx-row-focused .dx-treelist-text-content { color: #155234 !important; font-weight: 700 }

/* Tree arrows */
#gridMenu .dx-treelist-collapsed span::before,
#gridMenu .dx-treelist-expanded span::before { color: var(--daun) }

/* Header filter icon */
#gridMenu .dx-header-filter { color: #c2ccc5 }
#gridMenu .dx-header-filter:hover { color: var(--daun) }

/* Pager */
#gridMenu .dx-pager { background: var(--kertas); border-top: 1px solid var(--garis); padding: 10px 14px }
#gridMenu .dx-pager .dx-page,
#gridMenu .dx-pager .dx-page-size { border-radius: 7px; font-size: 12.5px }
#gridMenu .dx-pager .dx-pages .dx-selection,
#gridMenu .dx-pager .dx-page-sizes .dx-selection { background: var(--hutan); color: #fff }
#gridMenu .dx-pager .dx-info { color: var(--redup); font-size: 12.5px }

/* ── Cell Styles ───────────────────────────── */

.gc-nama-lbl { font-weight: 600 }

/* Type badge */
.gc-type { display:inline-flex;align-items:center;gap:4px;font-size:10.5px;font-weight:700;padding:2px 9px;border-radius:20px;letter-spacing:.02em }
.gc-type.t-menu   { background:var(--biru-soft);color:#1a3d52 }
.gc-type.t-screen { background:var(--daun-pucat);color:#14532D }
.gc-type.t-button { background:var(--emas-soft);color:#6b4f04 }

/* Icon name chip (text only) */
.gc-icon-txt {
  display: inline-block;
  background: var(--kertas-2); border: 1px solid var(--garis);
  border-radius: 7px; padding: 2px 9px;
  font-family: 'IBM Plex Mono', monospace;
  font-size: 11.5px; font-weight: 600; color: var(--redup);
}

/* Mono text (controller / fungsi) */
.gc-mono { font-family: 'IBM Plex Mono', monospace; font-size: 12px; color: var(--redup) }


/* Status pill */
.gc-pill {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 11.5px; font-weight: 700; padding: 3px 11px; border-radius: 20px;
}
.gc-pill::before { content: ""; width: 6px; height: 6px; border-radius: 50% }
.gc-pill.on  { background: var(--daun-pucat); color: #1B5E3F }
.gc-pill.on::before  { background: #2D6A4F }
.gc-pill.off { background: var(--stempel-soft); color: #9A3422 }
.gc-pill.off::before { background: var(--stempel) }

/* Urutan badge */
.gc-urutan {
  display: inline-flex; align-items: center; justify-content: center;
  min-width: 26px; height: 26px; padding: 0 6px;
  border-radius: 7px; background: var(--kertas-2); border: 1px solid var(--garis);
  font-size: 12.5px; font-weight: 700; color: var(--redup);
}

/* ── Modal ─────────────────────────────────── */
.mf-card { max-width: 580px; width: 100%; padding: 0; text-align: left }

.mf-head {
  display: flex; align-items: center; gap: 12px;
  padding: 14px 20px; border-bottom: 1px solid var(--garis);
}
.mf-head-icon {
  flex: 0 0 34px; width: 34px; height: 34px; border-radius: 9px;
  background: var(--daun-pucat); color: var(--daun);
  display: flex; align-items: center; justify-content: center;
}
.mf-head-icon svg { width: 17px; height: 17px }
.mf-head h3 { font-size: 15px; font-weight: 800; margin: 0 0 1px }
.mf-sub { font-size: 12px; color: var(--redup); margin: 0 }

.mf-body { padding: 16px 20px; display: flex; flex-direction: column; gap: 0 }

.ff { margin-bottom: 10px; display: flex; flex-direction: column }
.ff > label { font-size: 12px; font-weight: 700; margin-bottom: 4px; color: var(--redup) }
.ff2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px }
.req { color: var(--stempel) }
.hint { font-size: 11px; color: #9aa89f; font-weight: 500 }

.mf-divider {
  display: flex; align-items: center; gap: 10px;
  margin: 6px 0 13px; font-size: 11px; font-weight: 700; color: var(--redup);
  letter-spacing: .06em; text-transform: uppercase;
}
.mf-divider::before, .mf-divider::after { content: ""; flex: 1; height: 1px; background: var(--garis) }

.mf-switch {
  display: flex; align-items: center; gap: 10px;
  padding: 8px 10px; background: var(--kertas);
  border: 1px solid var(--garis); border-radius: 9px; margin-top: 2px;
}
.mf-switch strong { display: block; font-size: 12.5px; font-weight: 700; color: var(--tinta) }
.mf-switch span { font-size: 11.5px; color: var(--redup) }


.mf-foot {
  display: flex; gap: 10px; justify-content: flex-end;
  padding: 12px 20px; border-top: 1px solid var(--garis);
  background: var(--kertas); border-radius: 0 0 14px 14px;
}
.mf-foot .mbtn { flex: 0 0 auto }
.mbtn-save {
  display: inline-flex; align-items: center; gap: 7px;
  background: var(--hutan); color: #fff; font-weight: 700;
  font-size: 14px; padding: 11px 22px; border-radius: 10px;
  border: none; cursor: pointer; transition: .14s;
}
.mbtn-save:hover { background: var(--hutan-2) }
.mbtn-save svg { width: 14px; height: 14px }

@media (max-width: 600px) {
  .mf-card { margin: 12px }
  .mf-body { padding: 14px 16px }
}
</style>
@endpush

@push('scripts')
<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

const ICONS     = ['dashboard','users','heart','bill','coins','building','shield','clipboard','card','menu','settings','truck','file'];
const urlList   = "{{ route('menu.list') }}";
const urlParent = "{{ route('menu.parent') }}";
const urlSave   = "{{ route('menu.save') }}";
const urlDelete = "{{ route('menu.delete') }}";

const IC = {
  dashboard : '<circle cx="12" cy="12" r="9"/><path d="M12 12 16 8"/><circle cx="12" cy="12" r="1.4" fill="currentColor"/>',
  users     : '<path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9.5" cy="7" r="3.5"/><path d="M21 21v-2a4 4 0 0 0-3-3.8"/>',
  heart     : '<path d="M20.8 5.6a5 5 0 0 0-7.1 0L12 7.3l-1.7-1.7a5 5 0 0 0-7.1 7.1L12 21l8.8-8.3a5 5 0 0 0 0-7.1z"/>',
  bill      : '<rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>',
  coins     : '<path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
  building  : '<path d="M3 21h18"/><path d="M5 21V9l7-5 7 5v12"/><path d="M9 21v-6h6v6"/>',
  shield    : '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
  clipboard : '<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 7h6M9 11h6M9 15h4"/>',
  card      : '<rect x="2" y="6" width="20" height="13" rx="2"/><path d="M2 10h20"/><circle cx="17" cy="14.5" r="1.4"/>',
  menu      : '<line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>',
  settings  : '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.6 1.6 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.6 1.6 0 0 0-2.7 1.1V21a2 2 0 1 1-4 0v-.1A1.6 1.6 0 0 0 6.6 19l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1A1.6 1.6 0 0 0 3 13.4H3a2 2 0 1 1 0-4h.1A1.6 1.6 0 0 0 4.6 6.6l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1A1.6 1.6 0 0 0 10 3.6V3a2 2 0 1 1 4 0v.1a1.6 1.6 0 0 0 2.7 1.1l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.6 1.6 0 0 0-.3 1.8V9a2 2 0 1 1 0 4h-.1z"/>',
  truck     : '<rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7z"/><circle cx="5.5" cy="18.5" r="2"/><circle cx="18.5" cy="18.5" r="2"/>',
  file      : '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/>',
};

function mkIc(name) {
  return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">'+(IC[name]||IC.menu)+'</svg>';
}

let grid, focusedRow = null;

$(function () {
  grid = $("#gridMenu").dxTreeList({
    dataSource: new DevExpress.data.CustomStore({
      key: "id",
      load: () => $.getJSON(urlList)
    }),
    keyExpr: "id",
    parentIdExpr: "parent_id",
    rootValue: null,
    autoExpandAll: true,
    showBorders: false,
    showColumnLines: false,
    showRowLines: true,
    rowAlternationEnabled: false,
    scrolling: { useNative: true, showScrollbar: 'onHover', mode: 'standard' },
    width: "100%",
    height: "100%",
    columnAutoWidth: false,
    wordWrapEnabled: false,
    headerFilter: { visible: true },
    paging: { pageSize: 50 },
    pager: {
      visible: true,
      displayMode: "compact",
      showPageSizeSelector: true,
      allowedPageSizes: [20, 50, "all"],
      showInfo: true,
      showNavigationButtons: true
    },
    focusedRowEnabled: true,
    onFocusedRowChanged: e => { focusedRow = e.row ? e.row.data : null; },
    columns: [
      {
        dataField: "nama", caption: "Nama Menu", minWidth: 180,
        cellTemplate: function(c, o) {
          $('<span class="gc-nama-lbl">').text(o.value).appendTo(c);
        }
      },
      {
        dataField: "type", caption: "Tipe", width: 100,
        cellTemplate: function(c, o) {
          var map = { menu:'Menu', screen:'Screen', button:'Button' };
          var cls = { menu:'t-menu', screen:'t-screen', button:'t-button' };
          var v = o.value || 'screen';
          $('<span class="gc-type '+(cls[v]||'t-screen')+'">').text(map[v]||v).appendTo(c);
        }
      },
      {
        dataField: "icon", caption: "Icon", width: 100,
        cellTemplate: function(c, o) {
          if (!o.value) { $('<span style="color:var(--garis-2)">—</span>').appendTo(c); return; }
          $('<span class="gc-icon-txt">').text(o.value).appendTo(c);
        }
      },
      {
        dataField: "controller", caption: "Controller", minWidth: 155,
        cellTemplate: function(c, o) {
          if (!o.value) { $('<span style="color:var(--garis-2)">—</span>').appendTo(c); return; }
          $('<span class="gc-mono">').text(o.value).appendTo(c);
        }
      },
      {
        dataField: "fungsi", caption: "Fungsi", width: 95,
        cellTemplate: function(c, o) {
          if (!o.value) { $('<span style="color:var(--garis-2)">—</span>').appendTo(c); return; }
          $('<span class="gc-mono">').text(o.value).appendTo(c);
        }
      },
      {
        dataField: "urutan", caption: "No.", width: 70,
        dataType: "number", sortOrder: "asc", alignment: "center",
        cellTemplate: function(c, o) {
          $('<span class="gc-urutan">').text(o.value).appendTo(c);
        }
      },
      {
        dataField: "aktif", caption: "Status", width: 110, alignment: "center",
        cellTemplate: function(c, o) {
          var on = (o.value === true || o.value === 1);
          $('<span class="gc-pill '+(on?'on':'off')+'">').text(on?'Aktif':'Nonaktif').appendTo(c);
        }
      }
    ]
  }).dxTreeList("instance");

  /* editor pada modal */
  $("#m_nama").dxTextBox({ placeholder: "cth. Data Warga" });
  $("#m_type").dxSelectBox({
    dataSource: [
      { id:'menu',   label:'Menu (Grup/Induk)' },
      { id:'screen', label:'Screen (Halaman)' },
      { id:'button', label:'Button (Aksi)' },
    ],
    valueExpr: 'id', displayExpr: 'label',
    value: 'screen',
    onValueChanged: function(e) {
      // Reload parent dropdown sesuai type yang dipilih
      var pb = $("#m_parent").dxSelectBox("instance");
      pb.option("value", null);
      pb.getDataSource().reload();
    }
  });
  $("#m_controller").dxTextBox({ placeholder: "cth. WargaController" });
  $("#m_fungsi").dxTextBox({ value: "index" });
  $("#m_url").dxTextBox({ placeholder: "cth. /warga (opsional)" });
  $("#m_urutan").dxNumberBox({ value: 1, min: 0, showSpinButtons: true });
  $("#m_icon").dxSelectBox({ dataSource: ICONS, showClearButton: true, acceptCustomValue: true, placeholder: "pilih icon" });
  $("#m_aktif").dxSwitch({ value: true });
  $("#m_parent").dxSelectBox({
    dataSource: new DevExpress.data.CustomStore({
      key: "id",
      load: () => $.getJSON(urlParent, {
        exclude:  $("#m_id").val() || 0,
        for_type: $("#m_type").dxSelectBox("instance").option("value") || 'screen'
      })
    }),
    valueExpr: "id", displayExpr: "nama",
    showClearButton: true, searchEnabled: true,
    placeholder: "— Pilih induk —"
  });
});

function menuAdd() {
  $("#m_id").val('');
  $("#menuModalTitle").text('Tambah Menu');
  $("#menuModalSub").text('Lengkapi informasi menu baru');
  $("#m_nama").dxTextBox("instance").option("value","");
  $("#m_controller").dxTextBox("instance").option("value","");
  $("#m_fungsi").dxTextBox("instance").option("value","index");
  $("#m_url").dxTextBox("instance").option("value","");
  $("#m_type").dxSelectBox("instance").option("value","screen");
  $("#m_urutan").dxNumberBox("instance").option("value",1);
  $("#m_icon").dxSelectBox("instance").option("value",null);
  $("#m_aktif").dxSwitch("instance").option("value",true);
  var pb = $("#m_parent").dxSelectBox("instance");
  pb.getDataSource().reload();
  pb.option("value",null);
  menuOpen();
}

function menuEdit() {
  if (!focusedRow) { DevExpress.ui.notify("Pilih baris menu terlebih dahulu","warning",2500); return; }
  var d = focusedRow;
  $("#m_id").val(d.id);
  $("#menuModalTitle").text('Ubah Menu');
  $("#menuModalSub").text('Mengubah: ' + d.nama);
  $("#m_nama").dxTextBox("instance").option("value", d.nama || "");
  $("#m_type").dxSelectBox("instance").option("value", d.type || "screen");
  $("#m_controller").dxTextBox("instance").option("value", d.controller || "");
  $("#m_fungsi").dxTextBox("instance").option("value", d.fungsi || "");
  $("#m_url").dxTextBox("instance").option("value", d.url || "");
  $("#m_urutan").dxNumberBox("instance").option("value", d.urutan || 1);
  $("#m_icon").dxSelectBox("instance").option("value", d.icon || null);
  $("#m_aktif").dxSwitch("instance").option("value", (d.aktif == 1 || d.aktif === true));
  var pb = $("#m_parent").dxSelectBox("instance");
  pb.getDataSource().reload().done(function(){ pb.option("value", d.parent_id || null); });
  menuOpen();
}

function menuSave() {
  var data = {
    id:         $("#m_id").val(),
    nama:       $("#m_nama").dxTextBox("instance").option("value"),
    type:       $("#m_type").dxSelectBox("instance").option("value") || 'screen',
    parent_id:  $("#m_parent").dxSelectBox("instance").option("value"),
    icon:       $("#m_icon").dxSelectBox("instance").option("value"),
    controller: $("#m_controller").dxTextBox("instance").option("value"),
    fungsi:     $("#m_fungsi").dxTextBox("instance").option("value"),
    url:        $("#m_url").dxTextBox("instance").option("value"),
    urutan:     $("#m_urutan").dxNumberBox("instance").option("value"),
    aktif:      $("#m_aktif").dxSwitch("instance").option("value") ? 1 : 0
  };
  if (!data.nama) { DevExpress.ui.notify("Nama menu wajib diisi","error",2500); return; }

  $.ajax({ url: urlSave, type: "POST", data: data })
    .done(function(res){
      DevExpress.ui.notify(res.message || "Tersimpan","success",2500);
      menuClose();
      afterChange();
    })
    .fail(function(xhr){
      var m = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Gagal menyimpan data";
      DevExpress.ui.notify(m,"error",3500);
    });
}

function menuDelete() {
  if (!focusedRow) { DevExpress.ui.notify("Pilih baris menu terlebih dahulu","warning",2500); return; }
  var nama = focusedRow.nama, id = focusedRow.id;
  var sub = focusedRow.parent_id == null ? ' Jika ini menu induk, sub-menunya ikut terhapus.' : '';
  document.getElementById('deleteModalMsg').textContent = 'Menu "' + nama + '" akan dihapus permanen.' + sub;
  document.getElementById('deleteConfirmBtn').onclick = function() {
    $.ajax({ url: urlDelete, type: "POST", data: { id: id } })
      .done(function(res){
        DevExpress.ui.notify(res.message || "Terhapus","success",2500);
        deleteClose();
        afterChange();
      })
      .fail(function(){ DevExpress.ui.notify("Gagal menghapus data","error",3000); });
  };
  deleteOpen();
}

function deleteOpen()  { document.getElementById('deleteModal').classList.add('show'); }
function deleteClose() { document.getElementById('deleteModal').classList.remove('show'); }

function menuOpen()  { document.getElementById('menuModal').classList.add('show'); }
function menuClose() { document.getElementById('menuModal').classList.remove('show'); }

function afterChange() {
  focusedRow = null;
  grid.refresh();
}

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') { menuClose(); deleteClose(); }
});
</script>
@endpush
