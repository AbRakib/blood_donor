<div class="field">
    <label for="setting-{{ $name }}">{{ __($label) }}</label>
    <div class="settings-input">
        <i class="fi fi-rr-{{ $icon }}" aria-hidden="true"></i>
        @if($type === 'textarea')
            <textarea id="setting-{{ $name }}" name="{{ $name }}" rows="2" maxlength="5000">{{ $setting?->{$name} }}</textarea>
        @else
            <input id="setting-{{ $name }}" name="{{ $name }}" type="{{ $type }}" maxlength="{{ $name === 'phone' ? 20 : 255 }}" value="{{ $setting?->{$name} }}" placeholder="{{ $placeholder ?? '' }}">
        @endif
    </div>
</div>
