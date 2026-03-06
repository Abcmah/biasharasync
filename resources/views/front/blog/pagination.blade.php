@if ($paginator->hasPages())
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="page-link" style="opacity:0.5;">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <a href="#" class="page-link active">{{ $page }}</a>
                @else
                    <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="page-link">Next &rarr;</a>
    @endif
@endif
