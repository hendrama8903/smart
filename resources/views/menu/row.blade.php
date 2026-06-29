<tr class="{{ $level ? 'lvl-child' : '' }}">
  <td>
    <div class="r-name">
      <span class="mini-av">@include('partials.menu-icon', ['icon' => $m->icon, 'cls' => ''])</span>
      <div>{{ $m->nama }}<div class="r-sub">{{ $level ? 'Sub-menu' : 'Menu induk' }}</div></div>
    </div>
  </td>
  <td class="hide-sm mono-sm">{{ $m->icon ?? '—' }}</td>
  <td class="hide-sm mono-sm">{{ $m->controller ? $m->controller.'@'.$m->fungsi : '—' }}</td>
  <td class="mono">{{ $m->urutan }}</td>
  <td class="hide-sm badge-role">{{ $m->roles ?? 'semua' }}</td>
  <td>
    @if($m->aktif)
      <span class="pill lunas">Aktif</span>
    @else
      <span class="pill belum">Nonaktif</span>
    @endif
  </td>
  <td style="text-align:right;white-space:nowrap">
    <a class="act-btn" href="{{ route('menu.edit', $m) }}" title="Ubah">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
    </a>
    <button class="act-btn del" type="button" title="Hapus" data-id="{{ $m->id }}" data-nama="{{ $m->nama }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
    </button>
  </td>
</tr>
