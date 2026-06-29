<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        return view('menu.index');
    }

    public function list()
    {
        return Menu::orderBy('urutan')->get([
            'id', 'parent_id', 'nama', 'type', 'icon', 'controller',
            'fungsi', 'url', 'urutan', 'roles', 'aktif',
        ]);
    }

    public function parentLookup(Request $request)
    {
        $type = $request->input('for_type', 'screen');

        return Menu::query()
            ->when($type === 'button',
                // Button → parent harus Screen
                fn ($q) => $q->where('type', 'screen'),
                // Screen / default → parent harus Menu (grup)
                fn ($q) => $q->where('type', 'menu')
            )
            ->when($request->filled('exclude'), fn ($q) => $q->where('id', '!=', $request->exclude))
            ->orderBy('urutan')
            ->get(['id', 'nama', 'type']);
    }

public function save(Request $request)
    {
        $data = $request->validate([
            'nama'       => ['required', 'string', 'max:100'],
            'type'       => ['required', 'in:menu,screen,button'],
            'parent_id'  => ['nullable', 'integer', 'exists:menu,id'],
            'icon'       => ['nullable', 'string', 'max:50'],
            'controller' => ['nullable', 'string', 'max:100'],
            'fungsi'     => ['nullable', 'string', 'max:50'],
            'url'        => ['nullable', 'string', 'max:150'],
            'urutan'     => ['required', 'integer', 'min:0'],
        ]);

        $data['aktif']     = $request->boolean('aktif');
        $data['parent_id'] = $request->parent_id ?: null;

        if ($request->filled('id')) {
            $menu = Menu::findOrFail($request->id);
            $menu->update($data);
            $msg = 'Menu berhasil diperbarui.';
        } else {
            $menu = Menu::create($data);
            $msg = 'Menu berhasil ditambahkan.';
        }

        return response()->json(['ok' => true, 'message' => $msg]);
    }

    public function remove(Request $request)
    {
        Menu::findOrFail($request->id)->delete();
        return response()->json(['ok' => true, 'message' => 'Menu berhasil dihapus.']);
    }
}
