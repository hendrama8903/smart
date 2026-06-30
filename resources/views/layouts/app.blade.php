<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('judul','Dashboard') — SMART</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link href="{{ asset('css/devextreme/dx.softblue.compact.css') }}" rel="stylesheet" /> 

<script src="{{ asset('js/devextreme/jquery.js') }}"></script>
    <script src="{{ asset('js/lucide.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/devextreme/dx.all.js') }}"></script>
    <script src="{{ asset('js/devextreme-license.js') }}" type="text/javascript"></script>
<style>
  
a{color:inherit;text-decoration:none}

:root{
  --hutan:#14532D;
  --hutan-2:#1B5E3F;
  --daun:#2D6A4F;
  --daun-pucat:#E3EDE6;
  --kertas:#FAF8F2;
  --kertas-2:#F2EFE6;
  --surface:#FFFFFF;
  --tinta:#17231C;
  --redup:#6A7A70;
  --garis:#E0DED3;
  --garis-2:#D2D6CE;
  --stempel:#B5402C;
  --stempel-soft:#F4E3DF;
  --emas:#B8860B;
  --emas-soft:#F6EFD9;
  --biru:#2C5C7A;
  --biru-soft:#E2ECF1;
  --radius:14px;
  --shadow:0 1px 2px rgba(20,40,30,.04), 0 8px 24px -12px rgba(20,40,30,.18);
}
*{box-sizing:border-box;margin:0;padding:0}
html{-webkit-text-size-adjust:100%}
body{
  font-family:'Plus Jakarta Sans',system-ui,sans-serif;
  background:#fff;
  color:var(--tinta);
  line-height:1.5;
  -webkit-font-smoothing:antialiased;
}
.mono{font-family:'IBM Plex Mono',monospace;font-feature-settings:"tnum"}
button{font-family:inherit;cursor:pointer;border:none;background:none}
a{color:inherit;text-decoration:none}

/* ---------- Layout shell ---------- */
.app{display:grid;grid-template-columns:300px 1fr;min-height:100vh;transition:grid-template-columns .26s cubic-bezier(.4,0,.2,1)}
.collapse-btn{display:none}

/* ---------- Sidebar: rail + panel (gaya DNCS) ---------- */
.sidebar{display:flex;position:sticky;top:0;height:100vh;overflow:hidden}

