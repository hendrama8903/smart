@extends('layouts.app')
@section('judul','Realisasi vs Rencana')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;gap:12px;flex-wrap:wrap">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="{{ route('laporan.index') }}" style="color:var(--redup);font-size:13px">← Laporan</a>
    <h2 style="font-size:22px;font-weight:800;letter-spacing:-.02em;margin:0">Realisasi vs Rencana</h2>
  </div>
  <div style="display:flex;gap:10px;align-items:center">
    <label style="font-size:11px;font-weight:700;color:var(--redup);letter-spacing:.06em;text-transform:uppercase">Tahun</label>
    <div id="filterTahun"></div>
    <button class="btn btn-unduh" type="button" onclick="exportExcel()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Unduh Excel
    </button>
  </div>
</div>

{{-- Summary Cards --}}
<div class="rv-summary">
  <div class="rv-card rv-green">
    <div class="rv-lbl">Rencana Pendapatan</div>
    <div class="rv-val" id="sRencanaMasuk">Rp 0</div>
  </div>
  <div class="rv-card rv-green2">
    <div class="rv-lbl">Realisasi Pendapatan</div>
    <div class="rv-val" id="sRealisasiMasuk">Rp 0</div>
    <div class="rv-pct" id="sPctMasuk">0%</div>
  </div>
  <div class="rv-card rv-red">
    <div class="rv-lbl">Rencana Pengeluaran</div>
    <div class="rv-val" id="sRencanaKeluar">Rp 0</div>
  </div>
  <div class="rv-card rv-red2">
    <div class="rv-lbl">Realisasi Pengeluaran</div>
    <div class="rv-val" id="sRealisasiKeluar">Rp 0</div>
    <div class="rv-pct" id="sPctKeluar">0%</div>
  </div>
  <div class="rv-card rv-blue">
    <div class="rv-lbl">Surplus Rencana</div>
    <div class="rv-val" id="sSaldoRencana">Rp 0</div>
  </div>
  <div class="rv-card rv-blue2">
    <div class="rv-lbl">Surplus Realisasi</div>
    <div class="rv-val" id="sSaldoRealisasi">Rp 0</div>
  </div>
</div>

{{-- Tabel Rincian --}}
<div class="rv-section">
  <div class="rv-section-head">Rincian per Pos Anggaran</div>
  <div id="gridRealisasi"></div>
</div>

{{-- Cashflow Bulanan --}}
<div class="rv-section" style="margin-top:16px">
  <div class="rv-section-head">Cashflow Bulanan {{ now()->year }}</div>
  <div class="rv-chart" id="chartCashflow"></div>
  <div class="rv-chart-legend">
    <span><i class="rv-dot rv-dot-masuk"></i> Pendapatan</span>
    <span><i class="rv-dot rv-dot-keluar"></i> Pengeluaran</span>
  </div>
</div>

@endsection

