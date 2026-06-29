@extends('layouts.app')
@section('judul','Booking Fasilitas')
@section('content')

<div class="keu-toolbar">
  <h2 class="keu-title">Booking Fasilitas RT</h2>
  <div class="keu-actions">
    <div id="filterBulan"></div>
    <div id="filterStatus"></div>
    <button class="btn" id="btnTambah" type="button" onclick="bookingAdd()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Tambah Booking
    </button>
    <button class="btn btn-unduh" id="btnUnduh" type="button" onclick="exportBooking()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Unduh
    </button>
  </div>
</div>

{{-- Summary --}}
<div class="iuran-summary">
  <div class="sum-card s-biru"><div class="sum-lbl">Total Booking</div><div class="sum-val" id="bTotal">0</div><div class="sum-sub">bulan ini</div></div>
  <div class="sum-card s-sebagian"><div class="sum-lbl">Menunggu Persetujuan</div><div class="sum-val" id="bMenunggu">0</div></div>
  <div class="sum-card s-lunas"><div class="sum-lbl">Disetujui</div><div class="sum-val" id="bDisetujui">0</div></div>
  <div class="sum-card" style="border-left:4px solid #2D6A4F"><div class="sum-lbl">Pemasukan Kas RT</div><div class="sum-val" id="bKasRt" style="font-size:17px">Rp 0</div></div>
</div>

<div class="grid-wrap" style="height:calc(100vh - 290px)"><div id="gridBooking"></div></div>

{{-- Modal Booking --}}
<div class="modal-overlay" id="bookingModal">
  <div class="modal-card keu-card" style="max-width:620px">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:var(--biru-soft);color:var(--biru)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </div>
      <div><h3 id="bookingModalTitle">Tambah Booking</h3><p class="keu-sub" id="bookingModalSub">Penggunaan fasilitas RT</p></div>
    </div>
    <form onsubmit="return false" class="keu-body" style="max-height:70vh;overflow-y:auto">
      <input type="hidden" id="b_id">

      <div class="ff2">
        <div class="ff"><label>Fasilitas <span class="req">*</span></label><div id="b_fasilitas"></div></div>
        <div class="ff"><label>Kategori Pengguna <span class="req">*</span></label><div id="b_kategori"></div></div>
      </div>
      <div class="ff"><label>Paket Tarif <span class="req">*</span></label><div id="b_tarif"></div></div>

      <div class="booking-calc" id="bookingCalc" style="display:none">
        <div class="bc-row"><span>Tarif per satuan</span><span id="bcNominal" class="mono-sm">—</span></div>
        <div class="bc-row"><span>Jumlah <span id="bcSatuan">(sesi)</span></span><div id="b_jumlah" style="width:120px"></div></div>
        <div class="bc-row bc-total"><span>Total Bayar</span><span id="bcTotal" class="mono-sm" style="color:var(--hutan);font-size:15px">Rp 0</span></div>
        <div class="bc-row" id="bcLainRow" style="display:none"><span id="bcLainLabel" style="color:var(--redup)">Biaya lain</span><span id="bcLain" class="mono-sm" style="color:var(--redup)">—</span></div>
        <div class="bc-row"><span style="color:#2D6A4F;font-weight:700">Masuk Kas RT</span><span id="bcKasRt" class="mono-sm" style="color:#2D6A4F">Rp 0</span></div>
      </div>

      <div class="ff"><label>Nama Pemohon <span class="req">*</span></label><div id="b_nama_pemohon"></div></div>
      <div class="ff"><label>Nama Acara <span class="req">*</span></label><div id="b_nama_acara"></div></div>
      <div class="ff"><label>Terhubung KK <small class="hint">(opsional)</small></label><div id="b_kk"></div></div>
      <div class="ff2">
        <div class="ff"><label>Tanggal Mulai <span class="req">*</span></label><div id="b_tgl_mulai"></div></div>
        <div class="ff"><label>Tanggal Selesai</label><div id="b_tgl_selesai"></div></div>
      </div>
      <div class="ff2">
        <div class="ff"><label>Jam Mulai</label><div id="b_jam_mulai"></div></div>
        <div class="ff"><label>Jam Selesai</label><div id="b_jam_selesai"></div></div>
      </div>
      <div class="ff"><label>Keterangan</label><div id="b_keterangan"></div></div>
    </form>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="bookingClose()">Batal</button>
      <button class="mbtn mbtn-save" onclick="bookingSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg> Simpan
      </button>
    </div>
  </div>
