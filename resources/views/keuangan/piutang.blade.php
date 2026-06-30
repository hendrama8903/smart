@extends('layouts.app')
@section('judul','Piutang Warga')
@section('content')

<div class="keu-toolbar">
  <h2 class="keu-title">Piutang Warga</h2>
  <div class="keu-actions">
    <button class="btn" id="btnTambah" type="button" onclick="piutangAdd()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Tambah Piutang
    </button>
  </div>
</div>

{{-- Summary --}}
<div class="iuran-summary" id="piutangSummary">
  <div class="sum-card s-belum"><div class="sum-lbl">Total Piutang Aktif</div><div class="sum-val" id="pTotalPiutang">Rp 0</div><div class="sum-sub" id="pAktifCount">0 orang</div></div>
  <div class="sum-card s-lunas"><div class="sum-lbl">Total Kembali</div><div class="sum-val" id="pTotalKembali">Rp 0</div></div>
  <div class="sum-card s-sebagian"><div class="sum-lbl">Sisa Belum Kembali</div><div class="sum-val" id="pTotalSisa">Rp 0</div></div>
  <div class="sum-card" style="border-left:4px solid #6B7280"><div class="sum-lbl">Macet</div><div class="sum-val" id="pMacetCount" style="font-size:28px">0</div><div class="sum-sub">orang</div></div>
</div>

<div class="grid-wrap" style="height:calc(100vh - 290px)"><div id="gridPiutang"></div></div>

{{-- Modal: Tambah/Ubah Piutang --}}
<div class="modal-overlay" id="piutangModal">
  <div class="modal-card keu-card">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:var(--stempel-soft);color:var(--stempel)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div><h3 id="piutangModalTitle">Tambah Piutang</h3><p class="keu-sub" id="piutangModalSub">Catat uang yang dipinjam warga</p></div>
    </div>
    <form id="formPiutang" onsubmit="return false" class="keu-body">
      <input type="hidden" id="p_id">
      <div class="ff"><label>Nama Peminjam <span class="req">*</span></label><div id="p_nama"></div></div>
      <div class="ff"><label>Terhubung KK <small class="hint">(opsional)</small></label><div id="p_kk"></div></div>
      <div class="ff2">
        <div class="ff"><label>Jumlah Pinjaman <span class="req">*</span></label><div id="p_jumlah"></div></div>
        <div class="ff"><label>Tanggal Pinjam <span class="req">*</span></label><div id="p_tgl_pinjam"></div></div>
      </div>
      <div class="ff"><label>Jatuh Tempo <small class="hint">(opsional)</small></label><div id="p_jatuh_tempo"></div></div>
      <div class="ff"><label>Keterangan</label><div id="p_keterangan"></div></div>
    </form>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="piutangClose()">Batal</button>
      <button class="mbtn mbtn-save" onclick="piutangSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg> Simpan
      </button>
    </div>
  </div>
</div>

{{-- Modal: Input Cicilan/Pengembalian --}}
<div class="modal-overlay" id="cicilanModal">
  <div class="modal-card keu-card">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:var(--daun-pucat);color:var(--daun)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
      </div>
      <div><h3>Catat Pengembalian</h3><p class="keu-sub" id="cicilanSub">—</p></div>
    </div>
    <div class="keu-body">
      <input type="hidden" id="c_piutang_id">
      <div class="bayar-info" id="cicilanInfo"></div>
      <div class="ff2">
        <div class="ff"><label>Jumlah Kembali <span class="req">*</span></label><div id="c_jumlah"></div></div>
        <div class="ff"><label>Tanggal <span class="req">*</span></label><div id="c_tanggal"></div></div>
      </div>
      <div class="ff"><label>Keterangan</label><div id="c_keterangan"></div></div>

      <div style="margin-top:8px;border-top:1px solid var(--garis);padding-top:12px">
        <p style="font-size:12px;font-weight:700;color:var(--redup);margin-bottom:8px">Riwayat Pengembalian</p>
        <div id="riwayatCicilan" style="font-size:12.5px;color:var(--redup)">Memuat...</div>
      </div>
    </div>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="cicilanClose()">Tutup</button>
      <button class="mbtn mbtn-save" onclick="cicilanSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg> Catat
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
      <button class="mbtn danger" id="deleteConfirmBtn">Ya</button>
    </div>
  </div>
</div>

@endsection

