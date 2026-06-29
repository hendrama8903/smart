<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Master Fasilitas ──────────────────────────────────────────
        Schema::create('fasilitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('deskripsi')->nullable();
            $table->enum('satuan', ['sesi', 'hari', 'unit', 'jam'])->default('sesi');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // ── 2. Tarif Fasilitas ───────────────────────────────────────────
        Schema::create('tarif_fasilitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fasilitas_id')->constrained('fasilitas')->cascadeOnDelete();
            $table->string('nama_tarif');                              // "Simple (< 4 jam)", "Full Day", dll.
            $table->enum('kategori', ['warga', 'luar_warga']);
            $table->decimal('nominal_total', 12, 2);                  // total dibayar penyewa
            $table->decimal('nominal_kas_rt', 12, 2)->default(0);     // bagian masuk Kas RT
            $table->decimal('nominal_lain', 12, 2)->default(0);       // biaya lain (ongkos BP, dll.)
            $table->string('keterangan_lain')->nullable();             // "Ongkos bongkar pasang"
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // ── 3. Update pendopo_booking ────────────────────────────────────
        Schema::table('pendopo_booking', function (Blueprint $table) {
            if (! Schema::hasColumn('pendopo_booking', 'tarif_fasilitas_id')) {
                $table->foreignId('tarif_fasilitas_id')->nullable()
                    ->constrained('tarif_fasilitas')->nullOnDelete()->after('kartu_keluarga_id');
            }
            if (! Schema::hasColumn('pendopo_booking', 'fasilitas_id')) {
                $table->foreignId('fasilitas_id')->nullable()
                    ->constrained('fasilitas')->nullOnDelete()->after('tarif_fasilitas_id');
            }
            if (! Schema::hasColumn('pendopo_booking', 'jumlah_unit')) {
                $table->decimal('jumlah_unit', 8, 2)->default(1)->after('is_warga');
            }
            if (! Schema::hasColumn('pendopo_booking', 'total_bayar')) {
                $table->decimal('total_bayar', 12, 2)->default(0)->after('jumlah_unit');
            }
            if (! Schema::hasColumn('pendopo_booking', 'total_kas_rt')) {
                $table->decimal('total_kas_rt', 12, 2)->default(0)->after('total_bayar');
            }
            if (! Schema::hasColumn('pendopo_booking', 'total_biaya_lain')) {
                $table->decimal('total_biaya_lain', 12, 2)->default(0)->after('total_kas_rt');
            }
            if (! Schema::hasColumn('pendopo_booking', 'status_bayar')) {
                $table->enum('status_bayar', ['belum', 'dp', 'lunas'])->default('belum')
                    ->after('status');
            }
            if (! Schema::hasColumn('pendopo_booking', 'tgl_bayar')) {
                $table->date('tgl_bayar')->nullable()->after('status_bayar');
            }
            if (! Schema::hasColumn('pendopo_booking', 'bukti_bayar')) {
                $table->string('bukti_bayar')->nullable()->after('tgl_bayar');
            }
        });

        // ── 4. Seed data fasilitas & tarif ──────────────────────────────
        $pendopo = DB::table('fasilitas')->insertGetId([
            'nama' => 'Pendopo', 'deskripsi' => 'Fasilitas pendopo RT', 'satuan' => 'sesi',
            'aktif' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $tenda = DB::table('fasilitas')->insertGetId([
            'nama' => 'Tenda RT', 'deskripsi' => 'Tenda dan kelengkapannya', 'satuan' => 'sesi',
            'aktif' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $kursi = DB::table('fasilitas')->insertGetId([
            'nama' => 'Kursi Plastik', 'deskripsi' => 'Kursi plastik RT', 'satuan' => 'unit',
            'aktif' => 1, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $tarifs = [
            // Pendopo
            ['fasilitas_id'=>$pendopo,'nama_tarif'=>'Simple (< 4 jam)','kategori'=>'warga',      'nominal_total'=>150000,'nominal_kas_rt'=>150000,'nominal_lain'=>0,'keterangan_lain'=>null],
            ['fasilitas_id'=>$pendopo,'nama_tarif'=>'Simple (< 4 jam)','kategori'=>'luar_warga',  'nominal_total'=>250000,'nominal_kas_rt'=>250000,'nominal_lain'=>0,'keterangan_lain'=>null],
            ['fasilitas_id'=>$pendopo,'nama_tarif'=>'Full Day (sehari penuh)','kategori'=>'warga', 'nominal_total'=>300000,'nominal_kas_rt'=>300000,'nominal_lain'=>0,'keterangan_lain'=>null],
            ['fasilitas_id'=>$pendopo,'nama_tarif'=>'Full Day (sehari penuh)','kategori'=>'luar_warga','nominal_total'=>600000,'nominal_kas_rt'=>600000,'nominal_lain'=>0,'keterangan_lain'=>null],
            // Tenda
            ['fasilitas_id'=>$tenda,'nama_tarif'=>'Bongkar Pasang','kategori'=>'warga',       'nominal_total'=>150000,'nominal_kas_rt'=>50000, 'nominal_lain'=>100000,'keterangan_lain'=>'Ongkos bongkar pasang'],
            ['fasilitas_id'=>$tenda,'nama_tarif'=>'Bongkar Pasang','kategori'=>'luar_warga',   'nominal_total'=>300000,'nominal_kas_rt'=>150000,'nominal_lain'=>150000,'keterangan_lain'=>'Ongkos bongkar pasang'],
            // Kursi
            ['fasilitas_id'=>$kursi,'nama_tarif'=>'Per Kursi','kategori'=>'warga',        'nominal_total'=>1000,'nominal_kas_rt'=>1000,'nominal_lain'=>0,'keterangan_lain'=>null],
            ['fasilitas_id'=>$kursi,'nama_tarif'=>'Per Kursi','kategori'=>'luar_warga',    'nominal_total'=>2000,'nominal_kas_rt'=>2000,'nominal_lain'=>0,'keterangan_lain'=>null],
        ];

        foreach ($tarifs as $t) {
            DB::table('tarif_fasilitas')->insert(array_merge($t, ['aktif'=>1,'created_at'=>now(),'updated_at'=>now()]));
        }
    }

    public function down(): void
    {
        Schema::table('pendopo_booking', function (Blueprint $table) {
            $table->dropColumn(['tarif_fasilitas_id','fasilitas_id','jumlah_unit','total_bayar','total_kas_rt','total_biaya_lain','status_bayar','tgl_bayar','bukti_bayar']);
        });
        Schema::dropIfExists('tarif_fasilitas');
        Schema::dropIfExists('fasilitas');
    }
};
