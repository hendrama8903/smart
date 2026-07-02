@extends('layouts.app')
@section('judul','Master Fasilitas & Tarif')
@section('content')

<div class="keu-toolbar">
  <h2 class="keu-title">Master Fasilitas &amp; Tarif Sewa</h2>
  <div class="keu-actions">
    <button class="btn" id="btnTambah" type="button" onclick="fasilitasAdd()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Tambah Fasilitas
    </button>
    <button class="btn btn-ubah" id="btnUbah" type="button" onclick="fasilitasEdit()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg> Ubah
    </button>
    <button class="btn btn-hapus" id="btnHapus" type="button" onclick="fasilitasDelete()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg> Hapus
    </button>
  </div>
</div>

<div class="grid-wrap"><div id="gridFasilitas"></div></div>

{{-- Modal Fasilitas --}}
<div class="modal-overlay" id="fasilitasModal">
  <div class="modal-card keu-card">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:var(--biru-soft);color:var(--biru)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V9l7-5 7 5v12"/></svg>
      </div>
      <h3 id="fasilitasModalTitle">Tambah Fasilitas</h3>
    </div>
    <form onsubmit="return false" class="keu-body">
      <input type="hidden" id="f_id">
      <div class="ff"><label>Nama Fasilitas <span class="req">*</span></label><div id="f_nama"></div></div>
      <div class="ff2">
        <div class="ff"><label>Satuan <span class="req">*</span></label><div id="f_satuan"></div></div>
        <div class="ff">
          <label>&nbsp;</label>
          <div class="mf-switch" style="margin-top:0">
            <div id="f_aktif"></div>
            <label class="mf-switch-lbl">Aktif</label>
          </div>
        </div>
      </div>
      <div class="ff"><label>Deskripsi</label><div id="f_deskripsi"></div></div>
    </form>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="fasilitasClose()">Batal</button>
      <button class="mbtn mbtn-save" onclick="fasilitasSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Simpan
      </button>
    </div>
  </div>
</div>

{{-- Modal Tarif --}}
<div class="modal-overlay" id="tarifModal">
  <div class="modal-card keu-card" style="max-width:560px">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:var(--emas-soft);color:#7a5c00">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <h3 id="tarifModalTitle">Tambah Tarif</h3>
    </div>
    <form onsubmit="return false" class="keu-body">
      <input type="hidden" id="t_id">
      <input type="hidden" id="t_fasilitas_id">
      <div class="ff2">
        <div class="ff"><label>Nama Tarif <span class="req">*</span></label><div id="t_nama_tarif"></div></div>
        <div class="ff"><label>Kategori Pengguna <span class="req">*</span></label><div id="t_kategori"></div></div>
      </div>
      <div class="ff"><label>Nominal Total (dibayar penyewa) <span class="req">*</span></label><div id="t_nominal_total"></div></div>
      <div class="ff2">
        <div class="ff"><label>Bagian Kas RT <span class="req">*</span></label><div id="t_nominal_kas_rt"></div></div>
        <div class="ff"><label>Biaya Lain <small class="hint">(ongkos, dll.)</small></label><div id="t_nominal_lain"></div></div>
      </div>
      <div class="ff"><label>Keterangan Biaya Lain</label><div id="t_keterangan_lain"></div></div>
      <div class="tarif-check">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span id="tarifCalcInfo">Kas RT + Biaya Lain harus = Nominal Total</span>
      </div>
    </form>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="tarifClose()">Batal</button>
      <button class="mbtn mbtn-save" onclick="tarifSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Simpan Tarif
      </button>
    </div>
  </div>
</div>

{{-- Modal Konfirmasi --}}
<div class="modal-overlay" id="deleteModal" onclick="if(event.target===this)deleteClose()">
  <div class="modal-card">
    <div class="modal-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></div>
    <h3 id="deleteTitle">Hapus?</h3><p id="deleteMsg"></p>
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