/* narrow rail */
.rail{
  flex:0 0 56px;width:56px;background:linear-gradient(180deg,#1b5e3f,#103a26);
  display:flex;flex-direction:column;align-items:center;padding:14px 0 12px;color:#cfe3d7;
}
.rail-top{display:flex;flex-direction:column;gap:6px;align-items:center}
.rail-ic{width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#cfe3d7;transition:.15s;cursor:pointer}
.rail-ic svg{width:20px;height:20px;stroke-width:2}
.rail-ic:hover{background:rgba(255,255,255,.12);color:#fff}
.rail-ic.on{background:#fff;color:var(--hutan)}
.rail-vtext{
  writing-mode:vertical-rl;transform:rotate(180deg);margin:22px 0 16px;
  font-weight:800;font-size:15px;letter-spacing:.01em;white-space:nowrap;line-height:1;
  background:linear-gradient(180deg,#ffffff,#8ed3af);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;
}
.rail-vtext em{font-style:normal;-webkit-text-fill-color:#86c4a4;font-weight:700}
.rail-seal-logo{
  width:44px;height:44px;margin-bottom:12px;flex:0 0 44px;
}
.rail-seal-logo img{
  width:100%;height:100%;object-fit:contain;border-radius:50%;
  filter:drop-shadow(0 2px 6px rgba(0,0,0,.4));
}
.rail-seal{
  width:38px;height:38px;flex:0 0 38px;border-radius:50%;border:1.5px solid var(--emas);
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  background:radial-gradient(circle at 50% 38%,#1d6b48,#103a26);transform:rotate(-6deg);margin-bottom:12px;position:relative;
}
.rail-seal::after{content:"";position:absolute;inset:4px;border:1px dashed rgba(184,134,11,.5);border-radius:50%}
.rail-seal b{font-size:11px;font-weight:800;color:#f6efd9;font-family:'IBM Plex Mono',monospace;line-height:1}
.rail-seal span{font-size:4.5px;letter-spacing:.1em;color:var(--emas);font-weight:700;margin-top:1px}
.rail-collapse{
  margin-top:auto;width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;
  color:#cfe3d7;border:1px solid rgba(255,255,255,.2);cursor:pointer;transition:.15s
}
.rail-collapse:hover{background:rgba(255,255,255,.12);color:#fff}
.rail-collapse svg{transition:transform .26s}

/* wide panel */
.panel{
  flex:1;min-width:0;background:#fff;border-right:1px solid var(--garis);
  display:flex;flex-direction:column;padding:16px 14px;overflow-y:auto;overflow-x:hidden;
  transition:padding .26s cubic-bezier(.4,0,.2,1),opacity .2s;
}
.psearch{display:flex;align-items:center;gap:8px;background:var(--kertas);border:1px solid var(--garis);border-radius:10px;padding:9px 12px;margin-bottom:14px}
.psearch input{border:none;outline:none;background:none;font-family:inherit;font-size:13px;width:100%;color:var(--tinta)}
.psearch input::placeholder{color:#9aa89f}
.pbrand{padding:2px 6px 14px;border-bottom:1px solid var(--garis);margin-bottom:10px;text-align:center}
.pbrand b{display:block;font-size:22px;font-weight:800;color:var(--hutan);letter-spacing:.02em}

/* tree nav (accordion) */
.tree{display:flex;flex-direction:column;gap:2px}
.tree-link{
  display:flex;align-items:center;gap:11px;width:100%;text-align:left;
  padding:10px 11px;border-radius:10px;color:var(--tinta);font-weight:600;font-size:14px;transition:.13s;
}
.tree-link:hover{background:var(--kertas-2)}
.tree-link.on{background:var(--daun-pucat);color:#155234}
.tree-link .ic{width:18px;height:18px;flex:0 0 18px;color:var(--daun);stroke-width:2}
.tree-link.on .ic{color:#1b5e3f}
.tree-link .chev{margin-left:auto;width:15px;height:15px;color:#a7b3aa;transition:transform .22s;stroke-width:2.6}
.tree-group.open > .tree-link .chev{transform:rotate(90deg)}
.tree-group.open > .tree-link{color:#155234}
.tree-sub{display:grid;grid-template-rows:0fr;transition:grid-template-rows .24s ease}
.tree-group.open > .tree-sub{grid-template-rows:1fr}
.tree-sub-inner{overflow:hidden;display:flex;flex-direction:column;gap:1px;padding-left:15px;margin:2px 0 4px;border-left:1.5px solid var(--garis)}
.tree-sub button,.tree-sub a{
  display:flex;align-items:center;gap:9px;width:100%;text-align:left;
  padding:8px 11px;border-radius:8px;color:var(--redup);font-weight:600;font-size:13px;transition:.13s;
}
.tree-sub button:hover,.tree-sub a:hover{background:var(--kertas-2);color:var(--tinta)}
.tree-sub button.on,.tree-sub a.on{background:var(--daun-pucat);color:#155234;font-weight:700}
.tree-sub button .ic,.tree-sub a .ic{width:15px;height:15px;flex:0 0 15px;color:#9aa89f;stroke-width:2}
.tree-sub button.on .ic,.tree-sub a.on .ic{color:var(--daun)}

/* collapsed (desktop): sembunyikan panel, sisakan rail */
@media(min-width:721px){
  .app.collapsed{grid-template-columns:56px 1fr}
  .app.collapsed .panel{padding-left:0;padding-right:0;opacity:0;pointer-events:none}
  .app.collapsed .rail-collapse svg{transform:rotate(180deg)}
}

/* ---------- Main ---------- */
.main{min-width:0;display:flex;flex-direction:column}
.topbar{
  display:flex;align-items:center;gap:16px;
  padding:16px 32px;background:#fff;
  border-bottom:1px solid var(--garis);position:sticky;top:0;z-index:20;
}
.menu-btn{display:none}
.crumb{font-size:12.5px;color:var(--redup);font-weight:600}
.crumb b{color:var(--tinta)}
.search{margin-left:auto;display:flex;align-items:center;gap:8px;background:var(--surface);border:1px solid var(--garis);border-radius:10px;padding:8px 12px;width:260px;max-width:40vw}
.search input{border:none;outline:none;font-family:inherit;font-size:13px;width:100%;background:none;color:var(--tinta)}
.search input::placeholder{color:#9AA89F}
.topbtn{width:38px;height:38px;border-radius:10px;background:var(--surface);border:1px solid var(--garis);display:flex;align-items:center;justify-content:center;color:var(--hutan);position:relative}
.topbtn .dot{position:absolute;top:8px;right:9px;width:7px;height:7px;border-radius:50%;background:var(--stempel);border:1.5px solid var(--kertas);display:none}
.notif-wrap{position:relative;margin-left:auto}
.notif-badge-count{position:absolute;top:-4px;right:-4px;min-width:18px;height:18px;padding:0 4px;border-radius:9px;background:var(--stempel);color:#fff;font-size:10px;font-weight:700;display:none;align-items:center;justify-content:center;border:2px solid var(--kertas);z-index:2}
.notif-panel{position:absolute;top:calc(100% + 10px);right:0;width:320px;background:#fff;border:1px solid var(--garis);border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.12);z-index:200;display:none;overflow:hidden}
.notif-panel.show{display:block;animation:fade .18s ease}
.notif-head{display:flex;align-items:center;justify-content:space-between;padding:14px 16px 10px;border-bottom:1px solid var(--garis)}
.notif-head>span{font-size:14px;font-weight:700;color:var(--tinta);display:flex;align-items:center;gap:6px}
.notif-head-count{font-size:11px;font-weight:600;color:#fff;background:var(--daun);border-radius:10px;padding:2px 8px}
.notif-baca-semua{font-size:11px;color:var(--daun);font-weight:600;background:none;border:none;cursor:pointer;padding:0;white-space:nowrap}
.notif-baca-semua:hover{text-decoration:underline}
.notif-empty{padding:32px 16px;text-align:center;color:var(--redup);font-size:13px}
.notif-list{max-height:360px;overflow-y:auto}
.notif-item{display:flex;gap:11px;padding:11px 16px;border-bottom:1px solid var(--garis);text-decoration:none;color:inherit;transition:background .15s}
.notif-item:last-child{border-bottom:none}
.notif-item:hover{background:var(--surface)}
.notif-icon{width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px}
.notif-icon.pengumuman{background:#e8f5e9;color:#388e3c}
.notif-icon.iuran{background:#fff3e0;color:#e65100}
.notif-icon.booking{background:#e3f2fd;color:#1565c0}
.notif-icon.piutang{background:#fce4ec;color:#c62828}
.notif-body{flex:1;min-width:0}
.notif-label{font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--redup);margin-bottom:2px}
.notif-judul{font-size:13px;font-weight:600;color:var(--tinta);line-height:1.35;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.notif-item.penting .notif-judul{color:var(--stempel)}
.notif-waktu{font-size:11px;color:var(--redup);margin-top:2px}

/* ── User avatar dropdown ── */
.user-wrap{position:relative}
.user-btn{width:38px;height:38px;border-radius:10px;background:var(--hutan);border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:.15s}
.user-btn:hover{background:var(--hutan-2)}
.user-av-mini{font-size:13px;font-weight:800;color:#fff;letter-spacing:.02em;line-height:1}
.user-panel{position:absolute;top:calc(100% + 10px);right:0;width:220px;background:#fff;border:1px solid var(--garis);border-radius:14px;box-shadow:0 8px 32px rgba(0,0,0,.12);z-index:200;display:none;overflow:hidden}
.user-panel.show{display:block;animation:fade .18s ease}
.user-panel-top{display:flex;align-items:center;gap:10px;padding:14px 16px;border-bottom:1px solid var(--garis)}
.user-av-lg{width:36px;height:36px;border-radius:10px;background:var(--hutan);color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;flex:0 0 36px}
.user-panel-name{font-size:13px;font-weight:700;color:var(--tinta);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.user-panel-role{font-size:11px;color:var(--redup);margin-top:1px}
.user-panel-logout{display:flex;align-items:center;gap:9px;width:100%;padding:12px 16px;background:none;border:none;cursor:pointer;font-size:13px;font-weight:600;color:var(--stempel);font-family:inherit;transition:background .14s}
.user-panel-logout:hover{background:#FFF5F5}
.user-panel-logout svg{flex:0 0 15px}

.content{padding:28px 32px 56px;max-width:1180px;width:100%}
.page{display:none;animation:fade .25s ease}
.page.show{display:block}
@keyframes fade{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}

.page-head{margin-bottom:24px}
.eyebrow{display:inline-flex;align-items:center;gap:7px;font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:var(--daun);font-weight:700;margin-bottom:8px}
.eyebrow::before{content:"";width:18px;height:2px;background:var(--emas)}
.page-head h1{font-size:27px;font-weight:800;letter-spacing:-.02em;margin-bottom:4px}
.page-head p{color:var(--redup);font-size:14px;max-width:560px}

/* ---------- Stat cards ---------- */
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px}
.stat{
  background:var(--surface);border:1px solid var(--garis);border-radius:var(--radius);
  padding:18px 18px 16px;box-shadow:var(--shadow);position:relative;overflow:hidden;
}
.stat::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--daun)}
.stat.s-emas::before{background:var(--emas)}
.stat.s-stempel::before{background:var(--stempel)}
.stat.s-biru::before{background:var(--biru)}
.stat .lbl{font-size:12px;color:var(--redup);font-weight:600;display:flex;align-items:center;gap:7px}
.stat .lbl .ic{width:15px;height:15px;color:var(--daun)}
.stat.s-emas .lbl .ic{color:var(--emas)}.stat.s-stempel .lbl .ic{color:var(--stempel)}.stat.s-biru .lbl .ic{color:var(--biru)}
.stat .val{font-size:26px;font-weight:700;margin-top:10px;letter-spacing:-.02em}
.stat .val small{font-size:14px;color:var(--redup);font-weight:600}
.stat .sub{font-size:11.5px;color:var(--redup);margin-top:6px;display:flex;align-items:center;gap:5px}
.up{color:#2D6A4F;font-weight:700}.down{color:var(--stempel);font-weight:700}

/* ---------- Section grid ---------- */
.grid2{display:grid;grid-template-columns:1.5fr 1fr;gap:16px}
.card{background:var(--surface);border:1px solid var(--garis);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden}
.card-head{padding:16px 20px;border-bottom:1px solid var(--garis);display:flex;align-items:center;justify-content:space-between}
.card-head h3{font-size:15px;font-weight:700;display:flex;align-items:center;gap:9px}
.card-head h3 .ic{width:17px;height:17px;color:var(--daun)}
.card-head .link{font-size:12.5px;color:var(--daun);font-weight:700}
.card-body{padding:6px 0}
.card-pad{padding:18px 20px}

/* ---------- Table ---------- */
table{width:100%;border-collapse:collapse;font-size:13.5px}
thead th{
  text-align:left;font-size:11px;letter-spacing:.06em;text-transform:uppercase;
  color:var(--redup);font-weight:700;padding:10px 20px;background:var(--kertas-2);
  border-bottom:1px solid var(--garis);
}
tbody td{padding:13px 20px;border-bottom:1px solid var(--garis)}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:#FBFAF5}
.r-name{font-weight:600;display:flex;align-items:center;gap:10px}
.mini-av{width:30px;height:30px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;background:var(--daun-pucat);color:var(--hutan);flex:0 0 30px}
.r-sub{font-size:11.5px;color:var(--redup);font-weight:500}

.pill{display:inline-flex;align-items:center;gap:6px;font-size:11.5px;font-weight:700;padding:4px 11px;border-radius:20px}
.pill::before{content:"";width:6px;height:6px;border-radius:50%}
.pill.lunas{background:var(--daun-pucat);color:#1B5E3F}.pill.lunas::before{background:#2D6A4F}
.pill.belum{background:var(--stempel-soft);color:#9A3422}.pill.belum::before{background:var(--stempel)}
.pill.tempo{background:var(--emas-soft);color:#8A6608}.pill.tempo::before{background:var(--emas)}
.pill.acc{background:var(--biru-soft);color:#234C66}.pill.acc::before{background:var(--biru)}

/* ---------- Lists ---------- */
.list{display:flex;flex-direction:column}
.li{display:flex;align-items:center;gap:13px;padding:14px 20px;border-bottom:1px solid var(--garis)}
.li:last-child{border-bottom:none}
.li .date{flex:0 0 48px;text-align:center;background:var(--kertas-2);border:1px solid var(--garis);border-radius:10px;padding:6px 0}
.li .date b{display:block;font-size:17px;font-weight:800;line-height:1;color:var(--hutan)}
.li .date span{font-size:10px;text-transform:uppercase;color:var(--redup);font-weight:700;letter-spacing:.05em}
.li .info{min-width:0;flex:1}
.li .info b{font-size:13.5px;font-weight:700;display:block}
.li .info span{font-size:12px;color:var(--redup)}
.li .amt{font-weight:700;font-size:13.5px;white-space:nowrap}
.li .amt.in{color:#2D6A4F}.li .amt.out{color:var(--stempel)}

/* ronda chips */
.ronda-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;padding:18px 20px}
.ronda{border:1px solid var(--garis);border-radius:12px;padding:14px;background:var(--kertas)}
.ronda .d{font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:var(--daun);font-weight:800;margin-bottom:8px}
.ronda .who{font-size:13px;font-weight:600;line-height:1.7}
.ronda.tonight{background:var(--hutan);border-color:var(--hutan)}
.ronda.tonight .d{color:var(--emas)}
.ronda.tonight .who{color:#DCEAE0}
.ronda.tonight .tag{display:inline-block;font-size:10px;background:var(--stempel);color:#fff;padding:2px 8px;border-radius:6px;font-weight:700;margin-bottom:8px}

/* pendopo */
.book{display:flex;gap:14px;padding:16px 20px;border-bottom:1px solid var(--garis)}
.book:last-child{border-bottom:none}
.book .cal{flex:0 0 56px;border-radius:12px;border:1px solid var(--garis);overflow:hidden;text-align:center}
.book .cal .m{background:var(--hutan);color:#fff;font-size:10px;font-weight:700;padding:3px 0;text-transform:uppercase;letter-spacing:.08em}
.book .cal .dd{font-size:22px;font-weight:800;padding:5px 0;color:var(--hutan)}
.book .bd{flex:1;min-width:0}
.book .bd b{font-size:14px;font-weight:700}
.book .bd .meta{font-size:12px;color:var(--redup);margin-top:3px;display:flex;flex-wrap:wrap;gap:4px 14px}
.book .bd .meta span{display:inline-flex;align-items:center;gap:5px}

.btn{display:inline-flex;align-items:center;gap:8px;background:var(--hutan);color:#fff;font-weight:700;font-size:13px;padding:9px 16px;border-radius:10px;transition:.15s}
.btn:hover{background:var(--hutan-2)}
.btn.ghost{background:var(--surface);color:var(--hutan);border:1px solid var(--garis-2)}
.btn.ghost:hover{background:var(--kertas-2)}
.btn .ic{width:16px;height:16px}
.head-actions{display:flex;gap:10px;align-items:center;flex-wrap:wrap}

.toolbar{display:flex;gap:10px;align-items:center;margin-bottom:16px;flex-wrap:wrap}
.tabs{display:inline-flex;background:var(--kertas-2);border:1px solid var(--garis);border-radius:10px;padding:3px}
.tabs button{padding:7px 14px;border-radius:8px;font-size:12.5px;font-weight:600;color:var(--redup)}
.tabs button.on{background:var(--surface);color:var(--hutan);box-shadow:var(--shadow)}

.progress{height:8px;background:var(--kertas-2);border-radius:20px;overflow:hidden;border:1px solid var(--garis)}
.progress i{display:block;height:100%;background:linear-gradient(90deg,var(--daun),var(--hutan));border-radius:20px}

.kas-summary{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px}

/* mobile overlay */
.scrim{display:none}


@media(max-width:920px){
  .stats{grid-template-columns:repeat(2,1fr)}
  .grid2{grid-template-columns:1fr}
  .kas-summary{grid-template-columns:1fr}
}
@media(max-width:720px){
  .app{grid-template-columns:1fr}
  .sidebar{position:fixed;left:0;top:0;z-index:60;width:300px;max-width:88vw;transform:translateX(-100%);transition:transform .25s;box-shadow:0 0 40px rgba(0,0,0,.25)}
  .sidebar.open{transform:none}
  .scrim.show{display:block;position:fixed;inset:0;background:rgba(20,30,24,.45);z-index:50}
  .menu-btn{display:flex}
  .topbar{padding:13px 16px;gap:12px}
  .notif-panel{width:calc(100vw - 32px);right:-8px}
  .user-panel{width:calc(100vw - 32px);right:-8px}
  .content{padding:20px 16px 32px}
  .ronda-grid{grid-template-columns:1fr}
  .page-head h1{font-size:22px}
  .page-head p{font-size:13.5px}


  /* sidebar foot lebih lega di drawer */
  .topbtn{width:40px;height:40px}
}
@media(max-width:600px){
  .stats{grid-template-columns:1fr}
  .hide-sm{display:none}
  .crumb{font-size:13px}
  .stat .val{font-size:24px}
  .card-head{padding:14px 16px}
  thead th{padding:9px 16px}
  tbody td{padding:12px 16px}
  .li{padding:13px 16px}
  .book{padding:14px 16px}
  .card-body,.card{-webkit-overflow-scrolling:touch}

  /* ── Mobile: grid 2-col → 1-col ── */
  .ff2,.ff3{grid-template-columns:1fr !important}

  /* ── Mobile: grid wrap horizontal scroll ── */
  .grid-wrap{overflow-x:auto}

  /* ── Mobile: toolbar wrap ── */
  .keu-toolbar,.wg-toolbar,.ut-toolbar,.pg-toolbar{flex-direction:column;align-items:flex-start !important}
  .keu-actions,.wg-actions,.ut-actions,.pg-actions{flex-wrap:wrap;width:100%}

  /* ── Mobile: tombol toolbar — icon only, kotak kompak seperti Master Role ── */
  .btn{font-size:0;gap:0;padding:0;min-width:unset !important;width:46px;height:46px;
       border-radius:12px;justify-content:center;flex:0 0 46px !important}
  .btn .ic{width:18px;height:18px;flex-shrink:0}

  /* ── Mobile: modal full width ── */
  .modal-overlay{padding:12px}
  .modal-card{border-radius:14px !important;max-width:100% !important;width:100% !important}
  .keu-card,.mk-card,.wg-card,.pg-card,.uf-card,.mf-card{max-width:100% !important;border-radius:14px !important}
  .keu-foot,.mk-foot,.wg-foot,.pg-foot,.uf-foot,.mf-foot{border-radius:0 0 14px 14px !important}

  /* ── Mobile: form body scroll ── */
  .keu-body,.wg-body,.pg-body,.uf-body{max-height:60vh;overflow-y:auto}

  /* ── Mobile: iuran summary ── */
  .iuran-summary,.kas-summary,.rv-summary{grid-template-columns:1fr 1fr !important}

  /* ── Mobile: search topbar ── */
  .search{display:none}
}

/* blok user di bawah panel */
.panel-user{margin-top:auto;display:flex;align-items:center;gap:10px;padding:12px 10px 4px;border-top:1px solid var(--garis)}
.pu-av{width:36px;height:36px;flex:0 0 36px;border-radius:10px;background:var(--daun-pucat);color:var(--hutan);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px}
.pu-info{min-width:0;flex:1}
.pu-info b{display:block;font-size:13px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pu-info span{font-size:11px;color:var(--redup)}
.pu-out{width:34px;height:34px;border-radius:9px;border:1px solid var(--garis);color:var(--stempel);display:flex;align-items:center;justify-content:center;background:none;cursor:pointer;transition:.15s}
.pu-out:hover{background:var(--stempel-soft)}
.panel{padding-bottom:14px}

/* ===== modal konfirmasi modern ===== */
.modal-overlay{position:fixed;inset:0;z-index:100;display:flex;align-items:center;justify-content:center;padding:20px;
  background:rgba(16,40,28,.45);backdrop-filter:blur(4px);opacity:0;visibility:hidden;transition:opacity .2s}
.modal-overlay.show{opacity:1;visibility:visible}
.modal-card{background:#fff;border-radius:14px;max-width:380px;width:100%;padding:30px 28px 24px;text-align:center;
  box-shadow:0 30px 70px -25px rgba(16,40,28,.5);transform:translateY(12px) scale(.97);transition:transform .22s cubic-bezier(.34,1.4,.5,1)}
.modal-overlay.show .modal-card{transform:none}
.modal-ic{width:60px;height:60px;border-radius:50%;background:var(--stempel-soft);color:var(--stempel);
  display:flex;align-items:center;justify-content:center;margin:0 auto 16px}
.modal-ic svg{width:28px;height:28px;stroke-width:2}
.modal-card h3{font-size:19px;font-weight:800;letter-spacing:-.01em;margin-bottom:8px}
.modal-card p{font-size:13.5px;color:var(--redup);line-height:1.55;margin-bottom:22px}
.modal-actions{display:flex;gap:10px}
.mbtn{flex:1;padding:12px;border-radius:11px;font-weight:700;font-size:14px;cursor:pointer;transition:.15s;border:none}
.mbtn.ghost{background:var(--kertas2);color:var(--tinta);border:1px solid var(--garis)}
.mbtn.ghost:hover{background:var(--garis)}
.mbtn.danger{background:var(--stempel);color:#fff}
.mbtn.danger:hover{filter:brightness(1.07)}
</style>
@stack('styles')
<style>
/* DevExtreme filterRow — global styling */
.dx-datagrid-filter-row td{padding:5px 8px!important;background:var(--kertas)}
.dx-datagrid-filter-row .dx-editor-cell .dx-texteditor-input{
  font-family:'Plus Jakarta Sans',system-ui,sans-serif;font-size:12.5px;
  padding:5px 8px;border:1px solid var(--garis-2)!important;
  border-radius:7px;background:#fff;color:var(--tinta);
}
.dx-datagrid-filter-row .dx-editor-cell .dx-texteditor-input:focus{
  border-color:var(--daun)!important;box-shadow:0 0 0 3px rgba(45,106,79,.1);outline:none;
}
.dx-datagrid-filter-row .dx-menu{display:none!important}
</style>
</head>
<body>
@php
  $u = auth()->user();
  $inisial = collect(explode(' ', $u->name))->map(fn($w)=>mb_substr($w,0,1))->take(2)->implode('');
  $roleLabel = optional($u->role)->label ?? 'Pengguna';
  // Sidebar hanya tampilkan type menu dan screen (bukan button)
  try {
    $menuTree = \App\Models\Menu::with([
        'children' => fn($q) => $q->where('aktif', true)
                                   ->whereIn('type', ['menu', 'screen'])
                                   ->orderBy('urutan'),
    ])->whereNull('parent_id')->where('aktif', true)
      ->whereIn('type', ['menu', 'screen'])->orderBy('urutan')->get();
  } catch (\Throwable) {
    $menuTree = collect();
  }

  // Hitung __perms dari button children screen yang aktif
  $__perms = ['add' => true, 'edit' => true, 'delete' => true, 'export' => true];
  try {
    $__activeScreen = null;
    foreach ($menuTree as $_m) {
        if ($_m->isActive()) { $__activeScreen = $_m; break; }
        foreach ($_m->children as $_c) {
            if ($_c->isActive()) { $__activeScreen = $_c; break 2; }
        }
    }
    if ($__activeScreen) {
        $__buttons = \App\Models\Menu::where('parent_id', $__activeScreen->id)
            ->where('type', 'button')->where('aktif', true)->get();
        if ($__buttons->isNotEmpty()) {
            // Ada button terdefinisi → hitung izin per tombol
            $__map = ['tambah'=>'add','ubah'=>'edit','hapus'=>'delete','ekspor'=>'export','export'=>'export','unduh'=>'export','download'=>'export','upload'=>'upload','impor'=>'upload','import'=>'upload'];
            $__perms = ['add' => false, 'edit' => false, 'delete' => false, 'export' => false];
            foreach ($__buttons as $_btn) {
                $__key = $__map[strtolower($_btn->nama)] ?? null;
                if ($__key) $__perms[$__key] = $_btn->visibleTo($u);
            }
        }
    }
  } catch (\Throwable) {
    // fallback akses penuh jika terjadi error
  }
@endphp
<script>window.__perms = @json($__perms);</script>
<div class="scrim" id="scrim" onclick="toggleNav(false)"></div>
<div class="app">

  <aside class="sidebar" id="sidebar">
    <div class="rail">
      <div class="rail-top">
        <a class="rail-ic {{ request()->routeIs('dashboard') ? 'on' : '' }}" href="{{ route('dashboard') }}" title="Dashboard">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 9.5 12 3l9 6.5"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/></svg>
        </a>
        <div class="rail-ic" title="Profil pengurus">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="8" r="4"/><path d="M5.5 21a6.5 6.5 0 0 1 13 0"/></svg>
        </div>
      </div>
      <div class="rail-vtext">PERMATA <em>REGENCY</em></div>
      <div class="rail-seal-logo">
        <img src="{{ asset('images/logo-rt.png') }}" alt="SMART RT 001"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
        <div class="rail-seal" style="display:none"><b>001</b><span>RT&middot;RW015</span></div>
      </div>
      <div class="rail-collapse" onclick="toggleCollapse()" title="Sembunyikan / tampilkan panel">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 6 9 12 15 18"/><polyline points="20 6 14 12 20 18"/></svg>
      </div>
    </div>

    <div class="panel">
      <div class="psearch">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#9aa89f" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input placeholder="Cari menu…" id="sidebarSearch" oninput="filterMenu(this.value)" autocomplete="off">
      </div>
      <div class="pbrand"><b>SMART</b></div>

      <nav class="tree">
  @foreach($menuTree as $item)
    @php
      // Menu group: tampil jika ada minimal 1 child yang visible
      // Screen: tampil berdasarkan roles-nya sendiri
      $itemVisible = ($item->type === 'menu' && $item->children->isNotEmpty())
          ? $item->children->contains(fn($c) => $c->visibleTo($u))
          : $item->visibleTo($u);
    @endphp
    @continue(! $itemVisible)
    @if($item->children->isEmpty())
      <a class="tree-link {{ $item->isActive() ? 'on' : '' }}" href="{{ $item->link() }}" @if($item->link()==='#') onclick="return false" @endif>
        @include('partials.menu-icon', ['icon' => $item->icon, 'cls' => 'ic'])
        {{ $item->nama }}
      </a>
    @else
      @php $childActive = $item->children->contains(fn($c) => $c->isActive()); @endphp
      <div class="tree-group {{ $childActive ? 'open' : '' }}">
        <button class="tree-link" type="button" onclick="toggleGroup(this)">
          @include('partials.menu-icon', ['icon' => $item->icon, 'cls' => 'ic'])
          {{ $item->nama }}
          <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="9 6 15 12 9 18"/></svg>
        </button>
        <div class="tree-sub"><div class="tree-sub-inner">
          @foreach($item->children as $child)
            @continue(! $child->visibleTo($u))
            <a class="{{ $child->isActive() ? 'on' : '' }}" href="{{ $child->link() }}" @if($child->link()==='#') onclick="return false" @endif>
              @include('partials.menu-icon', ['icon' => $child->icon, 'cls' => 'ic'])
              {{ $child->nama }}
            </a>
          @endforeach
        </div></div>
      </div>
    @endif
  @endforeach
</nav>

      <div class="panel-user">
        <div class="pu-av">{{ $inisial }}</div>
        <div class="pu-info"><b>{{ $u->name }}</b><span>{{ $roleLabel }}</span></div>
        <form id="logoutForm" method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="pu-out" type="button" onclick="openLogout()" title="Keluar">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          </button>
        </form>
      </div>
    </div>
  </aside>

  <main class="main">
    <div class="topbar">
      <button class="topbtn menu-btn" type="button" onclick="toggleNav(true)">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
      <div class="crumb">SMART &nbsp;/&nbsp; <b id="crumb">@yield('judul','Dashboard')</b></div>
      <div class="notif-wrap">
        <button class="topbtn" type="button" id="notifBtn" title="Notifikasi">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
        </button>
        <span class="notif-badge-count" id="notifBadge"></span>
        <div class="notif-panel" id="notifPanel">
          <div class="notif-head">
            <span>Notifikasi <span class="notif-head-count" id="notifHeadCount" style="display:none"></span></span>
            <button class="notif-baca-semua" id="notifBacaSemua" type="button">Tandai semua dibaca</button>
          </div>
          <div id="notifList"><div class="notif-empty">Memuat...</div></div>
        </div>
      </div>

      <div class="user-wrap">
        <button class="user-btn" type="button" id="userBtn" title="{{ $u->name }}">
          <span class="user-av-mini">{{ $inisial }}</span>
        </button>
        <div class="user-panel" id="userPanel">
          <div class="user-panel-top">
            <div class="user-av-lg">{{ $inisial }}</div>
            <div style="min-width:0">
              <div class="user-panel-name">{{ $u->name }}</div>
              <div class="user-panel-role">{{ $roleLabel }}</div>
            </div>
          </div>
          <button class="user-panel-logout" type="button" onclick="userPanelClose();openLogout()">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Keluar
          </button>
        </div>
      </div>
    </div>

    <div class="content">
      @yield('content')
    </div>
  </main>
</div>


<script>
function toggleGroup(h){ h.closest('.tree-group').classList.toggle('open'); }

function filterMenu(q) {
  q = q.trim().toLowerCase();
  var tree = document.querySelector('.tree');
  if (!tree) return;

  if (!q) {
    // Kosong → tampilkan semua kembali ke kondisi normal
    tree.querySelectorAll('.tree-link, .tree-sub button, .tree-sub a').forEach(function(el) {
      el.style.display = '';
    });
    tree.querySelectorAll('.tree-group').forEach(function(g) {
      // Kembalikan grup yang aktif tetap terbuka, yang lain tutup
      if (!g.querySelector('.on')) g.classList.remove('open');
    });
    return;
  }

  // Cari di semua link (parent + child)
  tree.querySelectorAll('.tree-group').forEach(function(group) {
    var parentLink  = group.querySelector(':scope > .tree-link');
    var childLinks  = group.querySelectorAll('.tree-sub button, .tree-sub a');
    var parentText  = parentLink ? parentLink.textContent.toLowerCase() : '';
    var parentMatch = parentText.includes(q);
    var childMatch  = false;

    childLinks.forEach(function(child) {
      var match = child.textContent.toLowerCase().includes(q);
      child.style.display = match ? '' : 'none';
      if (match) childMatch = true;
    });

    if (parentMatch || childMatch) {
      parentLink && (parentLink.style.display = '');
      group.classList.add('open');  // buka grup jika ada match
    } else {
      parentLink && (parentLink.style.display = 'none');
      group.classList.remove('open');
    }
  });

  // Link top-level yang bukan grup (screen langsung)
  tree.querySelectorAll(':scope > .tree-link').forEach(function(link) {
    link.style.display = link.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
}
function toggleNav(open){
  document.getElementById('sidebar').classList.toggle('open',open);
  document.getElementById('scrim').classList.toggle('show',open);
}
function toggleCollapse(){ document.querySelector('.app').classList.toggle('collapsed'); }
document.querySelectorAll('.tabs').forEach(t=>{
  t.querySelectorAll('button').forEach(b=>b.addEventListener('click',()=>{
    t.querySelectorAll('button').forEach(x=>x.classList.remove('on'));b.classList.add('on');
  }));
});
</script>
{{-- DevExtreme global defaults: filter row aktif di semua grid --}}
<script>
$(function(){
  if(typeof DevExpress !== 'undefined' && DevExpress.ui && DevExpress.ui.dxDataGrid){
    DevExpress.ui.dxDataGrid.defaultOptions({
      options: {
        filterRow: {
          visible: true,
          applyFilter: 'auto'
        }
      }
    });
  }
});
</script>
@stack('scripts')
<!-- modal konfirmasi keluar -->
<div class="modal-overlay" id="logoutModal" onclick="if(event.target===this)closeLogout()">
  <div class="modal-card">
    <div class="modal-ic">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
    </div>
    <h3>Keluar dari SMART?</h3>
    <p>Anda akan keluar dari sesi ini dan perlu masuk kembali untuk membuka dashboard.</p>
    <div class="modal-actions">
      <button class="mbtn ghost" type="button" onclick="closeLogout()">Batal</button>
      <button class="mbtn danger" type="button" onclick="document.getElementById('logoutForm').submit()">Ya, Keluar</button>
    </div>
  </div>
</div>

<script>
function openLogout(){ document.getElementById('logoutModal').classList.add('show'); }
function closeLogout(){ document.getElementById('logoutModal').classList.remove('show'); }
document.addEventListener('keydown', e=>{ if(e.key==='Escape') closeLogout(); });

// Cegah halaman ter-restore dari cache browser (bfcache) saat tombol Back ditekan setelah logout.
window.addEventListener('pageshow', function(e){
  if (e.persisted) { window.location.reload(); }
});

// Auto-tooltip: ambil teks tombol .btn dan jadikan title untuk hover keterangan
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('.btn').forEach(function(btn){
    if (btn.title) return;
    var text = Array.from(btn.childNodes)
      .filter(function(n){ return n.nodeType === 3; })
      .map(function(n){ return n.textContent.trim(); })
      .filter(Boolean).join(' ');
    if (text) btn.title = text;
  });
});

// ── Notifikasi ──
(function(){
  var btn    = document.getElementById('notifBtn');
  var panel  = document.getElementById('notifPanel');
  var badge  = document.getElementById('notifBadge');
  var list   = document.getElementById('notifList');
  var hcount = document.getElementById('notifHeadCount');
  var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

  var icons = {
    pengumuman: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
    iuran:      '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>',
    booking:    '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
    piutang:    '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
  };

  function postJson(url) {
    return fetch(url, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
    });
  }

  function updateBadge(total) {
    if (total > 0) {
      badge.textContent = total > 9 ? '9+' : total;
      badge.style.display = 'flex';
      hcount.textContent = total + ' belum dibaca';
      hcount.style.display = '';
    } else {
      badge.style.display = 'none';
      hcount.style.display = 'none';
    }
  }

  function renderList(items) {
    if (!items || items.length === 0) {
      list.innerHTML = '<div class="notif-empty">Semua notifikasi sudah dibaca</div>';
      return;
    }
    var html = '';
    items.forEach(function(item){
      html += '<a href="' + item.url + '" class="notif-item' + (item.penting ? ' penting' : '') + '" data-id="' + item.id + '">'
        + '<div class="notif-icon ' + item.type + '">' + (icons[item.type] || '') + '</div>'
        + '<div class="notif-body">'
        + '<div class="notif-label">' + item.label + '</div>'
        + '<div class="notif-judul">' + item.judul + '</div>'
        + '<div class="notif-waktu">' + item.dibuat + '</div>'
        + '</div></a>';
    });
    list.innerHTML = html;

    // Klik item → mark as read lalu navigasi
    list.querySelectorAll('.notif-item[data-id]').forEach(function(el){
      el.addEventListener('click', function(e){
        e.preventDefault();
        var id  = el.dataset.id;
        var url = el.getAttribute('href');
        postJson('{{ url("/notifikasi") }}/' + id + '/baca').finally(function(){
          window.location.href = url;
        });
      });
    });
  }

  function loadNotif() {
    fetch('{{ route("notifikasi.data") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function(r){ return r.json(); })
      .then(function(res){
        updateBadge(res.total || 0);
        renderList(res.items);
      })
      .catch(function(){ list.innerHTML = '<div class="notif-empty">Gagal memuat notifikasi</div>'; });
  }

  // Tombol "Tandai semua dibaca"
  document.getElementById('notifBacaSemua')?.addEventListener('click', function(){
    postJson('{{ route("notifikasi.baca-semua") }}').then(function(){
      updateBadge(0);
      list.innerHTML = '<div class="notif-empty">Semua notifikasi sudah dibaca</div>';
    });
  });

  btn.addEventListener('click', function(e){
    e.stopPropagation();
    // Tutup user panel jika terbuka
    document.getElementById('userPanel')?.classList.remove('show');
    panel.classList.toggle('show');
    if (panel.classList.contains('show')) loadNotif();
  });

  document.addEventListener('click', function(e){
    if (!panel.contains(e.target) && e.target !== btn) {
      panel.classList.remove('show');
    }
  });

  // Muat badge saat halaman terbuka
  loadNotif();
})();

// ── User avatar dropdown ──
(function(){
  var userBtn   = document.getElementById('userBtn');
  var userPanel = document.getElementById('userPanel');
  if (!userBtn || !userPanel) return;

  userBtn.addEventListener('click', function(e){
    e.stopPropagation();
    // Tutup notif panel jika terbuka
    document.getElementById('notifPanel')?.classList.remove('show');
    userPanel.classList.toggle('show');
  });

  document.addEventListener('click', function(e){
    if (!userPanel.contains(e.target) && e.target !== userBtn) {
      userPanel.classList.remove('show');
    }
  });
})();

function userPanelClose(){
  document.getElementById('userPanel')?.classList.remove('show');
}
</script>
</body>
</html>