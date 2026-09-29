<div class="social-share flex flex-wrap items-center gap-2">
    @foreach ($buttons as $button)
        <a href="{{ $button['url'] }}"
           class="social-share-{{ $button['service'] }} inline-flex items-center gap-2 rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 transition hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-400 focus-visible:ring-offset-2 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800"
           title="{{ $button['label'] }}"
           @unless ($labels) aria-label="{{ $button['label'] }}" @endunless
           @if ($button['external']) target="_blank" rel="noopener noreferrer" @endif
        >
            <i class="{{ $button['icon'] }}" aria-hidden="true"></i>
            @if ($labels)<span>{{ $button['label'] }}</span>@endif
        </a>
    @endforeach
</div>