/* ── Toolbar ── */
.keu-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;gap:12px;flex-wrap:wrap}
.keu-title{font-size:20px;font-weight:800;letter-spacing:-.02em;color:var(--tinta);margin:0;white-space:nowrap}
.keu-actions{display:flex;gap:8px;flex-shrink:0;flex-wrap:wrap}
.keu-actions .btn{min-width:100px;justify-content:center}
.btn-ubah{background:var(--emas)!important;color:#fff!important}.btn-ubah:hover{filter:brightness(1.08)}
.btn-hapus{background:var(--stempel)!important;color:#fff!important}.btn-hapus:hover{filter:brightness(1.08)}
.btn[disabled]{opacity:.45;cursor:not-allowed;pointer-events:none}

/* ── Grid wrap ── */
.grid-wrap{
  background:var(--surface);border:1px solid var(--garis);
  overflow:auto;box-shadow:var(--shadow);
  height:calc(100vh - 140px);
}

/* ── Responsive ── */
@media(max-width:640px){
  .keu-toolbar{flex-direction:column;align-items:stretch;gap:10px}
  .keu-title{font-size:18px;white-space:normal}
  .keu-actions{display:flex;flex-wrap:wrap;gap:8px}
  .grid-wrap{height:calc(100vh - 210px)}
}
#gridFasilitas,.dx-widget{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
#gridFasilitas .dx-datagrid{border:none;color:var(--tinta)}
#gridFasilitas .dx-datagrid-headers{background:var(--kertas-2);border-bottom:1px solid var(--garis)}
#gridFasilitas .dx-datagrid-headers .dx-header-row>td{font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:11px 14px;border:none}
#gridFasilitas .dx-data-row>td{padding:10px 14px;font-size:13px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis);vertical-align:middle}
#gridFasilitas .dx-datagrid-rowsview .dx-data-row:hover>td{background:#FBFAF5}
#gridFasilitas .dx-datagrid-rowsview .dx-row-focused>td{background:var(--daun-pucat)!important;color:#155234!important}
#gridFasilitas .dx-pager{display:none}
#gridFasilitas .dx-toolbar{display:none}

/* Sub-grid tarif */
.dx-master-detail-cell{padding:0!important;background:var(--kertas)!important}
.tarif-panel{padding:14px 20px}
.tarif-panel-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.tarif-panel-head h4{font-size:13px;font-weight:700;color:var(--tinta)}
.tarif-grid .dx-datagrid-headers .dx-header-row>td{font-size:10px;padding:8px 12px}
.tarif-grid .dx-data-row>td{padding:7px 12px;font-size:12.5px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis)}
.tarif-grid .dx-datagrid{border:1px solid var(--garis)}

.cat-warga{display:inline-flex;align-items:center;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;background:var(--daun-pucat);color:#14532D}
.cat-luar{display:inline-flex;align-items:center;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;background:var(--stempel-soft);color:#9A3422}
.gc-pill{display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:700;padding:3px 11px;border-radius:20px}
.gc-pill.on{background:var(--daun-pucat);color:#1B5E3F}.gc-pill.off{background:var(--stempel-soft);color:#9A3422}
.mono-sm{font-family:'IBM Plex Mono',monospace;font-size:12.5px;font-weight:600}
.wg-ic-btn{width:26px;height:26px;border-radius:6px;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:.13s}
.wg-ic-btn svg{width:12px;height:12px}
.wg-ic-edit{background:var(--emas-soft);color:#7a5c00}.wg-ic-edit:hover{background:var(--emas);color:#fff}
.wg-ic-del{background:var(--stempel-soft);color:var(--stempel)}.wg-ic-del:hover{background:var(--stempel);color:#fff}
.btn-add-tarif{font-size:12px;padding:5px 11px;border-radius:7px;border:1.5px solid var(--daun);color:var(--daun);background:transparent;cursor:pointer;font-weight:700;transition:.13s}
.btn-add-tarif:hover{background:var(--daun-pucat)}

.keu-card{max-width:480px;width:100%;padding:0;text-align:left}
.keu-head{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--garis)}
.keu-head-icon{flex:0 0 34px;width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center}
.keu-head-icon svg{width:17px;height:17px}
.keu-head h3{font-size:15px;font-weight:800;margin:0 0 1px}
.keu-body{padding:16px 20px;display:flex;flex-direction:column;gap:0}
.ff{margin-bottom:10px;display:flex;flex-direction:column}
.ff>label{font-size:12px;font-weight:700;margin-bottom:4px;color:var(--redup)}
.ff2{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.hint{font-size:11px;color:#9aa89f;font-weight:500}.req{color:var(--stempel)}
.mf-switch{display:flex;align-items:center;gap:8px;margin-top:4px}
.tarif-check{display:flex;align-items:center;gap:7px;font-size:12px;color:var(--redup);padding:8px 0}
.tarif-check svg{flex:0 0 14px}
.keu-foot{display:flex;gap:10px;justify-content:flex-end;padding:12px 20px;border-top:1px solid var(--garis);background:var(--kertas);border-radius:0 0 14px 14px}
.keu-foot .mbtn{flex:0 0 auto;min-width:90px}
.keu-foot .mbtn.ghost{background:var(--stempel-soft);color:var(--stempel);border:1.5px solid #f5c6bb}
.keu-foot .mbtn.ghost:hover{background:#fde5e0}
.mbtn-save{display:inline-flex;align-items:center;gap:7px;background:var(--hutan);color:#fff;font-weight:700;font-size:13px;padding:8px 18px;border-radius:10px;border:none;cursor:pointer;transition:.14s}
.mbtn-save:hover{background:var(--hutan-2)}
.mbtn-save svg{width:13px;height:13px}
</style>
@endpush

@push('scripts')
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});
const urlFList   = "{{ route('fasilitas.list') }}";
const urlFSave   = "{{ route('fasilitas.save') }}";
const urlFDelete = "{{ route('fasilitas.delete') }}";
const urlTList   = "{{ url('fasilitas/tarif') }}";
const urlTSave   = "{{ route('fasilitas.tarif.save') }}";
const urlTDelete = "{{ route('fasilitas.tarif.delete') }}";

let grid, focusedRow=null;
function rupiah(n){return 'Rp '+(n||0).toLocaleString('id-ID');}

$(function(){
  var p=window.__perms||{};
  if(!p.add)   document.getElementById('btnTambah').disabled=true;
  if(!p.edit)  document.getElementById('btnUbah').disabled=true;
  if(!p.delete)document.getElementById('btnHapus').disabled=true;

  grid=$("#gridFasilitas").dxDataGrid({
    dataSource:new DevExpress.data.CustomStore({key:"id",load:()=>$.getJSON(urlFList)}),
    showBorders:false,showColumnLines:true,showRowLines:true,
    rowAlternationEnabled:true,width:"100%",height:"100%",
    columnAutoWidth:true,focusedRowEnabled:true,
    scrolling:{useNative:true,showScrollbar:'onHover',mode:'standard'},
    onFocusedRowChanged:e=>{focusedRow=e.row?e.row.data:null;},
    paging:{enabled:false},
    masterDetail:{enabled:true,template:function(c,info){buildTarifPanel(c,info.data);}},
    columns:[
      {caption:"No.",width:52,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"nama",caption:"Nama Fasilitas",minWidth:160,
        cellTemplate:function(c,o){$('<span style="font-weight:700;font-size:14px">').text(o.value).appendTo(c);}},
      {dataField:"deskripsi",caption:"Deskripsi",minWidth:180,
        cellTemplate:function(c,o){$('<span style="color:var(--redup);font-size:12.5px">').text(o.value||'—').appendTo(c);}},
      {dataField:"satuan",caption:"Satuan",width:90,alignment:"center",
        cellTemplate:function(c,o){
          $('<span style="display:inline-flex;align-items:center;justify-content:center;padding:2px 10px;border-radius:20px;background:var(--biru-soft);color:#1a3d52;font-size:11px;font-weight:700">').text(o.value||'—').appendTo(c);
        }},
      {dataField:"jumlah_tarif",caption:"Jml. Tarif",width:90,alignment:"center",
        cellTemplate:function(c,o){
          $('<span style="display:inline-flex;align-items:center;justify-content:center;min-width:28px;height:28px;border-radius:7px;background:var(--kertas-2);border:1px solid var(--garis);font-size:12.5px;font-weight:700;color:var(--redup)">').text(o.value||0).appendTo(c);
        }},
      {dataField:"aktif",caption:"Status",width:90,alignment:"center",
        cellTemplate:function(c,o){
          var on=o.value===true||o.value===1;
          $('<span class="gc-pill '+(on?'on':'off')+'">').text(on?'Aktif':'Nonaktif').appendTo(c);
        }},
    ]
  }).dxDataGrid("instance");

  initFasilitasEditors(); initTarifEditors();
});

// ── Detail panel: tabel tarif ──────────────────────────────────────────
function buildTarifPanel(container, fas){
  var panel=$('<div class="tarif-panel">');
  var head=$('<div class="tarif-panel-head">');
  $('<h4>').text('Tarif — '+fas.nama+' (per '+fas.satuan+')').appendTo(head);
  $('<button class="btn-add-tarif" type="button">+ Tambah Tarif</button>')
    .on('click',function(){tarifAdd(fas.id,fas.nama);}).appendTo(head);
  head.appendTo(panel);

  var subGrid=$('<div class="tarif-grid">').appendTo(panel);
  panel.appendTo(container);

  subGrid.dxDataGrid({
    dataSource:new DevExpress.data.CustomStore({key:"id",load:()=>$.getJSON(urlTList+'/'+fas.id)}),
    showBorders:true,showColumnLines:true,showRowLines:true,
    rowAlternationEnabled:true,width:"100%",
    scrolling:{useNative:true,showScrollbar:'onHover'},
    paging:{enabled:false},
    columns:[
      {dataField:"nama_tarif",caption:"Nama Tarif",minWidth:150,
        cellTemplate:function(c,o){$('<span style="font-weight:600">').text(o.value).appendTo(c);}},
      {dataField:"kategori",caption:"Kategori",width:110,alignment:"center",
        cellTemplate:function(c,o){
          var cls=o.value==='warga'?'cat-warga':'cat-luar';
          var lbl=o.value==='warga'?'Warga RT':'Luar Warga';
          $('<span class="'+cls+'">').text(lbl).appendTo(c);
        }},
      {dataField:"nominal_total",caption:"Total Bayar",width:120,alignment:"right",
        cellTemplate:function(c,o){$('<span class="mono-sm">').text(rupiah(o.value)).appendTo(c);}},
      {dataField:"nominal_kas_rt",caption:"Kas RT",width:110,alignment:"right",
        cellTemplate:function(c,o){$('<span class="mono-sm" style="color:#2D6A4F">').text(rupiah(o.value)).appendTo(c);}},
      {dataField:"nominal_lain",caption:"Biaya Lain",width:110,alignment:"right",
        cellTemplate:function(c,o){
          if(!o.value){$('<span style="color:var(--garis-2)">—</span>').appendTo(c);return;}
          var wrap=$('<div>');
          $('<span class="mono-sm" style="color:var(--redup)">').text(rupiah(o.value)).appendTo(wrap);
          if(o.row.data.keterangan_lain) $('<div style="font-size:10.5px;color:#9aa89f">').text(o.row.data.keterangan_lain).appendTo(wrap);
          wrap.appendTo(c);
        }},
      {caption:"Aksi",width:80,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){
          var d=o.row.data;
          var wrap=$('<div style="display:flex;gap:4px;justify-content:center">');
          $('<button class="wg-ic-btn wg-ic-edit" title="Ubah"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg></button>').on('click',function(){tarifEdit(d,fas.nama);}).appendTo(wrap);
          $('<button class="wg-ic-btn wg-ic-del" title="Hapus"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button>').on('click',function(){tarifDeleteConfirm(d);}).appendTo(wrap);
          wrap.appendTo(c);
        }},
    ]
  });
}

// ── Fasilitas CRUD ─────────────────────────────────────────────────────
function initFasilitasEditors(){
  $("#f_nama").dxTextBox({placeholder:"cth. Pendopo"});
  $("#f_deskripsi").dxTextBox({placeholder:"Deskripsi fasilitas..."});
  $("#f_aktif").dxSwitch({value:true});
  $("#f_satuan").dxSelectBox({
    dataSource:[{id:'sesi',l:'Sesi'},{id:'hari',l:'Hari'},{id:'unit',l:'Unit'},{id:'jam',l:'Jam'}],
    valueExpr:'id',displayExpr:'l',value:'sesi'
  });
}

function fasilitasAdd(){
  $("#f_id").val('');
  document.getElementById('fasilitasModalTitle').textContent='Tambah Fasilitas';
  $("#f_nama").dxTextBox("instance").option("value","");
  $("#f_deskripsi").dxTextBox("instance").option("value","");
  $("#f_satuan").dxSelectBox("instance").option("value","sesi");
  $("#f_aktif").dxSwitch("instance").option("value",true);
  document.getElementById('fasilitasModal').classList.add('show');
}

function fasilitasEdit(){
  if(!focusedRow){DevExpress.ui.notify("Pilih baris terlebih dahulu","warning",2500);return;}
  var d=focusedRow;
  $("#f_id").val(d.id);
  document.getElementById('fasilitasModalTitle').textContent='Ubah Fasilitas';
  $("#f_nama").dxTextBox("instance").option("value",d.nama||"");
  $("#f_deskripsi").dxTextBox("instance").option("value",d.deskripsi||"");
  $("#f_satuan").dxSelectBox("instance").option("value",d.satuan||"sesi");
  $("#f_aktif").dxSwitch("instance").option("value",d.aktif===true||d.aktif===1);
  document.getElementById('fasilitasModal').classList.add('show');
}

function fasilitasSave(){
  var data={
    id:$("#f_id").val(),
    nama:$("#f_nama").dxTextBox("instance").option("value"),
    deskripsi:$("#f_deskripsi").dxTextBox("instance").option("value"),
    satuan:$("#f_satuan").dxSelectBox("instance").option("value"),
    aktif:$("#f_aktif").dxSwitch("instance").option("value")?1:0,
  };
  if(!data.nama){DevExpress.ui.notify("Nama fasilitas wajib diisi","error",2500);return;}
  $.ajax({url:urlFSave,type:"POST",data:data})
    .done(function(r){DevExpress.ui.notify(r.message,"success",2500);fasilitasClose();grid.refresh();})
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);});
}

function fasilitasDelete(){
  if(!focusedRow){DevExpress.ui.notify("Pilih baris terlebih dahulu","warning",2500);return;}
  var d=focusedRow;
  document.getElementById('deleteTitle').textContent="Hapus Fasilitas?";
  document.getElementById('deleteMsg').textContent='"'+d.nama+'" akan dihapus. Pastikan tidak ada tarif yang terhubung.';
  document.getElementById('deleteConfirmBtn').onclick=function(){
    $.ajax({url:urlFDelete,type:"POST",data:{id:d.id}})
      .done(function(r){DevExpress.ui.notify(r.message,"success",2500);deleteClose();focusedRow=null;grid.refresh();})
      .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);deleteClose();});
  };
  document.getElementById('deleteModal').classList.add('show');
}

// ── Tarif CRUD ─────────────────────────────────────────────────────────
function initTarifEditors(){
  $("#t_nama_tarif").dxTextBox({placeholder:"cth. Simple (< 4 jam)"});
  $("#t_kategori").dxSelectBox({
    dataSource:[{id:'warga',l:'Warga RT'},{id:'luar_warga',l:'Luar Warga'}],
    valueExpr:'id',displayExpr:'l',value:'warga'
  });
  $("#t_nominal_total").dxNumberBox({min:0,format:"#,##0",onValueChanged:updateTarifCalc});
  $("#t_nominal_kas_rt").dxNumberBox({min:0,format:"#,##0",onValueChanged:updateTarifCalc});
  $("#t_nominal_lain").dxNumberBox({min:0,format:"#,##0",value:0,onValueChanged:updateTarifCalc});
  $("#t_keterangan_lain").dxTextBox({placeholder:"cth. Ongkos bongkar pasang"});
}

function updateTarifCalc(){
  var total=$("#t_nominal_total").dxNumberBox("instance").option("value")||0;
  var kas=$("#t_nominal_kas_rt").dxNumberBox("instance").option("value")||0;
  var lain=$("#t_nominal_lain").dxNumberBox("instance").option("value")||0;
  var ok=Math.abs((kas+lain)-total)<1;
  document.getElementById('tarifCalcInfo').textContent=
    'Kas RT ('+rupiah(kas)+') + Biaya Lain ('+rupiah(lain)+') = '+rupiah(kas+lain)+(ok?' ✓':' ≠ Total '+rupiah(total));
  document.getElementById('tarifCalcInfo').style.color=ok?'#2D6A4F':'var(--stempel)';
}

function tarifAdd(fasId,fasNama){
  $("#t_id").val(''); $("#t_fasilitas_id").val(fasId);
  document.getElementById('tarifModalTitle').textContent='Tambah Tarif';
  document.getElementById('tarifModalSub').textContent=fasNama;
  $("#t_nama_tarif").dxTextBox("instance").option("value","");
  $("#t_kategori").dxSelectBox("instance").option("value","warga");
  $("#t_nominal_total").dxNumberBox("instance").option("value",0);
  $("#t_nominal_kas_rt").dxNumberBox("instance").option("value",0);
  $("#t_nominal_lain").dxNumberBox("instance").option("value",0);
  $("#t_keterangan_lain").dxTextBox("instance").option("value","");
  updateTarifCalc();
  document.getElementById('tarifModal').classList.add('show');
}

function tarifEdit(d,fasNama){
  $("#t_id").val(d.id); $("#t_fasilitas_id").val(d.fasilitas_id);
  document.getElementById('tarifModalTitle').textContent='Ubah Tarif';
  document.getElementById('tarifModalSub').textContent=fasNama;
  $("#t_nama_tarif").dxTextBox("instance").option("value",d.nama_tarif||"");
  $("#t_kategori").dxSelectBox("instance").option("value",d.kategori||"warga");
  $("#t_nominal_total").dxNumberBox("instance").option("value",d.nominal_total||0);
  $("#t_nominal_kas_rt").dxNumberBox("instance").option("value",d.nominal_kas_rt||0);
  $("#t_nominal_lain").dxNumberBox("instance").option("value",d.nominal_lain||0);
  $("#t_keterangan_lain").dxTextBox("instance").option("value",d.keterangan_lain||"");
  updateTarifCalc();
  document.getElementById('tarifModal').classList.add('show');
}

function tarifSave(){
  var data={
    id:$("#t_id").val(),
    fasilitas_id:$("#t_fasilitas_id").val(),
    nama_tarif:$("#t_nama_tarif").dxTextBox("instance").option("value"),
    kategori:$("#t_kategori").dxSelectBox("instance").option("value"),
    nominal_total:$("#t_nominal_total").dxNumberBox("instance").option("value"),
    nominal_kas_rt:$("#t_nominal_kas_rt").dxNumberBox("instance").option("value"),
    nominal_lain:$("#t_nominal_lain").dxNumberBox("instance").option("value")||0,
    keterangan_lain:$("#t_keterangan_lain").dxTextBox("instance").option("value"),
    aktif:1,
  };
  if(!data.nama_tarif){DevExpress.ui.notify("Nama tarif wajib diisi","error",2500);return;}
  $.ajax({url:urlTSave,type:"POST",data:data})
    .done(function(r){DevExpress.ui.notify(r.message,"success",2500);tarifClose();grid.refresh();})
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);});
}

function tarifDeleteConfirm(d){
  document.getElementById('deleteTitle').textContent="Hapus Tarif?";
  document.getElementById('deleteMsg').textContent='"'+d.nama_tarif+'" ('+['warga','luar_warga'].includes(d.kategori)?{warga:'Warga RT',luar_warga:'Luar Warga'}[d.kategori]:d.kategori+') akan dihapus.';
  document.getElementById('deleteConfirmBtn').onclick=function(){
    $.ajax({url:urlTDelete,type:"POST",data:{id:d.id}})
      .done(function(r){DevExpress.ui.notify(r.message,"success",2500);deleteClose();grid.refresh();})
      .fail(function(){DevExpress.ui.notify("Gagal","error",3000);});
  };
  document.getElementById('deleteModal').classList.add('show');
}

function fasilitasClose(){document.getElementById('fasilitasModal').classList.remove('show');}
function tarifClose(){document.getElementById('tarifModal').classList.remove('show');}
function deleteClose(){document.getElementById('deleteModal').classList.remove('show');}
document.addEventListener('keydown',function(e){if(e.key==='Escape'){fasilitasClose();tarifClose();deleteClose();}});
</script>
@endpush
