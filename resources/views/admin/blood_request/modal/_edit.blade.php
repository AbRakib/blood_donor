<section class="request-form-section">
    <h3>{{ __('Request details') }}</h3>
    <div class="request-form-grid">
        <div class="field">
            <label for="request-blood-group">{{ __('Blood group') }} <span class="request-required">*</span></label>
            <select id="request-blood-group" name="blood_group_id" required></select>
        </div>
        <div class="field">
            <label for="request-bags_needed">{{ __('Number of bags') }} <span class="request-required">*</span></label>
            <input id="request-bags_needed" name="bags_needed" type="number" value="1" placeholder="{{ __('') }}" required min="1" step="1">
        </div>
        <div class="field">
            <label for="request-urgency">{{ __('Urgency') }} <span class="request-required">*</span></label>
            <select id="request-urgency" name="urgency" required>
                @foreach (['normal' => 'Normal', 'urgent' => 'Urgent', 'emergency' => 'Very urgent', 'critical' => 'Critical'] as $value => $label)
                    <option value="{{ $value }}">{{ __($label) }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label for="request-needed_at">{{ __('Needed at') }} <span class="request-required">*</span></label>
            <input id="request-needed_at" name="needed_at" type="date" value="" placeholder="{{ __('') }}" required>
        </div>
        <div class="field">
            <label for="request-expires_at">{{ __('Expires at') }} <span class="request-required">*</span></label>
            <input id="request-expires_at" name="expires_at" type="date" value="" placeholder="{{ __('') }}" required>
        </div>
    </div>
</section>
<section class="request-form-section">
    <h3>{{ __('Patient & contact') }}</h3>
    <div class="request-form-grid">
        <div class="field">
            <label for="request-patient_name">{{ __('Patient name') }} <span class="request-required">*</span></label>
            <input id="request-patient_name" name="patient_name" type="text" value="" placeholder="{{ __('Enter patient name') }}" required>
        </div>
        <div class="field">
            <label for="request-contact_name">{{ __('Contact name') }} <span class="request-required">*</span></label>
            <input id="request-contact_name" name="contact_name" type="text" value="" placeholder="{{ __('Enter contact name') }}" required>
        </div>
        <div class="field">
            <label for="request-contact_phone">{{ __('Contact phone') }} <span class="request-required">*</span></label>
            <input id="request-contact_phone" name="contact_phone" type="tel" value="" placeholder="{{ __('Enter phone number') }}" required>
        </div>
    </div>
</section>
<section class="request-form-section">
    <h3>{{ __('Hospital') }}</h3>
    <div class="request-form-grid">
        <div class="field">
            <label for="request-hospital_name">{{ __('Hospital') }} <span class="request-required">*</span></label>
            <input id="request-hospital_name" name="hospital_name" type="text" value="" placeholder="{{ __('Enter hospital name') }}" required>
        </div>
        <div class="field">
            <label for="request-hospital_address">{{ __('Hospital address') }} <span class="request-required">*</span></label>
            <input id="request-hospital_address" name="hospital_address" type="text" value="" placeholder="{{ __('Enter hospital address') }}" required>
        </div>
    </div>
</section>
<details class="request-optional">
    <summary>{{ __('Location (optional)') }}</summary>
    <div class="request-form-grid" data-request-locations></div>
</details>
