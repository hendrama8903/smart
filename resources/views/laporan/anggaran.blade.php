@extends('layouts.app')
@section('judul','Master Anggaran')
@section('content')

<div class="keu-toolbar">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="{{ route('laporan.index') }}" style="color:var(--redup);font-size:13px">← Laporan</a>
    <h2 class="keu-title">Master Anggaran</h2>
  </div>
  <div class="keu-actions">
    <div id="filterTahun"></div>
    <button class="btn" id="btnTambah" type="button" onclick="anggaranAdd()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Tambah Pos
    </button>
    <button class="btn btn-ubah" id="btnUbah" type="button" onclick="anggaranEdit()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg> Ubah
    </button>
    <button class="btn btn-hapus" id="btnHapus" type="button" onclick="anggaranDelete()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg> Hapus
    </button>
    <button class="btn" style="background:#6366F1!important" type="button" onclick="openSalin()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Salin dari Tahun Lain
    </button>
  </div>
</div>

<div class="grid-wrap" style="height:calc(100vh - 130px)"><div id="gridAnggaran"></div></div>

{{-- Modal Anggaran --}}
<div class="modal-overlay" id="anggaranModal">
  <div class="modal-card keu-card" style="max-width:520px">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:var(--daun-pucat);color:var(--daun)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      </div>
      <div><h3 id="anggaranModalTitle">Tambah Pos Anggaran</h3><p class="keu-sub">Rencana pendapatan / pengeluaran</p></div>
    </div>
    <form onsubmit="return false" class="keu-body">
      <input type="hidden" id="a_id">
      <div class="ff2">
        <div class="ff"><label>Tahun <span class="req">*</span></label><div id="a_tahun"></div></div>
        <div class="ff"><label>Tipe <span class="req">*</span></label><div id="a_tipe"></div></div>
      </div>
      <div class="ff"><label>Kategori Kas</label><div id="a_kategori"></div></div>
      <div class="ff"><label>Nama Pos <span class="req">*</span></label><div id="a_nama_pos"></div></div>
      <div class="ff"><label>Nominal Rencana (setahun) <span class="req">*</span></label><div id="a_nominal"></div></div>
      <div class="ff"><label>Keterangan</label><div id="a_keterangan"></div></div>
    </form>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="anggaranClose()">Batal</button>
      <button class="mbtn mbtn-save" onclick="anggaranSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg> Simpan
      </button>
    </div>
  </div>
</div>

{{-- Modal Salin --}}
<div class="modal-overlay" id="salinModal" onclick="if(event.target===this)salinClose()">
  <div class="modal-card keu-card" style="max-width:400px">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:#EDE9FE;color:#6366F1">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
      </div>
      <div><h3>Salin Anggaran</h3><p class="keu-sub">Duplikat pos anggaran dari tahun lain</p></div>
    </div>
    <div class="keu-body">
      <div class="ff2">
        <div class="ff"><label>Dari Tahun</label><div id="s_dari"></div></div>
        <div class="ff"><label>Ke Tahun</label><div id="s_ke"></div></div>
      </div>
    </div>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="salinClose()">Batal</button>
      <button class="mbtn mbtn-save" style="background:#6366F1" onclick="doSalin()">Salin</button>
    </div>
  </div>
</div>

{{-- Modal Hapus --}}
<div class="modal-overlay" id="deleteModal" onclick="if(event.target===this)deleteClose()">
  <div class="modal-card">
    <div class="modal-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></div>
    <h3>Hapus Pos?</h3><p id="deleteMsg"></p>
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
.keu-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;gap:16px;flex-wrap:wrap}
.keu-title{font-size:22px;font-weight:800;letter-spacing:-.02em;color:var(--tinta);margin:0}
.keu-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.btn-ubah{background:var(--emas)!important;color:#fff!important}.btn-ubah:hover{filter:brightness(1.08)}
.btn-hapus{background:var(--stempel)!important;color:#fff!important}.btn-hapus:hover{filter:brightness(1.08)}
.btn[disabled]{opacity:.45;cursor:not-allowed;pointer-events:none}
.grid-wrap{background:var(--surface);border:1px solid var(--garis);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow)}
#gridAnggaran,.dx-widget{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
#gridAnggaran .dx-datagrid{border:none;color:var(--tinta)}
#gridAnggaran .dx-datagrid-headers{background:var(--kertas-2);border-bottom:1px solid var(--garis)}
#gridAnggaran .dx-datagrid-headers .dx-header-row>td{font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:12px 14px;border:none}
#gridAnggaran .dx-data-row>td{padding:11px 14px;font-size:13.5px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis);vertical-align:middle}
#gridAnggaran .dx-datagrid-rowsview .dx-data-row:hover>td{background:#FBFAF5}
#gridAnggaran .dx-datagrid-rowsview .dx-row-focused>td{background:var(--daun-pucat)!important;color:#155234!important}
#gridAnggaran .dx-pager{display:none}
#gridAnggaran .dx-toolbar{display:none}
.mono-sm{font-family:'IBM Plex Mono',monospace;font-size:12.5px;font-weight:600}
.keu-card{max-width:480px;width:100%;padding:0;text-align:left}
.keu-head{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--garis)}
.keu-head-icon{flex:0 0 34px;width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center}
.keu-head-icon svg{width:17px;height:17px}
.keu-head h3{font-size:15px;font-weight:800;margin:0 0 1px}
.keu-sub{font-size:12px;color:var(--redup);margin:0}
.keu-body{padding:18px 20px;display:flex;flex-direction:column;gap:0}
.ff{margin-bottom:13px;display:flex;flex-direction:column}
.ff>label{font-size:12px;font-weight:700;margin-bottom:5px;color:var(--redup)}
.ff2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.req{color:var(--stempel)}
.keu-foot{display:flex;gap:10px;justify-content:flex-end;padding:12px 20px;border-top:1px solid var(--garis);background:var(--kertas);border-radius:0 0 20px 20px}
.keu-foot .mbtn{flex:0 0 auto}
.mbtn-save{display:inline-flex;align-items:center;gap:7px;background:var(--hutan);color:#fff;font-weight:700;font-size:14px;padding:10px 20px;border-radius:10px;border:none;cursor:pointer;transition:.14s}
.mbtn-save:hover{background:var(--hutan-2)}.mbtn-save svg{width:14px;height:14px}
</style>
@endpush

@push('scripts')
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});
const urlList      = "{{ route('anggaran.list') }}";
const urlSave      = "{{ route('anggaran.save') }}";
const urlDelete    = "{{ route('anggaran.delete') }}";
const urlKategori  = "{{ route('anggaran.kategori') }}";
const urlSalin     = "{{ route('anggaran.salin') }}";

