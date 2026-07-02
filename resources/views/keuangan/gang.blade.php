@extends('layouts.app')
@section('judul','Master Gang')
@section('content')

<div class="keu-toolbar">
  <h2 class="keu-title">Gang &amp; Koordinator</h2>
  <div class="keu-actions">
    <button class="btn" id="btnTambah" type="button" onclick="gangAdd()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Tambah Gang
    </button>
    <button class="btn btn-ubah" id="btnUbah" type="button" onclick="gangEdit()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg> Ubah
    </button>
    <button class="btn btn-hapus" id="btnHapus" type="button" onclick="gangDelete()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg> Hapus
    </button>
  </div>
</div>
<div class="grid-wrap"><div id="gridGang"></div></div>

{{-- Modal Gang --}}
<div class="modal-overlay" id="gangModal">
  <div class="modal-card keu-card">
    <div class="keu-head">
      <div class="keu-head-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
      <h3 id="gangModalTitle">Tambah Gang</h3>
    </div>
    <form id="formGang" onsubmit="return false" class="keu-body">
      <input type="hidden" id="g_id">
      <div class="ff"><label>Nama Gang <span class="req">*</span></label><div id="g_nama"></div></div>
      <div class="ff"><label>Keterangan</label><div id="g_keterangan"></div></div>
      <div class="mf-switch">
        <div id="g_aktif"></div>
        <label class="mf-switch-lbl">Gang Aktif</label>
      </div>
    </form>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="gangClose()">Batal</button>
      <button class="mbtn mbtn-save" onclick="gangSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Simpan
      </button>
    </div>
  </div>
</div>

{{-- Modal Konfirmasi --}}
<div class="modal-overlay" id="deleteModal" onclick="if(event.target===this)deleteClose()">
  <div class="modal-card">
    <div class="modal-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></div>
    <h3 id="deleteTitle">Hapus?</h3><p id="deleteMsg"></p>
    <div class="modal-actions">
      <button class="mbtn ghost" onclick="deleteClose()">Batal</button>
      <button class="mbtn danger" id="deleteConfirmBtn">Ya, Hapus</button>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.content{max-width:none;padding-bottom:0}
