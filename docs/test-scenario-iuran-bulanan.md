# Skenario Test Manual — Iuran Bulanan

Dokumen ini untuk pengujian manual (di browser) halaman **Keuangan > Iuran Bulanan**
(`/iuran`, `IuranController`, view `resources/views/keuangan/iuran.blade.php`).

Fokus: fungsi **Bayar**, **Riwayat**, **Keringanan**, **Buka/Tutup Periode**, dan
perubahan tombol Riwayat (teks → ikon) yang baru diterapkan.

## Cara pakai dokumen ini
- Kolom **Hasil Aktual** & **Status** diisi manual saat menjalankan test.
- Status: ✅ Lulus / ❌ Gagal / ⏭️ Dilewati (tulis alasan bila dilewati).
- Login dengan role sesuai kolom **Role** sebelum menjalankan setiap skenario.

---

## 1. Buka Periode

| No | Role | Langkah | Hasil yang Diharapkan |
|----|------|---------|------------------------|
| 1.1 | admin/ketua/bendahara/sekretaris | Klik **Buka Periode** | Modal "Buka Periode Iuran" muncul, field Jenis Iuran/Tahun/Bulan kosong/default (tahun & bulan = bulan berjalan) |
| 1.2 | (lanjutan 1.1) | Pilih jenis iuran dengan periode **tahunan** | Field "Bulan" otomatis disembunyikan |
| 1.3 | (lanjutan 1.1) | Pilih jenis iuran periode **bulanan**, kosongkan Tahun, klik Buka Periode | Muncul notifikasi error "Pilih jenis iuran dan tahun" |
| 1.4 | (lanjutan 1.1) | Isi jenis iuran bulanan + tahun, kosongkan Bulan, klik Buka Periode | Muncul notifikasi error "Pilih bulan" |
| 1.5 | (lanjutan 1.1) | Isi lengkap (jenis, tahun, bulan) yang **belum pernah dibuka**, klik Buka Periode | Notifikasi sukses "Periode ... dibuka. N tagihan berhasil dibuat.", modal tertutup, filter periode otomatis pindah ke periode baru, grid & ringkasan ter-refresh |
| 1.6 | (lanjutan 1.1) | Ulangi 1.5 dengan kombinasi jenis+tahun+bulan **yang sama** | Muncul error "Periode ini sudah pernah dibuka." (HTTP 422), tidak ada tagihan duplikat |
| 1.7 | warga/koordinator (bukan pengurus) | Buka halaman Iuran Bulanan | Tombol **Buka Periode** & **Import Tunggakan** tersembunyi (`isAdmin=false`) |

## 2. Filter Periode, Gang & Ringkasan (Summary Cards)

| No | Role | Langkah | Hasil yang Diharapkan |
|----|------|---------|------------------------|
| 2.1 | siapa saja | Buka halaman, belum ada periode untuk jenis iuran terpilih | Banner "Belum ada periode dibuka..." tampil, ringkasan menampilkan Rp 0 / 0 KK di semua card |
| 2.2 | siapa saja | Pilih jenis iuran lain di filter | Daftar periode ikut berubah sesuai jenis iuran; periode **status Buka** dipilih otomatis (atau periode terbaru bila semua tutup) |
| 2.3 | siapa saja | Pilih periode tertentu di filter | Badge status periode di sebelah judul menampilkan "Buka" (hijau) atau "Tutup" (abu-abu) sesuai data; grid & ringkasan ter-refresh sesuai periode terpilih |
| 2.4 | siapa saja | Pilih Gang tertentu di filter | Grid hanya menampilkan KK pada gang tersebut |
| 2.5 | siapa saja | Bandingkan angka di 4 summary card (Total Tagihan, Sudah Bayar, Sebagian Bayar, Belum Bayar) dengan data di grid | Total KK & nominal harus konsisten dengan jumlah baris berstatus lunas/sebagian/belum di grid |

## 3. Bayar (Input Pembayaran — FIFO)

> Regression check: sebelumnya field pembayaran tidak ter-reset antar sesi (nilai lama masih tampil seolah form belum baru). Sudah diperbaiki di `openBayar()`.

| No | Role | Langkah | Hasil yang Diharapkan |
|----|------|---------|------------------------|
| 3.1 | admin/ketua/bendahara/sekretaris/koordinator (KK miliknya) | Klik **Bayar** pada baris status "Belum"/"Sebagian" | Modal "Input Pembayaran" **muncul** (bug lama: modal tidak muncul sama sekali — pastikan ini sudah tidak terjadi lagi) |
| 3.2 | (lanjutan 3.1) | Amati field saat modal baru terbuka | Jumlah Bayar otomatis terisi = sisa tagihan periode ini; Tanggal Bayar = hari ini; Metode & Keterangan **kosong** (bukan sisa isian sebelumnya) |
| 3.3 | Isi & simpan pembayaran sebagian (kurang dari sisa tagihan) | — | Notifikasi sukses, modal tertutup, status baris berubah jadi "Sebagian", kolom Dibayar & Sisa ter-update |
| 3.4 | Klik **Bayar** lagi pada KK yang **sama** (masih Sebagian) | — | Modal terbuka dengan Jumlah Bayar = **sisa terbaru** (bukan nilai/sisa dari transaksi sebelumnya), Metode & Keterangan kosong lagi |
| 3.5 | Klik **Bayar** pada KK **lain** setelah menutup modal KK sebelumnya | — | Field nominal/metode/keterangan tidak membawa sisa data dari KK sebelumnya |
| 3.6 | Simpan pembayaran hingga lunas (nominal = sisa penuh) | — | Status baris jadi "Lunas", tombol **Bayar** hilang dari baris tersebut (hanya Riwayat & Keringanan yang tampil) |
| 3.7 | Kosongkan Jumlah Bayar, klik Simpan | — | Notifikasi error "Jumlah bayar harus lebih dari 0", tidak terkirim ke server |
| 3.8 | Isi Jumlah Bayar dengan 0 atau negatif (jika bisa diketik) | — | Ditolak dengan pesan error yang sama seperti 3.7 |
| 3.9 | Bayar untuk KK yang punya tunggakan periode-periode sebelumnya (jenis iuran sama), nominal melebihi tagihan periode aktif | — | Sesuai logika FIFO backend: kelebihan bayar otomatis mengalokasikan ke tagihan tertua yang belum lunas berikutnya (cek lewat Riwayat di baris 4.x, bagian alokasi per bulan) |
| 3.10 | Simpan pembayaran, lalu cek menu **Kas RT** untuk periode/tanggal yang sama | — | Ada baris kas masuk baru dengan kategori "Iuran", nominal sama dengan jumlah bayar, keterangan berisi nama jenis iuran + kepala keluarga |
| 3.11 | (Regresi tombol) Klik ikon **Riwayat** di footer modal Bayar (bukan di grid) | — | Modal Bayar tertutup, modal Riwayat terbuka menampilkan riwayat KK yang sedang diinput |

## 4. Riwayat Pembayaran

> Perubahan UI: tombol "Riwayat" di kolom Aksi grid sekarang **ikon jam** (bukan teks), dengan tooltip "Riwayat" saat hover. Tombol "Bayar" tetap teks.

| No | Role | Langkah | Hasil yang Diharapkan |
|----|------|---------|------------------------|
| 4.1 | siapa saja yang boleh akses halaman | Arahkan kursor ke ikon Riwayat di kolom Aksi | Muncul tooltip "Riwayat"; ikon tampil konsisten (ukuran/warna) dengan ikon Keringanan di sebelahnya |
| 4.2 | siapa saja | Klik ikon **Riwayat** pada KK yang **belum pernah bayar** | Modal Riwayat **muncul** (bug lama: modal tidak muncul), menampilkan pesan "Belum ada riwayat pembayaran." |
| 4.3 | siapa saja | Klik ikon **Riwayat** pada KK yang sudah pernah bayar 1x atau lebih | Modal muncul berisi daftar transaksi terurut dari **tanggal terbaru**, masing-masing menampilkan tanggal, total bayar, metode, nama petugas, keterangan (jika ada) |
| 4.4 | (lanjutan 4.3) | Perhatikan rincian alokasi di tiap item riwayat | Menampilkan baris "→ Bulan MM/YYYY : Rp ..." sesuai hasil alokasi FIFO — total alokasi per transaksi harus sama dengan jumlah total transaksi tersebut |
| 4.5 | (lanjutan 4.3) | Klik **Bayar Lagi** di footer modal Riwayat | Modal Riwayat tertutup, modal Bayar terbuka (form kosong sesuai skenario 3.2) |
| 4.6 | siapa saja | Klik **Tutup** di modal Riwayat, atau klik area gelap di luar modal, atau tekan `Esc` | Modal tertutup dengan benar |

## 5. Keringanan / Catatan Khusus

> Regresi: tombol ini punya bug identik (elemen subjudul hilang) — pastikan modal tetap terbuka normal.

| No | Role | Langkah | Hasil yang Diharapkan |
|----|------|---------|------------------------|
| 5.1 | admin/ketua/bendahara/sekretaris | Klik ikon Keringanan (bentuk hati) pada sebuah baris | Modal "Keringanan / Catatan Khusus" **muncul** |
| 5.2 | (lanjutan 5.1) | Aktifkan switch "Berikan Keringanan", isi catatan, Simpan | Notifikasi sukses, ikon Keringanan di grid berubah warna (menyala/emas) menandakan aktif |
| 5.3 | Buka lagi modal Keringanan pada baris yang sama | — | Switch & catatan menampilkan nilai tersimpan sebelumnya (bukan default kosong) |
| 5.4 | Nonaktifkan switch, Simpan | — | Ikon kembali ke warna non-aktif |

## 6. Tutup Buku

