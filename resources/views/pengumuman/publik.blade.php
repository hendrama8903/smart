@extends('layouts.app')
@section('judul','Pengumuman RT')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
  <h2 style="font-size:22px;font-weight:800;letter-spacing:-.02em;margin:0">Pengumuman RT</h2>
  <div id="filterKategori"></div>
</div>

<div id="pgList" class="pg-list">
  <div class="pg-loading">Memuat pengumuman...</div>
</div>

@endsection

@push('styles')
<style>
.content{max-width:800px;padding-bottom:32px}

/* List pengumuman */
.pg-list{display:flex;flex-direction:column;gap:12px}
.pg-loading{text-align:center;padding:40px;color:var(--redup);font-size:13.5px}
.pg-empty{text-align:center;padding:60px 20px;color:var(--redup)}
.pg-empty svg{width:48px;height:48px;color:var(--garis-2);margin-bottom:12px}

.pg-item{background:var(--surface);border:1px solid var(--garis);border-radius:14px;padding:20px 22px;box-shadow:var(--shadow);transition:.15s}
.pg-item:hover{border-color:var(--daun);box-shadow:0 0 0 3px rgba(45,106,79,.06),var(--shadow)}
.pg-item.pg-item-penting{border-left:4px solid var(--stempel)}

.pg-item-head{display:flex;align-items:flex-start;gap:12px;margin-bottom:10px}
.pg-item-meta{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:6px}
.pg-kat{display:inline-flex;align-items:center;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px}
.pg-kat-informasi{background:var(--biru-soft);color:#1a3d52}
.pg-kat-rapat{background:var(--daun-pucat);color:#14532D}
.pg-kat-kegiatan{background:var(--emas-soft);color:#7a5c00}
.pg-kat-keuangan{background:#E8F5E9;color:#2D6A4F}
.pg-kat-darurat{background:var(--stempel-soft);color:#9A3422}
.pg-kat-lainnya{background:var(--kertas-2);color:var(--redup);border:1px solid var(--garis)}
.pg-date{font-size:12px;color:var(--redup);font-weight:600}
.pg-urgent{font-size:11px;font-weight:700;padding:2px 7px;border-radius:20px;background:#FFE0E0;color:#C0392B}
.pg-judul{font-size:17px;font-weight:800;color:var(--tinta);margin:0}
.pg-isi{font-size:13.5px;color:var(--redup);line-height:1.6;margin-top:8px;white-space:pre-line}
.pg-file{display:inline-flex;align-items:center;gap:8px;margin-top:12px;padding:10px 14px;background:var(--biru-soft);border:1px solid #90CAF9;border-radius:10px;font-size:13px;font-weight:600;color:var(--biru);text-decoration:none;transition:.13s}
.pg-file:hover{background:var(--biru);color:#fff}
.pg-file svg{width:15px;height:15px;flex:0 0 15px}
.pg-pembuat{font-size:11.5px;color:var(--redup);margin-top:10px}

.pg-icon{width:42px;height:42px;border-radius:12px;background:var(--kertas-2);display:flex;align-items:center;justify-content:center;flex:0 0 42px}
.pg-icon svg{width:20px;height:20px;color:var(--redup)}
.pg-icon-rapat svg{color:var(--daun)}
.pg-icon-darurat svg{color:var(--stempel)}
.pg-icon-keuangan svg{color:#2D6A4F}
</style>
@endpush

@push('scripts')
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});
const urlList = "{{ route('pengumuman.publik.list') }}";
const KATEGORI = [
  {id:null,l:'Semua'},{id:'informasi',l:'Informasi'},{id:'rapat',l:'Hasil Rapat'},
  {id:'kegiatan',l:'Kegiatan'},{id:'keuangan',l:'Keuangan'},{id:'darurat',l:'Darurat'},{id:'lainnya',l:'Lainnya'},
];
const ICONS = {
  rapat:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
  kegiatan:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
  keuangan:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
  darurat:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
  default:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
};
let curKat=null;

$(function(){
  $("#filterKategori").dxSelectBox({
    dataSource:KATEGORI,valueExpr:'id',displayExpr:'l',value:null,width:160,
    onValueChanged:function(e){curKat=e.value;loadData();}
  });
  loadData();
});

function loadData(){
  var p={}; if(curKat) p.kategori=curKat;
  document.getElementById('pgList').innerHTML='<div class="pg-loading">Memuat...</div>';
  $.getJSON(urlList,p,function(data){
    if(!data.length){
      document.getElementById('pgList').innerHTML=
        '<div class="pg-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg><p>Belum ada pengumuman.</p></div>';
      return;
    }
    var html='';
    data.forEach(function(p){
      var icon=ICONS[p.kategori]||ICONS.default;
      var iconCls='pg-icon'+(p.kategori==='darurat'?' pg-icon-darurat':p.kategori==='rapat'?' pg-icon-rapat':p.kategori==='keuangan'?' pg-icon-keuangan':'');
      html+='<div class="pg-item'+(p.penting?' pg-item-penting':'')+'">'+
        '<div class="pg-item-head">'+
          '<div class="'+iconCls+'">'+icon+'</div>'+
          '<div style="flex:1;min-width:0">'+
            '<div class="pg-item-meta">'+
              '<span class="pg-kat pg-kat-'+p.kategori+'">'+p.kategori_label+'</span>'+
              (p.penting?'<span class="pg-urgent">🔴 Penting</span>':'')+
              '<span class="pg-date">'+p.tanggal+'</span>'+
            '</div>'+
            '<h3 class="pg-judul">'+escHtml(p.judul)+'</h3>'+
          '</div>'+
        '</div>'+
        (p.isi?'<p class="pg-isi">'+escHtml(p.isi)+'</p>':'')+
        (p.file_url?
          '<a href="'+p.file_url+'" class="pg-file" target="_blank" download="'+escHtml(p.nama_file)+'">'+
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>'+
            escHtml(p.nama_file)+
            ' <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>'+
          '</a>':'')+
        '<div class="pg-pembuat">Diterbitkan oleh '+escHtml(p.pembuat||'Pengurus RT')+'</div>'+
      '</div>';
    });
    document.getElementById('pgList').innerHTML=html;
  });
}

function escHtml(s){var d=document.createElement('div');d.appendChild(document.createTextNode(s||''));return d.innerHTML;}
</script>
@endpush
