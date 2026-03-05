@if ($paginator->hasPages())
    <nav aria-label="Paginación">
        <div class="d-flex flex-column flex-sm-row align-items-center justify-content-center gap-4 mt-3 px-1">
            
            {{-- Info de registros --}}
            <div class="text-muted" style="font-size: 0.82rem;">
                Mostrando
                <span class="fw-semibold text-dark">{{ $paginator->lastItem() }}</span>
                de
                <span class="fw-semibold text-dark">{{ $paginator->total() }}</span>
                resultados
            </div>

            {{-- Botones de paginación --}}
            <ul class="pagination pagination-sm mb-0" style="gap: 4px;">
                
                {{-- Flecha Anterior --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link border-0 rounded-2 px-2" style="background: #f1f3f5; color: #adb5bd;">
                            <i class="fas fa-chevron-left" style="font-size: 0.65rem;"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link border-0 rounded-2 px-2 text-secondary" 
                           href="{{ $paginator->previousPageUrl() }}" rel="prev"
                           style="background: #f8f9fa; transition: all 0.2s;"
                           onmouseover="this.style.background='#e9ecef'" 
                           onmouseout="this.style.background='#f8f9fa'">
                            <i class="fas fa-chevron-left" style="font-size: 0.65rem;"></i>
                        </a>
                    </li>
                @endif

                {{-- Números de página --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="page-item disabled">
                            <span class="page-link border-0 rounded-2 px-2" 
                                  style="background: transparent; color: #6c757d; font-size: 0.8rem;">
                                {{ $element }}
                            </span>
                        </li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active">
                                    <span class="page-link border-0 rounded-2 px-3 fw-bold" 
                                          style="background: #344767; color: #fff; font-size: 0.8rem; min-width: 32px; text-align: center;">
                                        {{ $page }}
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link border-0 rounded-2 px-3 text-dark" 
                                       href="{{ $url }}"
                                       style="background: #f8f9fa; font-size: 0.8rem; min-width: 32px; text-align: center; transition: all 0.2s;"
                                       onmouseover="this.style.background='#e2e6ea'" 
                                       onmouseout="this.style.background='#f8f9fa'">
                                        {{ $page }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Flecha Siguiente --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link border-0 rounded-2 px-2 text-secondary" 
                           href="{{ $paginator->nextPageUrl() }}" rel="next"
                           style="background: #f8f9fa; transition: all 0.2s;"
                           onmouseover="this.style.background='#e9ecef'" 
                           onmouseout="this.style.background='#f8f9fa'">
                            <i class="fas fa-chevron-right" style="font-size: 0.65rem;"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link border-0 rounded-2 px-2" style="background: #f1f3f5; color: #adb5bd;">
                            <i class="fas fa-chevron-right" style="font-size: 0.65rem;"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif
