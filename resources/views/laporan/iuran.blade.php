@extends('layouts.app')
@section('judul','Laporan Iuran')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;gap:12px;flex-wrap:wrap">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="{{ route('laporan.index') }}" style="color:var(--redup);font-size:13px">← Laporan</a>
    <h2 style="font-size:22px;font-weight:800;letter-spacing:-.02em;margin:0">Laporan Iuran</h2>
  </div>
  <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
    <div id="filterTahun"></div>
    <div id="filterJenis"></div>
    <button class="btn" style="background:var(--biru)!important;color:#fff!important" type="button" onclick="exportExcel()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Unduh Excel
    </button>
  </div>
</div>

{{-- Summary --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px">
  <div style="background:#E8F5E9;border:1px solid #A5D6A7;border-radius:12px;padding:16px 18px">
    <div style="font-size:11px;font-weight:700;color:var(--redup);text-transform:uppercase;margin-bottom:6px">Total Tagihan</div>
    <div style="font-size:20px;font-weight:800;font-family:'IBM Plex Mono',monospace" id="sTotal">Rp 0</div>
  </div>
  <div style="background:var(--biru-soft);border:1px solid #90CAF9;border-radius:12px;padding:16px 18px">
    <div style="font-size:11px;font-weight:700;color:var(--redup);text-transform:uppercase;margin-bottom:6px">Terkumpul</div>
    <div style="font-size:20px;font-weight:800;font-family:'IBM Plex Mono',monospace" id="sDibayar">Rp 0</div>
  </div>
  <div style="background:#FFEBEE;border:1px solid #FFCDD2;border-radius:12px;padding:16px 18px">
    <div style="font-size:11px;font-weight:700;color:var(--redup);text-transform:uppercase;margin-bottom:6px">Tunggakan</div>
    <div style="font-size:20px;font-weight:800;font-family:'IBM Plex Mono',monospace;color:var(--stempel)" id="sTunggakan">Rp 0</div>
  </div>
</div>

{{-- Tabs --}}
<div class="lap-tabs">
  <button class="lap-tab on" onclick="switchTab(this,'tabTunggakan')">Tunggakan</button>
  <button class="lap-tab" onclick="switchTab(this,'tabRekap')">Rekap per Bulan</button>
</div>

<div class="rv-section">
  <div id="tabTunggakan">
    <div id="gridTunggakan"></div>
  </div>
  <div id="tabRekap" style="display:none">
    <div id="gridRekap"></div>
  </div>
</div>

@endsection
@push('styles')
<style>
.content{max-width:none;padding-bottom:32px}
.rv-section{background:var(--surface);border:1px solid var(--garis);border-radius:0 0 14px 14px;overflow:hidden;box-shadow:var(--shadow)}
.lap-tabs{display:flex;gap:0;border:1px solid var(--garis);border-bottom:none;border-radius:12px 12px 0 0;overflow:hidden;margin-top:16px}
.lap-tab{flex:1;padding:10px;font-size:13px;font-weight:700;color:var(--redup);background:var(--kertas-2);border:none;cursor:pointer;transition:.13s;border-right:1px solid var(--garis)}
.lap-tab:last-child{border-right:none}
.lap-tab.on{background:var(--surface);color:var(--hutan)}
.lap-tab:hover:not(.on){background:var(--kertas)}
#gridTunggakan .dx-datagrid,#gridRekap .dx-datagrid{border:none}
#gridTunggakan .dx-datagrid-headers .dx-header-row>td,#gridRekap .dx-datagrid-headers .dx-header-row>td{font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:11px 14px;border:none;background:var(--kertas-2)}
#gridTunggakan .dx-data-row>td,#gridRekap .dx-data-row>td{padding:10px 14px;font-size:13px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis)}
#gridTunggakan .dx-pager,#gridRekap .dx-pager{background:var(--kertas);border-top:1px solid var(--garis);padding:8px 14px}
#gridTunggakan .dx-toolbar,#gridRekap .dx-toolbar{display:none}
@media(max-width:640px){
  .rv-section{overflow:auto}
}
</style>
@endpush
@push('scripts')
<script src="{{ asset('js/devextreme/exceljs.min.js') }}"></script>
<script src="{{ asset('js/devextreme/FileSaver.min.js') }}"></script>
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});
const urlData  = "{{ route('laporan.iuran.data') }}";
const urlJenis = "{{ route('laporan.jenis') }}";
let g1, g2, curTahun={{ now()->year }}, curJenis=null, cached=null;
function rp(n){return 'Rp '+(n||0).toLocaleString('id-ID');}