@push('styles')
<style>
.content{max-width:none;padding-bottom:32px}
.btn-unduh{background:var(--biru)!important;color:#fff!important}.btn-unduh:hover{filter:brightness(1.08)}

/* Summary */
.rv-summary{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:20px}
.rv-card{border-radius:12px;padding:14px 16px;border:1px solid var(--garis)}
.rv-card::before{display:none}
.rv-green {background:#E8F5E9;border-color:#A5D6A7}
.rv-green2{background:#C8E6C9;border-color:#81C784}
.rv-red   {background:#FFEBEE;border-color:#FFCDD2}
.rv-red2  {background:#FFCDD2;border-color:#EF9A9A}
.rv-blue  {background:var(--biru-soft);border-color:#90CAF9}
.rv-blue2 {background:#BBDEFB;border-color:#64B5F6}
.rv-lbl{font-size:10.5px;font-weight:700;color:var(--redup);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px}
.rv-val{font-size:16px;font-weight:800;color:var(--tinta);font-family:'IBM Plex Mono',monospace}
.rv-pct{font-size:11.5px;font-weight:700;color:var(--redup);margin-top:3px}

/* Table */
.rv-section{background:var(--surface);border:1px solid var(--garis);border-radius:14px;overflow:auto;box-shadow:var(--shadow)}
.rv-section-head{padding:13px 18px;border-bottom:1px solid var(--garis);font-size:13px;font-weight:700;background:var(--kertas-2)}
#gridRealisasi,.dx-widget{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
#gridRealisasi .dx-datagrid{border:none}
#gridRealisasi .dx-datagrid-headers .dx-header-row>td{font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:11px 14px;border:none;background:var(--kertas-2)}
#gridRealisasi .dx-data-row>td{padding:10px 14px;font-size:13px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis);vertical-align:middle}
#gridRealisasi .dx-datagrid-rowsview .dx-data-row:hover>td{background:#FBFAF5}
#gridRealisasi .dx-pager{display:none}
#gridRealisasi .dx-toolbar{display:none}

/* Chart */
.rv-chart{padding:20px 16px 0;height:200px;display:flex;align-items:flex-end;gap:6px;overflow-x:auto}
.rv-cf-col{display:flex;flex-direction:column;align-items:center;gap:5px;min-width:60px;flex:1}
.rv-cf-bars{display:flex;align-items:flex-end;gap:3px;height:160px;width:100%}
.rv-cf-bar{flex:1;border-radius:4px 4px 0 0;min-height:2px;transition:height .4s}
.rv-cf-masuk{background:#2D6A4F}
.rv-cf-keluar{background:var(--stempel)}
.rv-cf-lbl{font-size:11px;color:var(--redup);font-weight:600;white-space:nowrap}
.rv-chart-legend{display:flex;gap:16px;padding:10px 20px 14px;font-size:12px;font-weight:600;color:var(--redup)}
.rv-chart-legend span{display:flex;align-items:center;gap:5px}
.rv-dot{display:inline-block;width:10px;height:10px;border-radius:3px}
.rv-dot-masuk{background:#2D6A4F}.rv-dot-keluar{background:var(--stempel)}

.mono-sm{font-family:'IBM Plex Mono',monospace;font-size:12.5px;font-weight:600}
.tipe-masuk{display:inline-flex;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;background:var(--daun-pucat);color:#14532D}
.tipe-keluar{display:inline-flex;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;background:var(--stempel-soft);color:#9A3422}
@media(max-width:900px){.rv-summary{grid-template-columns:repeat(2,1fr)}}
@media(max-width:640px){
  .rv-summary{grid-template-columns:1fr 1fr}
  .rv-section{overflow:auto}
  .btn-unduh{flex-wrap:wrap}
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/devextreme/exceljs.min.js') }}"></script>
<script src="{{ asset('js/devextreme/FileSaver.min.js') }}"></script>
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});
const urlData = "{{ route('laporan.realisasi.data') }}";

let grid, curTahun={{ now()->year }}, cachedData=null;
function rp(n){ return 'Rp '+(n||0).toLocaleString('id-ID'); }
function pct(p){ return p+'%'; }

$(function(){
  var tahunOpts=[];
  for(var y=2020;y<=2030;y++) tahunOpts.push({id:y,l:y+''});
  $("#filterTahun").dxSelectBox({
    dataSource:tahunOpts, valueExpr:'id', displayExpr:'l', value:curTahun, width:100,
    onValueChanged:function(e){curTahun=e.value;loadData();}
  });

  grid = $("#gridRealisasi").dxDataGrid({
    dataSource:[],
    showBorders:false, showColumnLines:true, showRowLines:true,
    rowAlternationEnabled:true, width:"100%",
    columnAutoWidth:false,
    scrolling: { useNative: true, showScrollbar: 'always', mode: 'standard' },
    filterRow:{visible:false},
    paging:{enabled:false},
    columns:[
      {caption:"No.",width:52,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"tipe",caption:"Tipe",width:110,
        cellTemplate:function(c,o){
          var cls=o.value==='masuk'?'tipe-masuk':'tipe-keluar';
          var lbl=o.value==='masuk'?'Pendapatan':'Pengeluaran';
          $('<span class="'+cls+'">').text(lbl).appendTo(c);
        }},
      {dataField:"nama_pos",caption:"Pos Anggaran",minWidth:180,
        cellTemplate:function(c,o){$('<span style="font-weight:600">').text(o.value).appendTo(c);}},
      {dataField:"rencana",caption:"Rencana",width:140,alignment:"right",
        cellTemplate:function(c,o){$('<span class="mono-sm">').text(rp(o.value)).appendTo(c);}},
      {dataField:"realisasi",caption:"Realisasi",width:140,alignment:"right",
        cellTemplate:function(c,o){$('<span class="mono-sm" style="color:'+(o.value>=o.row.data.rencana?'#2D6A4F':'var(--stempel)')+'">').text(rp(o.value)).appendTo(c);}},
      {dataField:"selisih",caption:"Selisih",width:130,alignment:"right",
        cellTemplate:function(c,o){
          var v=o.value; var col=v>=0?'#2D6A4F':'var(--stempel)';
          $('<span class="mono-sm" style="color:'+col+';font-weight:700">').text((v>=0?'+':'')+rp(v)).appendTo(c);
        }},
      {dataField:"pct",caption:"Realisasi %",width:110,alignment:"center",
        cellTemplate:function(c,o){
          var v=o.value; var col=v>=100?'#2D6A4F':v>=70?'var(--emas)':'var(--stempel)';
          $('<span style="font-size:13px;font-weight:800;color:'+col+'">').text(v+'%').appendTo(c);
        }},
    ]
  }).dxDataGrid("instance");

  loadData();
});

function loadData(){
  $.getJSON(urlData,{tahun:curTahun},function(d){
    cachedData=d;
    // Update cards
    document.getElementById('sRencanaMasuk').textContent    = rp(d.total_rencana_masuk);
    document.getElementById('sRealisasiMasuk').textContent  = rp(d.total_realisasi_masuk);
    var pctM = d.total_rencana_masuk>0?Math.round(d.total_realisasi_masuk/d.total_rencana_masuk*100):0;
    document.getElementById('sPctMasuk').textContent        = pctM+'% dari rencana';
    document.getElementById('sRencanaKeluar').textContent   = rp(d.total_rencana_keluar);
    document.getElementById('sRealisasiKeluar').textContent = rp(d.total_realisasi_keluar);
    var pctK = d.total_rencana_keluar>0?Math.round(d.total_realisasi_keluar/d.total_rencana_keluar*100):0;
    document.getElementById('sPctKeluar').textContent       = pctK+'% dari rencana';
    var sR=d.saldo_rencana, sA=d.saldo_realisasi;
    document.getElementById('sSaldoRencana').textContent    = rp(Math.abs(sR));
    document.getElementById('sSaldoRealisasi').textContent  = rp(Math.abs(sA));
    document.getElementById('sSaldoRealisasi').style.color  = sA>=0?'#2D6A4F':'var(--stempel)';

    // Update grid
    grid.option("dataSource", d.rows);

    // Render chart
    renderChart(d.cashflow_bulan);
  });
}

function renderChart(data){
  var chart=document.getElementById('chartCashflow');
  chart.innerHTML='';
  var maxVal=1;
  data.forEach(function(cf){var v=Math.max(cf.masuk,cf.keluar);if(v>maxVal)maxVal=v;});
  data.forEach(function(cf){
    var col=document.createElement('div'); col.className='rv-cf-col';
    var bars=document.createElement('div'); bars.className='rv-cf-bars';
    var bM=document.createElement('div'); bM.className='rv-cf-bar rv-cf-masuk';
    bM.style.height=Math.round(cf.masuk/maxVal*100)+'%';
    bM.title='Pendapatan: '+rp(cf.masuk);
    var bK=document.createElement('div'); bK.className='rv-cf-bar rv-cf-keluar';
    bK.style.height=Math.round(cf.keluar/maxVal*100)+'%';
    bK.title='Pengeluaran: '+rp(cf.keluar);
    bars.appendChild(bM); bars.appendChild(bK);
    var lbl=document.createElement('div'); lbl.className='rv-cf-lbl'; lbl.textContent=cf.label;
    col.appendChild(bars); col.appendChild(lbl);
    chart.appendChild(col);
  });
}

function exportExcel(){
  if(!cachedData){return;}
  var wb=new ExcelJS.Workbook();
  var sh=wb.addWorksheet('Realisasi vs Rencana '+curTahun);
  sh.columns=[
    {header:'Tipe',key:'tipe',width:14},{header:'Pos Anggaran',key:'nama_pos',width:30},
    {header:'Rencana',key:'rencana',width:16},{header:'Realisasi',key:'realisasi',width:16},
    {header:'Selisih',key:'selisih',width:16},{header:'% Realisasi',key:'pct',width:12},
  ];
  sh.getRow(1).font={bold:true,color:{argb:'FFFFFFFF'}};
  sh.getRow(1).fill={type:'pattern',pattern:'solid',fgColor:{argb:'FF14532D'}};
  cachedData.rows.forEach(function(d,i){
    var row=sh.addRow({tipe:d.tipe==='masuk'?'Pendapatan':'Pengeluaran',nama_pos:d.nama_pos,rencana:d.rencana,realisasi:d.realisasi,selisih:d.selisih,pct:d.pct+'%'});
    if(i%2===0) row.fill={type:'pattern',pattern:'solid',fgColor:{argb:'FFE8F5E9'}};
  });
  wb.xlsx.writeBuffer().then(function(buf){
    saveAs(new Blob([buf],{type:'application/octet-stream'}),'RealisasiVsRencana_'+curTahun+'.xlsx');
  });
}
</script>
@endpush
