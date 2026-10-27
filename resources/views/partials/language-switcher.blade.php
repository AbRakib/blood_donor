<form method="POST" action="{{ route('locale.update', app()->getLocale()) }}" style="display:flex;align-items:center;gap:8px;flex-shrink:0">
    @csrf
    <label for="language-select" style="display:flex;align-items:center;color:#697581">
        <i class="fi fi-rr-globe" aria-hidden="true"></i>
        <span class="sr-only">{{ __('Language') }}</span>
    </label>
    <select id="language-select" name="locale" aria-label="{{ __('Language') }}" onchange="this.form.action = this.selectedOptions[0].dataset.url; this.form.requestSubmit()" style="font:inherit;font-size:14px;color:#182331;background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:8px 10px;cursor:pointer">
        @foreach (['en' => 'English', 'bn' => 'বাংলা'] as $locale => $label)
            <option value="{{ $locale }}" lang="{{ $locale }}" data-url="{{ route('locale.update', $locale) }}" @selected(app()->getLocale() === $locale)>{{ $label }}</option>
        @endforeach
    </select>
</form>
