@extends('layouts.app')
@section('judul','Iuran Bulanan')
@section('content')

<div class="keu-toolbar">
  <h2 class="keu-title">Iuran Bulanan</h2>
  <div class="keu-actions">
    <div id="filterPeriode"></div>
    <div id="filterJenis"></div>
    <div id="filterGang"></div>
    <button class="btn btn-generate" id="btnGenerate" type="button" onclick="openGenerate()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      Generate Tagihan
    </button>
    <button class="btn btn-unduh" id="btnUnduh" type="button" onclick="exportIuran()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Unduh
    </button>
    <button class="btn btn-import" id="btnImport" type="button" onclick="openImportTunggakan()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
      Import Tunggakan
    </button>
  </div>
</div>

{{-- Summary Cards --}}
<div class="iuran-summary" id="iuranSummary">
  <div class="sum-card s-total"><div class="sum-lbl">Total Tagihan</div><div class="sum-val" id="sumTotal">Rp 0</div></div>
  <div class="sum-card s-lunas"><div class="sum-lbl">Sudah Bayar</div><div class="sum-val" id="sumLunas">Rp 0</div><div class="sum-sub" id="sumLunasKk">0 KK</div></div>
  <div class="sum-card s-sebagian"><div class="sum-lbl">Sebagian Bayar</div><div class="sum-val" id="sumSebagian">Rp 0</div><div class="sum-sub" id="sumSebagianKk">0 KK</div></div>
  <div class="sum-card s-belum"><div class="sum-lbl">Belum Bayar</div><div class="sum-val" id="sumBelum">Rp 0</div><div class="sum-sub" id="sumBelumKk">0 KK</div></div>
</div>

<div class="grid-wrap" style="height:calc(100vh - 290px)"><div id="gridIuran"></div></div>

{{-- Modal: Input Pembayaran --}}
<div class="modal-overlay" id="bayarModal">
  <div class="modal-card keu-card">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:var(--daun-pucat);color:var(--daun)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
      </div>
      <div><h3 id="bayarTitle">Input Pembayaran</h3><p class="keu-sub" id="bayarSub">—</p></div>
    </div>
    <form id="formBayar" onsubmit="return false" class="keu-body">
      <input type="hidden" id="b_tagihan_id">
      <div class="bayar-info" id="bayarInfo"></div>
      <div class="ff"><label>Jumlah Bayar <span class="req">*</span></label><div id="b_nominal"></div></div>
      <div class="ff2">
        <div class="ff"><label>Tanggal Bayar <span class="req">*</span></label><div id="b_tanggal"></div></div>
        <div class="ff"><label>Metode</label><div id="b_metode"></div></div>
      </div>
      <div class="ff"><label>Keterangan</label><div id="b_keterangan"></div></div>
    </form>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="bayarClose()">Batal</button>
      <button class="mbtn mbtn-save" onclick="bayarSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg> Simpan Pembayaran
      </button>
    </div>
  </div>
</div>

{{-- Modal: Generate Tagihan --}}
<div class="modal-overlay" id="generateModal" onclick="if(event.target===this)generateClose()">
  <div class="modal-card keu-card">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:var(--biru-soft);color:var(--biru)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </div>
      <div><h3>Generate Tagihan</h3><p class="keu-sub">Buat tagihan untuk semua KK aktif</p></div>
    </div>
    <div class="keu-body">
      <div class="import-info" style="margin-bottom:14px">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>Sistem akan membuat tagihan untuk semua KK yang belum memiliki tagihan di periode tersebut.</span>
      </div>
      <div class="ff2">
        <div class="ff"><label>Periode <span class="req">*</span></label><div id="gen_periode"></div></div>
        <div class="ff"><label>Jenis Iuran <span class="req">*</span></label><div id="gen_jenis"></div></div>
      </div>
    </div>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="generateClose()">Batal</button>
      <button class="mbtn mbtn-save" onclick="doGenerate()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg> Generate
      </button>
    </div>
  </div>
</div>

