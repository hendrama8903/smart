@extends('layouts.app')
@section('judul','Master Role')
@section('content')

<div class="keu-toolbar">
  <h2 class="keu-title">Master Role</h2>
  <div class="keu-actions">
    <button class="btn" id="btnTambah" type="button" onclick="roleAdd()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Tambah Role
    </button>
    <button class="btn btn-ubah" id="btnUbah" type="button" onclick="roleEdit()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
      Ubah
    </button>
    <button class="btn btn-hapus" id="btnHapus" type="button" onclick="roleDelete()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
      Hapus
    </button>
  </div>
</div>

<div class="grid-wrap" style="height:calc(100vh - 130px)">
  <div id="gridRole"></div>
</div>

{{-- Modal Role --}}
<div class="modal-overlay" id="roleModal">
  <div class="modal-card keu-card">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:var(--daun-pucat);color:var(--daun)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      </div>
      <div><h3 id="roleModalTitle">Tambah Role</h3><p class="keu-sub">Data peran / hak akses pengguna</p></div>
    </div>
    <form onsubmit="return false" class="keu-body">
      <input type="hidden" id="r_id">
      <div class="ff">
        <label>Nama Role <span class="req">*</span></label>
        <div id="r_nama"></div>
        <div class="fhint" id="namaHint" style="display:none"></div>
      </div>
      <div class="ff"><label>Label (tampilan) <span class="req">*</span></label><div id="r_label"></div></div>
      <div class="ff"><label>Keterangan</label><div id="r_keterangan"></div></div>
    </form>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="roleClose()">Batal</button>
      <button class="mbtn mbtn-save" onclick="roleSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg> Simpan
      </button>
    </div>
  </div>
</div>

{{-- Modal Pengaturan Akses Menu --}}
<div class="modal-overlay" id="menuAccessModal">
  <div class="modal-card" style="max-width:620px;width:100%;padding:0;text-align:left">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:var(--biru-soft);color:var(--biru)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><path d="M17 14v6M14 17h6"/></svg>
      </div>
      <div>
        <h3 id="menuAccessTitle">Pengaturan Akses Menu</h3>
        <p class="keu-sub" id="menuAccessSub">Centang menu yang boleh diakses oleh role ini</p>
      </div>
    </div>
    <div style="padding:16px 20px;max-height:65vh;overflow-y:auto">
      <div class="ma-info">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>Menu dengan <strong>kosong = semua role</strong> akan tetap bisa diakses. Centang di sini untuk memberi akses <em>eksplisit</em> ke role ini.</span>
      </div>

      <div class="ma-quick">
        <button class="ma-btn-quick" onclick="checkAll(true)">✓ Centang Semua</button>
        <button class="ma-btn-quick" onclick="checkAll(false)">✗ Hapus Semua</button>
      </div>

      <div id="menuAccessTree"></div>
    </div>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="menuAccessClose()">Batal</button>
      <button class="mbtn mbtn-save" onclick="saveMenuAccess()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg> Simpan Akses Menu
      </button>
    </div>
  </div>
</div>

