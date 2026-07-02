@extends('layouts.app')
@section('judul','Iuran Bulanan')
@section('content')

<div class="keu-toolbar">
  <div class="keu-title-wrap">
    <h2 class="keu-title">Iuran Bulanan</h2>
    <span class="keu-periode-sub" id="periodeInfo" style="display:none">
      <span id="pbarLabel">—</span>
      <span class="pbar-status" id="pbarStatus"></span>
    </span>
  </div>
  <div class="keu-actions">
    <button class="btn btn-buka-periode" id="btnBukaPeriode" type="button" onclick="openBukaPeriode()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg>
      Buka Periode
    </button>
    <button class="btn btn-tutup-buku" id="btnTutupBuku" type="button" onclick="openTutupBuku()" style="display:none">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      Tutup Buku
    </button>
    <button class="btn btn-unduh" id="btnUnduh" type="button" onclick="exportIuran()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Unduh
    </button>
    <button class="btn btn-import" id="btnImport" type="button" onclick="openImportTunggakan()">
      <svg class="ic" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
      Import Tunggakan
    </button>
  </div>
</div>

{{-- Filter bar --}}
<div class="keu-filters">
  <div id="filterPeriode"></div>
  <div id="filterGang"></div>
</div>

<div class="periode-empty" id="periodeEmpty" style="display:none">
  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
  Belum ada periode dibuka. Klik <strong>Buka Periode</strong> untuk memulai.
</div>

{{-- Summary Cards --}}
<div class="iuran-summary" id="iuranSummary">
  <div class="sum-card s-total">
    <div class="sum-lbl">Total Tagihan</div>
    <div class="sum-row"><span class="sum-val" id="sumTotal">Rp 0</span><span class="sum-sub" id="sumTotalKk">0 KK</span></div>
  </div>
  <div class="sum-card s-lunas">
    <div class="sum-lbl">Sudah Bayar</div>
    <div class="sum-row"><span class="sum-val" id="sumLunas">Rp 0</span><span class="sum-sub" id="sumLunasKk">0 KK</span></div>
  </div>
  <div class="sum-card s-sebagian">
    <div class="sum-lbl">Sebagian Bayar</div>
    <div class="sum-row"><span class="sum-val" id="sumSebagian">Rp 0</span><span class="sum-sub" id="sumSebagianKk">0 KK</span></div>
  </div>
  <div class="sum-card s-belum">
    <div class="sum-lbl">Belum Bayar</div>
    <div class="sum-row"><span class="sum-val" id="sumBelum">Rp 0</span><span class="sum-sub" id="sumBelumKk">0 KK</span></div>
  </div>
</div>

<div class="grid-wrap" style="height:calc(100vh - 265px)"><div id="gridIuran"></div></div>

{{-- Modal: Buka Periode --}}
<div class="modal-overlay" id="bukaPeriodeModal" onclick="if(event.target===this)bukaPeriodeClose()">
  <div class="modal-card keu-card">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:var(--daun-pucat);color:var(--daun)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg>
      </div>
      <h3>Buka Periode Iuran</h3>
    </div>
    <div class="keu-body">
      <div class="import-info" style="margin-bottom:14px">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>Setelah periode dibuka, petugas dapat mencatat pembayaran. Sekretaris melakukan tutup buku di akhir periode.</span>
      </div>
      <div class="ff"><label>Jenis Iuran <span class="req">*</span></label><div id="bp_jenis"></div></div>
      <div class="ff2">
        <div class="ff"><label>Tahun <span class="req">*</span></label><div id="bp_tahun"></div></div>
        <div class="ff" id="bp_bulan_wrap"><label>Bulan <span class="req">*</span></label><div id="bp_bulan"></div></div>
      </div>
    </div>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="bukaPeriodeClose()">Batal</button>
      <button class="mbtn mbtn-save" onclick="doBukaPeriode()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Buka Periode
      </button>
    </div>
  </div>
</div>

{{-- Modal: Tutup Buku --}}
<div class="modal-overlay" id="tutupBukuModal" onclick="if(event.target===this)tutupBukuClose()">
  <div class="modal-card keu-card" style="max-width:460px">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:#FEF3C7;color:#92400E">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      </div>
      <h3>Tutup Buku Periode</h3>
    </div>
    <div class="keu-body">
      <div class="import-info" style="background:#FEF3C7;color:#78350F;margin-bottom:14px">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <span>KK yang belum lunas akan otomatis ditandai sebagai <strong>tunggakan</strong>. Pembayaran tunggakan masih bisa dicatat setelah periode ditutup.</span>
      </div>
      <div class="ff"><label>Catatan Penutupan</label><div id="tb_catatan"></div></div>
    </div>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="tutupBukuClose()">Batal</button>
      <button class="mbtn mbtn-save" style="background:#B8860B" onclick="doTutupBuku()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Ya, Tutup Buku
      </button>
    </div>
  </div>
</div>

