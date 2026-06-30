@extends('layouts.app')
@section('judul','Gang Saya')
@section('content')

@if(! $koordinator)
{{-- Bukan koordinator --}}
<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:60vh;text-align:center;gap:16px">
  <div style="width:72px;height:72px;border-radius:50%;background:var(--kertas-2);display:flex;align-items:center;justify-content:center">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--redup)" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
  </div>
  <div>
    <h2 style="font-size:18px;font-weight:700;color:var(--tinta);margin-bottom:6px">Anda Belum Terdaftar sebagai Koordinator</h2>
    <p style="font-size:13.5px;color:var(--redup);max-width:380px">Hubungi pengurus RT untuk mendaftarkan Anda sebagai koordinator gang.</p>
    <p style="font-size:12px;color:var(--redup);margin-top:6px">Pastikan akun Anda terhubung ke data warga yang terdaftar sebagai koordinator.</p>
  </div>
</div>

@else
{{-- Koordinator aktif --}}

{{-- Info Card --}}
<div class="mk-info-card">
  <div class="mk-info-icon">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
  </div>
  <div class="mk-info-body">
    <div class="mk-info-label">Koordinator Gang</div>
    <div class="mk-info-name">{{ optional($koordinator->warga)->nama ?? '—' }}</div>
    <div class="mk-info-meta">
      @if($koordinator->gang)
        <span class="mk-gang-badge">{{ $koordinator->gang->nama_gang }}</span>
      @else
        <span style="color:var(--redup);font-size:12.5px">Belum ditugaskan ke gang tertentu</span>
      @endif
      @if($koordinator->keterangan)
        <span style="color:var(--redup);font-size:12.5px">· {{ $koordinator->keterangan }}</span>
      @endif
    </div>
  </div>
  <div class="mk-info-actions">
    <button class="btn" type="button" onclick="openTambahAnggota()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Tambah Anggota
    </button>
  </div>
</div>

{{-- Grid Anggota --}}
<div class="grid-wrap" style="height:calc(100vh - 200px)">
  <div id="gridAnggota"></div>
</div>

{{-- Modal Tambah Anggota --}}
<div class="modal-overlay" id="anggotaModal" onclick="if(event.target===this)this.classList.remove('show')">
  <div class="modal-card mk-card">
    <div class="mk-modal-head">
      <div class="mk-modal-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
      </div>
      <div>
        <h3>Tambah Anggota</h3>
        <p class="mk-modal-sub">Pilih warga yang menjadi anggota gang Anda</p>
      </div>
    </div>
    <div class="mk-modal-body">
      <div class="ff">
        <label>Pilih Warga <span class="req">*</span></label>
        <div id="anggota_kk"></div>
        <div class="fhint">Cari berdasarkan nama atau NIK. Bisa pilih lebih dari satu.</div>
      </div>
    </div>
    <div class="mk-modal-foot">
      <button class="mbtn ghost" onclick="document.getElementById('anggotaModal').classList.remove('show')">Batal</button>
      <button class="mbtn mbtn-save" onclick="simpanAnggota()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg> Simpan
      </button>
    </div>
  </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div class="modal-overlay" id="deleteModal" onclick="if(event.target===this)deleteClose()">
  <div class="modal-card">
    <div class="modal-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></div>
    <h3>Hapus Anggota?</h3>
    <p id="deleteMsg"></p>
    <div class="modal-actions">
      <button class="mbtn ghost" onclick="deleteClose()">Batal</button>
      <button class="mbtn danger" id="deleteConfirmBtn">Ya, Hapus</button>
    </div>
  </div>
</div>

@endif
@endsection

@push('styles')
<style>
.content{max-width:none;padding-bottom:0}

