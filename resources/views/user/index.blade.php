@extends('layouts.app')

@section('judul', 'Master User')

@section('content')

<div class="user-toolbar">
  <h2 class="ut-title">Daftar Pengguna Sistem</h2>
  <div class="ut-actions">
    <button class="btn" id="btnTambah" type="button" onclick="userAdd()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
      </svg>
      Tambah
    </button>
    <button class="btn btn-ubah" id="btnUbah" type="button" onclick="userEdit()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>
      </svg>
      Ubah
    </button>
    <button class="btn btn-hapus" id="btnHapus" type="button" onclick="userDelete()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
      </svg>
      Hapus
    </button>
    <button class="btn btn-unduh" id="btnUnduh" type="button" onclick="exportUser()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="7 10 12 15 17 10"/>
        <line x1="12" y1="15" x2="12" y2="3"/>
      </svg>
      Unduh
    </button>
  </div>
</div>

<div class="grid-wrap">
  <div id="gridUser"></div>
</div>

<!-- Modal Form -->
<div class="modal-overlay" id="userModal">
  <div class="modal-card uf-card">

    <div class="uf-head">
      <div class="uf-head-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
        </svg>
      </div>
      <div>
        <h3 id="userModalTitle">Tambah User</h3>
        <p id="userModalSub" class="uf-sub">Lengkapi informasi pengguna baru</p>
      </div>
    </div>

    <form id="formUser" onsubmit="return false" class="uf-body">
      <input type="hidden" id="u_id">

      <div class="ff"><label>Nama Lengkap <span class="req">*</span></label><div id="u_name"></div></div>

      <div class="ff2">
        <div class="ff"><label>Username <span class="req">*</span></label><div id="u_username"></div></div>
        <div class="ff"><label>Email <span class="req">*</span></label><div id="u_email"></div></div>
      </div>

      <div class="ff2">
        <div class="ff">
          <label>Password <span id="u_pass_hint" class="hint">(wajib diisi)</span></label>
          <div id="u_password"></div>
        </div>
        <div class="ff"><label>Role</label><div id="u_role"></div></div>
      </div>

      <div class="ff">
        <label>Terhubung ke Warga <small class="hint">(opsional)</small></label>
        <div id="u_warga"></div>
      </div>

      <div class="uf-switch">
        <div id="u_status_switch"></div>
        <div>
          <strong>Akun Aktif</strong>
          <span>User bisa login ke sistem</span>
        </div>
      </div>
    </form>

    <div class="uf-foot">
      <button class="mbtn ghost" type="button" onclick="userClose()">Batal</button>
      <button class="mbtn mbtn-save" type="button" onclick="userSave()">
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
<div class="modal-overlay" id="deleteUserModal" onclick="if(event.target===this)deleteUserClose()">
  <div class="modal-card">
    <div class="modal-ic">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="3 6 5 6 21 6"/>
        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
        <path d="M10 11v6"/><path d="M14 11v6"/>
        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
      </svg>
    </div>
    <h3>Hapus User?</h3>
    <p id="deleteUserMsg">User ini akan dihapus permanen.</p>
    <div class="modal-actions">
      <button class="mbtn ghost" type="button" onclick="deleteUserClose()">Batal</button>
      <button class="mbtn danger" type="button" id="deleteUserConfirmBtn">Ya, Hapus</button>
    </div>
  </div>
</div>

@endsection

@push('styles')
<style>
.content { max-width: none; padding-bottom: 0 }

/* ── Toolbar ──────────────────────────────── */
.user-toolbar {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 18px; gap: 16px;
}
.ut-title { font-size: 24px; font-weight: 800; letter-spacing: -.02em; color: var(--tinta); margin: 0 }
.ut-actions { display: flex; gap: 8px; align-items: center; flex-shrink: 0 }

/* ── Grid container ───────────────────────── */
.grid-wrap {
  background: var(--surface); border: 1px solid var(--garis);
  border-radius: var(--radius); overflow: auto; box-shadow: var(--shadow);
  height: calc(100vh - 162px);
}