{{-- Modal: Import Tunggakan Historis --}}
<div class="modal-overlay" id="importTunggakanModal" onclick="if(event.target===this)importTunggakanClose()">
  <div class="modal-card keu-card">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:#FEF3C7;color:#92400E">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
      </div>
      <div><h3>Import Tunggakan Historis</h3><p class="keu-sub">Data iuran tahun-tahun sebelumnya</p></div>
    </div>
    <div class="keu-body">
      <div class="import-info">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>Gunakan template Excel. Data yang sudah ada akan diupdate, bukan duplikat. Tandai <strong>keringanan=1</strong> untuk warga yang bayar seikhlasnya.</span>
      </div>
      <a class="btn ghost" style="font-size:12px;padding:7px 13px;margin-bottom:14px;display:inline-flex;gap:7px"
         href="{{ asset('templates/template_import_tunggakan.xlsx') }}" download>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Unduh Template
      </a>
      <div id="importTunggakanUploader"></div>
      <div id="importTunggakanResult" style="display:none;margin-top:12px"></div>
    </div>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="importTunggakanClose()">Tutup</button>
    </div>
  </div>
</div>

{{-- Modal: Keringanan / Catatan Khusus --}}
<div class="modal-overlay" id="keringananModal" onclick="if(event.target===this)keringananClose()">
  <div class="modal-card keu-card" style="max-width:420px">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:#FEF3C7;color:#92400E">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      </div>
      <div><h3>Keringanan / Catatan Khusus</h3><p class="keu-sub" id="keringananSub">—</p></div>
    </div>
    <div class="keu-body">
      <input type="hidden" id="kr_tagihan_id">
      <div class="mf-switch" style="margin-bottom:14px">
        <div id="kr_aktif"></div>
        <div><strong>Berikan Keringanan</strong><span>Warga tidak mampu bayar penuh — bayar seikhlasnya</span></div>
      </div>
      <div class="ff"><label>Catatan Khusus</label><div id="kr_catatan"></div></div>
    </div>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="keringananClose()">Batal</button>
      <button class="mbtn mbtn-save" style="background:#B8860B" onclick="keringananSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg> Simpan
      </button>
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
.btn-generate{background:#6366F1!important;color:#fff!important}.btn-generate:hover{filter:brightness(1.08)}
.btn-unduh{background:var(--biru)!important;color:#fff!important}.btn-unduh:hover{filter:brightness(1.08)}
.btn-import{background:#B8860B!important;color:#fff!important}.btn-import:hover{filter:brightness(1.08)}
.btn[disabled]{opacity:.45;cursor:not-allowed;pointer-events:none}

/* Summary */
.iuran-summary{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:14px}
.sum-card{background:var(--surface);border:1px solid var(--garis);border-radius:12px;padding:14px 16px;position:relative;overflow:hidden}
.sum-card::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px}
.s-total::before{background:var(--biru)}
.s-lunas::before{background:#2D6A4F}
.s-sebagian::before{background:var(--emas)}
.s-belum::before{background:var(--stempel)}
.sum-lbl{font-size:11.5px;color:var(--redup);font-weight:600;margin-bottom:6px}
.sum-val{font-size:20px;font-weight:800;letter-spacing:-.02em;color:var(--tinta)}
.sum-sub{font-size:11px;color:var(--redup);margin-top:3px}

/* Grid */
.grid-wrap{background:var(--surface);border:1px solid var(--garis);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow)}
#gridIuran,.dx-widget{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
#gridIuran .dx-datagrid{border:none;color:var(--tinta)}
#gridIuran .dx-datagrid-headers{background:var(--kertas-2);border-bottom:1px solid var(--garis)}
#gridIuran .dx-datagrid-headers .dx-header-row>td{font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:11px 14px;border:none}
#gridIuran .dx-data-row>td{padding:10px 14px;font-size:13px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis);vertical-align:middle}
#gridIuran .dx-datagrid-rowsview .dx-data-row:hover>td{background:#FBFAF5}
#gridIuran .dx-pager{background:var(--kertas);border-top:1px solid var(--garis);padding:8px 14px}
#gridIuran .dx-toolbar{display:none}

/* Status pill */
.st-lunas{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--daun-pucat);color:#1B5E3F}
.st-lunas::before{content:"";width:6px;height:6px;border-radius:50%;background:#2D6A4F}
.st-sebagian{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--emas-soft);color:#7a5c00}
.st-sebagian::before{content:"";width:6px;height:6px;border-radius:50%;background:var(--emas)}
.st-belum{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--stempel-soft);color:#9A3422}
.st-belum::before{content:"";width:6px;height:6px;border-radius:50%;background:var(--stempel)}
.btn-bayar{padding:4px 12px;border-radius:7px;font-size:12px;font-weight:700;background:var(--hutan);color:#fff;border:none;cursor:pointer;transition:.13s}
.btn-bayar:hover{background:var(--hutan-2)}
.mono-sm{font-family:'IBM Plex Mono',monospace;font-size:12px}

/* Bayar info */
.bayar-info{padding:10px 13px;background:var(--kertas);border:1px solid var(--garis);border-radius:9px;margin-bottom:14px;font-size:13px}
.bayar-info b{color:var(--tinta)}
.ff{margin-bottom:13px;display:flex;flex-direction:column}
.ff>label{font-size:12px;font-weight:700;margin-bottom:5px;color:var(--redup)}
.ff2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.req{color:var(--stempel)}

/* Modal */
.keu-card{max-width:520px;width:100%;padding:0;text-align:left}
.keu-head{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--garis)}
.keu-head-icon{flex:0 0 34px;width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center}
.keu-head-icon svg{width:17px;height:17px}
.keu-head h3{font-size:15px;font-weight:800;margin:0 0 1px}
.keu-sub{font-size:12px;color:var(--redup);margin:0}
.keu-body{padding:18px 20px;display:flex;flex-direction:column;gap:0}
.keu-foot{display:flex;gap:10px;justify-content:flex-end;padding:12px 20px;border-top:1px solid var(--garis);background:var(--kertas);border-radius:0 0 20px 20px}
.keu-foot .mbtn{flex:0 0 auto}
.mbtn-save{display:inline-flex;align-items:center;gap:7px;background:var(--hutan);color:#fff;font-weight:700;font-size:14px;padding:10px 20px;border-radius:10px;border:none;cursor:pointer;transition:.14s}
.mbtn-save:hover{background:var(--hutan-2)}.mbtn-save svg{width:14px;height:14px}
.import-info{display:flex;align-items:flex-start;gap:8px;padding:10px 12px;background:var(--biru-soft);border-radius:8px;font-size:12.5px;color:#1a3d52}
.import-info svg{flex:0 0 15px;margin-top:1px}

@media(max-width:900px){.iuran-summary{grid-template-columns:repeat(2,1fr)}}
@media(max-width:480px){.iuran-summary{grid-template-columns:1fr}}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/devextreme/exceljs.min.js') }}"></script>
<script src="{{ asset('js/devextreme/FileSaver.min.js') }}"></script>
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});

const urlTagihanList = "{{ route('iuran.list') }}";
const urlContext     = "{{ route('iuran.context') }}";
const urlBayar       = "{{ route('iuran.bayar') }}";
const urlGenerate    = "{{ route('iuran.generate') }}";
const urlRekap       = "{{ route('iuran.rekap') }}";
const urlJenisList   = "{{ route('iuran.jenis.list') }}";
const urlGangList    = "{{ route('gang.list') }}";

let grid, jenisList=[], gangList=[];
let curPeriode = "{{ now()->format('Y-m') }}";
let curJenis   = null;
let curGang    = null;

function rupiah(n){ return 'Rp '+(n||0).toLocaleString('id-ID'); }

// ── Init filter bar ────────────────────────────────────────────────────
$(function(){
  var p=window.__perms||{};
  if(!p.add)    document.getElementById('btnGenerate').disabled=true;
  if(!p.export) document.getElementById('btnUnduh').disabled=true;

  // Load context role + lookups
  $.when($.getJSON(urlContext), $.getJSON(urlJenisList), $.getJSON(urlGangList)).done(function(ctx,j,g){
    var roleCtx = ctx[0];
    // Sembunyikan Generate & Import Tunggakan untuk koordinator (tidak boleh generate untuk semua)
    if(!roleCtx.is_admin){
      document.getElementById('btnGenerate').style.display='none';
      document.getElementById('btnImport') && (document.getElementById('btnImport').style.display='none');
    }
    // Jika koordinator, tambahkan info gang di toolbar
    if(roleCtx.is_koordinator && roleCtx.koordinator){
      var badge=$('<span style="font-size:12px;font-weight:700;padding:4px 12px;border-radius:20px;background:var(--hutan);color:#fff;margin-right:4px">').text('Gang: '+roleCtx.koordinator.gang);
      $(document.querySelector('.keu-actions')).prepend(badge);
    }
    jenisList = j[0]; gangList = g[0];

    $("#filterPeriode").dxDateBox({
      value: curPeriode+'-01', type:'date', displayFormat:'MMM yyyy',
      calendarOptions:{zoomLevel:'year',maxZoomLevel:'year'},
      width:130,
      onValueChanged:function(e){
        curPeriode=new Date(e.value).toISOString().slice(0,7);
        refreshAll();
      }
    });
    $("#filterJenis").dxSelectBox({
      dataSource:[{id:null,nama:'Semua Jenis'},...jenisList],
      valueExpr:'id', displayExpr:'nama', value:null, width:150,
      onValueChanged:function(e){curJenis=e.value;refreshAll();}
    });
    $("#filterGang").dxSelectBox({
      dataSource:[{id:null,nama_gang:'Semua Gang'},...gangList],
      valueExpr:'id', displayExpr:'nama_gang', value:null, width:140,
      onValueChanged:function(e){curGang=e.value;refreshAll();}
    });

    initGrid(); initBayarEditors(); initGenerateEditors();
    refreshAll();
  });
});

function refreshAll(){ grid&&grid.refresh(); loadRekap(); }

function loadRekap(){
  var params={periode:curPeriode};
  if(curJenis) params.jenis_iuran_id=curJenis;
  $.getJSON(urlRekap, params, function(r){
    document.getElementById('sumTotal').textContent   = rupiah(r.total_tagihan);
    document.getElementById('sumLunas').textContent   = rupiah(r.total_dibayar);
    document.getElementById('sumLunasKk').textContent = r.lunas+' KK';
    document.getElementById('sumSebagian').textContent = 'Rp '+((r.total_tagihan-r.total_dibayar-r.total_sisa)||0).toLocaleString('id-ID');
    document.getElementById('sumSebagianKk').textContent = r.sebagian+' KK';
    document.getElementById('sumBelum').textContent   = rupiah(r.total_sisa);
    document.getElementById('sumBelumKk').textContent = r.belum+' KK';
  });
}

function initGrid(){
  grid = $("#gridIuran").dxDataGrid({
    dataSource: new DevExpress.data.CustomStore({
      key:"kk_id",
      load:function(){
        var p={periode:curPeriode};
        if(curJenis) p.jenis_iuran_id=curJenis;
        if(curGang)  p.gang_id=curGang;
        return $.getJSON(urlTagihanList, p);
      }
    }),
    showBorders:false, showColumnLines:true, showRowLines:true,
    rowAlternationEnabled:true, width:"100%", height:"100%",
    columnAutoWidth:true,
    headerFilter:{visible:true},
    paging:{pageSize:50},
    pager:{visible:true,displayMode:"compact",showPageSizeSelector:true,allowedPageSizes:[25,50,"all"],showInfo:true},
    columns:[
      {caption:"No.",width:50,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"blok_no",caption:"Blok/No.",width:100,
        cellTemplate:function(c,o){
          if(!o.value){$('<span style="color:var(--garis-2)">—</span>').appendTo(c);return;}
          $('<span style="display:inline-flex;align-items:center;font-size:12px;font-weight:700;padding:2px 10px;border-radius:20px;background:var(--biru-soft);color:#1a3d52">').text(o.value).appendTo(c);
        }},
      {dataField:"kepala_keluarga",caption:"Kepala Keluarga",minWidth:170,
        cellTemplate:function(c,o){$('<span style="font-weight:600">').text(o.value||'—').appendTo(c);}},
      {dataField:"gang",caption:"Gang",width:120,
        cellTemplate:function(c,o){$('<span style="font-size:12.5px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
      {dataField:"nominal",caption:"Tagihan",width:110,alignment:"right",
        cellTemplate:function(c,o){$('<span class="mono-sm">').text(rupiah(o.value)).appendTo(c);}},
      {dataField:"nominal_dibayar",caption:"Dibayar",width:110,alignment:"right",
        cellTemplate:function(c,o){$('<span class="mono-sm" style="color:'+(o.value>0?'#2D6A4F':'var(--redup)')+'">').text(rupiah(o.value)).appendTo(c);}},
      {dataField:"sisa",caption:"Sisa",width:110,alignment:"right",
        cellTemplate:function(c,o){
          if(o.value<=0){$('<span class="mono-sm" style="color:var(--redup)">—</span>').appendTo(c);return;}
          $('<span class="mono-sm" style="color:var(--stempel);font-weight:700">').text(rupiah(o.value)).appendTo(c);
        }},
      {dataField:"status",caption:"Status",width:110,alignment:"center",
        cellTemplate:function(c,o){
          var cls={lunas:'st-lunas',sebagian:'st-sebagian',belum:'st-belum'};
          var lbl={lunas:'Lunas',sebagian:'Sebagian',belum:'Belum'};
          var v=o.value||'belum';
          $('<span class="'+(cls[v]||'st-belum')+'">').text(lbl[v]||v).appendTo(c);
        }},
      {dataField:"tanggal_bayar",caption:"Tgl. Bayar",width:100,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
      {caption:"Aksi",width:130,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){
          var d=o.row.data;
          var wrap=$('<div style="display:flex;gap:5px;justify-content:center;align-items:center">');
          if(d.status==='lunas'){
            $('<span style="font-size:11px;color:#2D6A4F;font-weight:700">✓ Lunas</span>').appendTo(wrap);
          } else if(!d.tagihan_id){
            // Tagihan belum di-generate oleh admin/bendahara
            $('<span style="font-size:11px;color:var(--redup)" title="Tagihan belum dibuat. Minta admin/bendahara untuk generate tagihan periode ini.">Belum ada tagihan</span>').appendTo(wrap);
          } else {
            $('<button class="btn-bayar" type="button">Bayar</button>')
              .on('click',function(){openBayar(d);}).appendTo(wrap);
          }
          // Tombol keringanan (flag khusus)
          if(d.tagihan_id){
            $('<button style="width:26px;height:26px;border-radius:6px;border:none;cursor:pointer;background:'+(d.is_keringanan?'#B8860B':'var(--kertas-2)')+';color:'+(d.is_keringanan?'#fff':'var(--redup)')+';display:inline-flex;align-items:center;justify-content:center" title="Keringanan / Catatan Khusus"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></button>')
              .on('click',function(){openKeringanan(d);}).appendTo(wrap);
          }
          wrap.appendTo(c);
        }},
    ]
  }).dxDataGrid("instance");
}

// ── Bayar ──────────────────────────────────────────────────────────────
function initBayarEditors(){
  $("#b_nominal").dxNumberBox({min:1,showSpinButtons:false,format:"#,##0"});
  $("#b_tanggal").dxDateBox({type:"date",displayFormat:"dd/MM/yyyy",value:new Date()});
  $("#b_keterangan").dxTextBox({placeholder:"Keterangan opsional..."});
  $("#b_metode").dxSelectBox({
    dataSource:["Transfer","Tunai","QRIS","Lainnya"],
    showClearButton:true, placeholder:"— Pilih metode —"
  });
}

function openBayar(d){
  $("#b_tagihan_id").val(d.tagihan_id);
  document.getElementById('bayarTitle').textContent = 'Input Pembayaran';
  document.getElementById('bayarSub').textContent   = d.kepala_keluarga + (d.blok_no?' — '+d.blok_no:'');
  document.getElementById('bayarInfo').innerHTML =
    '<b>Tagihan: </b>'+rupiah(d.nominal)+
    ' &nbsp;|&nbsp; <b>Sudah bayar: </b>'+rupiah(d.nominal_dibayar)+
    ' &nbsp;|&nbsp; <b>Sisa: </b><span style="color:var(--stempel);font-weight:700">'+rupiah(d.sisa)+'</span>';
  $("#b_nominal").dxNumberBox("instance").option("value", d.sisa);
  document.getElementById('bayarModal').classList.add('show');
}

function bayarSave(){
  var data={
    tagihan_id: $("#b_tagihan_id").val(),
    nominal_bayar: $("#b_nominal").dxNumberBox("instance").option("value"),
    tanggal_bayar: formatDate($("#b_tanggal").dxDateBox("instance").option("value")),
    metode: $("#b_metode").dxSelectBox("instance").option("value"),
    keterangan: $("#b_keterangan").dxTextBox("instance").option("value"),
  };
  if(!data.tagihan_id){DevExpress.ui.notify("Data tagihan tidak valid","error",2500);return;}
  if(!data.nominal_bayar||data.nominal_bayar<1){DevExpress.ui.notify("Jumlah bayar harus lebih dari 0","error",2500);return;}
  $.ajax({url:urlBayar,type:"POST",data:data})
    .done(function(r){DevExpress.ui.notify(r.message,"success",2500);bayarClose();refreshAll();})
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);});
}

function bayarClose(){document.getElementById('bayarModal').classList.remove('show');}

// ── Generate ───────────────────────────────────────────────────────────
function initGenerateEditors(){
  $("#gen_periode").dxDateBox({
    value:curPeriode+'-01',type:'date',displayFormat:'MMM yyyy',
    calendarOptions:{zoomLevel:'year',maxZoomLevel:'year'}
  });
  $("#gen_jenis").dxSelectBox({
    dataSource:jenisList, valueExpr:'id', displayExpr:'nama',
    placeholder:'— Pilih jenis iuran —'
  });
}

function openGenerate(){document.getElementById('generateModal').classList.add('show');}
function generateClose(){document.getElementById('generateModal').classList.remove('show');}

function doGenerate(){
  var periodeVal = $("#gen_periode").dxDateBox("instance").option("value");
  var jenisVal   = $("#gen_jenis").dxSelectBox("instance").option("value");
  if(!periodeVal||!jenisVal){DevExpress.ui.notify("Pilih periode dan jenis iuran","error",2500);return;}
  var periode = new Date(periodeVal).toISOString().slice(0,7);
  $.ajax({url:urlGenerate,type:"POST",data:{periode:periode,jenis_iuran_id:jenisVal}})
    .done(function(r){DevExpress.ui.notify(r.message,"success",3500);generateClose();refreshAll();})
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);});
}

