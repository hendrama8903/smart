@extends('layouts.app')
@section('judul','Laporan Kas RT')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;gap:12px;flex-wrap:wrap">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="{{ route('laporan.index') }}" style="color:var(--redup);font-size:13px">← Laporan</a>
    <h2 style="font-size:22px;font-weight:800;letter-spacing:-.02em;margin:0">Laporan Kas RT</h2>
  </div>
  <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
    <div id="filterTahun"></div>
    <div id="filterTipe"></div>
    <button class="btn" style="background:var(--biru)!important;color:#fff!important" type="button" onclick="exportExcel()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Unduh Excel
    </button>
  </div>
</div>

{{-- Summary --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px">
  <div style="background:#E8F5E9;border:1px solid #A5D6A7;border-radius:12px;padding:16px 18px">
    <div style="font-size:11px;font-weight:700;color:var(--redup);text-transform:uppercase;margin-bottom:6px">Total Pendapatan</div>
    <div style="font-size:22px;font-weight:800;font-family:'IBM Plex Mono',monospace;color:var(--tinta)" id="sTotalMasuk">Rp 0</div>
  </div>
  <div style="background:#FFEBEE;border:1px solid #FFCDD2;border-radius:12px;padding:16px 18px">
    <div style="font-size:11px;font-weight:700;color:var(--redup);text-transform:uppercase;margin-bottom:6px">Total Pengeluaran</div>
    <div style="font-size:22px;font-weight:800;font-family:'IBM Plex Mono',monospace;color:var(--tinta)" id="sTotalKeluar">Rp 0</div>
  </div>
  <div style="background:var(--biru-soft);border:1px solid #90CAF9;border-radius:12px;padding:16px 18px">
    <div style="font-size:11px;font-weight:700;color:var(--redup);text-transform:uppercase;margin-bottom:6px">Saldo Akhir</div>
    <div style="font-size:22px;font-weight:800;font-family:'IBM Plex Mono',monospace;color:var(--tinta)" id="sSaldo">Rp 0</div>
  </div>
</div>

<div style="background:var(--surface);border:1px solid var(--garis);border-radius:14px;overflow:hidden;box-shadow:var(--shadow);height:calc(100vh - 290px)">
  <div id="gridKasLap"></div>
</div>

@endsection
@push('scripts')
<script src="{{ asset('js/devextreme/exceljs.min.js') }}"></script>
<script src="{{ asset('js/devextreme/FileSaver.min.js') }}"></script>
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});
const urlData = "{{ route('laporan.kas.data') }}";
let grid, curTahun={{ now()->year }}, curTipe=null, cached=null;
function rp(n){return 'Rp '+(n||0).toLocaleString('id-ID');}

$(function(){
  var yOpts=[]; for(var y=2020;y<=2030;y++) yOpts.push({id:y,l:y+''});
  $("#filterTahun").dxSelectBox({dataSource:yOpts,valueExpr:'id',displayExpr:'l',value:curTahun,width:100,
    onValueChanged:function(e){curTahun=e.value;loadData();}});
  $("#filterTipe").dxSelectBox({
    dataSource:[{id:null,l:'Semua'},{id:'masuk',l:'Pendapatan'},{id:'keluar',l:'Pengeluaran'}],
    valueExpr:'id',displayExpr:'l',value:null,width:130,
    onValueChanged:function(e){curTipe=e.value;loadData();}});

  grid=$("#gridKasLap").dxDataGrid({
    dataSource:[],showBorders:false,showColumnLines:true,showRowLines:true,
    rowAlternationEnabled:true,width:"100%",height:"100%",columnAutoWidth:true,
    filterRow:{visible:false},paging:{pageSize:100},
    pager:{visible:true,displayMode:"compact",showPageSizeSelector:true,allowedPageSizes:[50,100,"all"],showInfo:true},
    columns:[
      {caption:"No.",width:55,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"tanggal",caption:"Tanggal",width:105},
      {dataField:"tipe",caption:"Tipe",width:110,
        cellTemplate:function(c,o){
          $('<span style="display:inline-flex;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;background:'+(o.value==='masuk'?'var(--daun-pucat)':'var(--stempel-soft)')+';color:'+(o.value==='masuk'?'#14532D':'#9A3422')+'">').text(o.value==='masuk'?'Pendapatan':'Pengeluaran').appendTo(c);
        }},
      {dataField:"kategori",caption:"Kategori",minWidth:140,cellTemplate:function(c,o){$('<span style="font-weight:600">').text(o.value||'—').appendTo(c);}},
      {dataField:"keterangan",caption:"Keterangan",minWidth:180,cellTemplate:function(c,o){$('<span style="font-size:12.5px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
      {dataField:"jumlah",caption:"Jumlah",width:140,alignment:"right",
        cellTemplate:function(c,o){
          var col=o.row.data.tipe==='masuk'?'#2D6A4F':'var(--stempel)';
          $('<span style="font-family:\'IBM Plex Mono\',monospace;font-size:13px;font-weight:600;color:'+col+'">').text((o.row.data.tipe==='masuk'?'+':'-')+rp(o.value)).appendTo(c);
        }},
      {dataField:"pencatat",caption:"Dicatat",width:130,cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
    ]
  }).dxDataGrid("instance");
  loadData();
});

function loadData(){
  var p={tahun:curTahun}; if(curTipe) p.tipe=curTipe;
  $.getJSON(urlData,p,function(d){
    cached=d;
    document.getElementById('sTotalMasuk').textContent  = rp(d.total_masuk);
    document.getElementById('sTotalKeluar').textContent = rp(d.total_keluar);
    var s=d.total_masuk-d.total_keluar;
    document.getElementById('sSaldo').textContent = rp(Math.abs(s));
    document.getElementById('sSaldo').style.color = s>=0?'#2D6A4F':'var(--stempel)';
    grid.option("dataSource",d.rows);
  });
}

function exportExcel(){
  if(!cached)return;
  var wb=new ExcelJS.Workbook(); var sh=wb.addWorksheet('Kas RT '+curTahun);
  sh.columns=[{header:'Tanggal',key:'tanggal',width:13},{header:'Tipe',key:'tipe',width:13},{header:'Kategori',key:'kategori',width:18},{header:'Keterangan',key:'keterangan',width:30},{header:'Jumlah',key:'jumlah',width:16},{header:'Dicatat',key:'pencatat',width:18}];
  sh.getRow(1).font={bold:true,color:{argb:'FFFFFFFF'}}; sh.getRow(1).fill={type:'pattern',pattern:'solid',fgColor:{argb:'FF2C5C7A'}};
  cached.rows.forEach(function(d,i){var row=sh.addRow(d);if(i%2===0)row.fill={type:'pattern',pattern:'solid',fgColor:{argb:'FFE2ECF1'}};});
  wb.xlsx.writeBuffer().then(function(buf){saveAs(new Blob([buf],{type:'application/octet-stream'}),'LaporanKas_'+curTahun+'.xlsx');});
}
</script>
@endpush
