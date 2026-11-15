<div class="formgrid">
    <div class="field">
        <label>{{ __('Blood group') }} *</label>
        <input name="name" required maxlength="5" placeholder="{{ __('Blood group') }}">
    </div>
    <div class="field">
        <label>{{ __('Sort order') }}</label>
        <input name="sort_order" type="number" min="0" max="255" step="1" value="0">
    </div>
    <div class="field">
        <label>{{ __('Status') }}</label>
        <select name="status">
            <option value="1">{{ __('Active') }}</option>
            <option value="0">{{ __('Inactive') }}</option>
        </select>
    </div>
</div>