// ── Export Excel ───────────────────────────────────────────────────────
function exportIuran(){
  var wb=new ExcelJS.Workbook();
  var sh=wb.addWorksheet('Iuran '+curPeriode);
  sh.columns=[
    {header:'No.',key:'no',width:6},
    {header:'Blok/No.',key:'blok_no',width:12},
    {header:'Kepala Keluarga',key:'kepala',width:25},
    {header:'Gang',key:'gang',width:15},
    {header:'Tagihan',key:'nominal',width:14},
    {header:'Dibayar',key:'dibayar',width:14},
    {header:'Sisa',key:'sisa',width:14},
    {header:'Status',key:'status',width:12},
    {header:'Tgl. Bayar',key:'tgl',width:13},
  ];
  sh.getRow(1).font={bold:true,color:{argb:'FFFFFFFF'}};
  sh.getRow(1).fill={type:'pattern',pattern:'solid',fgColor:{argb:'FF2D6A4F'}};

  var params={periode:curPeriode};
  if(curJenis) params.jenis_iuran_id=curJenis;
  if(curGang)  params.gang_id=curGang;
  $.getJSON(urlTagihanList, params, function(data){
    data.forEach(function(d,i){
      var row=sh.addRow({no:i+1,blok_no:d.blok_no,kepala:d.kepala_keluarga,gang:d.gang,
        nominal:d.nominal,dibayar:d.nominal_dibayar,sisa:d.sisa,status:d.status,tgl:d.tanggal_bayar||''});
      if(i%2===0) row.fill={type:'pattern',pattern:'solid',fgColor:{argb:'FFF2EFE6'}};
    });
    wb.xlsx.writeBuffer().then(function(buf){
      saveAs(new Blob([buf],{type:'application/octet-stream'}),'Iuran_'+curPeriode+'.xlsx');
    });
  });
}

