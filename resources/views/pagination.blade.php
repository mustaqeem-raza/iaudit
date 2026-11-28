{{--
    Paging under every list: "Showing 1 to 15 of 80" and the page numbers.
    Used by {{ $paginator->links() }}; set as the default in AppServiceProvider.
--}}
@if ($paginator->total())
    <nav class="paging" aria-label="Pages">
        <p class="sub">Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }}</p>

        @if ($paginator->hasPages())
            <ul class="pagination">
                @if ($paginator->onFirstPage())
                    <li class="disabled" aria-disabled="true"><span aria-hidden="true">&lsaquo;</span></li>
                @else
                    <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page">&lsaquo;</a></li>
                @endif

                @foreach ($elements as $element)
                    {{-- "…" between page ranges --}}
                    @if (is_string($element))
                        <li class="disabled" aria-disabled="true"><span>{{ $element }}</span></li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="active" aria-current="page"><span>{{ $page }}</span></li>
                            @else
                                <li><a href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <li><a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page">&rsaquo;</a></li>
                @else
                    <li class="disabled" aria-disabled="true"><span aria-hidden="true">&rsaquo;</span></li>
                @endif
            </ul>
        @endif
    </nav>
@endif
