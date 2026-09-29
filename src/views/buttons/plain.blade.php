<ul class="social-share">
    @foreach ($buttons as $button)
        <li>
            <a href="{{ $button['url'] }}"
               class="social-share-{{ $button['service'] }}"
               title="{{ $button['label'] }}"
               @unless ($labels) aria-label="{{ $button['label'] }}" @endunless
               @if ($button['external']) target="_blank" rel="noopener noreferrer" @endif
            >
                <i class="{{ $button['icon'] }}" aria-hidden="true"></i>
                @if ($labels)<span>{{ $button['label'] }}</span>@endif
            </a>
        </li>
    @endforeach
</ul>
