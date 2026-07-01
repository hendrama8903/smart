<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }

    public function list()
    {
        return User::with('role', 'warga')
            ->orderBy('name')
            ->get()
            ->map(fn ($u) => [
                'id'          => $u->id,
                'name'        => $u->name,
                'username'    => $u->username,
                'email'       => $u->email,
                'role_id'     => $u->role_id,
                'role_nama'   => optional($u->role)->nama,
                'role_label'  => optional($u->role)->label,
                'warga_id'    => $u->warga_id,
                'warga_nama'  => optional($u->warga)->nama,
                'status'      => $u->status,
                'created_at'  => optional($u->created_at)->format('d/m/Y'),
            ]);
    }

    public function save(Request $request)
    {
        $isEdit = $request->filled('id');

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($request->id)],
            'email'    => ['nullable', 'email', 'max:150', Rule::unique('users')->ignore($request->id)],
            'password' => [$isEdit ? 'nullable' : 'required', 'string', 'min:6', 'max:100'],
            'role_id'  => ['nullable', 'exists:roles,id'],
            'warga_id' => ['nullable', 'exists:warga,id'],
            'status'   => ['required', 'in:aktif,nonaktif'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $data['warga_id'] = $request->warga_id ?: null;
        $data['role_id']  = $request->role_id  ?: null;

        if ($isEdit) {
            User::findOrFail($request->id)->update($data);
            $msg = 'User berhasil diperbarui.';
        } else {
            User::create($data);
            $msg = 'User berhasil ditambahkan.';
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    public function remove(Request $request)
    {
        $user = User::findOrFail($request->id);

        if ($user->id === Auth::id()) {
            return response()->json(['ok' => false, 'message' => 'Tidak bisa menghapus akun sendiri.'], 422);
        }

        $user->delete();
        return response()->json(['ok' => true, 'message' => 'User berhasil dihapus.']);
    }

    public function roleLookup()
    {
        return Role::orderBy('label')->get(['id', 'nama', 'label']);
    }

    public function wargaLookup(Request $request)
    {
        return Warga::query()
            ->when($request->filled('q'), fn ($q) => $q->where('nama', 'like', '%'.$request->q.'%'))
            ->where(function ($q) use ($request) {
                // tampilkan warga yang belum punya akun, atau yang sedang terpilih
                $q->whereDoesntHave('user')
                  ->when($request->filled('current'), fn ($q2) => $q2->orWhere('id', $request->current));
            })
            ->orderBy('nama')
            ->limit(30)
            ->get(['id', 'nama', 'nik']);
    }
}