.keu-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;gap:16px}
.keu-title{font-size:24px;font-weight:800;letter-spacing:-.02em;color:var(--tinta);margin:0}
.keu-actions{display:flex;gap:8px;align-items:center}
.grid-wrap{background:var(--surface);border:1px solid var(--garis);overflow:auto;box-shadow:var(--shadow);height:calc(100vh - 162px)}
.btn-ubah{background:var(--emas)!important;color:#fff!important}.btn-ubah:hover{filter:brightness(1.08)}
.btn-hapus{background:var(--stempel)!important;color:#fff!important}.btn-hapus:hover{filter:brightness(1.08)}
.btn[disabled]{opacity:.45;cursor:not-allowed;pointer-events:none}
#gridGang,.dx-widget{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
#gridGang .dx-datagrid{border:none;color:var(--tinta)}
#gridGang .dx-datagrid-headers{background:var(--kertas-2);border-bottom:1px solid var(--garis)}
#gridGang .dx-datagrid-headers .dx-header-row>td{font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:13px 14px;border:none}
#gridGang .dx-data-row>td{padding:11px 14px;font-size:13.5px;border-bottom:1px solid var(--garis);border-right:none;vertical-align:middle}
#gridGang .dx-datagrid-rowsview .dx-data-row:hover>td{background:#FBFAF5}
#gridGang .dx-datagrid-rowsview .dx-row-focused>td{background:var(--daun-pucat)!important;color:#155234!important}
#gridGang .dx-pager{background:var(--kertas);border-top:1px solid var(--garis);padding:10px 14px}
#gridGang .dx-toolbar{display:none}
.gc-pill{display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:700;padding:3px 11px;border-radius:20px}
.gc-pill::before{content:"";width:6px;height:6px;border-radius:50%}
.gc-pill.on{background:var(--daun-pucat);color:#1B5E3F}.gc-pill.on::before{background:#2D6A4F}
.gc-pill.off{background:var(--stempel-soft);color:#9A3422}.gc-pill.off::before{background:var(--stempel)}
.keu-card{max-width:480px;width:100%;padding:0;text-align:left}
.keu-head{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--garis)}
.keu-head-icon{flex:0 0 34px;width:34px;height:34px;border-radius:9px;background:var(--daun-pucat);color:var(--daun);display:flex;align-items:center;justify-content:center}
.keu-head-icon svg{width:17px;height:17px}
.keu-head h3{font-size:15px;font-weight:800;margin:0 0 1px}
.keu-body{padding:18px 20px;display:flex;flex-direction:column;gap:0}
.ff{margin-bottom:13px;display:flex;flex-direction:column}
.ff>label{font-size:12px;font-weight:700;margin-bottom:5px;color:var(--redup)}
.fhint{font-size:11.5px;color:var(--redup);margin-top:4px}
.req{color:var(--stempel)}
.mf-switch{display:flex;align-items:center;gap:8px;margin-top:4px}
.keu-foot{display:flex;gap:10px;justify-content:flex-end;padding:12px 20px;border-top:1px solid var(--garis);background:var(--kertas);border-radius:0 0 14px 14px}
.keu-foot .mbtn{flex:0 0 auto;min-width:90px}
.keu-foot .mbtn.ghost{background:var(--stempel-soft);color:var(--stempel);border:1.5px solid #f5c6bb}
.keu-foot .mbtn.ghost:hover{background:#fde5e0}
.mbtn-save{display:inline-flex;align-items:center;gap:7px;background:var(--hutan);color:#fff;font-weight:700;font-size:13px;padding:8px 18px;border-radius:10px;border:none;cursor:pointer;transition:.14s}
.mbtn-save:hover{background:var(--hutan-2)}
.mbtn-save svg{width:13px;height:13px}
@media(max-width:640px){
  .keu-toolbar{flex-direction:column;align-items:stretch;gap:10px}
  .keu-title{font-size:18px;white-space:normal}
  .keu-actions{display:flex;flex-wrap:wrap;gap:8px}
  .grid-wrap{height:calc(100vh - 210px)}
}
</style>
@endpush

@push('scripts')
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});
const urlList  = "{{ route('gang.list') }}";
const urlSave  = "{{ route('gang.save') }}";
const urlDelete= "{{ route('gang.delete') }}";

let grid, focusedRow = null;

$(function(){
  var p = window.__perms||{};
  if(!p.add)    document.getElementById('btnTambah').disabled=true;
  if(!p.edit)   document.getElementById('btnUbah').disabled=true;
  if(!p.delete) document.getElementById('btnHapus').disabled=true;

  grid = $("#gridGang").dxDataGrid({
    dataSource: new DevExpress.data.CustomStore({key:"id", load:()=>$.getJSON(urlList)}),
    showBorders:false, showColumnLines:true, showRowLines:true,
    rowAlternationEnabled:true, width:"100%", height:"100%",
    columnAutoWidth:true,
    scrolling: { useNative: true, showScrollbar: 'always', mode: 'standard' },
    focusedRowEnabled:true,
    onFocusedRowChanged: e=>{ focusedRow = e.row?e.row.data:null; },
    paging:{pageSize:50},
    pager:{visible:true,displayMode:"compact",showPageSizeSelector:true,allowedPageSizes:[20,50,"all"],showInfo:true},
    columns:[
      {caption:"No.",width:52,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"nama_gang",caption:"Nama Gang",minWidth:160,
        cellTemplate:function(c,o){$('<span style="font-weight:600">').text(o.value).appendTo(c);}},
      {dataField:"koordinator",caption:"Koordinator",minWidth:160,
        cellTemplate:function(c,o){
          if(!o.value){$('<span style="color:var(--garis-2)">— Belum ditentukan —</span>').appendTo(c);return;}
          $('<span>').text(o.value).appendTo(c);
        }},
      {dataField:"jumlah_kk",caption:"Jumlah KK",width:100,alignment:"center",
        cellTemplate:function(c,o){
          $('<span style="display:inline-flex;align-items:center;justify-content:center;min-width:28px;height:28px;padding:0 6px;border-radius:7px;background:var(--kertas-2);border:1px solid var(--garis);font-size:12.5px;font-weight:700;color:var(--redup)">').text(o.value||0).appendTo(c);
        }},
      {dataField:"keterangan",caption:"Keterangan",minWidth:160,
        cellTemplate:function(c,o){$('<span style="font-size:12.5px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
      {dataField:"aktif",caption:"Status",width:100,alignment:"center",
        cellTemplate:function(c,o){
          var on=(o.value===true||o.value===1);
          $('<span class="gc-pill '+(on?'on':'off')+'">').text(on?'Aktif':'Nonaktif').appendTo(c);
        }},
    ]
  }).dxDataGrid("instance");

  $("#g_nama").dxTextBox({placeholder:"cth. Gang Mawar"});
  $("#g_keterangan").dxTextBox({placeholder:"Keterangan tambahan..."});
  $("#g_aktif").dxSwitch({value:true});
});

function gangAdd(){
  $("#g_id").val('');
  $("#gangModalTitle").text('Tambah Gang');
  $("#g_nama").dxTextBox("instance").option("value","");
  $("#g_keterangan").dxTextBox("instance").option("value","");
  $("#g_aktif").dxSwitch("instance").option("value",true);
  document.getElementById('gangModal').classList.add('show');
}

function gangEdit(){
  if(!focusedRow){DevExpress.ui.notify("Pilih baris terlebih dahulu","warning",2500);return;}
  var d=focusedRow;
  $("#g_id").val(d.id);
  $("#gangModalTitle").text('Ubah Gang');
  $("#g_nama").dxTextBox("instance").option("value",d.nama_gang||"");
  $("#g_keterangan").dxTextBox("instance").option("value",d.keterangan||"");
  $("#g_aktif").dxSwitch("instance").option("value",d.aktif===true||d.aktif===1);
  document.getElementById('gangModal').classList.add('show');
}

function gangSave(){
  var data={
    id:       $("#g_id").val(),
    nama_gang:$("#g_nama").dxTextBox("instance").option("value"),
    keterangan:$("#g_keterangan").dxTextBox("instance").option("value"),
    aktif:    $("#g_aktif").dxSwitch("instance").option("value")?1:0,
  };
  if(!data.nama_gang){DevExpress.ui.notify("Nama gang wajib diisi","error",2500);return;}
  $.ajax({url:urlSave,type:"POST",data:data})
    .done(function(r){DevExpress.ui.notify(r.message,"success",2500);gangClose();grid.refresh();})
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal menyimpan","error",3000);});
}

function gangDelete(){
  if(!focusedRow){DevExpress.ui.notify("Pilih baris terlebih dahulu","warning",2500);return;}
  var d=focusedRow;
  document.getElementById('deleteTitle').textContent="Hapus Gang?";
  document.getElementById('deleteMsg').textContent='"'+d.nama_gang+'" akan dihapus. Pastikan sudah tidak ada KK di gang ini.';
  document.getElementById('deleteConfirmBtn').onclick=function(){
    $.ajax({url:urlDelete,type:"POST",data:{id:d.id}})
      .done(function(r){DevExpress.ui.notify(r.message,"success",2500);deleteClose();focusedRow=null;grid.refresh();})
      .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);deleteClose();});
  };
  document.getElementById('deleteModal').classList.add('show');
}

function gangClose(){document.getElementById('gangModal').classList.remove('show');}
function deleteClose(){document.getElementById('deleteModal').classList.remove('show');}
document.addEventListener('keydown',function(e){if(e.key==='Escape'){gangClose();deleteClose();}});
</script>
@endpush
