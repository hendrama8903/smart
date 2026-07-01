@extends('layouts.app')
@section('judul','Kas RT')
@section('content')

<div class="keu-toolbar">
  <h2 class="keu-title">Kas RT</h2>
  <div class="keu-actions">
    <div id="filterBulan"></div>
    <div id="filterTipe"></div>
    <button class="btn" id="btnMasuk" type="button" onclick="kasAdd('masuk')">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Kas Masuk
    </button>
    <button class="btn btn-hapus" id="btnKeluar" type="button" onclick="kasAdd('keluar')">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg> Kas Keluar
    </button>
    <button class="btn btn-unduh" id="btnUnduh" type="button" onclick="exportKas()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Unduh
    </button>
  </div>
</div>

{{-- Summary Cards --}}
<div class="kas-summary">
  <div class="sum-card s-biru">
    <div class="sum-lbl">Saldo Kas RT</div>
    <div class="sum-val" id="kSaldo">Rp 0</div>
    <div class="sum-sub">Saldo total</div>
  </div>
  <div class="sum-card s-lunas">
    <div class="sum-lbl">Pemasukan Bulan Ini</div>
    <div class="sum-val" id="kMasuk">Rp 0</div>
  </div>
  <div class="sum-card s-belum">
    <div class="sum-lbl">Pengeluaran Bulan Ini</div>
    <div class="sum-val" id="kKeluar">Rp 0</div>
  </div>
  <div class="sum-card s-sebagian">
    <div class="sum-lbl">Net Bulan Ini</div>
    <div class="sum-val" id="kNet">Rp 0</div>
  </div>
</div>

<div class="grid-wrap" style="height:calc(100vh - 290px)"><div id="gridKas"></div></div>

{{-- Modal Kas --}}
<div class="modal-overlay" id="kasModal">
  <div class="modal-card keu-card">
    <div class="keu-head">
      <div class="keu-head-icon" id="kasModalIcon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
      </div>
      <h3 id="kasModalTitle">Kas Masuk</h3>
    </div>
    <form id="formKas" onsubmit="return false" class="keu-body">
      <input type="hidden" id="k_id">
      <input type="hidden" id="k_tipe">
      <div class="ff2">
        <div class="ff"><label>Tanggal <span class="req">*</span></label><div id="k_tanggal"></div></div>
        <div class="ff"><label>Kategori <span class="req">*</span></label><div id="k_kategori"></div></div>
      </div>
      <div class="ff"><label>Jumlah <span class="req">*</span></label><div id="k_jumlah"></div></div>
      <div class="ff"><label>Keterangan</label><div id="k_keterangan"></div></div>
      <div class="ff">
        <label>Bukti <small class="hint">(foto/PDF, maks. 5 MB)</small></label>
        <div class="bukti-wrap">
          <div class="bukti-preview" id="k_bukti_preview">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
          </div>
          <div style="flex:1"><div id="k_bukti_uploader"></div></div>
        </div>
        <input type="hidden" id="k_bukti_path">
      </div>
    </form>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="kasClose()">Batal</button>
      <button class="mbtn mbtn-save" onclick="kasSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Simpan
      </button>
    </div>
  </div>
</div>

