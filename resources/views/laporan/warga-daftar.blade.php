@extends('layouts.app')
@section('judul','Daftar Warga Lengkap')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:12px">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="{{ route('laporan.warga.index') }}" style="color:var(--redup);font-size:13px">← Laporan Warga</a>
    <h2 style="font-size:22px;font-weight:800;letter-spacing:-.02em;margin:0">Daftar Warga Lengkap</h2>
  </div>
  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
    <div id="filterGang"></div>
    <div id="filterStatusWarga"></div>
    <div id="filterStatusTinggal"></div>
    <button class="btn" style="background:var(--biru)!important;color:#fff!important" type="button" onclick="exportExcel()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Unduh Excel
    </button>
  </div>
</div>

<div style="background:var(--surface);border:1px solid var(--garis);border-radius:14px;overflow:auto;box-shadow:var(--shadow);height:calc(100vh - 160px)">
  <div id="gridDaftarWarga"></div>
</div>

@endsection
@push('styles')
<style>
.content{max-width:none;padding-bottom:0}
@media(max-width:640px){
  .wdaftar-toolbar{flex-direction:column;align-items:stretch;gap:10px}
  .wdaftar-title{font-size:18px;white-space:normal}
  .wdaftar-actions{display:flex;flex-wrap:wrap;gap:8px}
}
</style>
@endpush
@push('scripts')
<script src="{{ asset('js/devextreme/exceljs.min.js') }}"></script>
<script src="{{ asset('js/devextreme/FileSaver.min.js') }}"></script>
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});
const urlData     = "{{ route('laporan.warga.daftar.data') }}";
const urlGangList = "{{ route('gang.lookup') }}";

let grid, curGang=null, curSW='aktif', curST=null, cached=[];