| No | Role | Langkah | Hasil yang Diharapkan |
|----|------|---------|------------------------|
| 6.1 | admin/ketua/bendahara/sekretaris, periode berstatus **Buka** | Klik **Tutup Buku** | Modal "Tutup Buku Periode" muncul dengan field Catatan Penutupan kosong |
| 6.2 | (lanjutan 6.1) | Isi catatan (opsional), klik "Ya, Tutup Buku" | Notifikasi sukses, badge status periode berubah jadi "Tutup", tombol Tutup Buku hilang untuk periode ini |
| 6.3 | (lanjutan 6.2) | Cek baris KK yang masih Belum/Sebagian pada periode yang baru ditutup | Baris tersebut mendapat label **"Tunggakan"** di sebelah nama kepala keluarga |
| 6.4 | Coba tutup buku periode yang **sudah tutup** (mis. via periode lama) | — | Tombol Tutup Buku tidak tampil untuk periode berstatus Tutup (sesuai `updatePeriodeBar()`); jika dipaksa lewat API, backend menolak dengan "Periode ini sudah ditutup." |
| 6.5 | Setelah tutup buku, coba **Bayar** tagihan yang jadi tunggakan | — | Tetap bisa dibayar (sesuai catatan di modal: "Pembayaran tunggakan masih bisa dicatat setelah periode ditutup") |

## 7. Import Tunggakan Historis

| No | Role | Langkah | Hasil yang Diharapkan |
|----|------|---------|------------------------|
| 7.1 | admin/ketua/bendahara/sekretaris | Klik **Import Tunggakan** | Modal muncul dengan link "Unduh Template" dan area upload |
| 7.2 | (lanjutan 7.1) | Upload file `.xlsx` sesuai template dengan No. KK valid | Notifikasi hasil sukses + jumlah baris berhasil, grid ter-refresh menampilkan data baru bila periode terkait sedang difilter |
| 7.3 | Upload file dengan No. KK yang **tidak ditemukan** | — | Baris tersebut dilewati, muncul daftar pesan error per baris (mis. "Baris X: No. KK '...' tidak ditemukan.") |
| 7.4 | Upload file dengan format periode salah (bukan `YYYY-MM`) | — | Baris dilewati dengan pesan error "Format periode harus YYYY-MM." |
| 7.5 | Upload file selain `.xlsx` atau berukuran > 5MB | — | Ditolak oleh validasi upload (`allowedFileExtensions`/`maxFileSize`) sebelum sampai ke server |

## 8. Unduh (Export Excel)

| No | Role | Langkah | Hasil yang Diharapkan |
|----|------|---------|------------------------|
| 8.1 | yang punya izin export | Klik **Unduh** tanpa memilih periode | Notifikasi warning "Pilih periode terlebih dahulu" |
| 8.2 | (lanjutan) | Pilih periode, klik **Unduh** | File `.xlsx` terunduh dengan nama sesuai label periode, kolom sesuai grid (Blok/No., Kepala Keluarga, Gang, Tagihan, Dibayar, Sisa, Status) dan jumlah baris sama dengan grid (memperhitungkan filter Gang aktif) |
| 8.3 | role tanpa izin export | Buka halaman | Tombol **Unduh** tampak nonaktif/disabled |

## 9. Otorisasi & Batasan Akses

| No | Role | Langkah | Hasil yang Diharapkan |
|----|------|---------|------------------------|
| 9.1 | koordinator gang | Buka Iuran Bulanan | Badge "Gang: <nama gang>" tampil di toolbar; grid hanya menampilkan KK anggota gang yang dikoordinasikannya |
| 9.2 | koordinator gang | Coba **Bayar**/lihat **Riwayat** untuk KK di luar gang binaannya (mis. lewat manipulasi request langsung) | Backend menolak dengan pesan "Anda tidak memiliki akses ke KK ini." (403) / riwayat kosong (403) |
| 9.3 | warga biasa (bukan koordinator) | Buka Iuran Bulanan | Hanya melihat data KK miliknya sendiri |
| 9.4 | admin/ketua/bendahara/sekretaris | Buka Iuran Bulanan | Melihat seluruh data tanpa batasan gang/KK |

## 10. Ringkasan Regresi Bug (checklist cepat)

Gunakan checklist ini setelah deploy untuk verifikasi cepat bahwa perbaikan sebelumnya tidak regresi:

- [ ] Klik **Bayar** → modal langsung muncul (tidak diam saja / tidak ada error di console browser).
- [ ] Klik **Riwayat** (ikon jam) → modal langsung muncul.
- [ ] Klik **Keringanan** (ikon hati) → modal langsung muncul.
- [ ] Klik **Tutup Buku** → modal langsung muncul.
- [ ] Buka modal Bayar dua kali berturut-turut (KK berbeda) → nominal, metode, keterangan tidak membawa nilai dari sesi sebelumnya.
- [ ] Buka DevTools Console saat mengklik semua tombol di atas → tidak ada `TypeError: Cannot set properties of null`.
