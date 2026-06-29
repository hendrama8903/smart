<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return view('pengaturan.role');
    }

    // ─── CRUD Role ────────────────────────────────────────────────────────
    public function list()
    {
        return Role::withCount('users')->orderBy('id')->get()
            ->map(fn ($r) => [
                'id'          => $r->id,
                'nama'        => $r->nama,
                'label'       => $r->label,
                'keterangan'  => $r->keterangan,
                'jumlah_user' => $r->users_count,
            ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'nama'       => ['required', 'string', 'max:50', 'regex:/^[a-z_]+$/'],
            'label'      => ['required', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'nama.regex' => 'Nama role hanya boleh huruf kecil dan underscore (contoh: kepala_rw)',
        ]);

        if ($request->filled('id')) {
            Role::findOrFail($request->id)->update($data);
            $msg = 'Role berhasil diperbarui.';
        } else {
            if (Role::where('nama', $data['nama'])->exists()) {
                return response()->json(['ok' => false, 'message' => 'Nama role sudah digunakan.'], 422);
            }
            Role::create($data);
            $msg = 'Role berhasil ditambahkan.';
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    public function remove(Request $request)
    {
        $role = Role::withCount('users')->findOrFail($request->id);

        if ($role->users_count > 0) {
            return response()->json([
                'ok'      => false,
                'message' => 'Role masih digunakan oleh ' . $role->users_count . ' user. Pindahkan user ke role lain terlebih dahulu.',
            ], 422);
        }

        // Hapus juga nama role dari semua menu yang mereferensikannya
        $roleName = $role->nama;
        Menu::all()->each(function ($menu) use ($roleName) {
            if (blank($menu->roles)) return;
            $roles = array_filter(array_map('trim', explode(',', $menu->roles)), fn ($r) => $r !== $roleName);
            $menu->update(['roles' => implode(',', $roles) ?: null]);
        });

        $role->delete();
        return response()->json(['ok' => true, 'message' => 'Role berhasil dihapus.']);
    }

    // ─── Akses Menu per Role ──────────────────────────────────────────────

    // Ambil semua menu + status akses untuk role tertentu
    public function menuAccess(Role $role)
    {
        $allMenus = Menu::orderBy('urutan')->get(['id', 'parent_id', 'nama', 'type', 'roles', 'aktif', 'urutan']);

        return $allMenus->map(fn ($m) => [
            'id'        => $m->id,
            'parent_id' => $m->parent_id,
            'nama'      => $m->nama,
            'type'      => $m->type,
            'aktif'     => $m->aktif,
            // null = semua role boleh → centang; ada nilai → cek apakah role ini termasuk
            'has_access' => blank($m->roles)
                ? true
                : in_array($role->nama, array_map('trim', explode(',', $m->roles))),
        ]);
    }

    // Simpan pengaturan akses menu untuk role tertentu
    public function saveMenuAccess(Request $request, Role $role)
    {
        $request->validate([
            'menu_access'   => ['nullable', 'array'],
            'menu_access.*' => ['integer'],
        ]);

        $allowedIds   = collect($request->input('menu_access', []))->map('intval')->toArray();
        $allMenus     = Menu::all();
        $allRoleNames = Role::pluck('nama')->toArray(); // semua nama role yang ada

        foreach ($allMenus as $menu) {
            $isNull       = blank($menu->roles);
            $currentRoles = $isNull
                ? $allRoleNames  // null berarti semua role → perlakukan sebagai semua role
                : array_values(array_filter(array_map('trim', explode(',', $menu->roles))));

            $hasNow     = $isNull || in_array($role->nama, $currentRoles);
            $shouldHave = in_array($menu->id, $allowedIds);

            if ($shouldHave && ! $hasNow) {
                // Tambahkan role ini ke daftar
                $newRoles = array_unique(array_merge($currentRoles, [$role->nama]));
                // Jika sudah mencakup semua role → set null (all access)
                sort($newRoles); $sorted = $allRoleNames; sort($sorted);
                $menu->update(['roles' => $newRoles == $sorted ? null : implode(',', $newRoles)]);

            } elseif (! $shouldHave && $hasNow) {
                // Hapus role ini dari daftar
                $newRoles = array_values(array_filter($currentRoles, fn ($r) => $r !== $role->nama));
                $menu->update(['roles' => empty($newRoles) ? null : implode(',', $newRoles)]);
            }
            // Jika tidak ada perubahan → lewati
        }

        return response()->json(['ok' => true, 'message' => 'Pengaturan akses menu untuk role "' . $role->label . '" berhasil disimpan.']);
    }
}