$(function(){
  $.getJSON(urlGangList, function(gangs){
    $("#filterGang").dxSelectBox({
      dataSource:[{id:null,nama_gang:'Semua Gang'},...gangs],
      valueExpr:'id',displayExpr:'nama_gang',value:null,width:160,
      onValueChanged:function(e){curGang=e.value;loadData();}
    });
  });
  $("#filterStatusWarga").dxSelectBox({
    dataSource:[{id:'aktif',l:'Aktif'},{id:'pindah',l:'Pindah'},{id:'meninggal',l:'Meninggal'},{id:null,l:'Semua'}],
    valueExpr:'id',displayExpr:'l',value:'aktif',width:130,
    onValueChanged:function(e){curSW=e.value;loadData();}
  });
  $("#filterStatusTinggal").dxSelectBox({
    dataSource:[{id:null,l:'Semua Tinggal'},{id:'tetap',l:'Tetap'},{id:'kontrak',l:'Kontrak'},{id:'kos',l:'Kos'},{id:'numpang',l:'Numpang'}],
    valueExpr:'id',displayExpr:'l',value:null,width:150,
    onValueChanged:function(e){curST=e.value;loadData();}
  });

  grid=$("#gridDaftarWarga").dxDataGrid({
    dataSource:[],showBorders:false,showColumnLines:true,showRowLines:true,
    rowAlternationEnabled:true,width:"100%",height:"100%",columnAutoWidth:false,
    scrolling: { useNative: true, showScrollbar: 'always', mode: 'standard' },
    filterRow:{visible:false},
    paging:{pageSize:100},
    pager:{visible:true,displayMode:"compact",showPageSizeSelector:true,allowedPageSizes:[50,100,"all"],showInfo:true},
    columns:[
      {caption:"No.",width:52,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"nama",caption:"Nama",minWidth:160,cellTemplate:function(c,o){
        var d=$('<div>');$('<div style="font-weight:600">').text(o.value||'—').appendTo(d);
        $('<div style="font-family:\'IBM Plex Mono\',monospace;font-size:11px;color:var(--redup)">').text(o.row.data.nik||'').appendTo(d);d.appendTo(c);}},
      {dataField:"jenis_kelamin",caption:"L/P",width:55,alignment:"center",
        cellTemplate:function(c,o){$('<span style="font-weight:700;color:'+(o.value==='L'?'var(--biru)':'var(--stempel)')+'">').text(o.value||'—').appendTo(c);}},
      {dataField:"umur",caption:"Umur",width:65,alignment:"center",
        cellTemplate:function(c,o){$('<span>').text(o.value!=null?o.value+' th':'—').appendTo(c);}},
      {dataField:"hubungan",caption:"Hubungan",width:130,
        cellTemplate:function(c,o){
          var map={kepala_keluarga:'Kepala KK',istri:'Istri',suami:'Suami',anak:'Anak',orang_tua:'Orang Tua',lainnya:'Lainnya'};
          $('<span style="font-size:12.5px;color:var(--redup)">').text(map[o.value]||o.value||'—').appendTo(c);}},
      {dataField:"gang",caption:"Gang",width:130,cellTemplate:function(c,o){$('<span style="font-size:12.5px">').text(o.value||'—').appendTo(c);}},
      {dataField:"blok_no",caption:"Blok/Rumah",width:100,
        cellTemplate:function(c,o){if(!o.value){$('<span style="color:var(--garis-2)">—</span>').appendTo(c);return;}
          $('<span style="display:inline-flex;font-size:11.5px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--biru-soft);color:#1a3d52">').text(o.value).appendTo(c);}},
      {dataField:"pekerjaan",caption:"Pekerjaan",minWidth:130,cellTemplate:function(c,o){$('<span style="font-size:12.5px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
      {dataField:"no_telepon",caption:"Telepon",width:130,cellTemplate:function(c,o){$('<span style="font-family:\'IBM Plex Mono\',monospace;font-size:12px">').text(o.value||'—').appendTo(c);}},
      {dataField:"status_tinggal",caption:"Tinggal",width:90,alignment:"center",
        cellTemplate:function(c,o){$('<span style="font-size:11.5px;font-weight:600;text-transform:capitalize">').text(o.value||'—').appendTo(c);}},
      {dataField:"status_warga",caption:"Status",width:90,alignment:"center",
        cellTemplate:function(c,o){
          var cls={aktif:'#C8E6C9,#2D6A4F',pindah:'#FFF9C4,#7a5c00',meninggal:'var(--kertas-2),var(--redup)'};
          var bg=(cls[o.value]||'var(--kertas-2),var(--redup)').split(',');
          $('<span style="display:inline-flex;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;background:'+bg[0]+';color:'+bg[1]+'">').text(o.value||'—').appendTo(c);}},
    ]
  }).dxDataGrid("instance");
  loadData();
});

function loadData(){
  var p={}; if(curGang)p.gang_id=curGang; if(curSW)p.status_warga=curSW; if(curST)p.status_tinggal=curST;
  $.getJSON(urlData,p,function(d){cached=d;grid.option("dataSource",d);});
}

function exportExcel(){
  if(!cached.length)return;
  var wb=new ExcelJS.Workbook(); var sh=wb.addWorksheet('Daftar Warga');
  sh.columns=[
    {header:'NIK',key:'nik',width:18},{header:'Nama',key:'nama',width:25},
    {header:'L/P',key:'jenis_kelamin',width:6},{header:'Umur',key:'umur',width:7},
    {header:'Hubungan',key:'hubungan',width:16},{header:'Gang',key:'gang',width:15},
    {header:'Blok/Rumah',key:'blok_no',width:12},{header:'Agama',key:'agama',width:14},
    {header:'Pendidikan',key:'pendidikan',width:12},{header:'Pekerjaan',key:'pekerjaan',width:20},
    {header:'No. KK',key:'no_kk',width:18},{header:'Kepala KK',key:'kepala_kk',width:22},
    {header:'Telepon',key:'no_telepon',width:16},{header:'Status Tinggal',key:'status_tinggal',width:14},
    {header:'Status Warga',key:'status_warga',width:13},
  ];
  sh.getRow(1).font={bold:true,color:{argb:'FFFFFFFF'}};
  sh.getRow(1).fill={type:'pattern',pattern:'solid',fgColor:{argb:'FF14532D'}};
  cached.forEach(function(d,i){var row=sh.addRow(d);if(i%2===0)row.fill={type:'pattern',pattern:'solid',fgColor:{argb:'FFE8F5E9'}};});
  wb.xlsx.writeBuffer().then(function(buf){saveAs(new Blob([buf],{type:'application/octet-stream'}),'DaftarWarga.xlsx');});
}
</script>
@endpush
