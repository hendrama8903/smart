@extends('layouts.app')
@section('judul','Iuran Saya')
@section('content')

@if(! $kkId)
<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:60vh;text-align:center;gap:16px">
  <div style="width:72px;height:72px;border-radius:50%;background:var(--kertas-2);display:flex;align-items:center;justify-content:center">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--redup)" stroke-width="1.5"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
  </div>
  <div>
    <h2 style="font-size:18px;font-weight:700;color:var(--tinta);margin-bottom:6px">Data Iuran Tidak Ditemukan</h2>
    <p style="font-size:13.5px;color:var(--redup)">Akun Anda belum terhubung ke data warga RT. Hubungi pengurus RT.</p>
  </div>
</div>
@else

{{-- Toolbar --}}
<div class="is-toolbar">
  <h2 class="is-title">Iuran Saya</h2>
  <div class="is-filters">
    <div class="is-filter-group">
      <label class="is-filter-label">Jenis Iuran</label>
      <div id="filterJenis"></div>
    </div>
    <div class="is-filter-group">
      <label class="is-filter-label">Tahun</label>
      <div id="filterTahun"></div>
    </div>
  </div>
</div>

{{-- Summary cards --}}
<div class="is-summary">
  <div class="is-card s-lunas">
    <div class="is-lbl">Lunas</div>
    <div class="is-val" id="sLunas">0</div>
    <div class="is-sub">tagihan</div>
  </div>
  <div class="is-card s-sebagian">
    <div class="is-lbl">Sebagian</div>
    <div class="is-val" id="sSebagian">0</div>
    <div class="is-sub">tagihan</div>
  </div>
  <div class="is-card s-belum">
    <div class="is-lbl">Belum Bayar</div>
    <div class="is-val" id="sBelum">0</div>
    <div class="is-sub">tagihan</div>
  </div>
  <div class="is-card s-tunggakan">
    <div class="is-lbl">Total Tunggakan</div>
    <div class="is-val is-val-sm" id="sTunggakan">Rp 0</div>
  </div>
</div>

{{-- Grid --}}
<div class="grid-wrap" style="height:calc(100vh - 280px)">
  <div id="gridIuranSaya"></div>
</div>

@endif
@endsection

@push('styles')
<style>
.content{max-width:none;padding-bottom:0}

/* Toolbar */
.is-toolbar{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:16px;gap:16px;flex-wrap:wrap}
.is-title{font-size:22px;font-weight:800;letter-spacing:-.02em;color:var(--tinta);margin:0}
.is-filters{display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap}
.is-filter-group{display:flex;flex-direction:column;gap:5px}
.is-filter-label{font-size:11px;font-weight:700;color:var(--redup);letter-spacing:.06em;text-transform:uppercase}

