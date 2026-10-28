<details class="language-switcher">
    <summary class="language-switcher-trigger" aria-label="{{ __('Language') }}">
        <i class="fi fi-rr-globe" aria-hidden="true"></i>
        <span>{{ app()->getLocale() === 'bn' ? 'বাংলা' : 'English' }}</span>
        <i class="fi fi-rr-angle-small-down language-switcher-chevron" aria-hidden="true"></i>
    </summary>
    <div class="language-switcher-menu" aria-label="{{ __('Language') }}">
        <span class="language-switcher-heading">{{ __('Language') }}</span>
        @foreach (['en' => 'English', 'bn' => 'বাংলা'] as $locale => $label)
            <form method="POST" action="{{ route('locale.update', $locale) }}">
                @csrf
                <button type="submit" name="locale" value="{{ $locale }}" class="language-switcher-option {{ app()->getLocale() === $locale ? 'is-selected' : '' }}" lang="{{ $locale }}" @if(app()->getLocale() === $locale) aria-current="true" @endif>
                    <span class="language-switcher-code" aria-hidden="true">{{ strtoupper($locale) }}</span>
                    <span>{{ $label }}</span>
                    @if (app()->getLocale() === $locale)
                        <i class="fi fi-rr-check" aria-hidden="true"></i>
                    @endif
                </button>
            </form>
        @endforeach
    </div>
</details>
