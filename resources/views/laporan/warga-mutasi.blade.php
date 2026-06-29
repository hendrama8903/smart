@extends('layouts.app')
@section('judul','Mutasi Warga')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:12px">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="{{ route('laporan.warga.index') }}" style="color:var(--redup);font-size:13px">← Laporan Warga</a>
    <h2 style="font-size:22px;font-weight:800;letter-spacing:-.02em;margin:0">Mutasi Warga</h2>
  </div>
  <div style="display:flex;gap:8px;align-items:center">
    <div id="filterTahun"></div>
    <div id="filterBulan"></div>
  </div>
</div>

{{-- Summary Cards --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:18px">
  <div style="background:#E8F5E9;border:1px solid #A5D6A7;border-radius:12px;padding:16px 18px">
    <div style="font-size:11px;font-weight:700;color:var(--redup);text-transform:uppercase;margin-bottom:6px">Warga Masuk</div>
    <div style="font-size:28px;font-weight:800;color:#2D6A4F" id="sMasuk">0</div>
    <div style="font-size:12px;color:var(--redup);margin-top:3px">warga baru</div>
  </div>
  <div style="background:var(--emas-soft);border:1px solid #F6D860;border-radius:12px;padding:16px 18px">
    <div style="font-size:11px;font-weight:700;color:var(--redup);text-transform:uppercase;margin-bottom:6px">Warga Pindah</div>
    <div style="font-size:28px;font-weight:800;color:#7a5c00" id="sPindah">0</div>
    <div style="font-size:12px;color:var(--redup);margin-top:3px">pindah keluar</div>
  </div>
  <div style="background:var(--stempel-soft);border:1px solid #EFC6BE;border-radius:12px;padding:16px 18px">
    <div style="font-size:11px;font-weight:700;color:var(--redup);text-transform:uppercase;margin-bottom:6px">Warga Meninggal</div>
    <div style="font-size:28px;font-weight:800;color:var(--stempel)" id="sMeninggal">0</div>
    <div style="font-size:12px;color:var(--redup);margin-top:3px">meninggal</div>
  </div>
</div>

{{-- Chart bulanan --}}
<div style="background:var(--surface);border:1px solid var(--garis);border-radius:14px;padding:18px 20px;box-shadow:var(--shadow);margin-bottom:16px">
  <div style="font-size:13px;font-weight:700;margin-bottom:14px">Grafik Mutasi per Bulan</div>
  <div style="display:flex;align-items:flex-end;gap:8px;height:140px" id="chartMutasi"></div>
  <div style="display:flex;gap:16px;margin-top:10px;font-size:12px;font-weight:600;color:var(--redup)">
    <span><i style="display:inline-block;width:10px;height:10px;border-radius:3px;background:#2D6A4F;margin-right:5px"></i>Masuk</span>
    <span><i style="display:inline-block;width:10px;height:10px;border-radius:3px;background:var(--emas);margin-right:5px"></i>Pindah</span>
    <span><i style="display:inline-block;width:10px;height:10px;border-radius:3px;background:var(--stempel);margin-right:5px"></i>Meninggal</span>
  </div>
</div>

{{-- Tabs detail --}}
<div style="display:flex;border:1px solid var(--garis);border-bottom:none;border-radius:12px 12px 0 0;overflow:hidden">
  <button class="mut-tab on" onclick="switchTab(this,'tabMasuk')">Warga Masuk (<span id="cMasuk">0</span>)</button>
  <button class="mut-tab" onclick="switchTab(this,'tabPindah')">Pindah (<span id="cPindah">0</span>)</button>
  <button class="mut-tab" onclick="switchTab(this,'tabMeninggal')">Meninggal (<span id="cMeninggal">0</span>)</button>
</div>
<div style="background:var(--surface);border:1px solid var(--garis);border-radius:0 0 14px 14px;overflow:hidden;box-shadow:var(--shadow);min-height:200px">
  <div id="tabMasuk"><div id="gridMasuk"></div></div>
  <div id="tabPindah" style="display:none"><div id="gridPindah"></div></div>
  <div id="tabMeninggal" style="display:none"><div id="gridMeninggal"></div></div>
</div>

@endsection
@push('styles')
<style>
.content{max-width:none;padding-bottom:32px}
.mut-tab{flex:1;padding:10px;font-size:13px;font-weight:700;color:var(--redup);background:var(--kertas-2);border:none;cursor:pointer;transition:.13s;border-right:1px solid var(--garis)}
.mut-tab:last-child{border-right:none}
.mut-tab.on{background:var(--surface);color:var(--hutan)}
.mut-tab:hover:not(.on){background:var(--kertas)}
#gridMasuk .dx-datagrid,#gridPindah .dx-datagrid,#gridMeninggal .dx-datagrid{border:none}
.dx-datagrid-headers .dx-header-row>td{font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:11px 14px;border:none;background:var(--kertas-2)}
.dx-data-row>td{padding:10px 14px;font-size:13px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis)}
.dx-pager{background:var(--kertas);border-top:1px solid var(--garis);padding:8px 14px}
.dx-toolbar{display:none}
</style>
@endpush
@push('scripts')
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});
const urlData = "{{ route('laporan.warga.mutasi.data') }}";
let g1,g2,g3, curTahun={{ now()->year }}, curBulan=null;