</div>

{{-- Modal Bayar --}}
<div class="modal-overlay" id="bayarModal" onclick="if(event.target===this)bayarClose()">
  <div class="modal-card keu-card" style="max-width:420px">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:var(--daun-pucat);color:var(--daun)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
      </div>
      <div><h3>Catat Pembayaran</h3><p class="keu-sub" id="bayarSub">—</p></div>
    </div>
    <div class="keu-body">
      <input type="hidden" id="pay_id">
      <div class="bayar-info" id="bayarInfo"></div>
      <div class="ff2">
        <div class="ff"><label>Status Bayar <span class="req">*</span></label><div id="pay_status"></div></div>
        <div class="ff"><label>Tanggal Bayar <span class="req">*</span></label><div id="pay_tgl"></div></div>
      </div>
    </div>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="bayarClose()">Batal</button>
      <button class="mbtn mbtn-save" onclick="bayarSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg> Simpan
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
.keu-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:16px;flex-wrap:wrap}
.keu-title{font-size:22px;font-weight:800;letter-spacing:-.02em;color:var(--tinta);margin:0}
.keu-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.btn-unduh{background:var(--biru)!important;color:#fff!important}.btn-unduh:hover{filter:brightness(1.08)}
.btn[disabled]{opacity:.45;cursor:not-allowed;pointer-events:none}
.iuran-summary{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:14px}
.sum-card{background:var(--surface);border:1px solid var(--garis);border-radius:12px;padding:14px 16px;position:relative;overflow:hidden}
.sum-card::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px}
.s-biru::before{background:var(--biru)}.s-lunas::before{background:#2D6A4F}
.s-belum::before{background:var(--stempel)}.s-sebagian::before{background:var(--emas)}
.sum-lbl{font-size:11.5px;color:var(--redup);font-weight:600;margin-bottom:6px}
.sum-val{font-size:22px;font-weight:800;letter-spacing:-.02em;color:var(--tinta)}
.sum-sub{font-size:11px;color:var(--redup);margin-top:3px}
.grid-wrap{background:var(--surface);border:1px solid var(--garis);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow)}
#gridBooking,.dx-widget{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
#gridBooking .dx-datagrid{border:none;color:var(--tinta)}
#gridBooking .dx-datagrid-headers{background:var(--kertas-2);border-bottom:1px solid var(--garis)}
#gridBooking .dx-datagrid-headers .dx-header-row>td{font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:11px 14px;border:none}
#gridBooking .dx-data-row>td{padding:10px 14px;font-size:13px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis);vertical-align:middle}
#gridBooking .dx-datagrid-rowsview .dx-data-row:hover>td{background:#FBFAF5}
#gridBooking .dx-pager{background:var(--kertas);border-top:1px solid var(--garis);padding:8px 14px}
#gridBooking .dx-toolbar{display:none}

