<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        return view('pengaturan.audit-log');
    }

    public function list(Request $request)
    {
        return AuditLog::with('user')
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('aksi'), fn ($q) => $q->where('aksi', $request->aksi))
            ->when($request->filled('modul'), fn ($q) => $q->where('modul', $request->modul))
            ->when($request->filled('dari'), fn ($q) => $q->whereDate('created_at', '>=', $request->dari))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('created_at', '<=', $request->sampai))
            ->orderBy('id', 'desc')
            ->limit(500) // batasi untuk performa
            ->get()
            ->map(fn ($a) => [
                'id'          => $a->id,
                'user'        => $a->nama_user,
                'aksi'        => $a->aksi,
                'aksi_label'  => $a->aksi_label,
                'aksi_color'  => $a->aksi_color,
                'modul'       => $a->modul,
                'modul_id'    => $a->modul_id,
                'deskripsi'   => $a->deskripsi,
                'sebelum'     => $a->sebelum ? json_encode($a->sebelum, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : null,
                'sesudah'     => $a->sesudah ? json_encode($a->sesudah, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : null,
                'ip_address'  => $a->ip_address,
                'waktu'       => $a->created_at?->locale('id')->isoFormat('D MMM YYYY, HH:mm:ss'),
                'waktu_raw'   => $a->created_at?->format('Y-m-d H:i:s'),
            ]);
    }

    public function userList()
    {
        return User::orderBy('name')->get(['id', 'name', 'username']);
    }

    public function modulList()
    {
        return AuditLog::select('modul')->distinct()->whereNotNull('modul')
            ->orderBy('modul')->pluck('modul');
    }
}