let grid, focusedRow=null, curTahun={{ now()->year }};
let kategoriMasuk=[], kategoriKeluar=[];

function rupiah(n){ return 'Rp ' + (n||0).toLocaleString('id-ID'); }

$(function(){
  $.when($.getJSON(urlKategori+'?tipe=masuk'), $.getJSON(urlKategori+'?tipe=keluar')).done(function(m,k){
    kategoriMasuk=m[0]; kategoriKeluar=k[0];
  });

  var tahunOpts=[];
  for(var y=2020;y<=2030;y++) tahunOpts.push({id:y,l:y+''});

  $("#filterTahun").dxSelectBox({
    dataSource:tahunOpts, valueExpr:'id', displayExpr:'l', value:curTahun, width:100,
    onValueChanged:function(e){curTahun=e.value;grid.refresh();}
  });

  grid = $("#gridAnggaran").dxDataGrid({
    dataSource: new DevExpress.data.CustomStore({key:"id", load:()=>$.getJSON(urlList,{tahun:curTahun})}),
    showBorders:false, showColumnLines:true, showRowLines:true,
    rowAlternationEnabled:true, width:"100%", height:"100%",
    columnAutoWidth:true, focusedRowEnabled:true,
    onFocusedRowChanged:e=>{focusedRow=e.row?e.row.data:null;},
    paging:{enabled:false},
    columns:[
      {caption:"No.",width:52,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"tipe",caption:"Tipe",width:100,
        cellTemplate:function(c,o){
          var cls=o.value==='masuk'?'tipe-masuk':'tipe-keluar';
          var lbl=o.value==='masuk'?'Pendapatan':'Pengeluaran';
          $('<span class="'+cls+'">').text(lbl).appendTo(c);
        }},
      {dataField:"kategori",caption:"Kategori",width:150,
        cellTemplate:function(c,o){$('<span style="color:var(--redup);font-size:12.5px">').text(o.value||'—').appendTo(c);}},
      {dataField:"nama_pos",caption:"Nama Pos Anggaran",minWidth:200,
        cellTemplate:function(c,o){$('<span style="font-weight:600">').text(o.value).appendTo(c);}},
      {dataField:"nominal_rencana",caption:"Rencana Setahun",width:160,alignment:"right",
        cellTemplate:function(c,o){$('<span class="mono-sm">').text(rupiah(o.value)).appendTo(c);}},
      {dataField:"keterangan",caption:"Keterangan",minWidth:150,
        cellTemplate:function(c,o){$('<span style="font-size:12.5px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
    ]
  }).dxDataGrid("instance");

  initEditors();
});

function initEditors(){
  var tahunOpts=[];
  for(var y=2020;y<=2030;y++) tahunOpts.push({id:y,l:y+''});

  $("#a_tahun").dxSelectBox({dataSource:tahunOpts,valueExpr:'id',displayExpr:'l',value:curTahun});
  $("#a_tipe").dxSelectBox({
    dataSource:[{id:'masuk',l:'Pendapatan (Masuk)'},{id:'keluar',l:'Pengeluaran (Keluar)'}],
    valueExpr:'id',displayExpr:'l',value:'masuk',
    onValueChanged:function(e){
      var sb=$("#a_kategori").dxSelectBox("instance");
      sb.option("dataSource",e.value==='masuk'?kategoriMasuk:kategoriKeluar);
      sb.option("value",null);
    }
  });
  $("#a_kategori").dxSelectBox({dataSource:kategoriMasuk,valueExpr:"id",displayExpr:"nama",showClearButton:true,placeholder:"— Pilih kategori (opsional) —"});
  $("#a_nama_pos").dxTextBox({placeholder:"cth. Iuran Sampah, Sewa Pendopo"});
  $("#a_nominal").dxNumberBox({min:0,format:"#,##0",placeholder:"Rp"});
  $("#a_keterangan").dxTextBox({placeholder:"Keterangan..."});

  // Salin
  $("#s_dari").dxSelectBox({dataSource:[...Array(11)].map((_,i)=>({id:2020+i,l:''+(2020+i)})),valueExpr:'id',displayExpr:'l',value:curTahun-1});
  $("#s_ke").dxSelectBox({dataSource:[...Array(11)].map((_,i)=>({id:2020+i,l:''+(2020+i)})),valueExpr:'id',displayExpr:'l',value:curTahun});
}

function anggaranAdd(){
  $("#a_id").val('');
  document.getElementById('anggaranModalTitle').textContent='Tambah Pos Anggaran';
  $("#a_tahun").dxSelectBox("instance").option("value",curTahun);
  $("#a_tipe").dxSelectBox("instance").option("value","masuk");
  $("#a_kategori").dxSelectBox("instance").option("dataSource",kategoriMasuk);
  $("#a_kategori").dxSelectBox("instance").option("value",null);
  $("#a_nama_pos").dxTextBox("instance").option("value","");
  $("#a_nominal").dxNumberBox("instance").option("value",null);
  $("#a_keterangan").dxTextBox("instance").option("value","");
  document.getElementById('anggaranModal').classList.add('show');
}

function anggaranEdit(){
  if(!focusedRow){DevExpress.ui.notify("Pilih baris terlebih dahulu","warning",2500);return;}
  var d=focusedRow;
  $("#a_id").val(d.id);
  document.getElementById('anggaranModalTitle').textContent='Ubah Pos Anggaran';
  $("#a_tahun").dxSelectBox("instance").option("value",d.tahun);
  $("#a_tipe").dxSelectBox("instance").option("value",d.tipe);
  var kat=d.tipe==='masuk'?kategoriMasuk:kategoriKeluar;
  var sb=$("#a_kategori").dxSelectBox("instance");
  sb.option("dataSource",kat); sb.option("value",d.kategori_id||null);
  $("#a_nama_pos").dxTextBox("instance").option("value",d.nama_pos||"");
  $("#a_nominal").dxNumberBox("instance").option("value",d.nominal_rencana||0);
  $("#a_keterangan").dxTextBox("instance").option("value",d.keterangan||"");
  document.getElementById('anggaranModal').classList.add('show');
}

function anggaranSave(){
  var data={
    id:         $("#a_id").val(),
    tahun:      $("#a_tahun").dxSelectBox("instance").option("value"),
    tipe:       $("#a_tipe").dxSelectBox("instance").option("value"),
    kategori_id:$("#a_kategori").dxSelectBox("instance").option("value"),
    nama_pos:   $("#a_nama_pos").dxTextBox("instance").option("value"),
    nominal_rencana:$("#a_nominal").dxNumberBox("instance").option("value"),
    keterangan: $("#a_keterangan").dxTextBox("instance").option("value"),
  };
  if(!data.nama_pos){DevExpress.ui.notify("Nama pos wajib diisi","error",2500);return;}
  $.ajax({url:urlSave,type:"POST",data:data})
    .done(function(r){DevExpress.ui.notify(r.message,"success",2500);anggaranClose();grid.refresh();})
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);});
}