const colDef=[
  {caption:"No.",width:52,alignment:"center",allowFiltering:false,allowSorting:false,cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
  {dataField:"nama",caption:"Nama",minWidth:160,cellTemplate:function(c,o){var d=$('<div>');$('<div style="font-weight:600">').text(o.value||'—').appendTo(d);$('<div style="font-family:\'IBM Plex Mono\',monospace;font-size:11px;color:var(--redup)">').text(o.row.data.nik||'').appendTo(d);d.appendTo(c);}},
  {dataField:"hubungan",caption:"Hubungan",width:120,cellTemplate:function(c,o){$('<span style="font-size:12.5px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
  {dataField:"umur",caption:"Umur",width:65,alignment:"center",cellTemplate:function(c,o){$('<span>').text(o.value!=null?o.value+' th':'—').appendTo(c);}},
  {dataField:"gang",caption:"Gang",width:130,cellTemplate:function(c,o){$('<span style="font-size:12.5px">').text(o.value||'—').appendTo(c);}},
  {dataField:"blok_no",caption:"Blok",width:90,cellTemplate:function(c,o){if(!o.value){$('<span style="color:var(--garis-2)">—</span>').appendTo(c);return;}$('<span style="display:inline-flex;font-size:11.5px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--biru-soft);color:#1a3d52">').text(o.value).appendTo(c);}},
  {dataField:"tgl_masuk",caption:"Tgl. Masuk",width:110,alignment:"center"},
  {dataField:"tgl_keluar",caption:"Tgl. Keluar",width:110,alignment:"center"},
  {dataField:"keterangan",caption:"Keterangan",minWidth:150,cellTemplate:function(c,o){$('<span style="font-size:12.5px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
];

$(function(){
  var yOpts=[]; for(var y=2020;y<=2030;y++) yOpts.push({id:y,l:y+''});
  var bOpts=[{id:null,l:'Semua Bulan'}];
  var bNames=['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  for(var m=1;m<=12;m++) bOpts.push({id:m,l:bNames[m-1]});

  $("#filterTahun").dxSelectBox({dataSource:yOpts,valueExpr:'id',displayExpr:'l',value:curTahun,width:100,onValueChanged:function(e){curTahun=e.value;loadData();}});
  $("#filterBulan").dxSelectBox({dataSource:bOpts,valueExpr:'id',displayExpr:'l',value:null,width:130,onValueChanged:function(e){curBulan=e.value;loadData();}});

  var mkGrid=function(id){return $("#"+id).dxDataGrid({dataSource:[],showBorders:false,showColumnLines:true,showRowLines:true,rowAlternationEnabled:true,width:"100%",filterRow:{visible:false},paging:{pageSize:50},pager:{visible:true,displayMode:"compact",showPageSizeSelector:true,allowedPageSizes:[25,50,"all"],showInfo:true},columns:colDef}).dxDataGrid("instance");};
  g1=mkGrid("gridMasuk"); g2=mkGrid("gridPindah"); g3=mkGrid("gridMeninggal");
  loadData();
});

function loadData(){
  var p={tahun:curTahun}; if(curBulan) p.bulan=curBulan;
  $.getJSON(urlData,p,function(d){
    document.getElementById('sMasuk').textContent     = d.masuk.length;
    document.getElementById('sPindah').textContent    = d.pindah.length;
    document.getElementById('sMeninggal').textContent = d.meninggal.length;
    document.getElementById('cMasuk').textContent     = d.masuk.length;
    document.getElementById('cPindah').textContent    = d.pindah.length;
    document.getElementById('cMeninggal').textContent = d.meninggal.length;
    g1.option("dataSource",d.masuk);
    g2.option("dataSource",d.pindah);
    g3.option("dataSource",d.meninggal);
    renderChart(d.per_bulan);
  });
}

function renderChart(data){
  var el=document.getElementById('chartMutasi'); el.innerHTML='';
  var maxVal=Math.max(...data.map(function(d){return Math.max(d.masuk,d.pindah,d.meninggal);}),1);
  data.forEach(function(d){
    var col=document.createElement('div');
    col.style.cssText='display:flex;flex-direction:column;align-items:center;gap:4px;flex:1';
    var bars=document.createElement('div');
    bars.style.cssText='display:flex;align-items:flex-end;gap:2px;height:120px;width:100%';
    [[d.masuk,'#2D6A4F'],[d.pindah,'var(--emas)'],[d.meninggal,'var(--stempel)']].forEach(function(b){
      var bar=document.createElement('div');
      bar.style.cssText='flex:1;border-radius:3px 3px 0 0;min-height:2px;background:'+b[1]+';height:'+Math.round(b[0]/maxVal*100)+'%';
      bar.title=b[0]; bars.appendChild(bar);
    });
    var lbl=document.createElement('div');
    lbl.style.cssText='font-size:11px;color:var(--redup);font-weight:600';lbl.textContent=d.label;
    col.appendChild(bars);col.appendChild(lbl);el.appendChild(col);
  });
}

function switchTab(btn,tabId){
  document.querySelectorAll('.mut-tab').forEach(function(b){b.classList.remove('on');});
  btn.classList.add('on');
  ['tabMasuk','tabPindah','tabMeninggal'].forEach(function(id){
    document.getElementById(id).style.display=id===tabId?'block':'none';
  });
}
</script>
@endpush
