@extends('layouts.app')
@section('judul','Pengumuman RT')
@section('content')

<div class="pg-toolbar">
  <h2 class="pg-title">Pengumuman RT</h2>
  <div class="pg-actions">
    <div id="filterKategori"></div>
    <button class="btn" id="btnTambah" type="button" onclick="pgAdd()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Buat Pengumuman
    </button>
    <button class="btn btn-hapus" id="btnHapus" type="button" onclick="pgDelete()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
      Hapus
    </button>
  </div>
</div>

<div class="grid-wrap" style="height:calc(100vh - 130px)">
  <div id="gridPengumuman"></div>
</div>

{{-- Modal Buat/Edit Pengumuman --}}
<div class="modal-overlay" id="pgModal">
  <div class="modal-card pg-card">
    <div class="pg-head">
      <div class="pg-head-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      </div>
      <h3 id="pgModalTitle">Buat Pengumuman</h3>
    </div>
    <form id="formPg" onsubmit="return false" class="pg-body">
      <input type="hidden" id="pg_id">
      <div class="ff2">
        <div class="ff"><label>Judul <span class="req">*</span></label><div id="pg_judul"></div></div>
        <div class="ff"><label>Kategori <span class="req">*</span></label><div id="pg_kategori"></div></div>
      </div>
      <div class="ff2">
        <div class="ff"><label>Tanggal <span class="req">*</span></label><div id="pg_tanggal"></div></div>
        <div class="ff">
          <label>&nbsp;</label>
          <div class="pg-flags">
            <label class="pg-flag-item">
              <div id="pg_penting"></div>
              <span>Tandai Penting 🔴</span>
            </label>
            <label class="pg-flag-item">
              <div id="pg_aktif"></div>
              <span>Aktif (tampil ke warga)</span>
            </label>
          </div>
        </div>
      </div>
      <div class="ff">
        <label>Isi Pengumuman</label>
        <div id="pg_isi"></div>
      </div>
      <div class="ff">
        <label>File Lampiran <small class="hint">(PDF, DOC, gambar — maks. 10 MB)</small></label>
        <div class="pg-file-wrap" id="pgFileWrap">
          <div class="pg-file-current" id="pgFileCurrent" style="display:none">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <span id="pgFileNama">—</span>
            <button type="button" class="pg-file-del" onclick="hapusFile()" title="Hapus file">×</button>
          </div>
          <div id="pg_file_uploader"></div>
        </div>
      </div>
    </form>
    <div class="pg-foot">
      <button class="mbtn ghost" onclick="pgClose()">Batal</button>
      <button class="mbtn mbtn-save" onclick="pgSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Terbitkan
      </button>
    </div>
  </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div class="modal-overlay" id="deleteModal" onclick="if(event.target===this)deleteClose()">
  <div class="modal-card">
    <div class="modal-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></div>
    <h3>Hapus Pengumuman?</h3><p id="deleteMsg"></p>
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
.pg-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;gap:16px;flex-wrap:wrap}
.pg-title{font-size:22px;font-weight:800;letter-spacing:-.02em;color:var(--tinta);margin:0}
.pg-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.btn-hapus{background:var(--stempel)!important;color:#fff!important}.btn-hapus:hover{filter:brightness(1.08)}
.btn[disabled]{opacity:.45;cursor:not-allowed;pointer-events:none}
.grid-wrap{background:var(--surface);border:1px solid var(--garis);overflow:auto;box-shadow:var(--shadow)}
#gridPengumuman,.dx-widget{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
#gridPengumuman .dx-datagrid{border:none;color:var(--tinta)}
#gridPengumuman .dx-datagrid-headers{background:var(--kertas-2);border-bottom:1px solid var(--garis)}
#gridPengumuman .dx-datagrid-headers .dx-header-row>td{font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:12px 14px;border:none}
#gridPengumuman .dx-data-row>td{padding:12px 14px;font-size:13.5px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis);vertical-align:middle}
#gridPengumuman .dx-datagrid-rowsview .dx-data-row:hover>td{background:#FBFAF5}
#gridPengumuman .dx-datagrid-rowsview .dx-row-focused>td{background:var(--daun-pucat)!important;color:#155234!important}
#gridPengumuman .dx-pager{background:var(--kertas);border-top:1px solid var(--garis);padding:8px 14px}
#gridPengumuman .dx-toolbar{display:none}

/* Kategori badges */
.pg-kat{display:inline-flex;align-items:center;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px}
.pg-kat-informasi{background:var(--biru-soft);color:#1a3d52}
.pg-kat-rapat{background:var(--daun-pucat);color:#14532D}
.pg-kat-kegiatan{background:var(--emas-soft);color:#7a5c00}
.pg-kat-keuangan{background:#E8F5E9;color:#2D6A4F}
.pg-kat-darurat{background:var(--stempel-soft);color:#9A3422}
.pg-kat-lainnya{background:var(--kertas-2);color:var(--redup);border:1px solid var(--garis)}
.pg-penting-badge{display:inline-flex;align-items:center;gap:4px;font-size:10.5px;font-weight:700;padding:2px 7px;border-radius:20px;background:#FFE0E0;color:#C0392B;margin-left:6px}

.wg-ic-btn{width:28px;height:28px;border-radius:7px;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:.13s}
.wg-ic-btn svg{width:13px;height:13px}
.wg-ic-edit{background:var(--emas-soft);color:#7a5c00}.wg-ic-edit:hover{background:var(--emas);color:#fff}
.wg-ic-del{background:var(--stempel-soft);color:var(--stempel)}.wg-ic-del:hover{background:var(--stempel);color:#fff}
.wg-ic-dl{background:var(--biru-soft);color:var(--biru)}.wg-ic-dl:hover{background:var(--biru);color:#fff}

/* Modal */
.pg-card{max-width:640px;width:100%;padding:0;text-align:left}
.pg-head{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--garis)}
.pg-head-icon{flex:0 0 34px;width:34px;height:34px;border-radius:9px;background:var(--daun-pucat);color:var(--daun);display:flex;align-items:center;justify-content:center}
.pg-head-icon svg{width:17px;height:17px}
.pg-head h3{font-size:15px;font-weight:800;margin:0 0 1px}
.pg-body{padding:18px 20px;display:flex;flex-direction:column;gap:0;max-height:65vh;overflow-y:auto}
.ff{margin-bottom:13px;display:flex;flex-direction:column}
.ff>label{font-size:12px;font-weight:700;margin-bottom:5px;color:var(--redup)}
.ff2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.hint{font-size:11px;color:#9aa89f;font-weight:500}.req{color:var(--stempel)}
.pg-flags{display:flex;flex-direction:column;gap:8px;padding:8px 10px;background:var(--kertas);border:1px solid var(--garis);border-radius:9px;height:100%;justify-content:center}
.pg-flag-item{display:flex;align-items:center;gap:8px;font-size:12.5px;font-weight:600;cursor:pointer}
.pg-file-wrap{display:flex;flex-direction:column;gap:8px}
.pg-file-current{display:flex;align-items:center;gap:8px;padding:8px 12px;background:var(--biru-soft);border:1px solid #90CAF9;border-radius:8px;font-size:12.5px;font-weight:600;color:var(--biru)}
.pg-file-del{background:none;border:none;color:var(--stempel);font-size:18px;cursor:pointer;font-weight:700;line-height:1;padding:0 4px}
.pg-foot{display:flex;gap:10px;justify-content:flex-end;padding:12px 20px;border-top:1px solid var(--garis);background:var(--kertas);border-radius:0 0 14px 14px}
.pg-foot .mbtn{flex:0 0 auto;min-width:90px}
.pg-foot .mbtn.ghost{background:var(--stempel-soft);color:var(--stempel);border:1.5px solid #f5c6bb}
.pg-foot .mbtn.ghost:hover{background:#fde5e0}
.mbtn-save{display:inline-flex;align-items:center;gap:7px;background:var(--hutan);color:#fff;font-weight:700;font-size:13px;padding:8px 18px;border-radius:10px;border:none;cursor:pointer;transition:.14s}
.mbtn-save:hover{background:var(--hutan-2)}
.mbtn-save svg{width:13px;height:13px}
@media(max-width:640px){
  .pg-toolbar{flex-direction:column;align-items:stretch;gap:10px}
  .pg-title{font-size:18px;white-space:normal}
  .pg-actions{display:flex;flex-wrap:wrap;gap:8px}
  .grid-wrap{height:calc(100vh - 210px)}
}
</style>
@endpush

@push('scripts')
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});
const urlList      = "{{ route('pengumuman.list') }}";
const urlSave      = "{{ route('pengumuman.save') }}";
const urlDelete    = "{{ route('pengumuman.delete') }}";
const urlHapusFile = "{{ route('pengumuman.hapus-file') }}";
const urlUpload    = "{{ route('pengumuman.save') }}"; // upload handled in save

let grid, focusedRow=null;
const KATEGORI = [
  {id:'informasi',l:'Informasi'},{id:'rapat',l:'Hasil Rapat'},
  {id:'kegiatan',l:'Kegiatan'},{id:'keuangan',l:'Keuangan'},
  {id:'darurat',l:'Darurat'},{id:'lainnya',l:'Lainnya'},
];

$(function(){
  var p=window.__perms||{};
  if(!p.add) document.getElementById('btnTambah').disabled=true;
  if(!p.delete) document.getElementById('btnHapus').disabled=true;

  $("#filterKategori").dxSelectBox({
    dataSource:[{id:null,l:'Semua Kategori'},...KATEGORI],
    valueExpr:'id',displayExpr:'l',value:null,width:160,
    onValueChanged:function(e){grid.getDataSource().reload();}
  });

  grid=$("#gridPengumuman").dxDataGrid({
    dataSource:new DevExpress.data.CustomStore({key:"id",load:function(){
      var kat=$("#filterKategori").dxSelectBox("instance").option("value");
      var p={}; if(kat) p.kategori=kat;
      return $.getJSON(urlList,p);
    }}),
    showBorders:false,showColumnLines:true,showRowLines:true,
    rowAlternationEnabled:true,width:"100%",height:"100%",
    columnAutoWidth:true,
    scrolling: { useNative: true, showScrollbar: 'always', mode: 'standard' },
    focusedRowEnabled:true,
    onFocusedRowChanged:e=>{focusedRow=e.row?e.row.data:null;},
    paging:{pageSize:50},
    pager:{visible:true,displayMode:"compact",showPageSizeSelector:true,allowedPageSizes:[25,50,"all"],showInfo:true},
    columns:[
      {caption:"No.",width:52,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"tanggal",caption:"Tanggal",width:110},
      {dataField:"kategori_label",caption:"Kategori",width:120,
        cellTemplate:function(c,o){
          var d=o.row.data;
          $('<span class="pg-kat pg-kat-'+d.kategori+'">').text(o.value).appendTo(c);
        }},
      {dataField:"judul",caption:"Judul Pengumuman",minWidth:220,
        cellTemplate:function(c,o){
          var d=o.row.data;
          var wrap=$('<div>');
          var title=$('<div style="font-weight:700;font-size:14px">').text(o.value||'—');
          if(d.penting) $('<span class="pg-penting-badge">🔴 Penting</span>').appendTo(title);
          title.appendTo(wrap);
          if(d.isi) $('<div style="font-size:12px;color:var(--redup);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:350px">').text(d.isi).appendTo(wrap);
          wrap.appendTo(c);
        }},
      {dataField:"nama_file",caption:"Lampiran",width:160,
        cellTemplate:function(c,o){
          if(!o.value){$('<span style="color:var(--garis-2);font-size:12px">Tidak ada lampiran</span>').appendTo(c);return;}
          $('<a style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:var(--biru)" href="'+o.row.data.file_url+'" target="_blank" download>'+
            '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>'+
            o.value+'</a>').appendTo(c);
        }},
      {dataField:"aktif",caption:"Status",width:90,alignment:"center",
        cellTemplate:function(c,o){
          var on=o.value===true||o.value===1;
          $('<span style="display:inline-flex;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;background:'+(on?'var(--daun-pucat)':'var(--stempel-soft)')+';color:'+(on?'#1B5E3F':'#9A3422')+'">').text(on?'Aktif':'Nonaktif').appendTo(c);
        }},
      {dataField:"pembuat",caption:"Dibuat",width:120,cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
      {caption:"",width:90,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){
          var d=o.row.data;
          var wrap=$('<div style="display:flex;gap:4px;justify-content:center">');
          $('<button class="wg-ic-btn wg-ic-edit" title="Ubah"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg></button>').on('click',function(){pgEdit(d);}).appendTo(wrap);
          $('<button class="wg-ic-btn wg-ic-del" title="Hapus"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button>').on('click',function(){pgDeleteConfirm(d);}).appendTo(wrap);
          wrap.appendTo(c);
        }},
    ]
  }).dxDataGrid("instance");

  initEditors();
});

function initEditors(){
  $("#pg_judul").dxTextBox({placeholder:"Judul pengumuman..."});
  $("#pg_isi").dxTextArea({placeholder:"Isi pengumuman (opsional)...",height:100,autoResizeEnabled:true});
  $("#pg_tanggal").dxDateBox({type:"date",displayFormat:"dd/MM/yyyy",value:new Date()});
  $("#pg_penting").dxSwitch({value:false});
  $("#pg_aktif").dxSwitch({value:true});
  $("#pg_kategori").dxSelectBox({dataSource:KATEGORI,valueExpr:'id',displayExpr:'l',value:'informasi'});
  $("#pg_file_uploader").dxFileUploader({
    uploadMode:'useForm', multiple:false, showFileList:true,
    allowedFileExtensions:['.pdf','.doc','.docx','.xls','.xlsx','.jpg','.jpeg','.png','.zip'],
    maxFileSize:10485760,
    labelText:'atau seret file ke sini',
    selectButtonText:'Pilih File Lampiran',
    accept:'.pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip'
  });
}

function pgAdd(){
  $("#pg_id").val('');
  document.getElementById('pgModalTitle').textContent='Buat Pengumuman';
  $("#pg_judul").dxTextBox("instance").option("value","");
  $("#pg_isi").dxTextArea("instance").option("value","");
  $("#pg_tanggal").dxDateBox("instance").option("value",new Date());
  $("#pg_kategori").dxSelectBox("instance").option("value","informasi");
  $("#pg_penting").dxSwitch("instance").option("value",false);
  $("#pg_aktif").dxSwitch("instance").option("value",true);
  $("#pg_file_uploader").dxFileUploader("instance").reset();
  document.getElementById('pgFileCurrent').style.display='none';
  document.getElementById('pgModal').classList.add('show');
}

function pgEdit(d){
  $("#pg_id").val(d.id);
  document.getElementById('pgModalTitle').textContent='Ubah Pengumuman';
  $("#pg_judul").dxTextBox("instance").option("value",d.judul||"");
  $("#pg_isi").dxTextArea("instance").option("value",d.isi||"");
  $("#pg_tanggal").dxDateBox("instance").option("value",d.tanggal_raw?new Date(d.tanggal_raw):new Date());
  $("#pg_kategori").dxSelectBox("instance").option("value",d.kategori||"informasi");
  $("#pg_penting").dxSwitch("instance").option("value",d.penting===true||d.penting===1);
  $("#pg_aktif").dxSwitch("instance").option("value",d.aktif===true||d.aktif===1);
  $("#pg_file_uploader").dxFileUploader("instance").reset();
  if(d.nama_file){
    document.getElementById('pgFileNama').textContent=d.nama_file;
    document.getElementById('pgFileCurrent').style.display='flex';
  } else {
    document.getElementById('pgFileCurrent').style.display='none';
  }
  document.getElementById('pgModal').classList.add('show');
}

function pgSave(){
  var judul=$("#pg_judul").dxTextBox("instance").option("value");
  if(!judul){DevExpress.ui.notify("Judul wajib diisi","error",2500);return;}

  var formData=new FormData();
  formData.append('_token',$('meta[name="csrf-token"]').attr('content'));
  formData.append('id',$("#pg_id").val());
  formData.append('judul',judul);
  formData.append('kategori',$("#pg_kategori").dxSelectBox("instance").option("value"));
  formData.append('isi',$("#pg_isi").dxTextArea("instance").option("value")||"");
  formData.append('tanggal',fmtDate($("#pg_tanggal").dxDateBox("instance").option("value")));
  formData.append('penting',$("#pg_penting").dxSwitch("instance").option("value")?1:0);
  formData.append('aktif',$("#pg_aktif").dxSwitch("instance").option("value")?1:0);

  // File lampiran
  var fileInst=$("#pg_file_uploader").dxFileUploader("instance");
  var files=fileInst.option("value");
  if(files&&files.length>0) formData.append('file_lampiran',files[0]);

  $.ajax({url:urlSave,type:"POST",data:formData,processData:false,contentType:false})
    .done(function(r){DevExpress.ui.notify(r.message,"success",2500);pgClose();grid.refresh();})
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);});
}

function hapusFile(){
  var id=$("#pg_id").val();
  if(!id){document.getElementById('pgFileCurrent').style.display='none';return;}
  $.ajax({url:urlHapusFile,type:"POST",data:{id:id}})
    .done(function(r){DevExpress.ui.notify(r.message,"success",2000);document.getElementById('pgFileCurrent').style.display='none';grid.refresh();})
    .fail(function(){DevExpress.ui.notify("Gagal","error",2000);});
}

function pgDelete(){
  if(!focusedRow){DevExpress.ui.notify("Pilih baris terlebih dahulu","warning",2500);return;}
  pgDeleteConfirm(focusedRow);
}
function pgDeleteConfirm(d){
  document.getElementById('deleteMsg').textContent='"'+d.judul+'" akan dihapus beserta file lampirannya.';
  document.getElementById('deleteConfirmBtn').onclick=function(){
    $.ajax({url:urlDelete,type:"POST",data:{id:d.id}})
      .done(function(r){DevExpress.ui.notify(r.message,"success",2500);deleteClose();focusedRow=null;grid.refresh();})
      .fail(function(){DevExpress.ui.notify("Gagal","error",3000);});
  };
  document.getElementById('deleteModal').classList.add('show');
}

function fmtDate(d){if(!d)return '';var dt=new Date(d);return dt.getFullYear()+'-'+String(dt.getMonth()+1).padStart(2,'0')+'-'+String(dt.getDate()).padStart(2,'0');}
function pgClose(){document.getElementById('pgModal').classList.remove('show');}
function deleteClose(){document.getElementById('deleteModal').classList.remove('show');}
document.addEventListener('keydown',function(e){if(e.key==='Escape'){pgClose();deleteClose();}});
</script>
@endpush