$(function(){
  $.getJSON(urlJenis,function(jenis){
    var yOpts=[]; for(var y=2020;y<=2030;y++) yOpts.push({id:y,l:y+''});
    $("#filterTahun").dxSelectBox({dataSource:yOpts,valueExpr:'id',displayExpr:'l',value:curTahun,width:100,onValueChanged:function(e){curTahun=e.value;loadData();}});
    $("#filterJenis").dxSelectBox({dataSource:[{id:null,nama:'Semua Jenis'},...jenis],valueExpr:'id',displayExpr:'nama',value:null,width:160,onValueChanged:function(e){curJenis=e.value;loadData();}});
  });

  g1=$("#gridTunggakan").dxDataGrid({
    dataSource:[],showBorders:false,showColumnLines:true,showRowLines:true,rowAlternationEnabled:true,width:"100%",
    columnAutoWidth:true,
    scrolling: { useNative: true, showScrollbar: 'always', mode: 'standard' },
    filterRow:{visible:false},paging:{pageSize:50},
    pager:{visible:true,displayMode:"compact",showPageSizeSelector:true,allowedPageSizes:[25,50,"all"],showInfo:true},
    columns:[
      {caption:"No.",width:52,alignment:"center",allowFiltering:false,allowSorting:false,cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"kepala_keluarga",caption:"Kepala Keluarga",minWidth:160,cellTemplate:function(c,o){$('<span style="font-weight:600">').text(o.value||'—').appendTo(c);}},
      {dataField:"blok_no",caption:"Blok/Rumah",width:100,cellTemplate:function(c,o){if(!o.value){$('<span style="color:var(--garis-2)">—</span>').appendTo(c);return;}$('<span style="display:inline-flex;font-size:11.5px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--biru-soft);color:#1a3d52">').text(o.value).appendTo(c);}},
      {dataField:"jenis_iuran",caption:"Jenis",width:120,cellTemplate:function(c,o){$('<span style="font-size:12.5px">').text(o.value||'—').appendTo(c);}},
      {dataField:"periode",caption:"Periode",width:90,alignment:"center"},
      {dataField:"nominal",caption:"Tagihan",width:120,alignment:"right",cellTemplate:function(c,o){$('<span style="font-family:\'IBM Plex Mono\',monospace;font-size:12.5px;font-weight:600">').text(rp(o.value)).appendTo(c);}},
      {dataField:"nominal_dibayar",caption:"Dibayar",width:120,alignment:"right",cellTemplate:function(c,o){$('<span style="font-family:\'IBM Plex Mono\',monospace;font-size:12.5px;color:#2D6A4F">').text(rp(o.value)).appendTo(c);}},
      {dataField:"sisa",caption:"Sisa",width:120,alignment:"right",cellTemplate:function(c,o){$('<span style="font-family:\'IBM Plex Mono\',monospace;font-size:12.5px;font-weight:700;color:var(--stempel)">').text(rp(o.value)).appendTo(c);}},
      {dataField:"status",caption:"Status",width:100,alignment:"center",cellTemplate:function(c,o){
        var cls={belum:'var(--stempel-soft)',sebagian:'var(--emas-soft)'};
        var col={belum:'#9A3422',sebagian:'#7a5c00'};
        var lbl={belum:'Belum',sebagian:'Sebagian'};
        $('<span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;background:'+cls[o.value]+';color:'+col[o.value]+'">').text(lbl[o.value]||o.value).appendTo(c);
      }},
    ]
  }).dxDataGrid("instance");

  g2=$("#gridRekap").dxDataGrid({
    dataSource:[],showBorders:false,showColumnLines:true,showRowLines:true,rowAlternationEnabled:true,width:"100%",
    columnAutoWidth:true,
    scrolling: { useNative: true, showScrollbar: 'always', mode: 'standard' },
    filterRow:{visible:false},paging:{pageSize:50},
    pager:{visible:true,displayMode:"compact",showPageSizeSelector:true,allowedPageSizes:[25,50,"all"],showInfo:true},
    columns:[
      {dataField:"bulan",caption:"Bulan",width:90},
      {dataField:"status",caption:"Status",width:100,cellTemplate:function(c,o){$('<span style="font-size:12.5px;font-weight:600">').text(o.value).appendTo(c);}},
      {dataField:"jumlah_kk",caption:"Jumlah KK",width:100,alignment:"center"},
      {dataField:"total_tagihan",caption:"Total Tagihan",width:140,alignment:"right",cellTemplate:function(c,o){$('<span style="font-family:\'IBM Plex Mono\',monospace;font-size:12.5px">').text(rp(o.value)).appendTo(c);}},
      {dataField:"total_dibayar",caption:"Total Dibayar",width:140,alignment:"right",cellTemplate:function(c,o){$('<span style="font-family:\'IBM Plex Mono\',monospace;font-size:12.5px;color:#2D6A4F">').text(rp(o.value)).appendTo(c);}},
    ]
  }).dxDataGrid("instance");

  loadData();
});