function formatDate(d){
  if(!d) return '';
  var dt=new Date(d);
  return dt.getFullYear()+'-'+String(dt.getMonth()+1).padStart(2,'0')+'-'+String(dt.getDate()).padStart(2,'0');
}

// ── Import Tunggakan ───────────────────────────────────────────────────
const urlImportTunggakan = "{{ route('iuran.import-tunggakan') }}";
const urlKeringanan      = "{{ route('iuran.keringanan') }}";

function openImportTunggakan(){
  document.getElementById('importTunggakanResult').style.display='none';
  document.getElementById('importTunggakanResult').innerHTML='';
  document.getElementById('importTunggakanModal').classList.add('show');

  if(!$("#importTunggakanUploader").data('dx-was-initialized')){
    $("#importTunggakanUploader").dxFileUploader({
      uploadUrl: urlImportTunggakan,
      uploadHeaders:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
      name:'file', uploadMode:'useButtons',
      allowedFileExtensions:['.xlsx'], maxFileSize:5242880,
      selectButtonText:'Pilih File Excel', uploadButtonText:'Mulai Import',
      labelText:'atau seret file .xlsx ke sini', showFileList:false,
      onUploaded:function(e){
        var r=JSON.parse(e.request.responseText);
        var html='';
        if(r.ok){
          html='<div style="padding:10px 12px;background:var(--daun-pucat);border-radius:8px;font-size:13px;color:#14532D"><strong>Berhasil!</strong> '+r.message+'</div>';
          refreshAll();
        } else {
          html='<div style="padding:10px 12px;background:var(--stempel-soft);border-radius:8px;font-size:12.5px;color:#9A3422"><strong>Gagal:</strong> '+(r.message||'Terjadi kesalahan')+'</div>';
        }
        if(r.errors&&r.errors.length){
          html+='<ul style="margin-top:8px;padding-left:16px;font-size:12px;color:#9A3422">';
          r.errors.forEach(function(e){html+='<li>'+e+'</li>';});
          html+='</ul>';
        }
        document.getElementById('importTunggakanResult').innerHTML=html;
        document.getElementById('importTunggakanResult').style.display='block';
      },
      onUploadError:function(){
        document.getElementById('importTunggakanResult').innerHTML='<div style="padding:10px 12px;background:var(--stempel-soft);border-radius:8px;font-size:12.5px;color:#9A3422">Upload gagal. Periksa format file.</div>';
        document.getElementById('importTunggakanResult').style.display='block';
      }
    });
  }
}
function importTunggakanClose(){document.getElementById('importTunggakanModal').classList.remove('show');}