{{-- Modal Konfirmasi --}}
<div class="modal-overlay" id="deleteModal" onclick="if(event.target===this)deleteClose()">
  <div class="modal-card">
    <div class="modal-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></div>
    <h3>Hapus Transaksi?</h3><p id="deleteMsg"></p>
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
.keu-toolbar{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;gap:16px;flex-wrap:wrap}
.keu-title{font-size:22px;font-weight:800;letter-spacing:-.02em;color:var(--tinta);margin:0}
.keu-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.btn-hapus{background:var(--stempel)!important;color:#fff!important}.btn-hapus:hover{filter:brightness(1.08)}
.btn-unduh{background:var(--biru)!important;color:#fff!important}.btn-unduh:hover{filter:brightness(1.08)}
.btn[disabled]{opacity:.45;cursor:not-allowed;pointer-events:none}

/* Summary */
.kas-summary{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:14px}
.sum-card{background:var(--surface);border:1px solid var(--garis);border-radius:12px;padding:14px 16px;position:relative;overflow:hidden}
.sum-card::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px}
.s-biru::before{background:var(--biru)}.s-lunas::before{background:#2D6A4F}
.s-belum::before{background:var(--stempel)}.s-sebagian::before{background:var(--emas)}
.sum-lbl{font-size:11.5px;color:var(--redup);font-weight:600;margin-bottom:6px}
.sum-val{font-size:20px;font-weight:800;letter-spacing:-.02em;color:var(--tinta)}
.sum-sub{font-size:11px;color:var(--redup);margin-top:3px}

/* Grid */
.grid-wrap{background:var(--surface);border:1px solid var(--garis);overflow:auto;box-shadow:var(--shadow)}
#gridKas,.dx-widget{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
#gridKas .dx-datagrid{border:none;color:var(--tinta)}
#gridKas .dx-datagrid-headers{background:var(--kertas-2);border-bottom:1px solid var(--garis)}
#gridKas .dx-datagrid-headers .dx-header-row>td{font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:11px 14px;border:none}
#gridKas .dx-data-row>td{padding:10px 14px;font-size:13px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis);vertical-align:middle}
#gridKas .dx-datagrid-rowsview .dx-data-row:hover>td{background:#FBFAF5}
#gridKas .dx-pager{background:var(--kertas);border-top:1px solid var(--garis);padding:8px 14px}
#gridKas .dx-toolbar{display:none}

.tipe-masuk{display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--daun-pucat);color:#1B5E3F}
.tipe-keluar{display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--stempel-soft);color:#9A3422}
.mono-sm{font-family:'IBM Plex Mono',monospace;font-size:12.5px;font-weight:600}
.wg-ic-btn{width:28px;height:28px;border-radius:7px;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:.13s}
.wg-ic-btn svg{width:13px;height:13px}
.wg-ic-edit{background:var(--emas-soft);color:#7a5c00}.wg-ic-edit:hover{background:var(--emas);color:#fff}
.wg-ic-del{background:var(--stempel-soft);color:var(--stempel)}.wg-ic-del:hover{background:var(--stempel);color:#fff}
.bukti-link{font-size:12px;color:var(--daun);font-weight:600;text-decoration:underline}

.keu-card{max-width:520px;width:100%;padding:0;text-align:left}
.keu-head{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--garis)}
.keu-head-icon{flex:0 0 34px;width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center}
.keu-head-icon svg{width:17px;height:17px}
.keu-head h3{font-size:15px;font-weight:800;margin:0 0 1px}
.keu-body{padding:18px 20px;display:flex;flex-direction:column;gap:0}
.ff{margin-bottom:13px;display:flex;flex-direction:column}
.ff>label{font-size:12px;font-weight:700;margin-bottom:5px;color:var(--redup)}
.ff2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.hint{font-size:11px;color:#9aa89f;font-weight:500}
.req{color:var(--stempel)}
.keu-foot{display:flex;gap:10px;justify-content:flex-end;padding:12px 20px;border-top:1px solid var(--garis);background:var(--kertas);border-radius:0 0 14px 14px}
.keu-foot .mbtn{flex:0 0 auto;min-width:90px}
.keu-foot .mbtn.ghost{background:var(--stempel-soft);color:var(--stempel);border:1.5px solid #f5c6bb}
.keu-foot .mbtn.ghost:hover{background:#fde5e0}
.mbtn-save{display:inline-flex;align-items:center;gap:7px;background:var(--hutan);color:#fff;font-weight:700;font-size:13px;padding:8px 18px;border-radius:10px;border:none;cursor:pointer;transition:.14s}
.mbtn-save:hover{background:var(--hutan-2)}
.mbtn-save svg{width:13px;height:13px}
.bukti-wrap{display:flex;align-items:flex-start;gap:12px}
.bukti-preview{flex:0 0 52px;width:52px;height:52px;border-radius:9px;background:var(--kertas-2);border:1.5px solid var(--garis);display:flex;align-items:center;justify-content:center;color:var(--redup);overflow:hidden}
.bukti-preview img{width:100%;height:100%;object-fit:cover}
@media(max-width:900px){.kas-summary{grid-template-columns:repeat(2,1fr)}}
@media(max-width:640px){
  .keu-toolbar{flex-direction:column;align-items:stretch;gap:10px}
  .keu-title{font-size:18px;white-space:normal}
  .keu-actions{display:flex;flex-wrap:wrap;gap:8px}
  .grid-wrap{height:calc(100vh - 210px)}
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/devextreme/exceljs.min.js') }}"></script>
<script src="{{ asset('js/devextreme/FileSaver.min.js') }}"></script>
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});
const urlList        = "{{ route('kas.list') }}";
const urlSave        = "{{ route('kas.save') }}";
const urlDelete      = "{{ route('kas.delete') }}";
const urlRingkasan   = "{{ route('kas.ringkasan') }}";
const urlKategori    = "{{ route('kas.kategori') }}";
const urlUploadBukti = "{{ route('kas.upload-bukti') }}";

let grid, focusedRow=null;
// Baca URL param jika datang dari dashboard cashflow
const _urlP  = new URLSearchParams(window.location.search);
let curBulan = _urlP.get('bulan') || "{{ now()->format('Y-m') }}";
let curTipe  = _urlP.get('tipe') || null;
let kategoriMasuk=[], kategoriKeluar=[];

function rupiah(n){return 'Rp '+(n||0).toLocaleString('id-ID');}
function fmtDate(d){if(!d)return '';var dt=new Date(d);return dt.getFullYear()+'-'+String(dt.getMonth()+1).padStart(2,'0')+'-'+String(dt.getDate()).padStart(2,'0');}

$(function(){
  var p=window.__perms||{};
  if(!p.add){document.getElementById('btnMasuk').disabled=true;document.getElementById('btnKeluar').disabled=true;}
  if(!p.export) document.getElementById('btnUnduh').disabled=true;

  // Load kategori
  $.when(
    $.getJSON(urlKategori+'?tipe=masuk'),
    $.getJSON(urlKategori+'?tipe=keluar')
  ).done(function(m,k){
    kategoriMasuk=m[0]; kategoriKeluar=k[0];
    initEditors();
  });

  $("#filterBulan").dxDateBox({
    value:curBulan+'-01', type:'date', displayFormat:'MMM yyyy',
    calendarOptions:{zoomLevel:'year',maxZoomLevel:'year'}, width:130,
    onValueChanged:function(e){curBulan=new Date(e.value).toISOString().slice(0,7);refreshAll();}
  });
  $("#filterTipe").dxSelectBox({
    dataSource:[{id:null,label:'Semua'},{id:'masuk',label:'Kas Masuk'},{id:'keluar',label:'Kas Keluar'}],
    valueExpr:'id', displayExpr:'label', value:curTipe, width:130,
    onValueChanged:function(e){curTipe=e.value;refreshAll();}
  });

  grid = $("#gridKas").dxDataGrid({
    dataSource: new DevExpress.data.CustomStore({
      key:"id",
      load:function(){
        var p={bulan:curBulan};
        if(curTipe) p.tipe=curTipe;
        return $.getJSON(urlList,p);
      }
    }),
    showBorders:false, showColumnLines:true, showRowLines:true,
    rowAlternationEnabled:true, width:"100%", height:"100%",
    columnAutoWidth:true,
    scrolling: { useNative: true, showScrollbar: 'always', mode: 'standard' },
    focusedRowEnabled:true,
    onFocusedRowChanged:e=>{focusedRow=e.row?e.row.data:null;},
    headerFilter:{visible:true},
    paging:{pageSize:50},
    pager:{visible:true,displayMode:"compact",showPageSizeSelector:true,allowedPageSizes:[25,50,"all"],showInfo:true},
    columns:[
      {caption:"No.",width:52,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"tanggal",caption:"Tanggal",width:105,
        cellTemplate:function(c,o){$('<span style="font-size:12.5px">').text(o.value||'—').appendTo(c);}},
      {dataField:"tipe",caption:"Tipe",width:100,
        cellTemplate:function(c,o){
          var cls=o.value==='masuk'?'tipe-masuk':'tipe-keluar';
          var lbl=o.value==='masuk'?'Masuk':'Keluar';
          $('<span class="'+cls+'">').text(lbl).appendTo(c);
        }},
      {dataField:"kategori",caption:"Kategori",minWidth:140,
        cellTemplate:function(c,o){$('<span style="font-weight:600">').text(o.value||'—').appendTo(c);}},
      {dataField:"jumlah",caption:"Jumlah",width:130,alignment:"right",
        cellTemplate:function(c,o){
          var col=o.row.data.tipe==='masuk'?'#2D6A4F':'var(--stempel)';
          var pref=o.row.data.tipe==='masuk'?'+':'-';
          $('<span class="mono-sm" style="color:'+col+'">').text(pref+rupiah(o.value)).appendTo(c);
        }},
      {dataField:"keterangan",caption:"Keterangan",minWidth:180,
        cellTemplate:function(c,o){$('<span style="font-size:12.5px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
      {dataField:"pencatat",caption:"Dicatat",width:120,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
      {caption:"Bukti",width:70,alignment:"center",
        cellTemplate:function(c,o){
          if(!o.row.data.bukti_url){$('<span style="color:var(--garis-2);font-size:11px">—</span>').appendTo(c);return;}
          $('<a class="bukti-link" href="'+o.row.data.bukti_url+'" target="_blank">Lihat</a>').appendTo(c);
        }},
      {caption:"Aksi",width:75,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){
          var d=o.row.data;
          var wrap=$('<div style="display:flex;gap:4px;justify-content:center">');
          $('<button class="wg-ic-btn wg-ic-edit" title="Ubah"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg></button>').on('click',function(){kasEdit(d);}).appendTo(wrap);
          $('<button class="wg-ic-btn wg-ic-del" title="Hapus"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button>').on('click',function(){kasDelete(d);}).appendTo(wrap);
          wrap.appendTo(c);
        }},
    ]
  }).dxDataGrid("instance");

  refreshAll();
});

function refreshAll(){ grid&&grid.refresh(); loadRingkasan(); }

function loadRingkasan(){
  $.getJSON(urlRingkasan,{bulan:curBulan},function(r){
    document.getElementById('kSaldo').textContent  = rupiah(r.saldo_total);
    document.getElementById('kMasuk').textContent  = rupiah(r.masuk_bulan);
    document.getElementById('kKeluar').textContent = rupiah(r.keluar_bulan);
    var net=r.net_bulan;
    document.getElementById('kNet').textContent    = (net>=0?'+':'')+rupiah(net);
    document.getElementById('kNet').style.color    = net>=0?'#2D6A4F':'var(--stempel)';
  });
}

function initEditors(){
  $("#k_tanggal").dxDateBox({type:"date",displayFormat:"dd/MM/yyyy",value:new Date()});
  $("#k_jumlah").dxNumberBox({min:1,format:"#,##0",placeholder:"Jumlah (Rp)"});
  $("#k_keterangan").dxTextBox({placeholder:"Keterangan transaksi..."});
  $("#k_kategori").dxSelectBox({dataSource:[],valueExpr:"id",displayExpr:"nama",placeholder:"— Pilih kategori —"});
  $("#k_bukti_uploader").dxFileUploader({
    uploadUrl:urlUploadBukti,
    uploadHeaders:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
    name:'bukti', uploadMode:'instantly',
    allowedFileExtensions:['.jpg','.jpeg','.png','.pdf'],
    maxFileSize:5242880, multiple:false, accept:'image/*,.pdf',
    labelText:'atau seret file ke sini', selectButtonText:'Pilih File', showFileList:false,
    onUploaded:function(e){
      var r=JSON.parse(e.request.responseText);
      if(r.ok){
        $("#k_bukti_path").val(r.path);
        if(r.url.match(/\.(jpg|jpeg|png)$/i)){
          $("#k_bukti_preview").html('<img src="'+r.url+'">');
        } else {
          $("#k_bukti_preview").html('<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2C5C7A" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>');
        }
      }
    }
  });
}

function kasAdd(tipe){
  $("#k_id").val(''); $("#k_tipe").val(tipe);
  document.getElementById('kasModalTitle').textContent = tipe==='masuk'?'Kas Masuk':'Kas Keluar';
  document.getElementById('kasModalSub').textContent   = tipe==='masuk'?'Catat pemasukan kas RT':'Catat pengeluaran kas RT';
  var icon=document.getElementById('kasModalIcon');
  icon.style.background = tipe==='masuk'?'var(--daun-pucat)':'var(--stempel-soft)';
  icon.style.color      = tipe==='masuk'?'var(--daun)':'var(--stempel)';
  $("#k_tanggal").dxDateBox("instance").option("value",new Date());
  $("#k_jumlah").dxNumberBox("instance").option("value",null);
  $("#k_keterangan").dxTextBox("instance").option("value","");
  $("#k_bukti_path").val('');
  $("#k_bukti_preview").html('<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>');
  var sb=$("#k_kategori").dxSelectBox("instance");
  sb.option("dataSource",tipe==='masuk'?kategoriMasuk:kategoriKeluar);
  sb.option("value",null);
  document.getElementById('kasModal').classList.add('show');
}

function kasEdit(d){
  $("#k_id").val(d.id); $("#k_tipe").val(d.tipe);
  document.getElementById('kasModalTitle').textContent = d.tipe==='masuk'?'Ubah Kas Masuk':'Ubah Kas Keluar';
  document.getElementById('kasModalSub').textContent   = d.keterangan||'';
  $("#k_tanggal").dxDateBox("instance").option("value",d.tanggal_raw?new Date(d.tanggal_raw):new Date());
  $("#k_jumlah").dxNumberBox("instance").option("value",d.jumlah);
  $("#k_keterangan").dxTextBox("instance").option("value",d.keterangan||"");
  $("#k_bukti_path").val('');
  var sb=$("#k_kategori").dxSelectBox("instance");
  sb.option("dataSource",d.tipe==='masuk'?kategoriMasuk:kategoriKeluar);
  sb.option("value",d.kategori_id);
  if(d.bukti_url){
    if(d.bukti_url.match(/\.(jpg|jpeg|png)$/i)){
      $("#k_bukti_preview").html('<img src="'+d.bukti_url+'">');
    }
  }
  document.getElementById('kasModal').classList.add('show');
}

function kasSave(){
  var data={
    id:$("#k_id").val(),
    tipe:$("#k_tipe").val(),
    tanggal:fmtDate($("#k_tanggal").dxDateBox("instance").option("value")),
    kategori_id:$("#k_kategori").dxSelectBox("instance").option("value"),
    jumlah:$("#k_jumlah").dxNumberBox("instance").option("value"),
    keterangan:$("#k_keterangan").dxTextBox("instance").option("value"),
    bukti:$("#k_bukti_path").val()||'',
  };
  if(!data.tanggal){DevExpress.ui.notify("Tanggal wajib diisi","error",2500);return;}
  if(!data.kategori_id){DevExpress.ui.notify("Kategori wajib dipilih","error",2500);return;}
  if(!data.jumlah||data.jumlah<1){DevExpress.ui.notify("Jumlah harus lebih dari 0","error",2500);return;}
  $.ajax({url:urlSave,type:"POST",data:data})
    .done(function(r){DevExpress.ui.notify(r.message,"success",2500);kasClose();refreshAll();})
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);});
}

function kasDelete(d){
  document.getElementById('deleteMsg').textContent=(d.tipe==='masuk'?'Kas Masuk':'Kas Keluar')+' '+rupiah(d.jumlah)+' ('+d.tanggal+') akan dihapus.';
  document.getElementById('deleteConfirmBtn').onclick=function(){
    $.ajax({url:urlDelete,type:"POST",data:{id:d.id}})
      .done(function(r){DevExpress.ui.notify(r.message,"success",2500);deleteClose();refreshAll();})
      .fail(function(){DevExpress.ui.notify("Gagal menghapus","error",3000);});
  };
  document.getElementById('deleteModal').classList.add('show');
}

function exportKas(){
  var wb=new ExcelJS.Workbook();
  var sh=wb.addWorksheet('Kas '+curBulan);
  sh.columns=[
    {header:'Tanggal',key:'tanggal',width:13},{header:'Tipe',key:'tipe',width:10},
    {header:'Kategori',key:'kategori',width:18},{header:'Jumlah',key:'jumlah',width:15},
    {header:'Keterangan',key:'keterangan',width:30},{header:'Dicatat Oleh',key:'pencatat',width:18},
  ];
  sh.getRow(1).font={bold:true,color:{argb:'FFFFFFFF'}};
  sh.getRow(1).fill={type:'pattern',pattern:'solid',fgColor:{argb:'FF2C5C7A'}};
  var p={bulan:curBulan};if(curTipe)p.tipe=curTipe;
  $.getJSON(urlList,p,function(data){
    data.forEach(function(d,i){
      var row=sh.addRow({tanggal:d.tanggal,tipe:d.tipe,kategori:d.kategori,jumlah:d.jumlah,keterangan:d.keterangan,pencatat:d.pencatat});
      if(i%2===0) row.fill={type:'pattern',pattern:'solid',fgColor:{argb:'FFE2ECF1'}};
    });
    wb.xlsx.writeBuffer().then(function(buf){saveAs(new Blob([buf],{type:'application/octet-stream'}),'Kas_'+curBulan+'.xlsx');});
  });
}

function kasClose(){document.getElementById('kasModal').classList.remove('show');}
function deleteClose(){document.getElementById('deleteModal').classList.remove('show');}
document.addEventListener('keydown',function(e){if(e.key==='Escape'){kasClose();deleteClose();}});
</script>
@endpush