function loadData(){
  var p={tahun:curTahun};if(curJenis)p.jenis_iuran_id=curJenis;
  $.getJSON(urlData,p,function(d){
    cached=d;
    document.getElementById('sTotal').textContent     = rp(d.total_tagihan);
    document.getElementById('sDibayar').textContent   = rp(d.total_dibayar);
    document.getElementById('sTunggakan').textContent = rp(d.total_tunggakan);
    g1.option("dataSource",d.tunggakan);
    g2.option("dataSource",d.data.map(function(r){return{bulan:r.bulan,status:r.status,jumlah_kk:r.jumlah_kk,total_tagihan:r.total,total_dibayar:r.dibayar};}));
  });
}

function switchTab(btn,tabId){
  document.querySelectorAll('.lap-tab').forEach(function(b){b.classList.remove('on');});
  btn.classList.add('on');
  ['tabTunggakan','tabRekap'].forEach(function(id){
    document.getElementById(id).style.display=id===tabId?'block':'none';
  });
}

function exportExcel(){
  if(!cached)return;
  var wb=new ExcelJS.Workbook();
  var sh1=wb.addWorksheet('Tunggakan');
  sh1.columns=[{header:'Kepala KK',key:'k',width:25},{header:'Blok',key:'b',width:12},{header:'Jenis',key:'j',width:15},{header:'Periode',key:'p',width:10},{header:'Tagihan',key:'t',width:14},{header:'Dibayar',key:'d',width:14},{header:'Sisa',key:'s',width:14},{header:'Status',key:'st',width:10}];
  sh1.getRow(1).font={bold:true,color:{argb:'FFFFFFFF'}};sh1.getRow(1).fill={type:'pattern',pattern:'solid',fgColor:{argb:'FF14532D'}};
  cached.tunggakan.forEach(function(r){sh1.addRow({k:r.kepala_keluarga,b:r.blok_no,j:r.jenis_iuran,p:r.periode,t:r.nominal,d:r.nominal_dibayar,s:r.sisa,st:r.status});});
  wb.xlsx.writeBuffer().then(function(buf){saveAs(new Blob([buf],{type:'application/octet-stream'}),'LaporanIuran_'+curTahun+'.xlsx');});
}
</script>
@endpush