.st-menunggu{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--emas-soft);color:#7a5c00}
.st-disetujui{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--daun-pucat);color:#1B5E3F}
.st-ditolak{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--stempel-soft);color:#9A3422}
.st-selesai{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--kertas-2);color:var(--redup);border:1px solid var(--garis)}
.pay-lunas{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--daun-pucat);color:#1B5E3F}
.pay-dp{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--biru-soft);color:#1a3d52}
.pay-belum{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--stempel-soft);color:#9A3422}
.cat-warga{display:inline-flex;align-items:center;font-size:10.5px;font-weight:700;padding:2px 7px;border-radius:20px;background:var(--daun-pucat);color:#14532D}
.cat-luar{display:inline-flex;align-items:center;font-size:10.5px;font-weight:700;padding:2px 7px;border-radius:20px;background:var(--stempel-soft);color:#9A3422}
.mono-sm{font-family:'IBM Plex Mono',monospace;font-size:12.5px;font-weight:600}
.wg-ic-btn{width:26px;height:26px;border-radius:6px;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:.13s}
.wg-ic-btn svg{width:12px;height:12px}
.wg-ic-pay{background:var(--daun-pucat);color:var(--daun)}.wg-ic-pay:hover{background:var(--daun);color:#fff}
.wg-ic-ok{background:#E0F2FE;color:#0284C7}.wg-ic-ok:hover{background:#0284C7;color:#fff}
.wg-ic-edit{background:var(--emas-soft);color:#7a5c00}.wg-ic-edit:hover{background:var(--emas);color:#fff}
.wg-ic-del{background:var(--stempel-soft);color:var(--stempel)}.wg-ic-del:hover{background:var(--stempel);color:#fff}

/* Booking calculator */
.booking-calc{background:var(--kertas);border:1px solid var(--garis);border-radius:10px;padding:12px 14px;margin-bottom:14px}
.bc-row{display:flex;align-items:center;justify-content:space-between;padding:4px 0;font-size:13px}
.bc-total{border-top:1px solid var(--garis);margin-top:6px;padding-top:8px;font-weight:700}

.keu-card{max-width:480px;width:100%;padding:0;text-align:left}
.keu-head{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--garis)}
.keu-head-icon{flex:0 0 34px;width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center}
.keu-head-icon svg{width:17px;height:17px}
.keu-head h3{font-size:15px;font-weight:800;margin:0 0 1px}
.keu-sub{font-size:12px;color:var(--redup);margin:0}
.keu-body{padding:18px 20px;display:flex;flex-direction:column;gap:0}
.ff{margin-bottom:13px;display:flex;flex-direction:column}
.ff>label{font-size:12px;font-weight:700;margin-bottom:5px;color:var(--redup)}
.ff2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.hint{font-size:11px;color:#9aa89f;font-weight:500}.req{color:var(--stempel)}
.bayar-info{padding:10px 13px;background:var(--kertas);border:1px solid var(--garis);border-radius:9px;margin-bottom:14px;font-size:13px}
.keu-foot{display:flex;gap:10px;justify-content:flex-end;padding:12px 20px;border-top:1px solid var(--garis);background:var(--kertas);border-radius:0 0 20px 20px}
.keu-foot .mbtn{flex:0 0 auto}
.mbtn-save{display:inline-flex;align-items:center;gap:7px;background:var(--hutan);color:#fff;font-weight:700;font-size:14px;padding:10px 20px;border-radius:10px;border:none;cursor:pointer;transition:.14s}
.mbtn-save:hover{background:var(--hutan-2)}.mbtn-save svg{width:14px;height:14px}
@media(max-width:900px){.iuran-summary{grid-template-columns:repeat(2,1fr)}}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/devextreme/exceljs.min.js') }}"></script>
<script src="{{ asset('js/devextreme/FileSaver.min.js') }}"></script>
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});
const urlList        = "{{ route('booking.list') }}";
const urlSave        = "{{ route('booking.save') }}";
const urlDelete      = "{{ route('booking.delete') }}";
const urlStatus      = "{{ route('booking.status') }}";
const urlBayar       = "{{ route('booking.bayar') }}";
const urlRingkasan   = "{{ route('booking.ringkasan') }}";
const urlFLookup     = "{{ route('fasilitas.lookup') }}";
const urlTLookup     = "{{ route('fasilitas.tarif-lookup') }}";
const urlKKLookup    = "{{ route('warga.kk-lookup') }}";

let grid;
let fasilitasList=[], curTarif=null, curBulan="{{ now()->format('Y-m') }}", curStatus=null;
function rupiah(n){return 'Rp '+(n||0).toLocaleString('id-ID');}
function fmtDate(d){if(!d)return '';var dt=new Date(d);return dt.getFullYear()+'-'+String(dt.getMonth()+1).padStart(2,'0')+'-'+String(dt.getDate()).padStart(2,'0');}

$(function(){
  var p=window.__perms||{};
  if(!p.add)    document.getElementById('btnTambah').disabled=true;
  if(!p.export) document.getElementById('btnUnduh').disabled=true;

  $.getJSON(urlFLookup,function(d){fasilitasList=d;});

  $("#filterBulan").dxDateBox({
    value:curBulan+'-01',type:'date',displayFormat:'MMM yyyy',
    calendarOptions:{zoomLevel:'year',maxZoomLevel:'year'},width:130,
    onValueChanged:function(e){curBulan=new Date(e.value).toISOString().slice(0,7);refreshAll();}
  });
  $("#filterStatus").dxSelectBox({
    dataSource:[{id:null,l:'Semua Status'},{id:'menunggu',l:'Menunggu'},{id:'disetujui',l:'Disetujui'},{id:'ditolak',l:'Ditolak'},{id:'selesai',l:'Selesai'}],
    valueExpr:'id',displayExpr:'l',value:null,width:140,
    onValueChanged:function(e){curStatus=e.value;refreshAll();}
  });

  grid=$("#gridBooking").dxDataGrid({
    dataSource:new DevExpress.data.CustomStore({
      key:"id",load:function(){
        var p={bulan:curBulan};
        if(curStatus)p.status=curStatus;
        return $.getJSON(urlList,p);
      }
    }),
    showBorders:false,showColumnLines:true,showRowLines:true,
    rowAlternationEnabled:true,width:"100%",height:"100%",
    columnAutoWidth:true,
    headerFilter:{visible:true},
    paging:{pageSize:50},
    pager:{visible:true,displayMode:"compact",showPageSizeSelector:true,allowedPageSizes:[25,50,"all"],showInfo:true},
    columns:[
      {caption:"No.",width:52,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"tanggal_mulai",caption:"Tanggal",width:105},
      {dataField:"fasilitas",caption:"Fasilitas",width:110,
        cellTemplate:function(c,o){$('<span style="font-weight:600">').text(o.value||'—').appendTo(c);}},
      {dataField:"nama_tarif",caption:"Paket",minWidth:130,
        cellTemplate:function(c,o){
          if(!o.value){$('<span style="color:var(--garis-2)">—</span>').appendTo(c);return;}
          var wrap=$('<div>');
          $('<div style="font-size:12.5px;font-weight:600">').text(o.value).appendTo(wrap);
          var cat=o.row.data.kategori;
          if(cat)$('<span class="'+(cat==='warga'?'cat-warga':'cat-luar')+'" style="margin-top:2px">').text(cat==='warga'?'Warga RT':'Luar Warga').appendTo(wrap);
          wrap.appendTo(c);
        }},
      {dataField:"nama_pemohon",caption:"Pemohon",minWidth:130,
        cellTemplate:function(c,o){
          var wrap=$('<div>');
          $('<div style="font-weight:600">').text(o.value).appendTo(wrap);
          if(o.row.data.nama_acara)$('<div style="font-size:11.5px;color:var(--redup)">').text(o.row.data.nama_acara).appendTo(wrap);
          wrap.appendTo(c);
        }},
      {dataField:"jumlah_unit",caption:"Jml",width:55,alignment:"center"},
      {dataField:"total_bayar",caption:"Total Bayar",width:115,alignment:"right",
        cellTemplate:function(c,o){$('<span class="mono-sm">').text(rupiah(o.value)).appendTo(c);}},
      {dataField:"total_kas_rt",caption:"Kas RT",width:110,alignment:"right",
        cellTemplate:function(c,o){$('<span class="mono-sm" style="color:#2D6A4F">').text(rupiah(o.value)).appendTo(c);}},
      {dataField:"status",caption:"Status",width:105,alignment:"center",
        cellTemplate:function(c,o){
          var cls={menunggu:'st-menunggu',disetujui:'st-disetujui',ditolak:'st-ditolak',selesai:'st-selesai'};
          var lbl={menunggu:'Menunggu',disetujui:'Disetujui',ditolak:'Ditolak',selesai:'Selesai'};
          $('<span class="'+(cls[o.value]||'st-menunggu')+'">').text(lbl[o.value]||o.value).appendTo(c);
        }},
      {dataField:"status_bayar",caption:"Bayar",width:90,alignment:"center",
        cellTemplate:function(c,o){
          var cls={lunas:'pay-lunas',dp:'pay-dp',belum:'pay-belum'};
          var lbl={lunas:'Lunas',dp:'DP',belum:'Belum'};
          $('<span class="'+(cls[o.value]||'pay-belum')+'">').text(lbl[o.value]||o.value).appendTo(c);
        }},
      {caption:"Aksi",width:120,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){
          var d=o.row.data;
          var wrap=$('<div style="display:flex;gap:4px;justify-content:center">');
          if(d.status==='menunggu'){
            $('<button class="wg-ic-btn wg-ic-ok" title="Setujui"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg></button>').on('click',function(){setujui(d.id);}).appendTo(wrap);
          }
          if(d.status==='disetujui'&&d.status_bayar!=='lunas'){
            $('<button class="wg-ic-btn wg-ic-pay" title="Catat Bayar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg></button>').on('click',function(){openBayar(d);}).appendTo(wrap);
          }
          $('<button class="wg-ic-btn wg-ic-edit" title="Ubah"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg></button>').on('click',function(){bookingEdit(d);}).appendTo(wrap);
          $('<button class="wg-ic-btn wg-ic-del" title="Hapus"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button>').on('click',function(){bookingDelete(d);}).appendTo(wrap);
          wrap.appendTo(c);
        }},
    ]
  }).dxDataGrid("instance");

  initEditors();
  refreshAll();
});

function refreshAll(){grid&&grid.refresh();loadRingkasan();}
function loadRingkasan(){
  $.getJSON(urlRingkasan,{bulan:curBulan},function(r){
    document.getElementById('bTotal').textContent=r.total;
    document.getElementById('bMenunggu').textContent=r.menunggu;
    document.getElementById('bDisetujui').textContent=r.disetujui;
    document.getElementById('bKasRt').textContent=rupiah(r.total_kas_rt);
  });
}

function initEditors(){
  $("#b_nama_pemohon").dxTextBox({placeholder:"Nama pemohon"});
  $("#b_nama_acara").dxTextBox({placeholder:"cth. Acara Pernikahan"});
  $("#b_tgl_mulai").dxDateBox({type:"date",displayFormat:"dd/MM/yyyy"});
  $("#b_tgl_selesai").dxDateBox({type:"date",displayFormat:"dd/MM/yyyy",showClearButton:true});
  $("#b_jam_mulai").dxTextBox({placeholder:"cth. 08:00",mask:"00:00"});
  $("#b_jam_selesai").dxTextBox({placeholder:"cth. 17:00",mask:"00:00"});
  $("#b_keterangan").dxTextBox({placeholder:"Keterangan tambahan..."});
  $("#b_jumlah").dxNumberBox({min:1,value:1,showSpinButtons:true,onValueChanged:calcTotal});

  $("#b_fasilitas").dxSelectBox({
    dataSource:new DevExpress.data.CustomStore({key:"id",load:()=>$.getJSON(urlFLookup)}),
    valueExpr:"id",displayExpr:"nama",placeholder:"— Pilih fasilitas —",
    onValueChanged:function(){reloadTarif();}
  });
  $("#b_kategori").dxSelectBox({
    dataSource:[{id:"warga",l:"Warga RT"},{id:"luar_warga",l:"Luar Warga"}],
    valueExpr:"id",displayExpr:"l",value:"warga",
    onValueChanged:function(){reloadTarif();}
  });
  $("#b_tarif").dxSelectBox({
    dataSource:[],valueExpr:"id",displayExpr:"nama_tarif",
    placeholder:"— Pilih paket tarif —",showClearButton:true,
    onValueChanged:function(e){
      curTarif=e.value?{"id":e.value}:null;
      if(e.component.option("dataSource").length){
        var found=e.component.option("dataSource").find(function(t){return t.id===e.value;});
        if(found){curTarif=found;calcTotal();}
      }
    }
  });
  $("#b_kk").dxSelectBox({
    dataSource:new DevExpress.data.CustomStore({key:"id",load:(opts)=>$.getJSON(urlKKLookup,{q:opts.searchValue||''})}),
    valueExpr:"id",displayExpr:function(i){return i?i.kepala_keluarga+(i.no_kk?' ('+i.no_kk+')':''):'';},
    searchEnabled:true,minSearchLength:0,showClearButton:true,placeholder:"— Pilih KK (opsional) —"
  });
  $("#pay_status").dxSelectBox({dataSource:[{id:"dp",l:"DP / Uang Muka"},{id:"lunas",l:"Lunas"}],valueExpr:"id",displayExpr:"l",value:"lunas"});
  $("#pay_tgl").dxDateBox({type:"date",displayFormat:"dd/MM/yyyy",value:new Date()});
}

function reloadTarif(){
  var fasId=$("#b_fasilitas").dxSelectBox("instance").option("value");
  var kat=$("#b_kategori").dxSelectBox("instance").option("value")||"warga";
  if(!fasId){return;}
  $.getJSON(urlTLookup,{fasilitas_id:fasId,kategori:kat},function(data){
    var sb=$("#b_tarif").dxSelectBox("instance");
    sb.option("dataSource",data);
    sb.option("value",null);
    curTarif=null;
    document.getElementById('bookingCalc').style.display='none';

    // Satuan label
    var fas=fasilitasList.find(function(f){return f.id===fasId;});
    if(fas) document.getElementById('bcSatuan').textContent='('+fas.satuan+')';
  });
}

function calcTotal(){
  if(!curTarif||!curTarif.nominal_total){document.getElementById('bookingCalc').style.display='none';return;}
  var unit=parseFloat($("#b_jumlah").dxNumberBox("instance").option("value"))||1;
  var total=curTarif.nominal_total*unit;
  var kasRt=curTarif.nominal_kas_rt*unit;
  var lain=(curTarif.nominal_lain||0)*unit;
  document.getElementById('bookingCalc').style.display='block';
  document.getElementById('bcNominal').textContent=rupiah(curTarif.nominal_total)+' / satuan';
  document.getElementById('bcTotal').textContent=rupiah(total);
  document.getElementById('bcKasRt').textContent=rupiah(kasRt);
  if(lain>0){
    document.getElementById('bcLainRow').style.display='flex';
    document.getElementById('bcLainLabel').textContent=curTarif.keterangan_lain||'Biaya lain';
    document.getElementById('bcLain').textContent=rupiah(lain);
  } else {
    document.getElementById('bcLainRow').style.display='none';
  }
}

function resetForm(){
  $("#b_id").val('');
  $("#b_nama_pemohon").dxTextBox("instance").option("value","");
  $("#b_nama_acara").dxTextBox("instance").option("value","");
  $("#b_tgl_mulai").dxDateBox("instance").option("value",null);
  $("#b_tgl_selesai").dxDateBox("instance").option("value",null);
  $("#b_jam_mulai").dxTextBox("instance").option("value","");
  $("#b_jam_selesai").dxTextBox("instance").option("value","");
  $("#b_keterangan").dxTextBox("instance").option("value","");
  $("#b_jumlah").dxNumberBox("instance").option("value",1);
  $("#b_kategori").dxSelectBox("instance").option("value","warga");
  $("#b_fasilitas").dxSelectBox("instance").option("value",null);
  $("#b_tarif").dxSelectBox("instance").option("dataSource",[]);
  $("#b_tarif").dxSelectBox("instance").option("value",null);
  $("#b_kk").dxSelectBox("instance").option("value",null);
  curTarif=null;
  document.getElementById('bookingCalc').style.display='none';
}

function bookingAdd(){
  resetForm();
  document.getElementById('bookingModalTitle').textContent='Tambah Booking';
  document.getElementById('bookingModal').classList.add('show');
}

function bookingEdit(d){
  $("#b_id").val(d.id);
  document.getElementById('bookingModalTitle').textContent='Ubah Booking';
  document.getElementById('bookingModalSub').textContent=d.nama_pemohon;
  $("#b_nama_pemohon").dxTextBox("instance").option("value",d.nama_pemohon||"");
  $("#b_nama_acara").dxTextBox("instance").option("value",d.nama_acara||"");
  $("#b_tgl_mulai").dxDateBox("instance").option("value",d.tanggal_mulai?new Date(d.tanggal_mulai.split('/').reverse().join('-')):null);
  $("#b_tgl_selesai").dxDateBox("instance").option("value",d.tanggal_selesai?new Date(d.tanggal_selesai.split('/').reverse().join('-')):null);
  $("#b_jam_mulai").dxTextBox("instance").option("value",d.jam_mulai||"");
  $("#b_jam_selesai").dxTextBox("instance").option("value",d.jam_selesai||"");
  $("#b_keterangan").dxTextBox("instance").option("value",d.keterangan||"");
  $("#b_jumlah").dxNumberBox("instance").option("value",d.jumlah_unit||1);
  $("#b_kategori").dxSelectBox("instance").option("value",d.kategori||"warga");
  $("#b_fasilitas").dxSelectBox("instance").option("value",d.fasilitas_id||null);
  var sb=$("#b_kk").dxSelectBox("instance");
  sb.getDataSource().reload().done(function(){sb.option("value",d.kartu_keluarga_id||null);});
  // Load tarif lalu set value
  if(d.fasilitas_id&&d.kategori){
    $.getJSON(urlTLookup,{fasilitas_id:d.fasilitas_id,kategori:d.kategori},function(data){
      var tsb=$("#b_tarif").dxSelectBox("instance");
      tsb.option("dataSource",data);
      var found=data.find(function(t){return t.id===d.tarif_fasilitas_id;});
      if(found){curTarif=found;tsb.option("value",d.tarif_fasilitas_id);calcTotal();}
    });
  }
  document.getElementById('bookingModal').classList.add('show');
}

function bookingSave(){
  var data={
    id:             $("#b_id").val(),
    fasilitas_id:   $("#b_fasilitas").dxSelectBox("instance").option("value"),
    tarif_fasilitas_id:$("#b_tarif").dxSelectBox("instance").option("value"),
    kartu_keluarga_id:$("#b_kk").dxSelectBox("instance").option("value"),
    nama_pemohon:   $("#b_nama_pemohon").dxTextBox("instance").option("value"),
    nama_acara:     $("#b_nama_acara").dxTextBox("instance").option("value"),
    tanggal_mulai:  fmtDate($("#b_tgl_mulai").dxDateBox("instance").option("value")),
    tanggal_selesai:fmtDate($("#b_tgl_selesai").dxDateBox("instance").option("value")),
    jam_mulai:      $("#b_jam_mulai").dxTextBox("instance").option("value"),
    jam_selesai:    $("#b_jam_selesai").dxTextBox("instance").option("value"),
    jumlah_unit:    $("#b_jumlah").dxNumberBox("instance").option("value"),
    keterangan:     $("#b_keterangan").dxTextBox("instance").option("value"),
  };
  if(!data.fasilitas_id)      {DevExpress.ui.notify("Pilih fasilitas","error",2500);return;}
  if(!data.tarif_fasilitas_id){DevExpress.ui.notify("Pilih paket tarif","error",2500);return;}
  if(!data.nama_pemohon)      {DevExpress.ui.notify("Nama pemohon wajib diisi","error",2500);return;}
  if(!data.nama_acara)        {DevExpress.ui.notify("Nama acara wajib diisi","error",2500);return;}
  if(!data.tanggal_mulai)     {DevExpress.ui.notify("Tanggal mulai wajib diisi","error",2500);return;}
  $.ajax({url:urlSave,type:"POST",data:data})
    .done(function(r){DevExpress.ui.notify(r.message,"success",2500);bookingClose();refreshAll();})
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal menyimpan","error",3000);});
}

function setujui(id){
  $.ajax({url:urlStatus,type:"POST",data:{id:id,status:"disetujui"}})
    .done(function(r){DevExpress.ui.notify(r.message,"success",2500);refreshAll();})
    .fail(function(){DevExpress.ui.notify("Gagal","error",3000);});
}

function openBayar(d){
  $("#pay_id").val(d.id);
  document.getElementById('bayarSub').textContent=d.nama_pemohon+' — '+d.nama_acara;
  document.getElementById('bayarInfo').innerHTML='<b>Total Bayar:</b> '+rupiah(d.total_bayar)+'&nbsp;|&nbsp;<b>Masuk Kas RT:</b> <span style="color:#2D6A4F;font-weight:700">'+rupiah(d.total_kas_rt)+'</span>';
  document.getElementById('bayarModal').classList.add('show');
}

function bayarSave(){
  var data={
    id:$("#pay_id").val(),
    status_bayar:$("#pay_status").dxSelectBox("instance").option("value"),
    tgl_bayar:fmtDate($("#pay_tgl").dxDateBox("instance").option("value")),
  };
  $.ajax({url:urlBayar,type:"POST",data:data})
    .done(function(r){DevExpress.ui.notify(r.message,"success",2500);bayarClose();refreshAll();})
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);});
}

function bookingDelete(d){
  document.getElementById('deleteTitle').textContent="Hapus Booking?";
  document.getElementById('deleteMsg').textContent='"'+d.nama_acara+'" ('+d.nama_pemohon+') akan dihapus.';
  document.getElementById('deleteConfirmBtn').onclick=function(){
    $.ajax({url:urlDelete,type:"POST",data:{id:d.id}})
      .done(function(r){DevExpress.ui.notify(r.message,"success",2500);deleteClose();refreshAll();})
      .fail(function(){DevExpress.ui.notify("Gagal","error",3000);});
  };
  document.getElementById('deleteModal').classList.add('show');
}

function exportBooking(){
  var wb=new ExcelJS.Workbook();
  var sh=wb.addWorksheet('Booking '+curBulan);
  sh.columns=[
    {header:'Tanggal',key:'tgl',width:13},{header:'Fasilitas',key:'fasilitas',width:15},
    {header:'Paket',key:'tarif',width:20},{header:'Kategori',key:'kat',width:12},
    {header:'Pemohon',key:'pemohon',width:22},{header:'Acara',key:'acara',width:25},
    {header:'Jml',key:'unit',width:6},{header:'Total Bayar',key:'total',width:14},
    {header:'Kas RT',key:'kas',width:14},{header:'Status',key:'status',width:12},
    {header:'Status Bayar',key:'bayar',width:13},
  ];
  sh.getRow(1).font={bold:true,color:{argb:'FFFFFFFF'}};
  sh.getRow(1).fill={type:'pattern',pattern:'solid',fgColor:{argb:'FF2C5C7A'}};
  var p={bulan:curBulan};if(curStatus)p.status=curStatus;
  $.getJSON(urlList,p,function(data){
    data.forEach(function(d,i){
      var row=sh.addRow({tgl:d.tanggal_mulai,fasilitas:d.fasilitas,tarif:d.nama_tarif,kat:d.kategori==='warga'?'Warga RT':'Luar Warga',pemohon:d.nama_pemohon,acara:d.nama_acara,unit:d.jumlah_unit,total:d.total_bayar,kas:d.total_kas_rt,status:d.status,bayar:d.status_bayar});
      if(i%2===0)row.fill={type:'pattern',pattern:'solid',fgColor:{argb:'FFE2ECF1'}};
    });
    wb.xlsx.writeBuffer().then(function(buf){saveAs(new Blob([buf],{type:'application/octet-stream'}),'Booking_'+curBulan+'.xlsx');});
  });
}

function bookingClose(){document.getElementById('bookingModal').classList.remove('show');}
function bayarClose(){document.getElementById('bayarModal').classList.remove('show');}
function deleteClose(){document.getElementById('deleteModal').classList.remove('show');}
document.addEventListener('keydown',function(e){if(e.key==='Escape'){bookingClose();bayarClose();deleteClose();}});
</script>
@endpush
