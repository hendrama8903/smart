@extends('layouts.app')
@section('judul','Demografi Warga')
@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="{{ route('laporan.warga.index') }}" style="color:var(--redup);font-size:13px">← Laporan Warga</a>
    <h2 style="font-size:22px;font-weight:800;letter-spacing:-.02em;margin:0">Demografi Warga</h2>
  </div>
  <div style="display:flex;gap:10px;align-items:center">
    <label style="font-size:11px;font-weight:700;color:var(--redup);letter-spacing:.06em;text-transform:uppercase">Gang</label>
    <div id="filterGang"></div>
  </div>
</div>

{{-- Summary --}}
<div class="dm-summary">
  <div class="dm-sum-card" style="border-left-color:#2D6A4F">
    <div class="dm-sum-lbl">Total Warga Aktif</div>
    <div class="dm-sum-val" id="sTotalWarga">0</div>
    <div class="dm-sum-sub">jiwa</div>
  </div>
  <div class="dm-sum-card" style="border-left-color:var(--biru)">
    <div class="dm-sum-lbl">Total KK</div>
    <div class="dm-sum-val" id="sTotalKK">0</div>
    <div class="dm-sum-sub">kartu keluarga</div>
  </div>
  <div class="dm-sum-card" style="border-left-color:var(--emas)">
    <div class="dm-sum-lbl">Laki-laki</div>
    <div class="dm-sum-val" id="sLaki">0</div>
    <div class="dm-sum-sub" id="sLakiPct">0%</div>
  </div>
  <div class="dm-sum-card" style="border-left-color:var(--stempel)">
    <div class="dm-sum-lbl">Perempuan</div>
    <div class="dm-sum-val" id="sPerempuan">0</div>
    <div class="dm-sum-sub" id="sPerempuanPct">0%</div>
  </div>
</div>

{{-- Charts grid --}}
<div class="dm-grid">

  {{-- Kelompok Usia --}}
  <div class="dm-card">
    <div class="dm-card-title">Kelompok Usia</div>
    <div id="chartUsia" class="dm-bar-chart"></div>
  </div>

  {{-- Agama --}}
  <div class="dm-card">
    <div class="dm-card-title">Agama</div>
    <div id="chartAgama" class="dm-bar-chart"></div>
  </div>

  {{-- Pendidikan --}}
  <div class="dm-card">
    <div class="dm-card-title">Pendidikan Terakhir</div>
    <div id="chartPendidikan" class="dm-bar-chart"></div>
  </div>

  {{-- Pekerjaan --}}
  <div class="dm-card">
    <div class="dm-card-title">10 Pekerjaan Terbanyak</div>
    <div id="chartPekerjaan" class="dm-bar-chart"></div>
  </div>

  {{-- Status Tinggal --}}
  <div class="dm-card">
    <div class="dm-card-title">Status Tinggal</div>
    <div id="chartStatus" class="dm-bar-chart"></div>
  </div>

  {{-- Hubungan --}}
  <div class="dm-card">
    <div class="dm-card-title">Hubungan dalam KK</div>
    <div id="chartHubungan" class="dm-bar-chart"></div>
  </div>

</div>

@endsection
@push('styles')
<style>
.content{max-width:none;padding-bottom:32px}
.dm-summary{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:18px}
.dm-sum-card{background:var(--surface);border:1px solid var(--garis);border-left:4px solid var(--daun);border-radius:12px;padding:14px 16px}
.dm-sum-lbl{font-size:11px;font-weight:700;color:var(--redup);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px}
.dm-sum-val{font-size:28px;font-weight:800;color:var(--tinta);line-height:1}
.dm-sum-sub{font-size:11.5px;color:var(--redup);margin-top:3px}

.dm-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px}
.dm-card{background:var(--surface);border:1px solid var(--garis);border-radius:14px;padding:18px 20px;box-shadow:var(--shadow)}
.dm-card-title{font-size:13px;font-weight:700;color:var(--tinta);margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--garis)}

