<div class="formgrid">
    <div class="field">
        <label>{{ __('Full name') }} *</label>
        <input name="name" required placeholder="{{ __('Donor name') }}">
    </div>
    <div class="field">
        <label>{{ __('Blood group') }} *</label>
        <select name="group" required>
            <option value="">{{ __('Select') }}</option>
            @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $group)
                <option>{{ $group }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label>{{ __('Mobile number') }}</label>
        <input name="phone" pattern="[0-9+\- ]{10,16}" placeholder="01XXXXXXXXX">
    </div>
    <div class="field">
        <label>{{ __('Area') }}</label>
        <input name="area" placeholder="{{ __('For example: Manda Sadar') }}">
    </div>
    <div class="field full">
        <label>{{ __('Full address') }}</label>
        <input name="address" placeholder="{{ __('Village, union, upazila') }}">
    </div>
</div>
