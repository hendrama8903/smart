@extends('layouts.app')
@section('judul','Audit Trail')
@section('content')

<div class="keu-toolbar">
  <h2 class="keu-title">Audit Trail</h2>
  <div class="keu-actions" style="flex-wrap:wrap">
    <div id="filterUser"></div>
    <div id="filterAksi"></div>
    <div id="filterModul"></div>
    <div id="filterDari"></div>
    <div id="filterSampai"></div>
    <button class="btn" type="button" onclick="loadData()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      Filter
    </button>
  </div>
</div>

<div class="at-info">
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  Menampilkan maks. 500 log terbaru. Klik baris untuk melihat detail perubahan data.
</div>

<div class="grid-wrap" style="height:calc(100vh - 170px)">
  <div id="gridAudit"></div>
</div>

{{-- Detail drawer --}}
<div class="modal-overlay" id="detailModal" onclick="if(event.target===this)detailClose()">
  <div class="modal-card" style="max-width:640px;padding:0;text-align:left">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:var(--biru-soft);color:var(--biru)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
      </div>
      <div><h3>Detail Perubahan</h3><p class="keu-sub" id="detailSub">—</p></div>
    </div>
    <div style="padding:18px 20px;max-height:70vh;overflow-y:auto">
      <div class="at-detail-grid" id="detailContent"></div>
    </div>
    <div style="display:flex;justify-content:flex-end;padding:12px 20px;border-top:1px solid var(--garis);background:var(--kertas);border-radius:0 0 20px 20px">
      <button class="mbtn ghost" onclick="detailClose()">Tutup</button>
    </div>
  </div>
</div>

@endsection

@push('styles')
<style>
.content{max-width:none;padding-bottom:0}
.keu-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;gap:10px;flex-wrap:wrap}
.keu-title{font-size:22px;font-weight:800;letter-spacing:-.02em;color:var(--tinta);margin:0}
.keu-actions{display:flex;gap:8px;align-items:center}
.at-info{display:flex;align-items:center;gap:8px;padding:9px 13px;background:var(--biru-soft);border-radius:9px;font-size:12.5px;color:#1a3d52;margin-bottom:12px}
.at-info svg{flex:0 0 14px}

.grid-wrap{background:var(--surface);border:1px solid var(--garis);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow)}
#gridAudit,.dx-widget{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
#gridAudit .dx-datagrid{border:none;color:var(--tinta)}
#gridAudit .dx-datagrid-headers{background:var(--kertas-2);border-bottom:1px solid var(--garis)}
#gridAudit .dx-datagrid-headers .dx-header-row>td{font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:11px 14px;border:none}
#gridAudit .dx-data-row>td{padding:9px 14px;font-size:13px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis);vertical-align:middle}
#gridAudit .dx-datagrid-rowsview .dx-data-row:hover>td{background:#FBFAF5;cursor:pointer}
#gridAudit .dx-pager{background:var(--kertas);border-top:1px solid var(--garis);padding:8px 14px}
#gridAudit .dx-toolbar{display:none}