/* Horizontal bar chart */
.dm-bar-chart{display:flex;flex-direction:column;gap:8px}
.dm-bar-row{display:flex;align-items:center;gap:10px}
.dm-bar-label{font-size:12px;color:var(--redup);width:110px;flex-shrink:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.dm-bar-wrap{flex:1;height:20px;background:var(--kertas-2);border-radius:6px;overflow:hidden}
.dm-bar-fill{height:100%;border-radius:6px;transition:width .4s ease;min-width:2px;background:var(--daun)}
.dm-bar-num{font-size:12px;font-weight:700;color:var(--tinta);width:50px;text-align:right;flex-shrink:0}

@media(max-width:1100px){.dm-grid{grid-template-columns:1fr 1fr}}
@media(max-width:700px){.dm-summary{grid-template-columns:1fr 1fr}.dm-grid{grid-template-columns:1fr}}
</style>
@endpush
@push('scripts')
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});
const urlData     = "{{ route('laporan.warga.demografi.data') }}";
const urlGangList = "{{ route('gang.lookup') }}";
let curGang=null;

$(function(){
  $.getJSON(urlGangList, function(gangs){
    $("#filterGang").dxSelectBox({
      dataSource:[{id:null,nama_gang:'Semua Gang'},...gangs],
      valueExpr:'id', displayExpr:'nama_gang', value:null, width:170,
      onValueChanged:function(e){curGang=e.value;loadData();}
    });
  });
  loadData();
});

function loadData(){
  var p={}; if(curGang) p.gang_id=curGang;
  $.getJSON(urlData, p, function(d){
    // Summary
    var total=d.total_warga||1;
    document.getElementById('sTotalWarga').textContent = d.total_warga;
    document.getElementById('sTotalKK').textContent    = d.total_kk;
    var l=d.gender['L']||0, p2=d.gender['P']||0;
    document.getElementById('sLaki').textContent        = l;
    document.getElementById('sLakiPct').textContent     = Math.round(l/total*100)+'%';
    document.getElementById('sPerempuan').textContent   = p2;
    document.getElementById('sPerempuanPct').textContent= Math.round(p2/total*100)+'%';

    // Charts
    renderBars('chartUsia',       d.usia,            '#2D6A4F');
    renderBars('chartAgama',      d.agama,           '#6366F1');
    renderBars('chartPendidikan', d.pendidikan,      '#2C5C7A');
    renderPekerjaan('chartPekerjaan', d.pekerjaan);
    renderBars('chartStatus',     d.status_tinggal,  '#B8860B');
    renderBars('chartHubungan',   d.hubungan,        '#B5402C');
  });
}

function renderBars(id, data, color){
  var el=document.getElementById(id); el.innerHTML='';
  var vals=Object.values(data); var maxVal=Math.max(...vals,1);
  Object.entries(data).forEach(function(e){
    var pct=Math.round(e[1]/maxVal*100);
    el.innerHTML+='<div class="dm-bar-row"><span class="dm-bar-label" title="'+e[0]+'">'+e[0]+'</span><div class="dm-bar-wrap"><div class="dm-bar-fill" style="width:'+pct+'%;background:'+(color||'var(--daun)')+'"></div></div><span class="dm-bar-num">'+e[1]+'</span></div>';
  });
  if(!vals.length) el.innerHTML='<p style="color:var(--redup);font-size:12.5px;text-align:center;padding:12px 0">Tidak ada data</p>';
}

function renderPekerjaan(id, rows){
  var el=document.getElementById(id); el.innerHTML='';
  if(!rows.length){el.innerHTML='<p style="color:var(--redup);font-size:12.5px;text-align:center;padding:12px 0">Tidak ada data</p>';return;}
  var maxVal=Math.max(...rows.map(function(r){return r.jumlah;}),1);
  rows.forEach(function(r){
    var pct=Math.round(r.jumlah/maxVal*100);
    el.innerHTML+='<div class="dm-bar-row"><span class="dm-bar-label" title="'+r.pekerjaan+'">'+r.pekerjaan+'</span><div class="dm-bar-wrap"><div class="dm-bar-fill" style="width:'+pct+'%;background:var(--emas)"></div></div><span class="dm-bar-num">'+r.jumlah+'</span></div>';
  });
}
</script>
@endpush
