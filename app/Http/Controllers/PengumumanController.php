<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
    // ─── Halaman pengurus (CRUD) ─────────────────────────────────────────
    public function index()
    {
        return view('pengumuman.index');
    }

    public function list(Request $request)
    {
        return Pengumuman::with('pembuat')
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori', $request->kategori))
            ->orderBy('penting', 'desc')
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn ($p) => [
                'id'            => $p->id,
                'judul'         => $p->judul,
                'kategori'      => $p->kategori,
                'kategori_label'=> $p->kategori_label,
                'isi'           => $p->isi,
                'tanggal'       => $p->tanggal?->format('d/m/Y'),
                'tanggal_raw'   => $p->tanggal?->format('Y-m-d'),
                'file_url'      => $p->file_url,
                'nama_file'     => $p->nama_file,
                'penting'       => $p->penting,
                'aktif'         => $p->aktif,
                'pembuat'       => optional($p->pembuat)->name,
            ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'judul'    => ['required', 'string', 'max:200'],
            'kategori' => ['required', 'in:informasi,rapat,kegiatan,keuangan,darurat,lainnya'],
            'isi'      => ['nullable', 'string'],
            'tanggal'  => ['required', 'date'],
            'penting'  => ['boolean'],
            'aktif'    => ['boolean'],
        ]);

        $data['penting']    = $request->boolean('penting');
        $data['aktif']      = $request->boolean('aktif', true);
        $data['dibuat_oleh'] = Auth::id();

        // Upload file lampiran
        if ($request->hasFile('file_lampiran')) {
            // Hapus file lama jika edit
            if ($request->filled('id')) {
                $old = Pengumuman::find($request->id);
                if ($old && $old->file_lampiran) {
                    Storage::disk('public')->delete($old->file_lampiran);
                }
            }
            $file               = $request->file('file_lampiran');
            $data['file_lampiran'] = $file->store('pengumuman', 'public');
            $data['nama_file']  = $file->getClientOriginalName();
        }

        if ($request->filled('id')) {
            $p = Pengumuman::findOrFail($request->id);
            // Jangan ubah pembuat saat edit
            unset($data['dibuat_oleh']);
            // Jika tidak upload file baru, pertahankan file lama
            if (! $request->hasFile('file_lampiran')) {
                unset($data['file_lampiran'], $data['nama_file']);
            }
            $p->update($data);
            $msg = 'Pengumuman berhasil diperbarui.';
        } else {
            Pengumuman::create($data);
            $msg = 'Pengumuman berhasil diterbitkan.';
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    public function hapusFile(Request $request)
    {
        $p = Pengumuman::findOrFail($request->id);
        if ($p->file_lampiran) {
            Storage::disk('public')->delete($p->file_lampiran);
            $p->update(['file_lampiran' => null, 'nama_file' => null]);
        }
        return response()->json(['ok' => true, 'message' => 'File berhasil dihapus.']);
    }

    public function remove(Request $request)
    {
        $p = Pengumuman::findOrFail($request->id);
        if ($p->file_lampiran) {
            Storage::disk('public')->delete($p->file_lampiran);
        }
        $p->delete();
        return response()->json(['ok' => true, 'message' => 'Pengumuman berhasil dihapus.']);
    }

    // ─── Halaman warga (read-only) ────────────────────────────────────────
    public function publikasi()
    {
        return view('pengumuman.publik');
    }

    public function publikasiList(Request $request)
    {
        return Pengumuman::with('pembuat')
            ->where('aktif', true)
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori', $request->kategori))
            ->orderBy('penting', 'desc')
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(fn ($p) => [
                'id'            => $p->id,
                'judul'         => $p->judul,
                'kategori'      => $p->kategori,
                'kategori_label'=> $p->kategori_label,
                'isi'           => $p->isi,
                'tanggal'       => $p->tanggal?->locale('id')->isoFormat('D MMMM YYYY'),
                'file_url'      => $p->file_url,
                'nama_file'     => $p->nama_file,
                'penting'       => $p->penting,
                'pembuat'       => optional($p->pembuat)->name,
            ]);
    }
}
