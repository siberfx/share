<div class="social-share d-flex flex-wrap align-items-center gap-2">
    @foreach ($buttons as $button)
        <a href="{{ $button['url'] }}"
           class="social-share-{{ $button['service'] }} btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-2"
           title="{{ $button['label'] }}"
           @unless ($labels) aria-label="{{ $button['label'] }}" @endunless
           @if ($button['external']) target="_blank" rel="noopener noreferrer" @endif
        >
            <i class="{{ $button['icon'] }}" aria-hidden="true"></i>
            @if ($labels)<span>{{ $button['label'] }}</span>@endif
        </a>
    @endforeach
</div>
