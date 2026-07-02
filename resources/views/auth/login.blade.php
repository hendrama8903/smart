<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — SMART Perum Permata Regency</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --hutan:#14532D;--hutan2:#0f3a25;--hutan3:#0b2c1c;
  --daun:#2D6A4F;--daun-pucat:#E3EDE6;
  --kertas:#FAF8F2;--kertas2:#F2EFE6;
  --tinta:#17231C;--redup:#6A7A70;--garis:#E2E0D6;--garis2:#D2D6CE;
  --emas:#C29A2E;--emas-soft:#F6EFD9;--stempel:#B5402C;
  --surface:#fff;
}
*{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}
body{
  font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:var(--tinta);
  background:var(--kertas);min-height:100vh;-webkit-font-smoothing:antialiased;
  display:flex;align-items:center;justify-content:center;padding:24px;
}
a{color:inherit;text-decoration:none}
button{font-family:inherit;cursor:pointer;border:none;background:none}

/* ── Outer card ─────────────────────────────────── */
.card{
  width:100%;max-width:1040px;background:var(--surface);
  border:1px solid var(--garis);border-radius:24px;overflow:hidden;
  display:grid;grid-template-columns:1.1fr 1fr;
  box-shadow:0 30px 70px -30px rgba(16,40,28,.4),0 2px 6px rgba(16,40,28,.05);
}