/* Aksi badges */
.at-badge{display:inline-flex;align-items:center;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px}
.at-green{background:#E8F5E9;color:#2D6A4F}
.at-blue{background:var(--biru-soft);color:#1a3d52}
.at-red{background:var(--stempel-soft);color:#9A3422}
.at-teal{background:#E0F2F1;color:#00695C}
.at-gray{background:var(--kertas-2);color:var(--redup)}
.at-purple{background:#EDE9FE;color:#5B21B6}

/* Detail */
.at-detail-grid{display:grid;gap:14px}
.at-detail-section{background:var(--kertas);border:1px solid var(--garis);border-radius:10px;padding:14px 16px}
.at-detail-title{font-size:11px;font-weight:700;color:var(--redup);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px}
.at-detail-pre{font-family:'IBM Plex Mono',monospace;font-size:12px;color:var(--tinta);white-space:pre-wrap;word-break:break-word;max-height:200px;overflow-y:auto;line-height:1.6}
.at-meta{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.at-meta-item label{display:block;font-size:10.5px;font-weight:700;color:var(--redup);text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px}
.at-meta-item span{font-size:13px;font-weight:600;color:var(--tinta)}

.keu-head{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--garis)}
.keu-head-icon{flex:0 0 34px;width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center}
.keu-head-icon svg{width:17px;height:17px}
.keu-head h3{font-size:15px;font-weight:800;margin:0 0 1px}
.keu-sub{font-size:12px;color:var(--redup);margin:0}
</style>
@endpush

@push('scripts')
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});
const urlList      = "{{ route('audit.list') }}";
const urlUserList  = "{{ route('audit.users') }}";
const urlModulList = "{{ route('audit.moduls') }}";

let grid, filters = {};

const AKSI_LIST = [
  {id:null,l:'Semua Aksi'},{id:'created',l:'Tambah'},{id:'updated',l:'Ubah'},
  {id:'deleted',l:'Hapus'},{id:'login',l:'Login'},{id:'logout',l:'Logout'},
  {id:'login_gagal',l:'Login Gagal'},{id:'bayar',l:'Bayar Iuran'},
  {id:'generate',l:'Generate'},{id:'export',l:'Export'},{id:'import',l:'Import'},
];
const COLOR_MAP = {green:'at-green',blue:'at-blue',red:'at-red',teal:'at-teal',gray:'at-gray',purple:'at-purple'};

$(function(){
  $.getJSON(urlUserList, function(users){
    $("#filterUser").dxSelectBox({
      dataSource:[{id:null,name:'Semua User'},...users],
      valueExpr:'id',displayExpr:'name',value:null,width:160,
      onValueChanged:function(e){filters.user_id=e.value;}
    });
  });
  $.getJSON(urlModulList, function(moduls){
    var list=[{id:null,l:'Semua Modul'},...moduls.map(function(m){return{id:m,l:m};})];
    $("#filterModul").dxSelectBox({dataSource:list,valueExpr:'id',displayExpr:'l',value:null,width:150,onValueChanged:function(e){filters.modul=e.value;}});
  });
  $("#filterAksi").dxSelectBox({dataSource:AKSI_LIST,valueExpr:'id',displayExpr:'l',value:null,width:140,onValueChanged:function(e){filters.aksi=e.value;}});
  $("#filterDari").dxDateBox({type:"date",displayFormat:"dd/MM/yyyy",showClearButton:true,placeholder:"Dari tanggal",width:130,onValueChanged:function(e){filters.dari=fmtDate(e.value);}});
  $("#filterSampai").dxDateBox({type:"date",displayFormat:"dd/MM/yyyy",showClearButton:true,placeholder:"Sampai",width:130,onValueChanged:function(e){filters.sampai=fmtDate(e.value);}});

  grid=$("#gridAudit").dxDataGrid({
    dataSource:[],showBorders:false,showColumnLines:true,showRowLines:true,
    rowAlternationEnabled:true,width:"100%",height:"100%",columnAutoWidth:true,
    filterRow:{visible:false},
    paging:{pageSize:50},
    pager:{visible:true,displayMode:"compact",showPageSizeSelector:true,allowedPageSizes:[25,50,100,"all"],showInfo:true},
    onRowClick:function(e){openDetail(e.data);},
    columns:[
      {caption:"No.",width:55,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"waktu",caption:"Waktu",width:160,
        cellTemplate:function(c,o){$('<span style="font-size:12.5px;font-family:\'IBM Plex Mono\',monospace">').text(o.value||'—').appendTo(c);}},
      {dataField:"user",caption:"User",width:140,
        cellTemplate:function(c,o){$('<span style="font-weight:600">').text(o.value||'System').appendTo(c);}},
      {dataField:"aksi_label",caption:"Aksi",width:110,
        cellTemplate:function(c,o){
          var col=COLOR_MAP[o.row.data.aksi_color]||'at-gray';
          $('<span class="at-badge '+col+'">').text(o.value||o.row.data.aksi).appendTo(c);
        }},
      {dataField:"modul",caption:"Modul",width:120,
        cellTemplate:function(c,o){$('<span style="font-size:12.5px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
      {dataField:"deskripsi",caption:"Keterangan",minWidth:200,
        cellTemplate:function(c,o){$('<span style="font-size:13px">').text(o.value||'—').appendTo(c);}},
      {dataField:"ip_address",caption:"IP Address",width:130,
        cellTemplate:function(c,o){$('<span style="font-family:\'IBM Plex Mono\',monospace;font-size:12px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
      {dataField:"sebelum",caption:"Ada Detail",width:90,alignment:"center",
        cellTemplate:function(c,o){
          if(o.value||o.row.data.sesudah){
            $('<span style="font-size:11.5px;color:var(--biru);font-weight:700">Lihat →</span>').appendTo(c);
          }
        }},
    ]
  }).dxDataGrid("instance");

  loadData();
});

function loadData(){
  var p={};
  if(filters.user_id) p.user_id=filters.user_id;
  if(filters.aksi) p.aksi=filters.aksi;
  if(filters.modul) p.modul=filters.modul;
  if(filters.dari) p.dari=filters.dari;
  if(filters.sampai) p.sampai=filters.sampai;
  $.getJSON(urlList,p,function(d){grid.option("dataSource",d);});
}

function openDetail(d){
  document.getElementById('detailSub').textContent = d.deskripsi || (d.modul+' #'+d.modul_id);
  var html='<div class="at-meta">';
  html+='<div class="at-meta-item"><label>User</label><span>'+esc(d.user||'System')+'</span></div>';
  html+='<div class="at-meta-item"><label>Aksi</label><span>'+esc(d.aksi_label)+'</span></div>';
  html+='<div class="at-meta-item"><label>Modul</label><span>'+esc(d.modul||'—')+(d.modul_id?' #'+d.modul_id:'')+'</span></div>';
  html+='<div class="at-meta-item"><label>Waktu</label><span>'+esc(d.waktu||'—')+'</span></div>';
  html+='<div class="at-meta-item"><label>IP Address</label><span>'+esc(d.ip_address||'—')+'</span></div>';
  html+='</div>';

  if(d.sebelum){
    html+='<div class="at-detail-section" style="margin-top:12px"><div class="at-detail-title">Data Sebelum</div><pre class="at-detail-pre">'+esc(d.sebelum)+'</pre></div>';
  }
  if(d.sesudah){
    html+='<div class="at-detail-section" style="margin-top:10px"><div class="at-detail-title">Data Sesudah</div><pre class="at-detail-pre">'+esc(d.sesudah)+'</pre></div>';
  }
  if(!d.sebelum && !d.sesudah){
    html+='<div class="at-detail-section" style="margin-top:12px;text-align:center;color:var(--redup)"><p style="font-size:12.5px">Tidak ada detail perubahan data untuk aksi ini.</p></div>';
  }

  document.getElementById('detailContent').innerHTML=html;
  document.getElementById('detailModal').classList.add('show');
}

function detailClose(){document.getElementById('detailModal').classList.remove('show');}
function esc(s){var d=document.createElement('div');d.appendChild(document.createTextNode(String(s)));return d.innerHTML;}
function fmtDate(d){if(!d)return '';var dt=new Date(d);return dt.getFullYear()+'-'+String(dt.getMonth()+1).padStart(2,'0')+'-'+String(dt.getDate()).padStart(2,'0');}
document.addEventListener('keydown',function(e){if(e.key==='Escape')detailClose();});
</script>
@endpush
