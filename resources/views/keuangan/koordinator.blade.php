@extends('layouts.app')
@section('judul','Master Koordinator Gang')
@section('content')

<div class="keu-toolbar">
  <h2 class="keu-title">Master Koordinator Gang</h2>
  <div class="keu-actions">
    <button class="btn" id="btnTambah" type="button" onclick="koordinatorAdd()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Tambah Koordinator
    </button>
    <button class="btn btn-ubah" id="btnUbah" type="button" onclick="koordinatorEdit()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
      Ubah
    </button>
    <button class="btn btn-hapus" id="btnHapus" type="button" onclick="koordinatorDelete()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
      Hapus
    </button>
  </div>
</div>

<div class="info-box" style="margin-bottom:14px">
  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  <span>Hanya warga dengan status <strong>Tetap</strong> dan <strong>Aktif</strong> yang bisa didaftarkan sebagai koordinator gang.</span>
</div>

<div class="grid-wrap" style="height:calc(100vh - 190px)"><div id="gridKoordinator"></div></div>

{{-- Modal Tambah Anggota --}}
<div class="modal-overlay" id="anggotaModal" onclick="if(event.target===this)this.classList.remove('show')">
  <div class="modal-card keu-card">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:var(--biru-soft);color:var(--biru)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
      </div>
      <div>
        <h3>Tambah Anggota</h3>
        <p class="keu-sub" id="anggotaModalSub">—</p>
      </div>
    </div>
    <div class="keu-body">
      <div class="ff">
        <label>Pilih Warga <span class="req">*</span></label>
        <div id="anggota_kk"></div>
        <div class="fhint">Pilih berdasarkan nama kepala keluarga, blok, atau No. KK. Bisa pilih lebih dari satu.</div>
      </div>
    </div>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="document.getElementById('anggotaModal').classList.remove('show')">Batal</button>
      <button class="mbtn mbtn-save" onclick="simpanAnggota()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg> Simpan
      </button>
    </div>
  </div>
</div>

{{-- Modal Koordinator --}}
<div class="modal-overlay" id="koordinatorModal">
  <div class="modal-card keu-card">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:var(--daun-pucat);color:var(--daun)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div>
        <h3 id="koordinatorModalTitle">Tambah Koordinator</h3>
        <p class="keu-sub">Warga tetap yang bertugas mengkoordinasi gang</p>
      </div>
    </div>
    <form onsubmit="return false" class="keu-body">
      <input type="hidden" id="k_id">
      <input type="hidden" id="k_warga_id_current">

      <div class="ff">
        <label>Warga <span class="req">*</span></label>
        <div id="k_warga"></div>
        <div class="fhint">Hanya menampilkan warga tetap & aktif yang belum menjadi koordinator.</div>
      </div>

      <div class="ff">
        <label>Gang yang Dikoordinir</label>
        <div id="k_gang"></div>
        <div class="fhint">Pilih gang yang menjadi tanggung jawab koordinator ini.</div>
      </div>
      <div class="ff"><label>Keterangan</label><div id="k_keterangan"></div></div>

      <div class="mf-switch">
        <div id="k_aktif"></div>
        <div><strong>Koordinator Aktif</strong><span>Tampil sebagai pilihan di Master Gang</span></div>
      </div>
    </form>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="koordinatorClose()">Batal</button>
      <button class="mbtn mbtn-save" onclick="koordinatorSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg> Simpan
      </button>
    </div>
  </div>
</div>

{{-- Modal Konfirmasi --}}
<div class="modal-overlay" id="deleteModal" onclick="if(event.target===this)deleteClose()">
  <div class="modal-card">
    <div class="modal-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></div>
    <h3>Hapus Koordinator?</h3><p id="deleteMsg"></p>
    <div class="modal-actions">
      <button class="mbtn ghost" onclick="deleteClose()">Batal</button>
      <button class="mbtn danger" id="deleteConfirmBtn">Ya, Hapus</button>
    </div>
  </div>