@push('styles')
<style>
.content{max-width:none;padding-bottom:0}
.keu-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:16px}
.keu-title{font-size:22px;font-weight:800;letter-spacing:-.02em;color:var(--tinta);margin:0}
.keu-actions{display:flex;gap:8px}
.btn[disabled]{opacity:.45;cursor:not-allowed;pointer-events:none}
.iuran-summary{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:14px}
.sum-card{background:var(--surface);border:1px solid var(--garis);border-radius:12px;padding:14px 16px;position:relative;overflow:hidden}
.sum-card::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px}
.s-belum::before{background:var(--stempel)}.s-lunas::before{background:#2D6A4F}.s-sebagian::before{background:var(--emas)}
.sum-lbl{font-size:11.5px;color:var(--redup);font-weight:600;margin-bottom:6px}
.sum-val{font-size:20px;font-weight:800;letter-spacing:-.02em;color:var(--tinta)}
.sum-sub{font-size:11px;color:var(--redup);margin-top:3px}
.grid-wrap{background:var(--surface);border:1px solid var(--garis);border-radius:var(--radius);overflow:auto;box-shadow:var(--shadow)}
#gridPiutang,.dx-widget{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
#gridPiutang .dx-datagrid{border:none;color:var(--tinta)}
#gridPiutang .dx-datagrid-headers{background:var(--kertas-2);border-bottom:1px solid var(--garis)}
#gridPiutang .dx-datagrid-headers .dx-header-row>td{font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:11px 14px;border:none}
#gridPiutang .dx-data-row>td{padding:10px 14px;font-size:13px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis);vertical-align:middle}
#gridPiutang .dx-datagrid-rowsview .dx-data-row:hover>td{background:#FBFAF5}
#gridPiutang .dx-pager{background:var(--kertas);border-top:1px solid var(--garis);padding:8px 14px}
#gridPiutang .dx-toolbar{display:none}
.p-aktif{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--emas-soft);color:#7a5c00}
.p-lunas{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--daun-pucat);color:#1B5E3F}
.p-macet{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--stempel-soft);color:#9A3422}
.mono-sm{font-family:'IBM Plex Mono',monospace;font-size:12.5px;font-weight:600}
.wg-ic-btn{width:28px;height:28px;border-radius:7px;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:.13s}
.wg-ic-btn svg{width:13px;height:13px}
.wg-ic-pay{background:var(--daun-pucat);color:var(--daun)}.wg-ic-pay:hover{background:var(--daun);color:#fff}
.wg-ic-edit{background:var(--emas-soft);color:#7a5c00}.wg-ic-edit:hover{background:var(--emas);color:#fff}
.wg-ic-del{background:var(--stempel-soft);color:var(--stempel)}.wg-ic-del:hover{background:var(--stempel);color:#fff}
.keu-card{max-width:520px;width:100%;padding:0;text-align:left}
.keu-head{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--garis)}
.keu-head-icon{flex:0 0 34px;width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center}
.keu-head-icon svg{width:17px;height:17px}
.keu-head h3{font-size:15px;font-weight:800;margin:0 0 1px}
.keu-sub{font-size:12px;color:var(--redup);margin:0}
.keu-body{padding:18px 20px;display:flex;flex-direction:column;gap:0;max-height:70vh;overflow-y:auto}
.ff{margin-bottom:13px;display:flex;flex-direction:column}
.ff>label{font-size:12px;font-weight:700;margin-bottom:5px;color:var(--redup)}
.ff2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.hint{font-size:11px;color:#9aa89f;font-weight:500}.req{color:var(--stempel)}
.bayar-info{padding:10px 13px;background:var(--kertas);border:1px solid var(--garis);border-radius:9px;margin-bottom:14px;font-size:13px}
.keu-foot{display:flex;gap:10px;justify-content:flex-end;padding:12px 20px;border-top:1px solid var(--garis);background:var(--kertas);border-radius:0 0 14px 14px}
.keu-foot .mbtn{flex:0 0 auto}
.mbtn-save{display:inline-flex;align-items:center;gap:7px;background:var(--hutan);color:#fff;font-weight:700;font-size:14px;padding:10px 20px;border-radius:10px;border:none;cursor:pointer;transition:.14s}
.mbtn-save:hover{background:var(--hutan-2)}.mbtn-save svg{width:14px;height:14px}
.riwayat-item{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--garis)}
.riwayat-item:last-child{border-bottom:none}
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
const urlList       = "{{ route('piutang.list') }}";
const urlSave       = "{{ route('piutang.save') }}";
const urlDelete     = "{{ route('piutang.delete') }}";
const urlCicilanList= "{{ url('piutang/cicilan') }}";
const urlCicilanSave= "{{ route('piutang.cicilan.save') }}";
const urlRingkasan  = "{{ route('piutang.ringkasan') }}";
const urlKKLookup   = "{{ route('warga.kk-lookup') }}";

let grid;
function rupiah(n){return 'Rp '+(n||0).toLocaleString('id-ID');}
function fmtDate(d){if(!d)return '';var dt=new Date(d);return dt.getFullYear()+'-'+String(dt.getMonth()+1).padStart(2,'0')+'-'+String(dt.getDate()).padStart(2,'0');}

$(function(){
  var p=window.__perms||{};
  if(!p.add) document.getElementById('btnTambah').disabled=true;

  loadRingkasan();

  grid=$("#gridPiutang").dxDataGrid({
    dataSource:new DevExpress.data.CustomStore({key:"id",load:()=>$.getJSON(urlList)}),
    showBorders:false,showColumnLines:true,showRowLines:true,
    rowAlternationEnabled:true,width:"100%",height:"100%",
    columnAutoWidth:false,
    scrolling: { useNative: true, showScrollbar: 'always', mode: 'standard' },
    paging:{pageSize:50},
    pager:{visible:true,displayMode:"compact",showPageSizeSelector:true,allowedPageSizes:[25,50,"all"],showInfo:true},
    columns:[
      {caption:"No.",width:52,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"nama_peminjam",caption:"Nama Peminjam",minWidth:160,
        cellTemplate:function(c,o){
          var wrap=$('<div>');
          $('<div style="font-weight:600">').text(o.value).appendTo(wrap);
          if(o.row.data.kepala_kk&&o.row.data.kepala_kk!==o.value)
            $('<div style="font-size:11.5px;color:var(--redup)">').text('KK: '+o.row.data.kepala_kk+(o.row.data.blok_no?' — '+o.row.data.blok_no:'')).appendTo(wrap);
          wrap.appendTo(c);
        }},
      {dataField:"tanggal_pinjam",caption:"Tgl. Pinjam",width:105},
      {dataField:"jatuh_tempo",caption:"Jatuh Tempo",width:105,
        cellTemplate:function(c,o){
          if(!o.value){$('<span style="color:var(--garis-2)">—</span>').appendTo(c);return;}
          var isLewat=(o.row.data.status==='aktif'&&new Date(o.value.split('/').reverse().join('-'))<new Date());
          $('<span style="color:'+(isLewat?'var(--stempel)':'var(--tinta)')+';font-weight:'+(isLewat?'700':'400')+'">').text(o.value+(isLewat?' ⚠':'')+'').appendTo(c);
        }},
      {dataField:"jumlah",caption:"Pinjaman",width:120,alignment:"right",
        cellTemplate:function(c,o){$('<span class="mono-sm" style="color:var(--stempel)">').text(rupiah(o.value)).appendTo(c);}},
      {dataField:"jumlah_kembali",caption:"Kembali",width:120,alignment:"right",
        cellTemplate:function(c,o){$('<span class="mono-sm" style="color:#2D6A4F">').text(rupiah(o.value)).appendTo(c);}},
      {dataField:"sisa",caption:"Sisa",width:120,alignment:"right",
        cellTemplate:function(c,o){
          if(o.value<=0){$('<span class="mono-sm" style="color:var(--redup)">—</span>').appendTo(c);return;}
          $('<span class="mono-sm" style="color:var(--stempel);font-weight:700">').text(rupiah(o.value)).appendTo(c);
        }},
      {dataField:"status",caption:"Status",width:90,alignment:"center",
        cellTemplate:function(c,o){
          var cls={aktif:'p-aktif',lunas:'p-lunas',macet:'p-macet'};
          var lbl={aktif:'Aktif',lunas:'Lunas',macet:'Macet'};
          $('<span class="'+(cls[o.value]||'p-aktif')+'">').text(lbl[o.value]||o.value).appendTo(c);
        }},
      {dataField:"keterangan",caption:"Keterangan",minWidth:150,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
      {caption:"Aksi",width:110,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){
          var d=o.row.data;
          var wrap=$('<div style="display:flex;gap:4px;justify-content:center">');
          if(d.status==='aktif'){
            $('<button class="wg-ic-btn wg-ic-pay" title="Catat Pengembalian"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></button>')
              .on('click',function(){openCicilan(d);}).appendTo(wrap);
          }
          $('<button class="wg-ic-btn wg-ic-edit" title="Ubah"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg></button>')
            .on('click',function(){piutangEdit(d);}).appendTo(wrap);
          $('<button class="wg-ic-btn wg-ic-del" title="Hapus"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button>')
            .on('click',function(){piutangDelete(d);}).appendTo(wrap);
          wrap.appendTo(c);
        }},
    ]
  }).dxDataGrid("instance");

  initEditors();
});

function loadRingkasan(){
  $.getJSON(urlRingkasan,function(r){
    document.getElementById('pTotalPiutang').textContent=rupiah(r.total_piutang);
    document.getElementById('pAktifCount').textContent=r.aktif+' orang';
    document.getElementById('pTotalKembali').textContent=rupiah(r.total_kembali);
    document.getElementById('pTotalSisa').textContent=rupiah(r.total_sisa);
    document.getElementById('pMacetCount').textContent=r.macet;
  });
}

function initEditors(){
  $("#p_nama").dxTextBox({placeholder:"Nama peminjam"});
  $("#p_jumlah").dxNumberBox({min:1,format:"#,##0",placeholder:"Rp"});
  $("#p_tgl_pinjam").dxDateBox({type:"date",displayFormat:"dd/MM/yyyy",value:new Date()});
  $("#p_jatuh_tempo").dxDateBox({type:"date",displayFormat:"dd/MM/yyyy",showClearButton:true});
  $("#p_keterangan").dxTextBox({placeholder:"Keperluan pinjaman..."});
  $("#p_kk").dxSelectBox({
    dataSource:new DevExpress.data.CustomStore({
      key:"id",load:(opts)=>$.getJSON(urlKKLookup,{q:opts.searchValue||''})
    }),
    valueExpr:"id",
    displayExpr:function(i){return i?i.kepala_keluarga+(i.no_kk?' ('+i.no_kk+')':''):'';},
    searchEnabled:true,minSearchLength:0,showClearButton:true,placeholder:"— Pilih KK (opsional) —"
  });
  $("#c_jumlah").dxNumberBox({min:1,format:"#,##0"});
  $("#c_tanggal").dxDateBox({type:"date",displayFormat:"dd/MM/yyyy",value:new Date()});
  $("#c_keterangan").dxTextBox({placeholder:"Keterangan..."});
}

function piutangAdd(){
  $("#p_id").val('');
  document.getElementById('piutangModalTitle').textContent='Tambah Piutang';
  document.getElementById('piutangModalSub').textContent='Catat uang yang dipinjam warga';
  $("#p_nama").dxTextBox("instance").option("value","");
  $("#p_jumlah").dxNumberBox("instance").option("value",null);
  $("#p_tgl_pinjam").dxDateBox("instance").option("value",new Date());
  $("#p_jatuh_tempo").dxDateBox("instance").option("value",null);
  $("#p_keterangan").dxTextBox("instance").option("value","");
  $("#p_kk").dxSelectBox("instance").option("value",null);
  document.getElementById('piutangModal').classList.add('show');
}

function piutangEdit(d){
  $("#p_id").val(d.id);
  document.getElementById('piutangModalTitle').textContent='Ubah Piutang';
  document.getElementById('piutangModalSub').textContent='Mengubah: '+d.nama_peminjam;
  $("#p_nama").dxTextBox("instance").option("value",d.nama_peminjam||"");
  $("#p_jumlah").dxNumberBox("instance").option("value",d.jumlah);
  $("#p_tgl_pinjam").dxDateBox("instance").option("value",d.tanggal_pinjam?new Date(d.tanggal_pinjam.split('/').reverse().join('-')):null);
  $("#p_jatuh_tempo").dxDateBox("instance").option("value",d.jatuh_tempo?new Date(d.jatuh_tempo.split('/').reverse().join('-')):null);
  $("#p_keterangan").dxTextBox("instance").option("value",d.keterangan||"");
  var sb=$("#p_kk").dxSelectBox("instance");
  sb.getDataSource().reload().done(function(){sb.option("value",d.kartu_keluarga_id||null);});
  document.getElementById('piutangModal').classList.add('show');
}

function piutangSave(){
  var data={
    id:          $("#p_id").val(),
    nama_peminjam:$("#p_nama").dxTextBox("instance").option("value"),
    kartu_keluarga_id:$("#p_kk").dxSelectBox("instance").option("value"),
    jumlah:      $("#p_jumlah").dxNumberBox("instance").option("value"),
    tanggal_pinjam:fmtDate($("#p_tgl_pinjam").dxDateBox("instance").option("value")),
    jatuh_tempo: fmtDate($("#p_jatuh_tempo").dxDateBox("instance").option("value")),
    keterangan:  $("#p_keterangan").dxTextBox("instance").option("value"),
  };
  if(!data.nama_peminjam){DevExpress.ui.notify("Nama peminjam wajib diisi","error",2500);return;}
  if(!data.jumlah||data.jumlah<1){DevExpress.ui.notify("Jumlah pinjaman harus lebih dari 0","error",2500);return;}
  $.ajax({url:urlSave,type:"POST",data:data})
    .done(function(r){DevExpress.ui.notify(r.message,"success",2500);piutangClose();grid.refresh();loadRingkasan();})
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);});
}

function piutangDelete(d){
  document.getElementById('deleteTitle').textContent="Hapus Piutang?";
  document.getElementById('deleteMsg').textContent='"'+d.nama_peminjam+'" ('+rupiah(d.jumlah)+') akan dihapus beserta riwayat cicilannya.';
  document.getElementById('deleteConfirmBtn').onclick=function(){
    $.ajax({url:urlDelete,type:"POST",data:{id:d.id}})
      .done(function(r){DevExpress.ui.notify(r.message,"success",2500);deleteClose();grid.refresh();loadRingkasan();})
      .fail(function(){DevExpress.ui.notify("Gagal","error",3000);});
  };
  document.getElementById('deleteModal').classList.add('show');
}

function openCicilan(d){
  $("#c_piutang_id").val(d.id);
  document.getElementById('cicilanSub').textContent=d.nama_peminjam;
  document.getElementById('cicilanInfo').innerHTML='<b>Pinjaman:</b> '+rupiah(d.jumlah)+' &nbsp;|&nbsp; <b>Sudah kembali:</b> '+rupiah(d.jumlah_kembali)+' &nbsp;|&nbsp; <b>Sisa:</b> <span style="color:var(--stempel);font-weight:700">'+rupiah(d.sisa)+'</span>';
  $("#c_jumlah").dxNumberBox("instance").option("value",d.sisa);
  $("#c_tanggal").dxDateBox("instance").option("value",new Date());
  $("#c_keterangan").dxTextBox("instance").option("value","");

  // Load riwayat
  document.getElementById('riwayatCicilan').innerHTML='Memuat...';
  $.getJSON(urlCicilanList+'/'+d.id,function(data){
    if(!data.length){document.getElementById('riwayatCicilan').innerHTML='<span style="color:var(--garis-2)">Belum ada pengembalian.</span>';return;}
    var html='';
    data.forEach(function(c){
      html+='<div class="riwayat-item"><span>'+c.tanggal+'</span><span class="mono-sm" style="color:#2D6A4F">+'+rupiah(c.jumlah)+'</span><span style="color:var(--redup);font-size:11.5px">'+( c.keterangan||'')+'</span></div>';
    });
    document.getElementById('riwayatCicilan').innerHTML=html;
  });
  document.getElementById('cicilanModal').classList.add('show');
}

function cicilanSave(){
  var data={
    piutang_id: $("#c_piutang_id").val(),
    jumlah:     $("#c_jumlah").dxNumberBox("instance").option("value"),
    tanggal:    fmtDate($("#c_tanggal").dxDateBox("instance").option("value")),
    keterangan: $("#c_keterangan").dxTextBox("instance").option("value"),
  };
  if(!data.jumlah||data.jumlah<1){DevExpress.ui.notify("Jumlah harus lebih dari 0","error",2500);return;}
  $.ajax({url:urlCicilanSave,type:"POST",data:data})
    .done(function(r){DevExpress.ui.notify(r.message,"success",2500);cicilanClose();grid.refresh();loadRingkasan();})
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);});
}

function piutangClose(){document.getElementById('piutangModal').classList.remove('show');}
function cicilanClose(){document.getElementById('cicilanModal').classList.remove('show');}
function deleteClose(){document.getElementById('deleteModal').classList.remove('show');}
document.addEventListener('keydown',function(e){if(e.key==='Escape'){piutangClose();cicilanClose();deleteClose();}});
</script>
@endpush
