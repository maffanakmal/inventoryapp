<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
        <div class="sb-sidenav-menu mt-3">
            <div class="nav">
                {{-- Loop utama untuk semua menu --}}
                @foreach ($links as $index => $link)
                    {{-- Jika bukan dropdown --}}
                    @if (!$link['is_dropdown'])
                        <a 
                            href="{{ route($link['route']) }}" 
                            class="nav-link {{ $link['is_active'] ? 'active' : '' }}"
                        >
                            <div class="sb-nav-link-icon">
                                <i class="{{ $link['icon'] }}"></i>
                            </div>
                            {{ $link['label'] }}
                        </a>
                    @else
                        {{-- Jika dropdown --}}
                        @php
                            $collapseId = 'collapse' . $index; // buat id unik
                        @endphp

                        <a class="nav-link collapsed {{ $link['is_active'] ? 'active' : '' }}" href="#" 
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $collapseId }}" 
                            aria-expanded="{{ $link['is_active'] ? 'true' : 'false' }}" 
                            aria-controls="{{ $collapseId }}">
                            <div class="sb-nav-link-icon">
                                <i class="{{ $link['icon'] }}"></i>
                            </div>
                            {{ $link['label'] }}
                            <div class="sb-sidenav-collapse-arrow">
                                <i class="fas fa-angle-down"></i>
                            </div>
                        </a>

                        <div 
                            class="collapse {{ $link['is_active'] ? 'show' : '' }}" 
                            id="{{ $collapseId }}" 
                            data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                @foreach ($link['sub_links'] as $sub)
                                    <a 
                                        href="{{ route($sub['route']) }}" 
                                        class="nav-link {{ $sub['is_active'] ? 'active' : '' }}"
                                    >
                                        {{ $sub['label'] }}
                                    </a>
                                @endforeach
                            </nav>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            {{ Auth::user()->name ?? 'Guest' }}
        </div>
    </nav>
</div>