/* ── Button variants ──────────────────────── */
.btn-ubah  { background: var(--emas) !important; color: #fff !important }
.btn-ubah:hover  { filter: brightness(1.08) }
.btn-hapus { background: var(--stempel) !important; color: #fff !important }
.btn-hapus:hover { filter: brightness(1.08) }
.btn-unduh { background: var(--biru) !important; color: #fff !important }
.btn-unduh:hover { filter: brightness(1.08) }
.btn[disabled] { opacity: .45; cursor: not-allowed; pointer-events: none }

/* Sembunyikan toolbar bawaan DevExtreme (export button sudah ada di toolbar atas) */
#gridUser .dx-toolbar { display: none }

/* ── DevExtreme Grid ───────────────────────── */
#gridUser, #gridUser .dx-widget {
  font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
}
#gridUser .dx-datagrid { border: none; color: var(--tinta) }
#gridUser .dx-datagrid-headers { background: var(--kertas-2); border-bottom: 1px solid var(--garis) }
#gridUser .dx-datagrid-headers .dx-header-row > td {
  font-size: 10.5px; letter-spacing: .08em; text-transform: uppercase;
  font-weight: 800; color: var(--redup); padding: 13px 14px; border: none;
}
#gridUser .dx-datagrid-rowsview { border-top: none }
#gridUser .dx-data-row > td {
  padding: 11px 14px; font-size: 13.5px; color: var(--tinta);
  border-bottom: 1px solid var(--garis); border-right: none; vertical-align: middle;
}
#gridUser .dx-datagrid-rowsview .dx-data-row:hover > td { background: #FBFAF5 }
#gridUser .dx-datagrid-rowsview .dx-row-focused > td,
#gridUser .dx-datagrid-rowsview .dx-selection.dx-row > td {
  background: var(--daun-pucat) !important; color: #155234 !important;
  border-bottom-color: #cfe1d6 !important;
}
#gridUser .dx-pager { background: var(--kertas); border-top: 1px solid var(--garis); padding: 10px 14px }
#gridUser .dx-pager .dx-page, #gridUser .dx-pager .dx-page-size { border-radius: 7px; font-size: 12.5px }
#gridUser .dx-pager .dx-pages .dx-selection,
#gridUser .dx-pager .dx-page-sizes .dx-selection { background: var(--hutan); color: #fff }
#gridUser .dx-pager .dx-info { color: var(--redup); font-size: 12.5px }