// ── Keringanan ─────────────────────────────────────────────────────────
function openKeringanan(d){
  $("#kr_tagihan_id").val(d.tagihan_id);
  document.getElementById('keringananSub').textContent = d.kepala_keluarga+(d.blok_no?' — '+d.blok_no:'');
  if(!$("#kr_aktif").data('dx-was-initialized')){
    $("#kr_aktif").dxSwitch({value:false});
    $("#kr_catatan").dxTextBox({placeholder:'cth. Warga tidak mampu, bayar seikhlasnya...'});
  }
  $("#kr_aktif").dxSwitch("instance").option("value", d.is_keringanan||false);
  $("#kr_catatan").dxTextBox("instance").option("value", d.catatan_khusus||'');
  document.getElementById('keringananModal').classList.add('show');
}

function keringananSave(){
  var data={
    tagihan_id:    $("#kr_tagihan_id").val(),
    is_keringanan: $("#kr_aktif").dxSwitch("instance").option("value")?1:0,
    catatan_khusus:$("#kr_catatan").dxTextBox("instance").option("value"),
  };
  $.ajax({url:urlKeringanan,type:"POST",data:data})
    .done(function(r){DevExpress.ui.notify(r.message,"success",2500);keringananClose();refreshAll();})
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);});
}
function keringananClose(){document.getElementById('keringananModal').classList.remove('show');}

document.addEventListener('keydown',function(e){
  if(e.key==='Escape'){bayarClose();generateClose();importTunggakanClose();keringananClose();}
});
</script>
@endpush