/* Info card koordinator */
.mk-info-card{
  display:flex;align-items:center;gap:16px;
  background:var(--surface);border:1px solid var(--garis);border-radius:14px;
  padding:18px 22px;margin-bottom:16px;box-shadow:var(--shadow);
}
.mk-info-icon{
  flex:0 0 48px;width:48px;height:48px;border-radius:13px;
  background:var(--daun-pucat);color:var(--daun);
  display:flex;align-items:center;justify-content:center;
}
.mk-info-icon svg{width:24px;height:24px}
.mk-info-body{flex:1;min-width:0}
.mk-info-label{font-size:11px;font-weight:700;color:var(--daun);letter-spacing:.1em;text-transform:uppercase;margin-bottom:3px}
.mk-info-name{font-size:18px;font-weight:800;color:var(--tinta);margin-bottom:5px}
.mk-info-meta{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.mk-gang-badge{display:inline-flex;align-items:center;font-size:12px;font-weight:700;padding:3px 11px;border-radius:20px;background:var(--hutan);color:#fff}
.mk-info-actions{flex-shrink:0}

/* Grid */
.grid-wrap{background:var(--surface);border:1px solid var(--garis);border-radius:var(--radius);overflow:auto;box-shadow:var(--shadow)}
#gridAnggota,.dx-widget{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
#gridAnggota .dx-datagrid{border:none;color:var(--tinta)}
#gridAnggota .dx-datagrid-headers{background:var(--kertas-2);border-bottom:1px solid var(--garis)}
#gridAnggota .dx-datagrid-headers .dx-header-row>td{font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:12px 14px;border:none}
#gridAnggota .dx-data-row>td{padding:12px 14px;font-size:13.5px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis);vertical-align:middle}
#gridAnggota .dx-datagrid-rowsview .dx-data-row:hover>td{background:#FBFAF5}
#gridAnggota .dx-pager{background:var(--kertas);border-top:1px solid var(--garis);padding:10px 14px}
#gridAnggota .dx-toolbar{display:none}

.gc-nik{font-family:'IBM Plex Mono',monospace;font-size:11.5px;color:var(--redup)}
.gc-sw-aktif{display:inline-flex;align-items:center;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--daun-pucat);color:#1B5E3F}
.gc-sw-pindah{display:inline-flex;align-items:center;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--emas-soft);color:#7a5c00}
.gc-sw-meninggal{display:inline-flex;align-items:center;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--kertas-2);color:var(--redup);border:1px solid var(--garis)}
.wg-ic-btn{width:28px;height:28px;border-radius:7px;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:.13s}
.wg-ic-btn svg{width:13px;height:13px}
.wg-ic-del{background:var(--stempel-soft);color:var(--stempel)}.wg-ic-del:hover{background:var(--stempel);color:#fff}

/* Modal */
.mk-card{max-width:480px;width:100%;padding:0;text-align:left}
.mk-modal-head{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--garis)}
.mk-modal-icon{flex:0 0 34px;width:34px;height:34px;border-radius:9px;background:var(--daun-pucat);color:var(--daun);display:flex;align-items:center;justify-content:center}
.mk-modal-icon svg{width:17px;height:17px}
.mk-modal-head h3{font-size:15px;font-weight:800;margin:0 0 1px}
.mk-modal-sub{font-size:12px;color:var(--redup);margin:0}
.mk-modal-body{padding:18px 20px}
.ff{margin-bottom:13px;display:flex;flex-direction:column}
.ff>label{font-size:12px;font-weight:700;margin-bottom:5px;color:var(--redup)}
.fhint{font-size:11.5px;color:var(--redup);margin-top:4px}
.req{color:var(--stempel)}
.mk-modal-foot{display:flex;gap:10px;justify-content:flex-end;padding:12px 20px;border-top:1px solid var(--garis);background:var(--kertas);border-radius:0 0 14px 14px}
.mk-modal-foot .mbtn{flex:0 0 auto}
.mbtn-save{display:inline-flex;align-items:center;gap:7px;background:var(--hutan);color:#fff;font-weight:700;font-size:14px;padding:10px 20px;border-radius:10px;border:none;cursor:pointer;transition:.14s}
.mbtn-save:hover{background:var(--hutan-2)}.mbtn-save svg{width:14px;height:14px}
@media(max-width:640px){
  .mk-info-card{flex-direction:column;align-items:stretch;gap:10px}
  .mk-info-name{font-size:18px;white-space:normal}
  .mk-info-actions{display:flex;flex-wrap:wrap;gap:8px}
  .grid-wrap{height:calc(100vh - 210px)}
}
</style>
@endpush

@push('scripts')
@if($koordinator)
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});

const urlAnggotaList   = "{{ route('my-koordinator.anggota.list') }}";
const urlAnggotaAdd    = "{{ route('my-koordinator.anggota.add') }}";
const urlAnggotaRemove = "{{ route('my-koordinator.anggota.remove') }}";
const urlKkLookup      = "{{ route('my-koordinator.kk-lookup') }}";

let grid;