{{-- Modal: Input Pembayaran --}}
<div class="modal-overlay" id="bayarModal" onclick="if(event.target===this)bayarClose()">
  <div class="modal-card keu-card" style="max-width:560px">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:var(--daun-pucat);color:var(--daun)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
      </div>
      <h3 id="bayarTitle">Input Pembayaran</h3>
    </div>
    <form id="formBayar" onsubmit="return false" class="keu-body">
      <input type="hidden" id="b_kk_id">
      <input type="hidden" id="b_jenis_id">
      <div class="bayar-alokasi-info" id="bayarAlokasiInfo"></div>
      <div class="ff"><label>Jumlah Bayar <span class="req">*</span></label><div id="b_nominal"></div></div>
      <div class="ff2">
        <div class="ff"><label>Tanggal Bayar <span class="req">*</span></label><div id="b_tanggal"></div></div>
        <div class="ff"><label>Metode</label><div id="b_metode"></div></div>
      </div>
      <div class="ff"><label>Keterangan</label><div id="b_keterangan"></div></div>
    </form>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="bayarClose()">Batal</button>
      <button class="mbtn" style="background:var(--biru);color:#fff;font-weight:700;padding:10px 16px;border-radius:10px;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:7px" onclick="openRiwayat()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Riwayat
      </button>
      <button class="mbtn mbtn-save" onclick="bayarSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Simpan
      </button>
    </div>
  </div>
</div>

{{-- Modal: Riwayat Pembayaran --}}
<div class="modal-overlay" id="riwayatModal" onclick="if(event.target===this)riwayatClose()">
  <div class="modal-card keu-card" style="max-width:560px">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:var(--biru-soft);color:var(--biru)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div>
      <h3>Riwayat Pembayaran</h3>
    </div>
    <div class="keu-body" style="padding:0;max-height:420px;overflow-y:auto">
      <div id="riwayatList" style="padding:14px 20px"></div>
    </div>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="riwayatClose()">Tutup</button>
      <button class="mbtn mbtn-save" onclick="riwayatClose();document.getElementById('bayarModal').classList.add('show')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Bayar Lagi
      </button>
    </div>
  </div>
</div>

{{-- Modal: Import Tunggakan Historis --}}
<div class="modal-overlay" id="importTunggakanModal" onclick="if(event.target===this)importTunggakanClose()">
  <div class="modal-card keu-card">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:#FEF3C7;color:#92400E">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
      </div>
      <h3>Import Tunggakan Historis</h3>
    </div>
    <div class="keu-body">
      <div class="import-info">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>Gunakan template Excel. Data yang sudah ada akan diupdate, bukan duplikat. Tandai <strong>keringanan=1</strong> untuk warga yang bayar seikhlasnya.</span>
      </div>
      <a class="btn ghost" style="font-size:12px;padding:7px 13px;margin:12px 0;display:inline-flex;gap:7px"
         href="{{ asset('templates/template_import_tunggakan.xlsx') }}" download>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Unduh Template
      </a>
      <div id="importTunggakanUploader"></div>
      <div id="importTunggakanResult" style="display:none;margin-top:12px"></div>
    </div>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="importTunggakanClose()">Tutup</button>
    </div>
  </div>
</div>

{{-- Modal: Keringanan --}}
<div class="modal-overlay" id="keringananModal" onclick="if(event.target===this)keringananClose()">
  <div class="modal-card keu-card" style="max-width:420px">
    <div class="keu-head">
      <div class="keu-head-icon" style="background:#FEF3C7;color:#92400E">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      </div>
      <h3>Keringanan / Catatan Khusus</h3>
    </div>
    <div class="keu-body">
      <input type="hidden" id="kr_tagihan_id">
      <div class="mf-switch" style="margin-bottom:14px">
        <div id="kr_aktif"></div>
        <label class="mf-switch-lbl">Berikan Keringanan</label>
      </div>
      <div class="ff"><label>Catatan Khusus</label><div id="kr_catatan"></div></div>
    </div>
    <div class="keu-foot">
      <button class="mbtn ghost" onclick="keringananClose()">Batal</button>
      <button class="mbtn mbtn-save" style="background:#B8860B" onclick="keringananSave()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Simpan
      </button>
    </div>
  </div>
</div>

@endsection