{{-- Modal Konfirmasi --}}
<div class="modal-overlay" id="deleteModal" onclick="if(event.target===this)deleteClose()">
  <div class="modal-card">
    <div class="modal-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></div>
    <h3 id="deleteTitle">Hapus Role?</h3><p id="deleteMsg"></p>
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
.keu-title{font-size:22px;font-weight:800;letter-spacing:-.02em;color:var(--tinta);margin:0}
.keu-actions{display:flex;gap:8px}
.btn-ubah{background:var(--emas)!important;color:#fff!important}.btn-ubah:hover{filter:brightness(1.08)}
.btn-hapus{background:var(--stempel)!important;color:#fff!important}.btn-hapus:hover{filter:brightness(1.08)}
.btn[disabled]{opacity:.45;cursor:not-allowed;pointer-events:none}
.grid-wrap{background:var(--surface);border:1px solid var(--garis);border-radius:var(--radius);overflow:auto;box-shadow:var(--shadow)}
#gridRole,.dx-widget{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
#gridRole .dx-datagrid{border:none;color:var(--tinta)}
#gridRole .dx-datagrid-headers{background:var(--kertas-2);border-bottom:1px solid var(--garis)}
#gridRole .dx-datagrid-headers .dx-header-row>td{font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:13px 14px;border:none}
#gridRole .dx-data-row>td{padding:12px 14px;font-size:13.5px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis);vertical-align:middle}
#gridRole .dx-datagrid-rowsview .dx-data-row:hover>td{background:#FBFAF5}
#gridRole .dx-datagrid-rowsview .dx-row-focused>td{background:var(--daun-pucat)!important;color:#155234!important}
#gridRole .dx-pager{display:none}
#gridRole .dx-toolbar{display:none}

/* Menu access tree */
.ma-info{display:flex;align-items:flex-start;gap:8px;padding:10px 12px;background:var(--biru-soft);border-radius:9px;font-size:12.5px;color:#1a3d52;margin-bottom:12px}
.ma-info svg{flex:0 0 14px;margin-top:1px}
.ma-quick{display:flex;gap:8px;margin-bottom:14px}
.ma-btn-quick{font-size:12px;padding:5px 12px;border-radius:7px;border:1.5px solid var(--garis-2);background:var(--kertas);color:var(--redup);cursor:pointer;font-weight:600;transition:.13s}
.ma-btn-quick:hover{border-color:var(--daun);color:var(--daun);background:var(--daun-pucat)}
.ma-group{margin-bottom:8px;border:1px solid var(--garis);border-radius:10px;overflow:hidden}
.ma-group-head{display:flex;align-items:center;gap:10px;padding:9px 14px;background:var(--kertas-2);font-size:13px;font-weight:700;color:var(--tinta)}
.ma-group-head .type-badge{font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:var(--biru-soft);color:#1a3d52}
.ma-item{display:flex;align-items:center;gap:10px;padding:8px 14px 8px 28px;border-top:1px solid var(--garis);cursor:pointer;transition:.1s}
.ma-item:hover{background:#FBFAF5}
.ma-item.child{padding-left:42px}
.ma-item input[type=checkbox]{width:16px;height:16px;accent-color:var(--daun);cursor:pointer;flex:0 0 16px}
.ma-item-name{font-size:13px;font-weight:600;color:var(--tinta);flex:1}
.ma-item-type{font-size:10.5px;font-weight:700;padding:1px 7px;border-radius:20px}
.ma-type-screen{background:var(--daun-pucat);color:#14532D}
.ma-type-button{background:var(--emas-soft);color:#7a5c00}
.ma-type-menu{background:var(--biru-soft);color:#1a3d52}

/* Modal shared */
.keu-card{max-width:480px;width:100%;padding:0;text-align:left}
.keu-head{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--garis)}
.keu-head-icon{flex:0 0 34px;width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center}
.keu-head-icon svg{width:17px;height:17px}
.keu-head h3{font-size:15px;font-weight:800;margin:0 0 1px}
.keu-sub{font-size:12px;color:var(--redup);margin:0}
.keu-body{padding:18px 20px;display:flex;flex-direction:column;gap:0}
.ff{margin-bottom:13px;display:flex;flex-direction:column}
.ff>label{font-size:12px;font-weight:700;margin-bottom:5px;color:var(--redup)}
.fhint{font-size:11.5px;color:var(--redup);margin-top:4px}
.fhint code{background:var(--kertas-2);padding:1px 5px;border-radius:4px;font-family:'IBM Plex Mono',monospace}
.req{color:var(--stempel)}
.keu-foot{display:flex;gap:10px;justify-content:flex-end;padding:12px 20px;border-top:1px solid var(--garis);background:var(--kertas);border-radius:0 0 14px 14px}
.keu-foot .mbtn{flex:0 0 auto}
.mbtn-save{display:inline-flex;align-items:center;gap:7px;background:var(--hutan);color:#fff;font-weight:700;font-size:14px;padding:10px 20px;border-radius:10px;border:none;cursor:pointer;transition:.14s}
.mbtn-save:hover{background:var(--hutan-2)}.mbtn-save svg{width:14px;height:14px}

.btn-access{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;padding:5px 12px;border-radius:7px;border:1.5px solid var(--biru);color:var(--biru);background:var(--biru-soft);cursor:pointer;transition:.13s}
.btn-access:hover{background:var(--biru);color:#fff}
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

const urlList          = "{{ route('role.list') }}";
const urlSave          = "{{ route('role.save') }}";
const urlDelete        = "{{ route('role.delete') }}";
const urlMenuAccess    = "{{ url('role') }}";  // + /{id}/menu-access
const urlSaveMenuAccess= "{{ url('role') }}";  // + /{id}/menu-access (POST)

let grid, focusedRow=null, curRoleId=null;

$(function(){
  var p=window.__perms||{};
  if(!p.add)    document.getElementById('btnTambah').disabled=true;
  if(!p.edit)   document.getElementById('btnUbah').disabled=true;
  if(!p.delete) document.getElementById('btnHapus').disabled=true;

  grid = $("#gridRole").dxDataGrid({
    dataSource: new DevExpress.data.CustomStore({key:"id", load:()=>$.getJSON(urlList)}),
    showBorders:false, showColumnLines:true, showRowLines:true,
    rowAlternationEnabled:true, width:"100%", height:"100%",
    columnAutoWidth:false,
    scrolling: { useNative: true, showScrollbar: 'always', mode: 'standard' },
    focusedRowEnabled:true,
    onFocusedRowChanged: e=>{ focusedRow=e.row?e.row.data:null; },
    paging:{enabled:false},
    columns:[
      {caption:"No.",width:52,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"nama",caption:"Nama Role",width:160,
        cellTemplate:function(c,o){
          $('<code style="font-family:\'IBM Plex Mono\',monospace;font-size:12.5px;background:var(--kertas-2);padding:2px 8px;border-radius:6px;font-weight:600">').text(o.value).appendTo(c);
        }},
      {dataField:"label",caption:"Label",minWidth:150,
        cellTemplate:function(c,o){$('<span style="font-weight:700">').text(o.value).appendTo(c);}},
      {dataField:"keterangan",caption:"Keterangan",minWidth:200,
        cellTemplate:function(c,o){$('<span style="color:var(--redup);font-size:12.5px">').text(o.value||'—').appendTo(c);}},
      {dataField:"jumlah_user",caption:"User",width:80,alignment:"center",
        cellTemplate:function(c,o){
          $('<span style="display:inline-flex;align-items:center;justify-content:center;min-width:28px;height:28px;border-radius:7px;background:var(--kertas-2);border:1px solid var(--garis);font-size:12.5px;font-weight:700;color:var(--redup)">').text(o.value||0).appendTo(c);
        }},
      {caption:"Akses Menu",width:130,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){
          $('<button class="btn-access" type="button">⚙ Atur Menu</button>')
            .on('click',function(){ openMenuAccess(o.row.data); })
            .appendTo(c);
        }},
    ]
  }).dxDataGrid("instance");

  initRoleEditors();
});

// ── Init Editors ───────────────────────────────────────────────────────
function initRoleEditors(){
  if($("#r_nama").data('dx-was-initialized')) return;
  $("#r_nama").dxTextBox({placeholder:"cth. koordinator"});
  $("#r_label").dxTextBox({placeholder:"cth. Koordinator Gang"});
  $("#r_keterangan").dxTextBox({placeholder:"Deskripsi singkat..."});
}

// ── CRUD Role ──────────────────────────────────────────────────────────
function roleAdd(){
  initRoleEditors();
  $("#r_id").val('');
  document.getElementById('roleModalTitle').textContent='Tambah Role';
  var namaTB = $("#r_nama").dxTextBox("instance");
  namaTB.option("value","");
  namaTB.option("disabled",false);
  document.getElementById('namaHint').style.display='none';
  $("#r_label").dxTextBox("instance").option("value","");
  $("#r_keterangan").dxTextBox("instance").option("value","");
  document.getElementById('roleModal').classList.add('show');
}

function roleEdit(){
  if(!focusedRow){DevExpress.ui.notify("Pilih baris terlebih dahulu","warning",2500);return;}
  initRoleEditors();
  var d=focusedRow;
  $("#r_id").val(d.id);
  document.getElementById('roleModalTitle').textContent='Ubah Role';
  // Nama tidak bisa diubah agar referensi di menu tidak rusak
  var namaTB = $("#r_nama").dxTextBox("instance");
  namaTB.option("value", d.nama||"");
  namaTB.option("disabled", true);
  var hint=document.getElementById('namaHint');
  hint.innerHTML='<span style="color:var(--redup)">🔒 Nama role tidak dapat diubah setelah disimpan.</span>';
  hint.style.display='';
  $("#r_label").dxTextBox("instance").option("value",d.label||"");
  $("#r_keterangan").dxTextBox("instance").option("value",d.keterangan||"");
  document.getElementById('roleModal').classList.add('show');
}

function roleSave(){
  var data={
    id:         $("#r_id").val(),
    nama:       $("#r_nama").dxTextBox("instance").option("value"),
    label:      $("#r_label").dxTextBox("instance").option("value"),
    keterangan: $("#r_keterangan").dxTextBox("instance").option("value"),
  };
  if(!data.label){DevExpress.ui.notify("Label wajib diisi","error",2500);return;}
  if(!data.id&&!data.nama){DevExpress.ui.notify("Nama role wajib diisi","error",2500);return;}
  $.ajax({url:urlSave,type:"POST",data:data})
    .done(function(r){DevExpress.ui.notify(r.message,"success",2500);roleClose();grid.refresh();})
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);});
}

function roleDelete(){
  if(!focusedRow){DevExpress.ui.notify("Pilih baris terlebih dahulu","warning",2500);return;}
  var d=focusedRow;
  document.getElementById('deleteTitle').textContent="Hapus Role?";
  document.getElementById('deleteMsg').textContent='"'+d.label+'" ('+d.nama+') akan dihapus. Role tidak boleh sedang digunakan user.';
  document.getElementById('deleteConfirmBtn').onclick=function(){
    $.ajax({url:urlDelete,type:"POST",data:{id:d.id}})
      .done(function(r){DevExpress.ui.notify(r.message,"success",2500);deleteClose();focusedRow=null;grid.refresh();})
      .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);deleteClose();});
  };
  document.getElementById('deleteModal').classList.add('show');
}

// ── Pengaturan Akses Menu ──────────────────────────────────────────────
function openMenuAccess(role){
  curRoleId = role.id;
  document.getElementById('menuAccessTitle').textContent = 'Akses Menu — ' + role.label;
  document.getElementById('menuAccessSub').textContent   = 'Centang menu yang boleh diakses role "' + role.nama + '"';

  var tree = document.getElementById('menuAccessTree');
  tree.innerHTML = '<div style="padding:20px;text-align:center;color:var(--redup)">Memuat...</div>';
  document.getElementById('menuAccessModal').classList.add('show');

  $.getJSON(urlMenuAccess+'/'+role.id+'/menu-access', function(menus){
    renderMenuTree(menus);
  });
}

function renderMenuTree(menus){
  var tree = document.getElementById('menuAccessTree');
  tree.innerHTML = '';

  // Kelompokkan: top-level dulu
  var topLevel = menus.filter(function(m){ return !m.parent_id; });
  var children = {};
  menus.filter(function(m){ return m.parent_id; }).forEach(function(m){
    if(!children[m.parent_id]) children[m.parent_id]=[];
    children[m.parent_id].push(m);
  });

  var typeLabel = {menu:'Menu Grup',screen:'Screen',button:'Button'};
  var typeCls   = {menu:'ma-type-menu',screen:'ma-type-screen',button:'ma-type-button'};

  topLevel.forEach(function(m){
    var group = document.createElement('div');
    group.className='ma-group';

    // Item utama
    var row = buildMenuRow(m, false, typeLabel, typeCls);
    group.appendChild(row);

    // Children
    if(children[m.id]){
      children[m.id].forEach(function(c){
        var crow = buildMenuRow(c, true, typeLabel, typeCls);
        group.appendChild(crow);

        // Sub-children (button)
        if(children[c.id]){
          children[c.id].forEach(function(b){
            var brow = buildMenuRow(b, true, typeLabel, typeCls);
            brow.style.paddingLeft='56px';
            group.appendChild(brow);
          });
        }
      });
    }
    tree.appendChild(group);
  });
}

function buildMenuRow(m, isChild, typeLabel, typeCls){
  var row = document.createElement('div');
  row.className = 'ma-item' + (isChild?' child':'');

  var cb = document.createElement('input');
  cb.type='checkbox';
  cb.id='ma_'+m.id;
  cb.dataset.menuId = m.id;
  cb.checked = m.has_access;

  var lbl = document.createElement('label');
  lbl.htmlFor='ma_'+m.id;
  lbl.className='ma-item-name';
  lbl.textContent=m.nama;

  var badge = document.createElement('span');
  badge.className='ma-item-type '+(typeCls[m.type]||'ma-type-screen');
  badge.textContent=typeLabel[m.type]||m.type;

  row.appendChild(cb);
  row.appendChild(lbl);
  row.appendChild(badge);
  row.addEventListener('click', function(e){
    if(e.target!==cb) cb.checked=!cb.checked;
  });
  return row;
}

function checkAll(checked){
  document.querySelectorAll('#menuAccessTree input[type=checkbox]').forEach(function(cb){
    cb.checked=checked;
  });
}

function saveMenuAccess(){
  var ids=[];
  document.querySelectorAll('#menuAccessTree input[type=checkbox]:checked').forEach(function(cb){
    ids.push(parseInt(cb.dataset.menuId));
  });

  $.ajax({
    url:         urlSaveMenuAccess+'/'+curRoleId+'/menu-access',
    type:        'POST',
    contentType: 'application/json',
    data:        JSON.stringify({
                   menu_access: ids,
                   _token: $('meta[name="csrf-token"]').attr('content')
                 })
  })
  .done(function(r){
    DevExpress.ui.notify(r.message,'success',2500);
    menuAccessClose();
  })
  .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||'Gagal','error',3000);});
}

function roleClose(){document.getElementById('roleModal').classList.remove('show');}
function menuAccessClose(){document.getElementById('menuAccessModal').classList.remove('show');}
function deleteClose(){document.getElementById('deleteModal').classList.remove('show');}
document.addEventListener('keydown',function(e){
  if(e.key==='Escape'){roleClose();menuAccessClose();deleteClose();}
});
</script>
@endpush