$(function(){
  grid = $("#gridAnggota").dxDataGrid({
    dataSource: new DevExpress.data.CustomStore({key:"id", load:()=>$.getJSON(urlAnggotaList)}),
    showBorders:false, showColumnLines:true, showRowLines:true,
    rowAlternationEnabled:true, width:"100%", height:"100%",
    columnAutoWidth:false,
    scrolling: { useNative: true, showScrollbar: 'always', mode: 'standard' },
    paging:{pageSize:50},
    pager:{visible:true,displayMode:"compact",showPageSizeSelector:true,allowedPageSizes:[25,50,"all"],showInfo:true},
    columns:[
      {caption:"No.",width:55,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"kepala_keluarga",caption:"Kepala Keluarga",width:280,
        cellTemplate:function(c,o){
          var d=$('<div>');
          $('<div style="font-weight:600;font-size:13.5px">').text(o.value||'—').appendTo(d);
          $('<div class="gc-nik">').text(o.row.data.no_kk||'').appendTo(d);
          d.appendTo(c);
        }},
      {dataField:"blok_no",caption:"Blok/Rumah",width:410,
        cellTemplate:function(c,o){
          if(!o.value){$('<span style="color:var(--garis-2)">—</span>').appendTo(c);return;}
          $('<span style="display:inline-flex;font-size:12px;font-weight:700;padding:2px 10px;border-radius:20px;background:var(--biru-soft);color:#1a3d52">').text(o.value).appendTo(c);
        }},
      {dataField:"no_telepon",caption:"No. Telepon",width:150,
        cellTemplate:function(c,o){
          $('<span style="font-family:\'IBM Plex Mono\',monospace;font-size:13px">').text(o.value||'—').appendTo(c);
        }},
      {dataField:"jumlah_jiwa",caption:"Jiwa",width:125,alignment:"center",
        cellTemplate:function(c,o){
          $('<span style="display:inline-flex;align-items:center;justify-content:center;min-width:28px;height:28px;border-radius:7px;background:var(--kertas-2);border:1px solid var(--garis);font-size:12.5px;font-weight:700;color:var(--redup)">').text(o.value||0).appendTo(c);
        }},
      {caption:"Aksi",width:100,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){
          $('<button class="wg-ic-btn wg-ic-del" title="Hapus dari anggota saya"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button>')
            .on('click',function(){
              var d=o.row.data;
              document.getElementById('deleteMsg').textContent='"'+d.nama+'" akan dihapus dari daftar anggota Anda.';
              document.getElementById('deleteConfirmBtn').onclick=function(){
                $.ajax({url:urlAnggotaRemove,type:'POST',data:{id:d.id}})
                  .done(function(r){DevExpress.ui.notify(r.message,'success',2500);deleteClose();grid.refresh();})
                  .fail(function(){DevExpress.ui.notify('Gagal','error',2500);});
              };
              document.getElementById('deleteModal').classList.add('show');
            }).appendTo(c);
        }},
    ]
  }).dxDataGrid("instance");

  // Init TagBox KK
  $("#anggota_kk").dxTagBox({
    dataSource: new DevExpress.data.CustomStore({
      key:'id',
      load:(opts)=>$.getJSON(urlKkLookup,{q:opts.searchValue||''})
    }),
    valueExpr:'id', displayExpr:'label',
    searchEnabled:true, minSearchLength:0,
    showSelectionControls:true,
    placeholder:'Cari nama kepala keluarga, blok, atau No. KK...',
    noDataText:'Semua KK aktif sudah ditambahkan.'
  });
});

function openTambahAnggota(){
  var tb=$("#anggota_kk").dxTagBox("instance");
  tb.option("value",[]);
  tb.getDataSource().reload();
  document.getElementById('anggotaModal').classList.add('show');
}

function simpanAnggota(){
  var ids=$("#anggota_kk").dxTagBox("instance").option("value");
  if(!ids||!ids.length){DevExpress.ui.notify("Pilih minimal satu KK","error",2500);return;}
  $.ajax({url:urlAnggotaAdd,type:'POST',data:{'kartu_keluarga_id[]':ids}})
    .done(function(r){
      DevExpress.ui.notify(r.message,'success',2500);
      document.getElementById('anggotaModal').classList.remove('show');
      grid.refresh();
    })
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||'Gagal','error',3000);});
}

function deleteClose(){document.getElementById('deleteModal').classList.remove('show');}
document.addEventListener('keydown',function(e){
  if(e.key==='Escape'){
    document.getElementById('anggotaModal').classList.remove('show');
    deleteClose();
  }
});
</script>
@endif
@endpush