@push('styles')
<style>
.content{max-width:none;padding-bottom:0}
.keu-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;gap:12px;flex-wrap:wrap}
.keu-title-wrap{display:flex;flex-direction:column;gap:2px;min-width:0}
.keu-title{font-size:22px;font-weight:800;letter-spacing:-.02em;color:var(--tinta);margin:0;line-height:1.2}
.keu-periode-sub{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:var(--redup)}
.keu-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.keu-filters{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:10px}
.btn-buka-periode{background:#6366F1!important;color:#fff!important}.btn-buka-periode:hover{filter:brightness(1.08)}
.btn-tutup-buku{background:#F59E0B!important;color:#fff!important}.btn-tutup-buku:hover{filter:brightness(1.08)}
.btn-unduh{background:var(--biru)!important;color:#fff!important}.btn-unduh:hover{filter:brightness(1.08)}
.btn-import{background:#B8860B!important;color:#fff!important}.btn-import:hover{filter:brightness(1.08)}
.btn[disabled]{opacity:.45;cursor:not-allowed;pointer-events:none}
.pbar-status{font-size:10.5px;font-weight:700;padding:1px 8px;border-radius:20px;white-space:nowrap}
.pbar-status.buka{background:#DCFCE7;color:#14532D}
.pbar-status.tutup{background:#F3F4F6;color:#6B7280}
.periode-empty{display:flex;align-items:center;gap:8px;padding:7px 12px;background:var(--biru-soft);border-radius:8px;font-size:12px;color:#1a3d52;margin-bottom:10px}
.periode-empty svg{flex:0 0 15px;color:var(--biru)}

/* Summary */
.iuran-summary{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:8px}
.sum-card{background:var(--surface);border:1px solid var(--garis);border-radius:8px;padding:7px 10px 7px 13px;position:relative;overflow:hidden}
.sum-card::before{content:"";position:absolute;left:0;top:0;bottom:0;width:3px}
.s-total::before{background:var(--biru)}
.s-lunas::before{background:#2D6A4F}
.s-sebagian::before{background:var(--emas)}
.s-belum::before{background:var(--stempel)}
.sum-lbl{font-size:10px;color:var(--redup);font-weight:600;margin-bottom:2px;text-transform:uppercase;letter-spacing:.04em}
.sum-row{display:flex;align-items:baseline;gap:6px}
.sum-val{font-size:13.5px;font-weight:800;letter-spacing:-.01em;color:var(--tinta)}
.sum-sub{font-size:10.5px;color:var(--redup);font-weight:500}

/* Grid */
.grid-wrap{background:var(--surface);border:1px solid var(--garis);overflow:auto;box-shadow:var(--shadow)}
#gridIuran,.dx-widget{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
#gridIuran .dx-datagrid{border:none;color:var(--tinta)}
#gridIuran .dx-datagrid-headers{background:var(--kertas-2);border-bottom:1px solid var(--garis)}
#gridIuran .dx-datagrid-headers .dx-header-row>td{font-size:10.5px;letter-spacing:.08em;text-transform:uppercase;font-weight:800;color:var(--redup);padding:11px 14px;border:none}
#gridIuran .dx-data-row>td{padding:10px 14px;font-size:13px;border-bottom:1px solid var(--garis);border-right:1px solid var(--garis);vertical-align:middle}
#gridIuran .dx-datagrid-rowsview .dx-data-row:hover>td{background:#FBFAF5}
#gridIuran .dx-pager{background:var(--kertas);border-top:1px solid var(--garis);padding:8px 14px}
#gridIuran .dx-toolbar{display:none}
.st-lunas{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--daun-pucat);color:#1B5E3F}
.st-lunas::before{content:"";width:6px;height:6px;border-radius:50%;background:#2D6A4F}
.st-sebagian{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--emas-soft);color:#7a5c00}
.st-sebagian::before{content:"";width:6px;height:6px;border-radius:50%;background:var(--emas)}
.st-belum{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;background:var(--stempel-soft);color:#9A3422}
.st-belum::before{content:"";width:6px;height:6px;border-radius:50%;background:var(--stempel)}
.tg-flag{font-size:10px;font-weight:700;padding:1px 7px;border-radius:10px;background:#FEF3C7;color:#92400E;margin-left:4px}
.btn-bayar{padding:4px 12px;border-radius:7px;font-size:12px;font-weight:700;background:var(--hutan);color:#fff;border:none;cursor:pointer;transition:.13s}
.btn-bayar:hover{background:var(--hutan-2)}
.btn-riwayat{padding:4px 10px;border-radius:7px;font-size:12px;font-weight:700;background:var(--biru-soft);color:var(--biru);border:none;cursor:pointer;transition:.13s}
.mono-sm{font-family:'IBM Plex Mono',monospace;font-size:12px}

/* Modal */
.keu-card{max-width:520px;width:100%;padding:0;text-align:left}
.keu-head{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--garis)}
.keu-head-icon{flex:0 0 34px;width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center}
.keu-head-icon svg{width:17px;height:17px}
.keu-head h3{font-size:15px;font-weight:800;margin:0 0 1px}
.keu-body{padding:18px 20px;display:flex;flex-direction:column;gap:0}
.keu-foot{display:flex;gap:10px;justify-content:flex-end;padding:12px 20px;border-top:1px solid var(--garis);background:var(--kertas);border-radius:0 0 14px 14px}
.keu-foot .mbtn{flex:0 0 auto;min-width:90px}
.keu-foot .mbtn.ghost{background:var(--stempel-soft);color:var(--stempel);border:1.5px solid #f5c6bb}
.keu-foot .mbtn.ghost:hover{background:#fde5e0}
.mbtn-save{display:inline-flex;align-items:center;gap:7px;background:var(--hutan);color:#fff;font-weight:700;font-size:13px;padding:8px 18px;border-radius:10px;border:none;cursor:pointer;transition:.14s}
.mbtn-save:hover{background:var(--hutan-2)}
.mbtn-save svg{width:13px;height:13px}
.import-info{display:flex;align-items:flex-start;gap:8px;padding:10px 12px;background:var(--biru-soft);border-radius:8px;font-size:12.5px;color:#1a3d52}
.import-info svg{flex:0 0 15px;margin-top:1px}
.ff{margin-bottom:13px;display:flex;flex-direction:column}
.ff>label{font-size:12px;font-weight:700;margin-bottom:5px;color:var(--redup)}
.ff2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.req{color:var(--stempel)}

/* Bayar alokasi preview */
.bayar-alokasi-info{background:var(--kertas);border:1px solid var(--garis);border-radius:9px;margin-bottom:14px;overflow:hidden}
.bai-header{padding:9px 13px;border-bottom:1px solid var(--garis);font-size:12px;font-weight:700;color:var(--redup);text-transform:uppercase;letter-spacing:.06em}
.bai-row{display:flex;justify-content:space-between;align-items:center;padding:7px 13px;border-bottom:1px solid var(--garis);font-size:12.5px}
.bai-row:last-child{border-bottom:none}
.bai-periode{color:var(--tinta);font-weight:600}
.bai-sisa{font-family:'IBM Plex Mono',monospace;color:var(--stempel);font-size:12px;font-weight:700}
.bai-empty{padding:12px 13px;font-size:12.5px;color:var(--redup);text-align:center}

/* Riwayat */
.rwt-item{border:1px solid var(--garis);border-radius:9px;margin-bottom:10px;overflow:hidden}
.rwt-head{display:flex;justify-content:space-between;align-items:center;padding:9px 13px;background:var(--kertas-2);font-size:12.5px}
.rwt-tgl{font-weight:700;color:var(--tinta)}
.rwt-total{font-family:'IBM Plex Mono',monospace;font-weight:700;color:#2D6A4F}
.rwt-meta{font-size:11px;color:var(--redup);padding:0 13px 5px}
.rwt-alokasi{padding:5px 13px 9px}
.rwt-alok-row{display:flex;justify-content:space-between;font-size:12px;padding:2px 0}
.rwt-alok-label{color:var(--redup)}
.rwt-alok-val{font-family:'IBM Plex Mono',monospace;color:var(--tinta);font-weight:600}

@media(max-width:900px){.iuran-summary{grid-template-columns:repeat(2,1fr)}}
@media(max-width:480px){.iuran-summary{grid-template-columns:1fr}}
@media(max-width:640px){
  .keu-toolbar{flex-wrap:wrap;gap:8px}
  .keu-title{font-size:18px;flex:1}
  .keu-filters{gap:6px}
  .grid-wrap{height:calc(100vh - 280px)}
  .periode-bar{flex-direction:column;align-items:flex-start;gap:6px}
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/devextreme/exceljs.min.js') }}"></script>
<script src="{{ asset('js/devextreme/FileSaver.min.js') }}"></script>
<script>
$.ajaxSetup({headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}});

const urlTagihanList   = "{{ route('iuran.list') }}";
const urlContext       = "{{ route('iuran.context') }}";
const urlRekap         = "{{ route('iuran.rekap') }}";
const urlJenisList     = "{{ route('iuran.jenis.list') }}";
const urlGangList      = "{{ route('gang.list') }}";
const urlPeriodeList   = "{{ route('iuran.periode.list') }}";
const urlBukaPeriode   = "{{ route('iuran.periode.buka') }}";
const urlTutupBuku     = "{{ route('iuran.periode.tutup') }}";
const urlPembayaran    = "{{ route('iuran.pembayaran.save') }}";
const urlPembayaranList= "{{ route('iuran.pembayaran.list') }}";
const urlKeringanan    = "{{ route('iuran.keringanan') }}";
const urlImportTunggakan = "{{ route('iuran.import-tunggakan') }}";

let grid, jenisList=[], gangList=[], periodeList=[];
let curJenis   = null;
let curPeriode = null; // periode_id
let curGang    = null;
let isAdmin    = false;
let bayarKkData = null;

function rupiah(n){ return 'Rp '+(n||0).toLocaleString('id-ID'); }
function formatDate(d){
  if(!d) return '';
  var dt=new Date(d);
  return dt.getFullYear()+'-'+String(dt.getMonth()+1).padStart(2,'0')+'-'+String(dt.getDate()).padStart(2,'0');
}

// ── Init ──────────────────────────────────────────────────────────────
$(function(){
  var p=window.__perms||{};
  if(!p.export) document.getElementById('btnUnduh').disabled=true;

  $.when($.getJSON(urlContext), $.getJSON(urlJenisList), $.getJSON(urlGangList))
    .done(function(ctx,j,g){
      var roleCtx = ctx[0];
      isAdmin = roleCtx.is_admin;

      if(!isAdmin){
        document.getElementById('btnBukaPeriode').style.display='none';
        document.getElementById('btnImport') && (document.getElementById('btnImport').style.display='none');
      }
      if(roleCtx.is_koordinator && roleCtx.koordinator){
        var badge=$('<span style="font-size:12px;font-weight:700;padding:4px 12px;border-radius:20px;background:var(--hutan);color:#fff;margin-right:4px">').text('Gang: '+roleCtx.koordinator.gang);
        $(document.querySelector('.keu-actions')).prepend(badge);
      }

      jenisList = j[0]; gangList = g[0];

      // Filter jenis (pilih pertama sebagai default)
      $("#filterJenis").dxSelectBox({
        dataSource: jenisList,
        valueExpr:'id', displayExpr:'nama',
        value: jenisList.length ? jenisList[0].id : null,
        width: 160,
        onValueChanged: function(e){ curJenis = e.value; loadPeriodeList(); }
      });

      // Filter gang
      $("#filterGang").dxSelectBox({
        dataSource:[{id:null,nama_gang:'Semua Gang'},...gangList],
        valueExpr:'id', displayExpr:'nama_gang', value:null, width:140,
        onValueChanged:function(e){curGang=e.value; refreshAll();}
      });

      curJenis = jenisList.length ? jenisList[0].id : null;
      loadPeriodeList();

      initGrid();
      initBayarEditors();
      initBukaPeriodeEditors();
    });
});

// ── Periode ────────────────────────────────────────────────────────────
function loadPeriodeList(){
  if(!curJenis){ showPeriodeEmpty(); return; }
  $.getJSON(urlPeriodeList, {jenis_iuran_id: curJenis}, function(list){
    periodeList = list;
    // Buat filter periode dropdown
    var periodeData = list.map(function(p){
      return { id: p.id, label: p.label+' ('+(p.status==='buka'?'Buka':'Tutup')+')' };
    });
    if(!periodeData.length){ showPeriodeEmpty(); return; }

    document.getElementById('periodeEmpty').style.display='none';

    // Pilih periode BUKA pertama, atau yang paling baru
    var defaultPeriode = list.find(function(p){return p.status==='buka';}) || list[0];
    curPeriode = defaultPeriode.id;

    if(!$("#filterPeriode").data('dx-was-initialized')){
      $("#filterPeriode").dxSelectBox({
        dataSource: periodeData,
        valueExpr:'id', displayExpr:'label',
        value: curPeriode, width:200,
        onValueChanged: function(e){ curPeriode=e.value; updatePeriodeBar(); refreshAll(); }
      });
    } else {
      $("#filterPeriode").dxSelectBox("instance").option("dataSource", periodeData);
      $("#filterPeriode").dxSelectBox("instance").option("value", curPeriode);
    }

    updatePeriodeBar();
    refreshAll();
  });
}

function updatePeriodeBar(){
  var p = periodeList.find(function(x){return x.id===curPeriode;});
  var infoEl = document.getElementById('periodeInfo');
  if(!p){ infoEl.style.display='none'; return; }

  infoEl.style.display='flex';
  document.getElementById('pbarLabel').textContent = p.label;

  var statusEl = document.getElementById('pbarStatus');
  statusEl.textContent = p.status==='buka' ? 'Buka' : 'Tutup';
  statusEl.className = 'pbar-status '+(p.status==='buka'?'buka':'tutup');

  document.getElementById('btnTutupBuku').style.display = (isAdmin && p.status==='buka') ? 'inline-flex' : 'none';
}

function showPeriodeEmpty(){
  document.getElementById('periodeInfo').style.display='none';
  document.getElementById('periodeEmpty').style.display='flex';
  curPeriode = null;
  refreshAll();
}

function refreshAll(){
  grid && grid.refresh();
  loadRekap();
}

function loadRekap(){
  if(!curPeriode){
    ['sumTotal','sumLunas','sumSebagian','sumBelum'].forEach(function(id){document.getElementById(id).textContent='Rp 0';});
    ['sumTotalKk','sumLunasKk','sumSebagianKk','sumBelumKk'].forEach(function(id){document.getElementById(id).textContent='0 KK';});
    return;
  }
  $.getJSON(urlRekap, {periode_id: curPeriode}, function(r){
    document.getElementById('sumTotal').textContent     = rupiah(r.total_tagihan);
    document.getElementById('sumTotalKk').textContent   = r.total_kk+' KK';
    document.getElementById('sumLunas').textContent     = rupiah(r.total_dibayar);
    document.getElementById('sumLunasKk').textContent   = r.lunas+' KK';
    document.getElementById('sumSebagian').textContent  = rupiah(r.total_dibayar - (r.total_tagihan - r.total_sisa - r.total_dibayar > 0 ? 0 : 0));
    document.getElementById('sumSebagianKk').textContent= r.sebagian+' KK';
    document.getElementById('sumBelum').textContent     = rupiah(r.total_sisa);
    document.getElementById('sumBelumKk').textContent   = r.belum+' KK';
  });
}

// ── Grid ───────────────────────────────────────────────────────────────
function initGrid(){
  grid = $("#gridIuran").dxDataGrid({
    dataSource: new DevExpress.data.CustomStore({
      key:"kk_id",
      load:function(){
        if(!curPeriode) return Promise.resolve([]);
        var p={periode_id: curPeriode};
        if(curGang) p.gang_id=curGang;
        return $.getJSON(urlTagihanList, p);
      }
    }),
    showBorders:false, showColumnLines:true, showRowLines:true,
    rowAlternationEnabled:true, width:"100%", height:"100%",
    columnAutoWidth:true,
    scrolling:{useNative:true,showScrollbar:'always',mode:'standard'},
    headerFilter:{visible:true},
    paging:{pageSize:50},
    pager:{visible:true,displayMode:"compact",showPageSizeSelector:true,allowedPageSizes:[25,50,"all"],showInfo:true},
    columns:[
      {caption:"No.",width:50,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){$('<span style="font-size:12px;color:var(--redup);font-weight:600">').text(o.rowIndex+1).appendTo(c);}},
      {dataField:"blok_no",caption:"Blok/No.",width:90,
        cellTemplate:function(c,o){
          if(!o.value){$('<span style="color:var(--garis-2)">—</span>').appendTo(c);return;}
          $('<span style="display:inline-flex;align-items:center;font-size:12px;font-weight:700;padding:2px 10px;border-radius:20px;background:var(--biru-soft);color:#1a3d52">').text(o.value).appendTo(c);
        }},
      {dataField:"kepala_keluarga",caption:"Kepala Keluarga",minWidth:160,
        cellTemplate:function(c,o){
          var wrap=$('<span style="font-weight:600">').text(o.value||'—');
          if(o.row.data.is_tunggakan) $('<span class="tg-flag">Tunggakan</span>').appendTo(wrap);
          wrap.appendTo(c);
        }},
      {dataField:"gang",caption:"Gang",width:110,
        cellTemplate:function(c,o){$('<span style="font-size:12.5px;color:var(--redup)">').text(o.value||'—').appendTo(c);}},
      {dataField:"nominal",caption:"Tagihan",width:110,alignment:"right",
        cellTemplate:function(c,o){$('<span class="mono-sm">').text(rupiah(o.value)).appendTo(c);}},
      {dataField:"nominal_dibayar",caption:"Dibayar",width:110,alignment:"right",
        cellTemplate:function(c,o){$('<span class="mono-sm" style="color:'+(o.value>0?'#2D6A4F':'var(--redup)')+'">').text(rupiah(o.value)).appendTo(c);}},
      {dataField:"sisa",caption:"Sisa",width:110,alignment:"right",
        cellTemplate:function(c,o){
          if(o.value<=0){$('<span class="mono-sm" style="color:var(--redup)">—</span>').appendTo(c);return;}
          $('<span class="mono-sm" style="color:var(--stempel);font-weight:700">').text(rupiah(o.value)).appendTo(c);
        }},
      {dataField:"status",caption:"Status",width:110,alignment:"center",
        cellTemplate:function(c,o){
          var cls={lunas:'st-lunas',sebagian:'st-sebagian',belum:'st-belum'};
          var lbl={lunas:'Lunas',sebagian:'Sebagian',belum:'Belum'};
          var v=o.value||'belum';
          $('<span class="'+(cls[v]||'st-belum')+'">').text(lbl[v]||v).appendTo(c);
        }},
      {caption:"Aksi",width:140,alignment:"center",allowFiltering:false,allowSorting:false,
        cellTemplate:function(c,o){
          var d=o.row.data;
          var wrap=$('<div style="display:flex;gap:5px;justify-content:center;align-items:center">');
          if(d.tagihan_id){
            if(d.status!=='lunas'){
              $('<button class="btn-bayar" type="button">Bayar</button>')
                .on('click',function(){openBayar(d);}).appendTo(wrap);
            }
            $('<button class="btn-riwayat" type="button">Riwayat</button>')
              .on('click',function(){openRiwayatOnly(d);}).appendTo(wrap);
            // Tombol keringanan
            $('<button style="width:26px;height:26px;border-radius:6px;border:none;cursor:pointer;background:'+(d.is_keringanan?'#B8860B':'var(--kertas-2)')+';color:'+(d.is_keringanan?'#fff':'var(--redup)')+';display:inline-flex;align-items:center;justify-content:center" title="Keringanan"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg></button>')
              .on('click',function(){openKeringanan(d);}).appendTo(wrap);
          } else {
            $('<span style="font-size:11px;color:var(--redup)">Belum ada tagihan</span>').appendTo(wrap);
          }
          wrap.appendTo(c);
        }},
    ]
  }).dxDataGrid("instance");
}

// ── Buka Periode ──────────────────────────────────────────────────────
function initBukaPeriodeEditors(){
  $("#bp_jenis").dxSelectBox({
    dataSource:jenisList, valueExpr:'id', displayExpr:'nama',
    placeholder:'— Pilih jenis iuran —',
    onValueChanged: function(e){
      var jenis = jenisList.find(function(j){return j.id===e.value;});
      var isBulanan = jenis && jenis.periode==='bulanan';
      document.getElementById('bp_bulan_wrap').style.display = isBulanan ? '' : 'none';
    }
  });
  $("#bp_tahun").dxNumberBox({value: new Date().getFullYear(), min:2000, max:2100, format:"####"});
  $("#bp_bulan").dxSelectBox({
    dataSource:[
      {id:1,n:'Januari'},{id:2,n:'Februari'},{id:3,n:'Maret'},{id:4,n:'April'},
      {id:5,n:'Mei'},{id:6,n:'Juni'},{id:7,n:'Juli'},{id:8,n:'Agustus'},
      {id:9,n:'September'},{id:10,n:'Oktober'},{id:11,n:'November'},{id:12,n:'Desember'}
    ],
    valueExpr:'id', displayExpr:'n',
    value: new Date().getMonth()+1,
    placeholder:'— Pilih bulan —'
  });
}

function openBukaPeriode(){
  // Set default jenis ke filter aktif
  if(curJenis) $("#bp_jenis").dxSelectBox("instance").option("value", curJenis);
  document.getElementById('bukaPeriodeModal').classList.add('show');
}
function bukaPeriodeClose(){ document.getElementById('bukaPeriodeModal').classList.remove('show'); }

function doBukaPeriode(){
  var jenisVal = $("#bp_jenis").dxSelectBox("instance").option("value");
  var tahunVal = $("#bp_tahun").dxNumberBox("instance").option("value");
  var bulanVal = $("#bp_bulan").dxSelectBox("instance").option("value");
  var jenis    = jenisList.find(function(j){return j.id===jenisVal;});

  if(!jenisVal||!tahunVal){ DevExpress.ui.notify("Pilih jenis iuran dan tahun","error",2500); return; }
  if(jenis&&jenis.periode==='bulanan'&&!bulanVal){ DevExpress.ui.notify("Pilih bulan","error",2500); return; }

  var data = {jenis_iuran_id:jenisVal, tahun:tahunVal};
  if(jenis&&jenis.periode==='bulanan') data.bulan=bulanVal;

  $.ajax({url:urlBukaPeriode, type:"POST", data:data})
    .done(function(r){
      DevExpress.ui.notify(r.message,"success",3500);
      bukaPeriodeClose();
      // Switch ke jenis iuran yang baru dibuka
      curJenis = jenisVal;
      $("#filterJenis").dxSelectBox("instance").option("value", curJenis);
      loadPeriodeList();
    })
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);});
}

// ── Tutup Buku ────────────────────────────────────────────────────────
function openTutupBuku(){
  if(!$("#tb_catatan").data('dx-was-initialized')){
    $("#tb_catatan").dxTextArea({height:80, placeholder:'Catatan penutupan (opsional)...'});
  }
  document.getElementById('tutupBukuModal').classList.add('show');
}
function tutupBukuClose(){ document.getElementById('tutupBukuModal').classList.remove('show'); }

function doTutupBuku(){
  var catatan = $("#tb_catatan").dxTextArea("instance").option("value");
  $.ajax({url:urlTutupBuku, type:"POST", data:{periode_id:curPeriode, catatan_penutupan:catatan}})
    .done(function(r){
      DevExpress.ui.notify(r.message,"success",3500);
      tutupBukuClose();
      loadPeriodeList();
    })
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);});
}

// ── Bayar (FIFO) ──────────────────────────────────────────────────────
function initBayarEditors(){
  $("#b_nominal").dxNumberBox({min:1, showSpinButtons:false, format:"#,##0"});
  $("#b_tanggal").dxDateBox({type:"date", displayFormat:"dd/MM/yyyy", value:new Date()});
  $("#b_keterangan").dxTextBox({placeholder:"Keterangan opsional..."});
  $("#b_metode").dxSelectBox({
    dataSource:["Transfer","Tunai","QRIS","Lainnya"],
    showClearButton:true, placeholder:"— Pilih metode —"
  });
}

function openBayar(d){
  bayarKkData = d;
  $("#b_kk_id").val(d.kk_id);
  $("#b_jenis_id").val(curJenis);
  document.getElementById('bayarTitle').textContent = 'Input Pembayaran';
  loadAlokasiPreview(d.kk_id);
  document.getElementById('bayarModal').classList.add('show');
}

function loadAlokasiPreview(kkId){
  // Ambil semua tagihan belum/sebagian untuk preview FIFO
  var params = {periode_id: curPeriode};
  $.getJSON(urlTagihanList, params, function(list){
    // Cari tagihan belum lunas dari KK ini (lintas periode = semua jenis)
    // Karena kita filter by periode, tampilkan info tagihan aktif
    var d = (list||[]).find(function(x){return x.kk_id==kkId;});
    var html='';
    if(d && d.sisa>0){
      html='<div class="bai-header">Tagihan yang akan dilunasi (FIFO)</div>';
      html+='<div class="bai-row"><span class="bai-periode">'+
        (periodeList.find(function(p){return p.id===curPeriode;})||{}).label+
        '</span><span class="bai-sisa">Sisa: '+rupiah(d.sisa)+'</span></div>';
    } else {
      html='<div class="bai-empty">Tidak ada tunggakan aktif. Pembayaran akan dialokasikan ke tagihan tertunggak.</div>';
    }
    document.getElementById('bayarAlokasiInfo').innerHTML=html;
    // Set nominal default = sisa tagihan periode ini
    if(d && d.sisa>0) $("#b_nominal").dxNumberBox("instance").option("value", d.sisa);
  });
}

function bayarSave(){
  var kkId   = $("#b_kk_id").val();
  var jenisId= $("#b_jenis_id").val();
  var jumlah = $("#b_nominal").dxNumberBox("instance").option("value");
  var tgl    = formatDate($("#b_tanggal").dxDateBox("instance").option("value"));
  var metode = $("#b_metode").dxSelectBox("instance").option("value");
  var ket    = $("#b_keterangan").dxTextBox("instance").option("value");

  if(!kkId||!jenisId){ DevExpress.ui.notify("Data tidak valid","error",2500); return; }
  if(!jumlah||jumlah<1){ DevExpress.ui.notify("Jumlah bayar harus lebih dari 0","error",2500); return; }

  $.ajax({url:urlPembayaran, type:"POST", data:{
    kartu_keluarga_id:kkId, jenis_iuran_id:jenisId,
    tanggal_bayar:tgl, jumlah_total:jumlah, metode:metode, keterangan:ket
  }})
  .done(function(r){
    DevExpress.ui.notify(r.message,"success",2500);
    bayarClose();
    refreshAll();
  })
  .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);});
}
function bayarClose(){ document.getElementById('bayarModal').classList.remove('show'); bayarKkData=null; }

// ── Riwayat Pembayaran ────────────────────────────────────────────────
function openRiwayat(){
  if(!bayarKkData) return;
  bayarClose();
  loadRiwayat(bayarKkData);
}

function openRiwayatOnly(d){
  bayarKkData = d;
  loadRiwayat(d);
}

function loadRiwayat(d){
  document.getElementById('riwayatList').innerHTML = '<div style="padding:20px;text-align:center;color:var(--redup);font-size:13px">Memuat...</div>';
  document.getElementById('riwayatModal').classList.add('show');

  $.getJSON(urlPembayaranList, {kartu_keluarga_id:d.kk_id, jenis_iuran_id:curJenis}, function(list){
    if(!list.length){
      document.getElementById('riwayatList').innerHTML='<div style="padding:20px;text-align:center;color:var(--redup);font-size:13px">Belum ada riwayat pembayaran.</div>';
      return;
    }
    var html='';
    list.forEach(function(p){
      html+='<div class="rwt-item">';
      html+='<div class="rwt-head"><span class="rwt-tgl">'+p.tanggal_bayar+'</span><span class="rwt-total">'+rupiah(p.jumlah_total)+'</span></div>';
      if(p.metode||p.petugas){
        html+='<div class="rwt-meta">'+(p.metode||'—')+(p.petugas?' &middot; Petugas: '+p.petugas:'')+(p.keterangan?' &middot; '+p.keterangan:'')+'</div>';
      }
      if(p.alokasi&&p.alokasi.length){
        html+='<div class="rwt-alokasi">';
        p.alokasi.forEach(function(a){
          html+='<div class="rwt-alok-row"><span class="rwt-alok-label">→ Bulan '+a.periode+'</span><span class="rwt-alok-val">'+rupiah(a.jumlah)+'</span></div>';
        });
        html+='</div>';
      }
      html+='</div>';
    });
    document.getElementById('riwayatList').innerHTML=html;
  });
}
function riwayatClose(){ document.getElementById('riwayatModal').classList.remove('show'); }

// ── Export Excel ──────────────────────────────────────────────────────
function exportIuran(){
  if(!curPeriode){ DevExpress.ui.notify("Pilih periode terlebih dahulu","warning",2000); return; }
  var p = periodeList.find(function(x){return x.id===curPeriode;});
  var label = p?p.label:'Iuran';
  var wb=new ExcelJS.Workbook();
  var sh=wb.addWorksheet(label);
  sh.columns=[
    {header:'No.',key:'no',width:6},{header:'Blok/No.',key:'blok_no',width:12},
    {header:'Kepala Keluarga',key:'kepala',width:25},{header:'Gang',key:'gang',width:15},
    {header:'Tagihan',key:'nominal',width:14},{header:'Dibayar',key:'dibayar',width:14},
    {header:'Sisa',key:'sisa',width:14},{header:'Status',key:'status',width:12},
  ];
  sh.getRow(1).font={bold:true,color:{argb:'FFFFFFFF'}};
  sh.getRow(1).fill={type:'pattern',pattern:'solid',fgColor:{argb:'FF2D6A4F'}};

  $.getJSON(urlTagihanList, {periode_id:curPeriode, gang_id:curGang||''}, function(data){
    data.forEach(function(d,i){
      var row=sh.addRow({no:i+1,blok_no:d.blok_no,kepala:d.kepala_keluarga,gang:d.gang,
        nominal:d.nominal,dibayar:d.nominal_dibayar,sisa:d.sisa,status:d.status});
      if(i%2===0) row.fill={type:'pattern',pattern:'solid',fgColor:{argb:'FFF2EFE6'}};
    });
    wb.xlsx.writeBuffer().then(function(buf){
      saveAs(new Blob([buf],{type:'application/octet-stream'}),label.replace(/ /g,'_')+'.xlsx');
    });
  });
}

// ── Import Tunggakan ──────────────────────────────────────────────────
function openImportTunggakan(){
  document.getElementById('importTunggakanResult').style.display='none';
  document.getElementById('importTunggakanResult').innerHTML='';
  document.getElementById('importTunggakanModal').classList.add('show');
  if(!$("#importTunggakanUploader").data('dx-was-initialized')){
    $("#importTunggakanUploader").dxFileUploader({
      uploadUrl:urlImportTunggakan,
      uploadHeaders:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
      name:'file', uploadMode:'useButtons',
      allowedFileExtensions:['.xlsx'], maxFileSize:5242880,
      selectButtonText:'Pilih File Excel', uploadButtonText:'Mulai Import',
      labelText:'atau seret file .xlsx ke sini', showFileList:false,
      onUploaded:function(e){
        var r=JSON.parse(e.request.responseText);
        var html=r.ok
          ?'<div style="padding:10px 12px;background:var(--daun-pucat);border-radius:8px;font-size:13px;color:#14532D"><strong>Berhasil!</strong> '+r.message+'</div>'
          :'<div style="padding:10px 12px;background:var(--stempel-soft);border-radius:8px;font-size:12.5px;color:#9A3422"><strong>Gagal:</strong> '+(r.message||'Terjadi kesalahan')+'</div>';
        if(r.errors&&r.errors.length){
          html+='<ul style="margin-top:8px;padding-left:16px;font-size:12px;color:#9A3422">';
          r.errors.forEach(function(e){html+='<li>'+e+'</li>';});
          html+='</ul>';
        }
        if(r.ok) refreshAll();
        document.getElementById('importTunggakanResult').innerHTML=html;
        document.getElementById('importTunggakanResult').style.display='block';
      },
      onUploadError:function(){
        document.getElementById('importTunggakanResult').innerHTML='<div style="padding:10px 12px;background:var(--stempel-soft);border-radius:8px;font-size:12.5px;color:#9A3422">Upload gagal.</div>';
        document.getElementById('importTunggakanResult').style.display='block';
      }
    });
  }
}
function importTunggakanClose(){ document.getElementById('importTunggakanModal').classList.remove('show'); }

// ── Keringanan ────────────────────────────────────────────────────────
function openKeringanan(d){
  $("#kr_tagihan_id").val(d.tagihan_id);
  if(!$("#kr_aktif").data('dx-was-initialized')){
    $("#kr_aktif").dxSwitch({value:false});
    $("#kr_catatan").dxTextBox({placeholder:'cth. Warga tidak mampu, bayar seikhlasnya...'});
  }
  $("#kr_aktif").dxSwitch("instance").option("value", d.is_keringanan||false);
  $("#kr_catatan").dxTextBox("instance").option("value", d.catatan_khusus||'');
  document.getElementById('keringananModal').classList.add('show');
}
function keringananSave(){
  var data={
    tagihan_id:     $("#kr_tagihan_id").val(),
    is_keringanan:  $("#kr_aktif").dxSwitch("instance").option("value")?1:0,
    catatan_khusus: $("#kr_catatan").dxTextBox("instance").option("value"),
  };
  $.ajax({url:urlKeringanan, type:"POST", data:data})
    .done(function(r){DevExpress.ui.notify(r.message,"success",2500);keringananClose();refreshAll();})
    .fail(function(xhr){DevExpress.ui.notify(xhr.responseJSON?.message||"Gagal","error",3000);});
}
function keringananClose(){ document.getElementById('keringananModal').classList.remove('show'); }

document.addEventListener('keydown',function(e){
  if(e.key==='Escape'){
    bayarClose(); bukaPeriodeClose(); tutupBukuClose();
    riwayatClose(); importTunggakanClose(); keringananClose();
  }
});
</script>
@endpush