/* ── Brand panel (kiri) ─────────────────────────── */
.brand{
  position:relative;overflow:hidden;
  background:radial-gradient(130% 130% at 75% 0%,#1d6b48 0%,var(--hutan) 40%,var(--hutan2) 70%,var(--hutan3) 100%);
  padding:40px 44px;display:flex;flex-direction:column;min-height:600px;
}

/* Dekorasi guilloche */
.guilloche{position:absolute;inset:0;opacity:.45;pointer-events:none}
.brand-noise{
  position:absolute;inset:0;pointer-events:none;opacity:.04;
  background-image:radial-gradient(rgba(255,255,255,.9) .5px,transparent .6px);background-size:14px 14px;
}

/* Kop atas */
.brand-kop{
  position:relative;display:flex;align-items:center;gap:8px;
  font-size:10.5px;letter-spacing:.18em;text-transform:uppercase;
  color:#9FD0B5;font-weight:700;
}
.brand-kop::before{content:"";width:22px;height:2px;background:var(--emas)}

/* Tengah — logo area */
.brand-mid{
  position:relative;flex:1;display:flex;flex-direction:column;
  align-items:center;justify-content:center;gap:20px;padding:32px 0;
}

/* Logo RT (bulat) */
.logo-rt{
  width:155px;height:155px;border-radius:50%;overflow:hidden;
  background:rgba(255,255,255,0.92);
  border:3px solid rgba(194,154,46,.6);
  box-shadow:0 0 0 8px rgba(194,154,46,.12), 0 16px 40px -16px rgba(0,0,0,.6);
  padding:6px;
}
.logo-rt img{width:100%;height:100%;object-fit:contain;border-radius:50%}


/* Tagline */
.brand-tagline{
  font-size:13.5px;color:#BBD7C7;font-weight:500;text-align:center;
  line-height:1.55;max-width:280px;
}

/* Fitur list */
.feat{display:flex;flex-direction:column;gap:9px;margin-top:4px}
.feat div{display:flex;align-items:center;gap:10px;font-size:13px;color:#CFE4D8;font-weight:500}
.feat svg{width:16px;height:16px;color:var(--emas);flex:0 0 16px}

/* Footer brand */
.brand-foot{
  position:relative;display:flex;align-items:center;justify-content:space-between;
  font-size:11px;color:#7FA88F;border-top:1px solid rgba(255,255,255,.1);padding-top:14px;
}
.brand-foot .mono{font-family:'IBM Plex Mono',monospace;letter-spacing:.06em}

/* ── Form panel (kanan) ─────────────────────────── */
.form{
  padding:48px 46px;display:flex;flex-direction:column;justify-content:center;
}
.form-head{margin-bottom:26px}
.form-head .eyebrow{
  display:inline-flex;align-items:center;gap:7px;
  font-size:10.5px;letter-spacing:.16em;text-transform:uppercase;
  color:var(--daun);font-weight:700;margin-bottom:10px;
}
.form-head .eyebrow::before{content:"";width:18px;height:2px;background:var(--emas)}
.form-head h2{font-size:26px;font-weight:800;letter-spacing:-.02em}
.form-head p{color:var(--redup);font-size:13.5px;margin-top:6px}

/* Mobile header — logo + nama app */
.mobile-header{ display:none }
.mh-logo{
  width:60px;height:60px;border-radius:50%;overflow:hidden;flex:0 0 60px;
  border:2.5px solid rgba(45,106,79,.25);background:var(--surface);
  box-shadow:0 2px 10px rgba(16,40,28,.1);
}
.mh-logo img{width:100%;height:100%;object-fit:contain}
.mh-logo-fallback{
  width:60px;height:60px;border-radius:50%;background:var(--daun);
  display:none;align-items:center;justify-content:center;flex:0 0 60px;
}
.mh-logo-fallback span{font-size:15px;font-weight:800;color:#F6EFD9;font-family:'IBM Plex Mono',monospace}
.mh-info b{display:block;font-size:20px;font-weight:800;color:var(--hutan);letter-spacing:-.01em;line-height:1.1}
.mh-info .mh-lokasi{display:block;font-size:11px;color:var(--redup);margin-top:4px;font-weight:500}
.mh-tagline{display:none}

.field{margin-bottom:16px}
.field label{display:block;font-size:12.5px;font-weight:700;color:var(--tinta);margin-bottom:7px}
.inp{display:flex;align-items:center;gap:10px;background:var(--surface);border:1.5px solid var(--garis2);border-radius:12px;padding:0 13px;transition:.15s}
.inp:focus-within{border-color:var(--daun);box-shadow:0 0 0 4px rgba(45,106,79,.1)}
.inp svg.lead{width:17px;height:17px;color:#9AA89F;flex:0 0 17px}
.inp input{flex:1;border:none;outline:none;background:none;font-family:inherit;font-size:14.5px;padding:13px 0;color:var(--tinta)}
.inp input::placeholder{color:#A7B3AA}
.inp .eye{padding:6px;color:#9AA89F;display:flex;border-radius:8px}
.inp .eye:hover{color:var(--daun);background:var(--kertas2)}

.row{display:flex;align-items:center;justify-content:space-between;margin:4px 0 22px}
.check{display:flex;align-items:center;gap:9px;font-size:13px;color:var(--redup);font-weight:600;cursor:pointer;user-select:none}
.check input{display:none}
.box{width:19px;height:19px;border-radius:6px;border:1.6px solid var(--garis2);display:flex;align-items:center;justify-content:center;transition:.15s}
.check input:checked + .box{background:var(--daun);border-color:var(--daun)}
.box svg{width:12px;height:12px;color:#fff;opacity:0;transition:.15s}
.check input:checked + .box svg{opacity:1}
.forgot{font-size:13px;color:var(--daun);font-weight:700}
.forgot:hover{text-decoration:underline}

.submit{
  width:100%;background:linear-gradient(180deg,#206543,#16472f);color:#fff;font-weight:700;font-size:15px;
  padding:14px;border-radius:12px;display:flex;align-items:center;justify-content:center;gap:10px;transition:.18s;
  box-shadow:0 8px 20px -8px rgba(20,83,45,.6);
}
.submit:hover{filter:brightness(1.08)}
.submit:active{transform:translateY(1px)}
.submit svg{width:18px;height:18px}
.spinner{width:18px;height:18px;border:2.5px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite;display:none}
@keyframes spin{to{transform:rotate(360deg)}}
.submit.loading .lbl,.submit.loading .arr{display:none}
.submit.loading .spinner{display:block}

.divider{display:flex;align-items:center;gap:14px;margin:22px 0 16px;color:#A7B3AA;font-size:12px;font-weight:600}
.divider::before,.divider::after{content:"";flex:1;height:1px;background:var(--garis)}
.help{text-align:center;font-size:13px;color:var(--redup)}
.help a{color:var(--daun);font-weight:700}
.formfoot{margin-top:24px;text-align:center;font-size:11px;color:#A7B3AA}

/* (mobile-logo digantikan .mobile-header) */
.alert-err{background:#FBEAE6;border:1px solid #E9C3BA;color:#9A3422;font-size:13px;font-weight:600;padding:11px 14px;border-radius:11px;margin-bottom:18px;display:flex;align-items:flex-start;gap:9px}
.alert-err svg{flex:0 0 16px;margin-top:1px}

/* ── Mobile & Tablet (≤900px) ───────────────────── */
@media(max-width:900px){
  body{ padding:0; align-items:flex-start; background:#fff }
  .card{
    grid-template-columns:1fr; max-width:100%;
    border-radius:0; border:none; box-shadow:none; min-height:100vh;
  }
  .brand{ display:none }
  .form{
    padding:52px 28px 48px;
    display:flex; flex-direction:column; justify-content:flex-start;
    min-height:100vh;
  }
  .mobile-header{
    display:flex; align-items:center; gap:14px; margin-bottom:10px;
    padding:14px 16px; background:var(--kertas); border:1px solid var(--garis);
    border-radius:14px;
  }
  .mh-tagline{
    display:block; font-size:14px; color:var(--daun); font-weight:700;
    text-align:center; letter-spacing:.01em; margin-bottom:28px;
  }
}

/* ── Mobile kecil (≤480px) ──────────────────────── */
@media(max-width:480px){
  .form{ padding:40px 20px 44px }
  .mh-tagline{ margin-bottom:24px }
  .row{ flex-direction:column; align-items:flex-start; gap:12px; margin-bottom:18px }
}
</style>
</head>
<body>
<div class="card">

  <!-- ══════════ KIRI: BRAND ══════════ -->
  <div class="brand">
    <svg class="guilloche" viewBox="0 0 500 650" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
      <g fill="none" stroke="#C29A2E" stroke-width="1" opacity="0.45">
        <circle cx="450" cy="60"  r="50"/><circle cx="450" cy="60"  r="80"/><circle cx="450" cy="60"  r="110"/><circle cx="450" cy="60"  r="140"/>
      </g>
      <g fill="none" stroke="#8FD3B0" stroke-width="1" opacity="0.3">
        <circle cx="30"  cy="600" r="60"/><circle cx="30"  cy="600" r="90"/><circle cx="30"  cy="600" r="120"/>
      </g>
      <g fill="none" stroke="#C29A2E" stroke-width="1" opacity="0.25">
        <path d="M0 320 Q125 270 250 320 T500 320"/>
        <path d="M0 340 Q125 290 250 340 T500 340"/>
        <path d="M0 360 Q125 310 250 360 T500 360"/>
      </g>
    </svg>
    <div class="brand-noise"></div>

    <div class="brand-kop">Sistem Manajemen Administrasi RT</div>

    <div class="brand-mid">
      <!-- Logo RT (lingkaran) -->
      <div class="logo-rt">
        <img src="{{ asset('images/logo-rt.png') }}" alt="Logo RT 001"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
        <div style="display:none;width:100%;height:100%;background:radial-gradient(circle,#237a52,#0f3a25);align-items:center;justify-content:center;flex-direction:column">
          <span style="font-size:28px;font-weight:800;color:#F6EFD9;font-family:'IBM Plex Mono',monospace">001</span>
          <span style="font-size:9px;color:var(--emas);letter-spacing:.18em;font-weight:700">RT•RW015</span>
        </div>
      </div>

      <div style="display:flex;flex-direction:column;gap:14px;align-items:flex-start">

        <p class="brand-tagline">Administrasi warga, iuran, kas, fasilitas, dan keamanan lingkungan dalam satu platform.</p>

        <div class="feat">
          <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Data warga &amp; kartu keluarga terpusat</div>
          <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Kas RT transparan &amp; mudah dipantau</div>
          <div><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Iuran, sewa pendopo &amp; ronda online</div>
        </div>
      </div>
    </div>

    <div class="brand-foot">
      <span>Perum Permata Regency</span>
      <span class="mono">SMART v1.0</span>
    </div>
  </div>

  <!-- ══════════ KANAN: FORM ══════════ -->
  <div class="form">

    <!-- Header mobile — tampil di semua ukuran mobile -->
    <div class="mobile-header">
      <div class="mh-logo">
        <img src="{{ asset('images/logo-rt.png') }}" alt="Logo RT 001"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
        <div class="mh-logo-fallback">
          <span>001</span>
        </div>
      </div>
      <div class="mh-info">
        <b>SMART</b>
        <span class="mh-lokasi">RT 001/015 Perum Permata Regency</span>
      </div>
    </div>
    <p class="mh-tagline">Sistem Manajemen Administrasi Rukun Tetangga</p>

    <form method="POST" action="{{ route('login') }}">
      @csrf

      @if ($errors->any())
        <div class="alert-err">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          {{ $errors->first() }}
        </div>
      @endif

      <div class="field">
        <label>Username</label>
        <div class="inp">
          <svg class="lead" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M5.5 21a6.5 6.5 0 0 1 13 0"/></svg>
          <input id="user" name="username" type="text" value="{{ old('username') }}" placeholder="cth. admin" autofocus autocomplete="username">
        </div>
      </div>

      <div class="field">
        <label>Kata Sandi</label>
        <div class="inp">
          <svg class="lead" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
          <input id="pass" name="password" type="password" placeholder="Masukkan kata sandi" autocomplete="current-password">
          <button class="eye" onclick="togglePass()" title="Tampilkan / sembunyikan" type="button">
            <svg id="eyeicon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>

      <div class="row">
        <label class="check">
          <input type="checkbox" name="remember" value="1" checked>
          <span class="box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></span>
          Ingat saya
        </label>
        <a class="forgot" href="#" onclick="return false">Lupa sandi?</a>
      </div>

      <button class="submit" type="submit" id="btnSubmit">
        <span class="lbl">Masuk ke SMART</span>
        <svg class="arr" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
        <div class="spinner"></div>
      </button>
    </form>

    <div class="divider">atau</div>
    <p class="help">Belum punya akses? <a href="#" onclick="return false">Hubungi pengurus RT</a></p>
  </div>
</div>

<script>
function togglePass(){
  const p  = document.getElementById('pass');
  const ic = document.getElementById('eyeicon');
  if(p.type==='password'){
    p.type='text';
    ic.innerHTML='<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/><line x1="3" y1="3" x2="21" y2="21" stroke-width="2"/>';
  }else{
    p.type='password';
    ic.innerHTML='<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>';
  }
}

// Loading state saat submit
document.querySelector('form').addEventListener('submit', function(){
  document.getElementById('btnSubmit').classList.add('loading');
});
</script>
</body>
</html>