</div>

@endsection

@push('styles')
<style>
.content{max-width:none;padding-bottom:0}
.keu-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;gap:16px}
.keu-title{font-size:22px;font-weight:800;letter-spacing:-.02em;color:var(--tinta);margin:0}
.keu-actions{display:flex;gap:8px}
.btn-ubah{background:var(--emas)!important;color:#fff!important}.btn-ubah:hover{filter:brightness(1.08)}
.btn-hapus{background:var(--stempel)!important;color:#fff!important}.btn-hapus:hover{filter:brightness(1.08)}
.btn[disabled]{opacity:.45;cursor:not-allowed;pointer-events:none}
.info-box{display:flex;align-items:center;gap:9px;padding:10px 13px;background:var(--biru-soft);border-radius:9px;font-size:12.5px;color:#1a3d52}
.info-box svg{flex:0 0 15px}
.grid-wrap{background:var(--surface);border:1px solid var(--garis);border-radius:var(--radius);overflow:auto;box-shadow:var(--shadow)}
#gridKoordinator,.dx-widget{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
#gridKoordinator .dx-datagrid{border:none;color:var(--tinta)}
#gridKoordinator .dx-datagrid-headers{background:var(--kertas-2);border-bottom:1px solid var(--garis)}
#gridKoordinator .dx-datagrid-headers .dx-header-row>td{font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:12px 14px;border:none}
#gridKoordinator .dx-data-row>td{padding:11px 14px;font-size:13px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis);vertical-align:middle}
#gridKoordinator .dx-datagrid-rowsview .dx-data-row:hover>td{background:#FBFAF5}
#gridKoordinator .dx-datagrid-rowsview .dx-row-focused>td{background:var(--daun-pucat)!important;color:#155234!important}
#gridKoordinator .dx-pager{background:var(--kertas);border-top:1px solid var(--garis);padding:8px 14px}
#gridKoordinator .dx-toolbar{display:none}
.gc-pill{display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:700;padding:3px 11px;border-radius:20px}
.gc-pill.on{background:var(--daun-pucat);color:#1B5E3F}
.gc-pill.off{background:var(--stempel-soft);color:#9A3422}
.gc-nik{font-family:'IBM Plex Mono',monospace;font-size:11.5px;color:var(--redup)}
/* Sub-grid anggota */
.dx-master-detail-cell{padding:0!important;background:var(--kertas)!important}
.anggota-panel{padding:14px 20px}
.anggota-panel-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.anggota-panel-head h4{font-size:13px;font-weight:700;color:var(--tinta)}
.anggota-grid .dx-datagrid{border:1px solid var(--garis);border-radius:10px}
.anggota-grid .dx-datagrid-headers .dx-header-row>td{font-size:10px;padding:8px 12px;background:var(--kertas-2)}
.anggota-grid .dx-data-row>td{padding:7px 12px;font-size:12.5px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis)}
.btn-add-anggota{font-size:12px;padding:5px 11px;border-radius:7px;border:1.5px solid var(--daun);color:var(--daun);background:transparent;cursor:pointer;font-weight:700;transition:.13s}
.btn-add-anggota:hover{background:var(--daun-pucat)}
.wg-ic-btn{width:26px;height:26px;border-radius:6px;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:.13s}
.wg-ic-btn svg{width:12px;height:12px}
.wg-ic-del{background:var(--stempel-soft);color:var(--stempel)}.wg-ic-del:hover{background:var(--stempel);color:#fff}

.keu-card{max-width:480px;width:100%;padding:0;text-align:left}
.keu-head{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--garis)}
.keu-head-icon{flex:0 0 34px;width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center}
.keu-head-icon svg{width:17px;height:17px}
.keu-head h3{font-size:15px;font-weight:800;margin:0 0 1px}
.keu-sub{font-size:12px;color:var(--redup);margin:0}
.keu-body{padding:18px 20px;display:flex;flex-direction:column;gap:0}
.ff{margin-bottom:13px;display:flex;flex-direction:column}
.ff>label{font-size:12px;font-weight:700;margin-bottom:5px;color:var(--redup)}
.fhint{font-size:11.5px;color:var(--redup);margin-top:4px}
.req{color:var(--stempel)}
.mf-switch{display:flex;align-items:center;gap:10px;padding:8px 10px;background:var(--kertas);border:1px solid var(--garis);border-radius:9px;margin-top:2px}
.mf-switch strong{display:block;font-size:12.5px;font-weight:700;color:var(--tinta)}
.mf-switch span{font-size:11.5px;color:var(--redup)}
.keu-foot{display:flex;gap:10px;justify-content:flex-end;padding:12px 20px;border-top:1px solid var(--garis);background:var(--kertas);border-radius:0 0 14px 14px}
.keu-foot .mbtn{flex:0 0 auto}
.mbtn-save{display:inline-flex;align-items:center;gap:7px;background:var(--hutan);color:#fff;font-weight:700;font-size:14px;padding:10px 20px;border-radius:10px;border:none;cursor:pointer;transition:.14s}
.mbtn-save:hover{background:var(--hutan-2)}.mbtn-save svg{width:14px;height:14px}
@media(max-width:640px){
  .keu-toolbar{flex-direction:column;align-items:stretch;gap:10px}
  .keu-title{font-size:18px;white-space:normal}
  .keu-actions{display:flex;flex-wrap:wrap;gap:8px}
  .grid-wrap{height:calc(100vh - 210px)}
}
</style>
@endpush

@push('scripts')
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});

const urlList             = "{{ route('koordinator.list') }}";
const urlSave             = "{{ route('koordinator.save') }}";
const urlDelete           = "{{ route('koordinator.delete') }}";
const urlWargaLookup      = "{{ route('koordinator.warga-lookup') }}";
const urlGangLookup       = "{{ route('gang.lookup') }}";
const urlAnggotaList    = "{{ url('koordinator') }}";   // + /{id}/anggota
const urlAnggotaAdd     = "{{ route('koordinator.anggota.add') }}";
const urlAnggotaRemove  = "{{ route('koordinator.anggota.remove') }}";
const urlAnggotaKkLookup = "{{ url('koordinator') }}"; // + /{id}/anggota-lookup

let grid, focusedRow = null;

$(function(){
  var p = window.__perms || {};
  if (!p.add)    document.getElementById('btnTambah').disabled = true;
  if (!p.edit)   document.getElementById('btnUbah').disabled   = true;
  if (!p.delete) document.getElementById('btnHapus').disabled  = true;

  grid = $("#gridKoordinator").dxDataGrid({
    dataSource: new DevExpress.data.CustomStore({key:"id", load:()=>$.getJSON(urlList)}),
    showBorders:false, showColumnLines:true, showRowLines:true,
    rowAlternationEnabled:true, width:"100%", height:"100%",
    columnAutoWidth:false,
    scrolling: { useNative: true, showScrollbar: 'always', mode: 'standard' },
    focusedRowEnabled:true,
    onFocusedRowChanged: e => { focusedRow = e.row ? e.row.data : null; },
    paging:{pageSize:50},
    pager:{visible:true,displayMode:"compact",showPageSizeSelector:true,allowedPageSizes:[25,50,"all"],showInfo:true},
    masterDetail:{
      enabled:true,
      template:function(container, info){ buildAnggotaPanel(container, info.data); }
    },
    columns:[
      {caption:"No.",width:52,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"nama",caption:"Nama Koordinator",minWidth:180,
        cellTemplate:function(c,o){
          var wrap=$('<div>');
          $('<div style="font-weight:600">').text(o.value||'—').appendTo(wrap);
          $('<div class="gc-nik">').text(o.row.data.nik||'').appendTo(wrap);
          wrap.appendTo(c);
        }},
      {dataField:"no_telepon",caption:"No. Telepon",width:140,
        cellTemplate:function(c,o){
          $('<span style="font-family:\'IBM Plex Mono\',monospace;font-size:12.5px">').text(o.value||'—').appendTo(c);
        }},
      {dataField:"blok_no",caption:"Blok/Rumah",width:110,
        cellTemplate:function(c,o){
          if(!o.value){$('<span style="color:var(--garis-2)">—</span>').appendTo(c);return;}
          $('<span style="display:inline-flex;align-items:center;font-size:12px;font-weight:700;padding:2px 10px;border-radius:20px;background:var(--biru-soft);color:#1a3d52">').text(o.value).appendTo(c);
        }},
      {dataField:"nama_gang",caption:"Gang yang Dikoordinir",minWidth:150,
        cellTemplate:function(c,o){
          if(!o.value){$('<span style="color:var(--garis-2)">Belum ditugaskan</span>').appendTo(c);return;}
          $('<span style="font-weight:600;color:var(--hutan)">').text(o.value).appendTo(c);
        }},
      {dataField:"keterangan",caption:"Keterangan",minWidth:150,
        cellTemplate:function(c,o){$('<span style="font-size:12.5px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
      {dataField:"aktif",caption:"Status",width:100,alignment:"center",
        cellTemplate:function(c,o){
          var on=o.value===true||o.value===1;
          $('<span class="gc-pill '+(on?'on':'off')+'">').text(on?'Aktif':'Nonaktif').appendTo(c);
        }},
    ]
  }).dxDataGrid("instance");

  initEditors();
});

function initEditors(){
  $("#k_keterangan").dxTextBox({placeholder:"Catatan tambahan..."});
  $("#k_aktif").dxSwitch({value:true});
  $("#k_gang").dxSelectBox({
    dataSource: new DevExpress.data.CustomStore({key:"id", load:()=>$.getJSON(urlGangLookup)}),
    valueExpr:"id", displayExpr:"nama_gang",
    showClearButton:true, placeholder:"— Pilih gang (opsional) —"
  });
  $("#k_warga").dxSelectBox({
    dataSource: new DevExpress.data.CustomStore({
      key:"id",
      load:(opts)=>$.getJSON(urlWargaLookup,{q:opts.searchValue||'',current:$("#k_warga_id_current").val()||''})
    }),
    valueExpr:"id", displayExpr:"label",
    searchEnabled:true, minSearchLength:0,
    showClearButton:false, placeholder:"— Cari nama atau NIK warga —"
  });
}

function koordinatorAdd(){
  $("#k_id").val('');
  $("#k_warga_id_current").val('');
  document.getElementById('koordinatorModalTitle').textContent = 'Tambah Koordinator';
  $("#k_warga").dxSelectBox("instance").option("value",null);
  $("#k_warga").dxSelectBox("instance").getDataSource().reload();
  $("#k_gang").dxSelectBox("instance").option("value",null);
  $("#k_keterangan").dxTextBox("instance").option("value","");
  $("#k_aktif").dxSwitch("instance").option("value",true);
  document.getElementById('koordinatorModal').classList.add('show');
}

function koordinatorEdit(){
  if(!focusedRow){DevExpress.ui.notify("Pilih baris terlebih dahulu","warning",2500);return;}
  var d = focusedRow;
  $("#k_id").val(d.id);
  $("#k_warga_id_current").val(d.warga_id||'');
  document.getElementById('koordinatorModalTitle').textContent = 'Ubah Koordinator';
  $("#k_gang").dxSelectBox("instance").getDataSource().reload().done(function(){
    $("#k_gang").dxSelectBox("instance").option("value", d.gang_id||null);
  });
  $("#k_keterangan").dxTextBox("instance").option("value",d.keterangan||"");
  $("#k_aktif").dxSwitch("instance").option("value",d.aktif===true||d.aktif===1);
  // Reload warga lookup lalu set value
  var sb = $("#k_warga").dxSelectBox("instance");
  sb.getDataSource().reload().done(function(){ sb.option("value", d.warga_id||null); });
  document.getElementById('koordinatorModal').classList.add('show');
}

function koordinatorSave(){
  var data = {
    id:          $("#k_id").val(),
    warga_id:    $("#k_warga").dxSelectBox("instance").option("value"),
    gang_id:     $("#k_gang").dxSelectBox("instance").option("value"),
    keterangan:  $("#k_keterangan").dxTextBox("instance").option("value"),
    aktif:       $("#k_aktif").dxSwitch("instance").option("value") ? 1 : 0,
  };
  if(!data.warga_id){DevExpress.ui.notify("Pilih warga terlebih dahulu","error",2500);return;}
  $.ajax({url:urlSave,type:"POST",data:data})
    .done(function(r){DevExpress.ui.notify(r.message,"success",2500);koordinatorClose();grid.refresh();})
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal menyimpan","error",3000);});
}

function koordinatorDelete(){
  if(!focusedRow){DevExpress.ui.notify("Pilih baris terlebih dahulu","warning",2500);return;}
  var d = focusedRow;
  document.getElementById('deleteMsg').textContent = '"'+d.nama+'" akan dihapus dari daftar koordinator.'+(d.gang?' Koordinator ini masih ditugaskan di gang "'+d.gang+'"':' Pastikan sudah tidak ditugaskan di gang manapun.');
  document.getElementById('deleteConfirmBtn').onclick = function(){
    $.ajax({url:urlDelete,type:"POST",data:{id:d.id}})
      .done(function(r){DevExpress.ui.notify(r.message,"success",2500);deleteClose();focusedRow=null;grid.refresh();})
      .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);deleteClose();});
  };
  document.getElementById('deleteModal').classList.add('show');
}

function koordinatorClose(){document.getElementById('koordinatorModal').classList.remove('show');}
function deleteClose(){document.getElementById('deleteModal').classList.remove('show');}

// ── Sub-grid Anggota ──────────────────────────────────────────────────
function buildAnggotaPanel(container, koordinator){
  var panel = $('<div class="anggota-panel">');

  var head = $('<div class="anggota-panel-head">');
  $('<h4>').text('Anggota — ' + (koordinator.nama||'') + (koordinator.nama_gang ? ' · '+koordinator.nama_gang : '')).appendTo(head);
  $('<button class="btn-add-anggota" type="button">+ Tambah Anggota</button>')
    .on('click', function(){ openTambahAnggota(koordinator); })
    .appendTo(head);
  head.appendTo(panel);

  var subGrid = $('<div class="anggota-grid">').appendTo(panel);
  panel.appendTo(container);

  var subGridRef = subGrid.dxDataGrid({
    dataSource: new DevExpress.data.CustomStore({
      key: 'id',
      load: () => $.getJSON(urlAnggotaList + '/' + koordinator.id + '/anggota')
    }),
    showBorders:true, showColumnLines:true, showRowLines:true,
    rowAlternationEnabled:true, width:'100%',
    paging:{enabled:false},
    onInitialized: function(e){ subGrid.data('grid', e.component); },
    columns:[
      {caption:'No.',width:45,alignment:'center',allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:11.5px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:'kepala_keluarga',caption:'Kepala Keluarga',minWidth:160,
        cellTemplate:function(c,o){
          var d=$('<div>');
          $('<div style="font-weight:600;font-size:13px">').text(o.value||'—').appendTo(d);
          $('<div style="font-family:\'IBM Plex Mono\',monospace;font-size:11px;color:var(--redup)">').text(o.row.data.no_kk||'').appendTo(d);
          d.appendTo(c);
        }},
      {dataField:'blok_no',caption:'Blok/Rumah',width:110,
        cellTemplate:function(c,o){
          if(!o.value){$('<span style="color:var(--garis-2)">—</span>').appendTo(c);return;}
          $('<span style="display:inline-flex;font-size:11.5px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--biru-soft);color:#1a3d52">').text(o.value).appendTo(c);
        }},
      {dataField:'no_telepon',caption:'Telepon',width:130,
        cellTemplate:function(c,o){$('<span style="font-family:\'IBM Plex Mono\',monospace;font-size:12px">').text(o.value||'—').appendTo(c);}},
      {dataField:'jumlah_jiwa',caption:'Jiwa',width:70,alignment:'center',
        cellTemplate:function(c,o){
          $('<span style="display:inline-flex;align-items:center;justify-content:center;min-width:26px;height:26px;border-radius:7px;background:var(--kertas-2);border:1px solid var(--garis);font-size:12px;font-weight:700;color:var(--redup)">').text(o.value||0).appendTo(c);
        }},
      {caption:'',width:50,alignment:'center',allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){
          $('<button class="wg-ic-btn wg-ic-del" title="Hapus dari anggota"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button>')
            .on('click', function(){
              if(!confirm('Hapus "'+o.row.data.nama+'" dari anggota koordinator?')) return;
              $.ajax({url:urlAnggotaRemove, type:'POST', data:{id:o.row.data.id}})
                .done(function(r){
                  DevExpress.ui.notify(r.message,'success',2000);
                  subGrid.data('grid').refresh();
                })
                .fail(function(){ DevExpress.ui.notify('Gagal','error',2000); });
            }).appendTo(c);
        }},
    ]
  }).dxDataGrid('instance');
}

// ── Modal Tambah Anggota ──────────────────────────────────────────────
var curKoordinatorId = null;

function openTambahAnggota(koordinator){
  curKoordinatorId = koordinator.id;
  document.getElementById('anggotaModalSub').textContent = koordinator.nama + (koordinator.nama_gang?' — '+koordinator.nama_gang:'');
  document.getElementById('anggotaModal').classList.add('show');

  // Init tagbox jika belum
  if (!$("#anggota_kk").data('dx-was-initialized')) {
    $("#anggota_kk").dxTagBox({
      dataSource: new DevExpress.data.CustomStore({
        key:'id',
        load:(opts)=>$.getJSON(urlAnggotaKkLookup+'/'+curKoordinatorId+'/anggota-lookup',{q:opts.searchValue||''})
      }),
      valueExpr:'id', displayExpr:'label',
      searchEnabled:true, minSearchLength:0, showSelectionControls:true,
      placeholder:'Cari nama kepala keluarga, blok, atau No. KK...',
      noDataText:'Semua KK aktif sudah ditambahkan.'
    });
  } else {
    var tb = $("#anggota_kk").dxTagBox("instance");
    tb.option("value",[]);
    tb.getDataSource().reload();
  }
}

function simpanAnggota(){
  var kkIds = $("#anggota_kk").dxTagBox("instance").option("value");
  if(!kkIds||!kkIds.length){DevExpress.ui.notify("Pilih minimal satu KK","error",2500);return;}
  $.ajax({
    url: urlAnggotaAdd, type:'POST',
    data: {koordinator_id: curKoordinatorId, 'kartu_keluarga_id[]': kkIds}
  })
  .done(function(r){
    DevExpress.ui.notify(r.message,'success',2500);
    document.getElementById('anggotaModal').classList.remove('show');
    grid.refresh(); // refresh main grid agar master-detail reload
  })
  .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||'Gagal','error',3000);});
}

document.addEventListener('keydown',function(e){
  if(e.key==='Escape'){
    koordinatorClose();
    deleteClose();
    document.getElementById('anggotaModal').classList.remove('show');
  }
});
</script>
@endpush
