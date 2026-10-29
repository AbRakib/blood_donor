<div class="field">
    <label for="setting-{{ $name }}">{{ __($label) }}</label>
    <label class="settings-upload" for="setting-{{ $name }}">
        <span class="settings-upload-image">
            <span class="settings-drop" aria-hidden="true"><i class="fi fi-rr-heart" aria-hidden="true"></i></span>
            <img data-preview="{{ $name }}" alt="{{ __($label) }}" @if($setting?->{$name.'_url'}) src="{{ $setting->{$name.'_url'} }}" @else hidden @endif>
        </span>
        <span class="settings-upload-copy"><span>{{ __($uploadLabel ?? 'Click to upload logo') }}</span><small id="setting-{{ $name }}-help">{{ __('PNG, JPG, WebP or GIF. Maximum 2 MB.') }}</small></span>
        <span class="settings-change"><i class="fi fi-rr-upload" aria-hidden="true"></i> {{ __('Change') }}</span>
        <input id="setting-{{ $name }}" class="settings-file" name="{{ $name }}" type="file" accept="image/png,image/jpeg,image/webp,image/gif" aria-describedby="setting-{{ $name }}-help">
    </label>
</div>