/* Summary cards */
.is-summary{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}
.is-card{background:var(--surface);border:1px solid var(--garis);border-radius:12px;padding:16px 20px;position:relative;overflow:hidden}
.is-card::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px}
.s-lunas::before{background:#2D6A4F}
.s-sebagian::before{background:var(--emas)}
.s-belum::before{background:var(--stempel)}
.s-tunggakan::before{background:var(--biru)}
.is-lbl{font-size:11px;font-weight:700;color:var(--redup);letter-spacing:.06em;text-transform:uppercase;margin-bottom:8px}
.is-val{font-size:28px;font-weight:800;color:var(--tinta);line-height:1}
.is-val-sm{font-size:20px}
.is-sub{font-size:11.5px;color:var(--redup);margin-top:4px}

/* Grid */
.grid-wrap{background:var(--surface);border:1px solid var(--garis);border-radius:var(--radius);overflow:auto;box-shadow:var(--shadow)}
#gridIuranSaya,.dx-widget{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
#gridIuranSaya .dx-datagrid{border:none;color:var(--tinta)}
#gridIuranSaya .dx-datagrid-headers{background:var(--kertas-2);border-bottom:1px solid var(--garis)}
#gridIuranSaya .dx-datagrid-headers .dx-header-row>td{font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:12px 14px;border:none}
#gridIuranSaya .dx-data-row>td{padding:13px 14px;font-size:13.5px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis);vertical-align:middle}
#gridIuranSaya .dx-datagrid-rowsview .dx-data-row:hover>td{background:#FBFAF5}
#gridIuranSaya .dx-pager{background:var(--kertas);border-top:1px solid var(--garis);padding:10px 14px}
#gridIuranSaya .dx-toolbar{display:none}

.st-lunas{display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;padding:3px 12px;border-radius:20px;background:var(--daun-pucat);color:#1B5E3F}
.st-sebagian{display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;padding:3px 12px;border-radius:20px;background:var(--emas-soft);color:#7a5c00}
.st-belum{display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;padding:3px 12px;border-radius:20px;background:var(--stempel-soft);color:#9A3422}
.badge-keringanan{display:inline-flex;align-items:center;gap:4px;font-size:10.5px;font-weight:700;padding:2px 8px;border-radius:20px;background:var(--emas-soft);color:#7a5c00;margin-left:6px}
.mono-sm{font-family:'IBM Plex Mono',monospace;font-size:13px;font-weight:600}

@media(max-width:900px){.is-summary{grid-template-columns:repeat(2,1fr)}}
@media(max-width:480px){.is-summary{grid-template-columns:1fr 1fr}}
@media(max-width:640px){
  .is-toolbar{flex-direction:column;align-items:stretch;gap:10px}
  .is-title{font-size:18px;white-space:normal}
  .is-filters{display:flex;flex-wrap:wrap;gap:8px}
  .grid-wrap{height:calc(100vh - 210px)}
}
</style>
@endpush

@push('scripts')
@if($kkId)
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});

const urlList      = "{{ route('iuran-saya.list') }}";
const urlRingkasan = "{{ route('iuran-saya.ringkasan') }}";
const urlJenis     = "{{ route('iuran-saya.jenis') }}";

let grid, curJenis=null, curTahun={{ now()->year }};

function rupiah(n){ return 'Rp '+(n||0).toLocaleString('id-ID'); }

$(function(){
  $.getJSON(urlJenis, function(jenis){
    $("#filterJenis").dxSelectBox({
      dataSource:[{id:null,nama:'Semua Jenis'},...jenis],
      valueExpr:'id', displayExpr:'nama', value:null, width:160,
      onValueChanged:function(e){curJenis=e.value;refreshAll();}
    });
  });

  var tahunOpts=[];
  for(var y={{ now()->year }};y>={{ now()->year - 5 }};y--) tahunOpts.push({id:y,l:y+''});
  $("#filterTahun").dxSelectBox({
    dataSource:tahunOpts, valueExpr:'id', displayExpr:'l',
    value:curTahun, width:100,
    onValueChanged:function(e){curTahun=e.value;refreshAll();}
  });

  grid = $("#gridIuranSaya").dxDataGrid({
    dataSource: new DevExpress.data.CustomStore({
      key:"id",
      load:function(){
        var p={tahun:curTahun};
        if(curJenis) p.jenis_iuran_id=curJenis;
        return $.getJSON(urlList,p);
      }
    }),
    showBorders:false, showColumnLines:true, showRowLines:true,
    rowAlternationEnabled:true, width:"100%", height:"100%",
    columnAutoWidth:false,
    scrolling: { useNative: true, showScrollbar: 'always', mode: 'standard' },
    filterRow:{ visible:false }, // filter dilakukan via dropdown di atas
    paging:{pageSize:50},
    pager:{visible:true,displayMode:"compact",showPageSizeSelector:true,allowedPageSizes:[25,50,"all"],showInfo:true},
    columns:[
      {caption:"No.",width:55,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"bulan",caption:"Periode",minWidth:150,
        cellTemplate:function(c,o){$('<span style="font-weight:700;font-size:14px">').text(o.value||'—').appendTo(c);}},
      {dataField:"jenis_iuran",caption:"Jenis Iuran",minWidth:150,
        cellTemplate:function(c,o){
          var wrap=$('<div>');
          $('<span style="font-weight:600">').text(o.value||'—').appendTo(wrap);
          if(o.row.data.is_keringanan){
            $('<span class="badge-keringanan">♥ Keringanan</span>').appendTo(wrap);
          }
          wrap.appendTo(c);
        }},
      {dataField:"nominal",caption:"Tagihan",width:140,alignment:"right",
        cellTemplate:function(c,o){$('<span class="mono-sm">').text(rupiah(o.value)).appendTo(c);}},
      {dataField:"nominal_dibayar",caption:"Dibayar",width:140,alignment:"right",
        cellTemplate:function(c,o){
          $('<span class="mono-sm" style="color:'+(o.value>0?'#2D6A4F':'var(--redup)')+'">').text(rupiah(o.value)).appendTo(c);
        }},
      {dataField:"sisa",caption:"Sisa",width:130,alignment:"right",
        cellTemplate:function(c,o){
          if(o.value<=0){$('<span style="color:var(--redup);font-size:13px">—</span>').appendTo(c);return;}
          $('<span class="mono-sm" style="color:var(--stempel);font-weight:700">').text(rupiah(o.value)).appendTo(c);
        }},
      {dataField:"status",caption:"Status",width:130,alignment:"center",
        cellTemplate:function(c,o){
          var cls={lunas:'st-lunas',sebagian:'st-sebagian',belum:'st-belum'};
          var lbl={lunas:'✓ Lunas',sebagian:'Sebagian',belum:'Belum Bayar'};
          $('<span class="'+(cls[o.value]||'st-belum')+'">').text(lbl[o.value]||o.value).appendTo(c);
        }},
      {dataField:"tanggal_bayar",caption:"Tgl. Bayar",width:120,alignment:"center",
        cellTemplate:function(c,o){$('<span style="font-size:12.5px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
    ]
  }).dxDataGrid("instance");

  refreshAll();
});

function refreshAll(){
  grid&&grid.refresh();
  loadRingkasan();
}

function loadRingkasan(){
  var p={tahun:curTahun};
  if(curJenis) p.jenis_iuran_id=curJenis;
  $.getJSON(urlRingkasan,p,function(r){
    document.getElementById('sLunas').textContent   = r.lunas;
    document.getElementById('sSebagian').textContent = r.sebagian;
    document.getElementById('sBelum').textContent   = r.belum;
    document.getElementById('sTunggakan').textContent = rupiah(r.total_tunggakan);
    document.getElementById('sTunggakan').style.color = r.total_tunggakan>0?'var(--stempel)':'var(--tinta)';
  });
}
</script>
@endif
@endpush