function anggaranDelete(){
  if(!focusedRow){DevExpress.ui.notify("Pilih baris terlebih dahulu","warning",2500);return;}
  var d=focusedRow;
  document.getElementById('deleteMsg').textContent='"'+d.nama_pos+'" akan dihapus.';
  document.getElementById('deleteConfirmBtn').onclick=function(){
    $.ajax({url:urlDelete,type:"POST",data:{id:d.id}})
      .done(function(r){DevExpress.ui.notify(r.message,"success",2500);deleteClose();focusedRow=null;grid.refresh();})
      .fail(function(){DevExpress.ui.notify("Gagal","error",3000);});
  };
  document.getElementById('deleteModal').classList.add('show');
}

function openSalin(){document.getElementById('salinModal').classList.add('show');}
function doSalin(){
  var dari=$("#s_dari").dxSelectBox("instance").option("value");
  var ke=$("#s_ke").dxSelectBox("instance").option("value");
  $.ajax({url:urlSalin,type:"POST",data:{dari_tahun:dari,ke_tahun:ke}})
    .done(function(r){DevExpress.ui.notify(r.message,"success",3000);salinClose();curTahun=ke;$("#filterTahun").dxSelectBox("instance").option("value",ke);grid.refresh();})
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);});
}
function salinClose(){document.getElementById('salinModal').classList.remove('show');}
function anggaranClose(){document.getElementById('anggaranModal').classList.remove('show');}
function deleteClose(){document.getElementById('deleteModal').classList.remove('show');}
document.addEventListener('keydown',function(e){if(e.key==='Escape'){anggaranClose();salinClose();deleteClose();}});
</script>
@endpush