/* ── Cell Styles ───────────────────────────── */
.gc-nama { font-weight: 600 }
.gc-user { display: flex; align-items: center; gap: 9px }
.gc-av {
  width: 30px; height: 30px; flex: 0 0 30px; border-radius: 9px;
  background: var(--daun-pucat); color: var(--hutan);
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 800;
}
.gc-mono { font-family: 'IBM Plex Mono', monospace; font-size: 12px; color: var(--redup) }
.gc-role {
  display: inline-flex; align-items: center;
  font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; letter-spacing: .02em;
}
.gc-role.ra { background: #e6f0e9; color: #14532D }
.gc-role.rk { background: var(--biru-soft); color: #1a3d52 }
.gc-role.rs { background: var(--emas-soft); color: #6b4f04 }
.gc-role.rb { background: var(--stempel-soft); color: #7a2818 }
.gc-role.rw { background: var(--kertas-2); color: var(--redup); border: 1px solid var(--garis) }

.gc-warga { font-size: 12.5px; color: var(--redup) }
.gc-pill {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 11.5px; font-weight: 700; padding: 3px 11px; border-radius: 20px;
}
.gc-pill::before { content: ""; width: 6px; height: 6px; border-radius: 50% }
.gc-pill.on  { background: var(--daun-pucat); color: #1B5E3F }
.gc-pill.on::before  { background: #2D6A4F }
.gc-pill.off { background: var(--stempel-soft); color: #9A3422 }
.gc-pill.off::before { background: var(--stempel) }

/* ── Modal ─────────────────────────────────── */
.uf-card { max-width: 580px; width: 100%; padding: 0; text-align: left }

.uf-head {
  display: flex; align-items: center; gap: 12px;
  padding: 14px 20px; border-bottom: 1px solid var(--garis);
}
.uf-head-icon {
  flex: 0 0 34px; width: 34px; height: 34px; border-radius: 9px;
  background: var(--daun-pucat); color: var(--daun);
  display: flex; align-items: center; justify-content: center;
}
.uf-head-icon svg { width: 17px; height: 17px }
.uf-head h3 { font-size: 15px; font-weight: 800; margin: 0 0 1px }
.uf-sub { font-size: 12px; color: var(--redup); margin: 0 }

.uf-body { padding: 18px 20px; display: flex; flex-direction: column; gap: 0 }

.ff { margin-bottom: 13px; display: flex; flex-direction: column }
.ff > label { font-size: 12px; font-weight: 700; margin-bottom: 5px; color: var(--redup) }
.ff2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px }
.req { color: var(--stempel) }
.hint { font-size: 11px; color: #9aa89f; font-weight: 500 }

.uf-switch {
  display: flex; align-items: center; gap: 10px;
  padding: 8px 10px; background: var(--kertas);
  border: 1px solid var(--garis); border-radius: 9px; margin-top: 2px;
}
.uf-switch strong { display: block; font-size: 12.5px; font-weight: 700; color: var(--tinta) }
.uf-switch span { font-size: 11.5px; color: var(--redup) }

.uf-foot {
  display: flex; gap: 10px; justify-content: flex-end;
  padding: 12px 20px; border-top: 1px solid var(--garis);
  background: var(--kertas); border-radius: 0 0 20px 20px;
}
.uf-foot .mbtn { flex: 0 0 auto }

.mbtn-save {
  display: inline-flex; align-items: center; gap: 7px;
  background: var(--hutan); color: #fff; font-weight: 700;
  font-size: 14px; padding: 11px 22px; border-radius: 10px;
  border: none; cursor: pointer; transition: .14s;
}
.mbtn-save:hover { background: var(--hutan-2) }
.mbtn-save svg { width: 14px; height: 14px }

@media (max-width: 600px) {
  .ff2 { grid-template-columns: 1fr }
  .uf-card { margin: 12px }
}
@media(max-width:640px){
  .user-toolbar{flex-direction:column;align-items:stretch;gap:10px}
  .ut-title{font-size:18px;white-space:normal}
  .ut-actions{display:flex;flex-wrap:wrap;gap:8px}
  .grid-wrap{height:calc(100vh - 210px)}
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/devextreme/exceljs.min.js') }}"></script>
<script src="{{ asset('js/devextreme/FileSaver.min.js') }}"></script>
<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

const urlList       = "{{ route('user.list') }}";
const urlSave       = "{{ route('user.save') }}";
const urlDelete     = "{{ route('user.delete') }}";
const urlRoleLookup = "{{ route('user.role-lookup') }}";
const urlWargaLookup= "{{ route('user.warga-lookup') }}";

const ROLE_CLS = { admin:'ra', ketua:'rk', sekretaris:'rs', bendahara:'rb', warga:'rw' };

function initials(name) {
  return (name || '').split(' ').map(w => w[0]).slice(0,2).join('').toUpperCase();
}

let grid, focusedRow = null;
let rolesData = [];

$(function () {
  // Terapkan izin aksi dari layout
  var p = window.__perms || {};
  if (!p.add)    document.getElementById('btnTambah').disabled = true;
  if (!p.edit)   document.getElementById('btnUbah').disabled   = true;
  if (!p.delete) document.getElementById('btnHapus').disabled  = true;
  if (!p.export) document.getElementById('btnUnduh').disabled  = true;

  grid = $("#gridUser").dxDataGrid({
    dataSource: new DevExpress.data.CustomStore({
      key: "id",
      load: () => $.getJSON(urlList)
    }),
    showBorders: false,
    showColumnLines: false,
    showRowLines: true,
    rowAlternationEnabled: false,
    width: "100%",
    height: "100%",
    columnAutoWidth: false,
    scrolling: { useNative: true, showScrollbar: 'always', mode: 'standard' },
    wordWrapEnabled: false,
    headerFilter: { visible: true },
    paging: { pageSize: 50 },
    pager: {
      visible: true, displayMode: "compact",
      showPageSizeSelector: true, allowedPageSizes: [20, 50, "all"],
      showInfo: true, showNavigationButtons: true
    },
    focusedRowEnabled: true,
    onFocusedRowChanged: e => { focusedRow = e.row ? e.row.data : null; },
    columns: [
      {
        dataField: "name", caption: "Nama", minWidth: 180,
        cellTemplate: function(c, o) {
          var wrap = $('<div class="gc-user">');
          $('<div class="gc-av">').text(initials(o.value)).appendTo(wrap);
          var info = $('<div>');
          $('<div class="gc-nama">').text(o.value).appendTo(info);
          if (o.row.data.email) {
            $('<div class="gc-mono">').text(o.row.data.email).appendTo(info);
          }
          info.appendTo(wrap);
          wrap.appendTo(c);
        }
      },
      {
        dataField: "username", caption: "Username", width: 140,
        cellTemplate: function(c, o) {
          if (!o.value) { $('<span style="color:var(--garis-2)">—</span>').appendTo(c); return; }
          $('<span class="gc-mono">').text('@' + o.value).appendTo(c);
        }
      },
      {
        dataField: "role_label", caption: "Role", width: 130,
        cellTemplate: function(c, o) {
          if (!o.value) { $('<span style="color:var(--garis-2)">—</span>').appendTo(c); return; }
          var cls = ROLE_CLS[o.row.data.role_nama] || 'rw';
          $('<span class="gc-role ' + cls + '">').text(o.value).appendTo(c);
        }
      },
      {
        dataField: "warga_nama", caption: "Terhubung Warga", minWidth: 160,
        cellTemplate: function(c, o) {
          if (!o.value) { $('<span style="color:var(--garis-2)">—</span>').appendTo(c); return; }
          $('<span class="gc-warga">').text(o.value).appendTo(c);
        }
      },
      {
        dataField: "status", caption: "Status", width: 110, alignment: "center",
        cellTemplate: function(c, o) {
          var on = (o.value === 'aktif');
          $('<span class="gc-pill ' + (on ? 'on' : 'off') + '">').text(on ? 'Aktif' : 'Nonaktif').appendTo(c);
        }
      },
      { dataField: "created_at", caption: "Dibuat", width: 110, alignment: "center" }
    ]
  }).dxDataGrid("instance");

  /* Editor pada modal */
  $("#u_name").dxTextBox({ placeholder: "cth. Budi Santoso" });
  $("#u_username").dxTextBox({ placeholder: "cth. budi.santoso" });
  $("#u_email").dxTextBox({ placeholder: "cth. budi@email.com", mode: "email" });
  $("#u_password").dxTextBox({ placeholder: "Minimal 6 karakter", mode: "password", showClearButton: true });
  $("#u_status_switch").dxSwitch({ value: true });

  // Roles dimuat sekali ke array agar set value langsung bisa bekerja
  $.getJSON(urlRoleLookup, function(data) {
    rolesData = data;
    $("#u_role").dxSelectBox("instance").option("dataSource", data);
  });

  $("#u_role").dxSelectBox({
    dataSource: [],
    valueExpr: "id", displayExpr: "label",
    showClearButton: true, placeholder: "— Pilih role —"
  });

  $("#u_warga").dxSelectBox({
    dataSource: new DevExpress.data.CustomStore({
      key: "id",
      load: (opts) => $.getJSON(urlWargaLookup, {
        q: opts.searchValue || '',
        current: $("#u_id").data('warga') || ''
      })
    }),
    valueExpr: "id",
    displayExpr: function(item) { return item ? item.nama + (item.nik ? ' — ' + item.nik : '') : ''; },
    searchEnabled: true, minSearchLength: 0,
    showClearButton: true, placeholder: "— Tidak dihubungkan —"
  });
});

function exportUser() {
  var workbook = new ExcelJS.Workbook();
  var sheet    = workbook.addWorksheet('Master User');

  DevExpress.excelExporter.exportDataGrid({
    component: grid,
    worksheet: sheet,
    autoFilterEnabled: true,
    customizeCell: function(opt) {
      if (opt.gridCell.rowType === 'header') {
        opt.excelCell.font      = { bold: true, color: { argb: 'FFFFFFFF' } };
        opt.excelCell.fill      = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2D6A4F' } };
        opt.excelCell.alignment = { horizontal: 'center' };
      }
      if (opt.gridCell.rowType === 'data' && opt.gridCell.rowIndex % 2 === 0) {
        opt.excelCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFF2EFE6' } };
      }
    }
  }).then(function() {
    workbook.xlsx.writeBuffer().then(function(buffer) {
      var now = new Date();
      var tgl = now.getFullYear() + '-'
        + String(now.getMonth() + 1).padStart(2, '0') + '-'
        + String(now.getDate()).padStart(2, '0');
      saveAs(new Blob([buffer], { type: 'application/octet-stream' }), 'MasterUser_' + tgl + '.xlsx');
    });
  });
}

function userAdd() {
  $("#u_id").val('').data('warga', '');
  $("#userModalTitle").text('Tambah User');
  $("#userModalSub").text('Lengkapi informasi pengguna baru');
  $("#u_pass_hint").text('(wajib diisi)').css('color', 'var(--stempel)');

  $("#u_name").dxTextBox("instance").option("value", "");
  $("#u_username").dxTextBox("instance").option("value", "");
  $("#u_email").dxTextBox("instance").option("value", "");
  $("#u_password").dxTextBox("instance").option("value", "");
  $("#u_status_switch").dxSwitch("instance").option("value", true);
  reloadRoleLookup(null);
  reloadWargaLookup(null);
  userOpen();
}

function userEdit() {
  if (!focusedRow) { DevExpress.ui.notify("Pilih baris user terlebih dahulu", "warning", 2500); return; }
  var d = focusedRow;
  $("#u_id").val(d.id).data('warga', d.warga_id || '');
  $("#userModalTitle").text('Ubah User');
  $("#userModalSub").text('Mengubah: ' + d.name);
  $("#u_pass_hint").text('(kosongkan jika tidak diubah)').css('color', '#9aa89f');

  $("#u_name").dxTextBox("instance").option("value", d.name || "");
  $("#u_username").dxTextBox("instance").option("value", d.username || "");
  $("#u_email").dxTextBox("instance").option("value", d.email || "");
  $("#u_password").dxTextBox("instance").option("value", "");
  $("#u_status_switch").dxSwitch("instance").option("value", d.status === 'aktif');
  reloadRoleLookup(d.role_id || null);
  reloadWargaLookup(d.warga_id || null);
  userOpen();
}

function reloadRoleLookup(roleId) {
  var rb = $("#u_role").dxSelectBox("instance");
  if (rolesData.length) {
    rb.option("dataSource", rolesData);
    rb.option("value", roleId);
  } else {
    $.getJSON(urlRoleLookup, function(data) {
      rolesData = data;
      rb.option("dataSource", data);
      rb.option("value", roleId);
    });
  }
}

function reloadWargaLookup(currentId) {
  var wb = $("#u_warga").dxSelectBox("instance");
  wb.getDataSource().reload().done(function() { wb.option("value", currentId); });
}

function userSave() {
  var data = {
    id:       $("#u_id").val(),
    name:     $("#u_name").dxTextBox("instance").option("value"),
    username: $("#u_username").dxTextBox("instance").option("value"),
    email:    $("#u_email").dxTextBox("instance").option("value"),
    password: $("#u_password").dxTextBox("instance").option("value"),
    role_id:  $("#u_role").dxSelectBox("instance").option("value"),
    warga_id: $("#u_warga").dxSelectBox("instance").option("value"),
    status:   $("#u_status_switch").dxSwitch("instance").option("value") ? 'aktif' : 'nonaktif',
  };

  if (!data.name)     { DevExpress.ui.notify("Nama lengkap wajib diisi", "error", 2500); return; }
  if (!data.username) { DevExpress.ui.notify("Username wajib diisi", "error", 2500); return; }
  if (!data.email)    { DevExpress.ui.notify("Email wajib diisi", "error", 2500); return; }
  if (!data.id && !data.password) { DevExpress.ui.notify("Password wajib diisi untuk user baru", "error", 2500); return; }

  $.ajax({ url: urlSave, type: "POST", data: data })
    .done(function(res) {
      DevExpress.ui.notify(res.message || "Tersimpan", "success", 2500);
      userClose();
      grid.refresh();
    })
    .fail(function(xhr) {
      var m = xhr.responseJSON?.message || xhr.responseJSON?.errors
        ? Object.values(xhr.responseJSON.errors || {}).flat().join(' ')
        : "Gagal menyimpan data";
      DevExpress.ui.notify(m, "error", 3500);
    });
}

function userDelete() {
  if (!focusedRow) { DevExpress.ui.notify("Pilih baris user terlebih dahulu", "warning", 2500); return; }
  var d = focusedRow;
  document.getElementById('deleteUserMsg').textContent = 'User "' + d.name + '" akan dihapus permanen.';
  document.getElementById('deleteUserConfirmBtn').onclick = function() {
    $.ajax({ url: urlDelete, type: "POST", data: { id: d.id } })
      .done(function(res) {
        DevExpress.ui.notify(res.message || "Terhapus", "success", 2500);
        deleteUserClose();
        focusedRow = null;
        grid.refresh();
      })
      .fail(function(xhr) {
        var m = xhr.responseJSON?.message || "Gagal menghapus data";
        DevExpress.ui.notify(m, "error", 3000);
      });
  };
  deleteUserOpen();
}

function userOpen()        { document.getElementById('userModal').classList.add('show'); }
function userClose()       { document.getElementById('userModal').classList.remove('show'); }
function deleteUserOpen()  { document.getElementById('deleteUserModal').classList.add('show'); }
function deleteUserClose() { document.getElementById('deleteUserModal').classList.remove('show'); }

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') { userClose(); deleteUserClose(); }
});
</script>
@endpush
