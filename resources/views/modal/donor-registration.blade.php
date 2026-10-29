<div id="modal" class="donation-modal hidden fixed inset-0 z-50 p-4 items-center justify-center">
    <div role="dialog" aria-modal="true" aria-labelledby="modalTitle" aria-describedby="modalDesc"
        class="donation-modal-panel">
        <button aria-label="{{ __('Close') }}" onclick="hideModal()" class="donation-modal-close">
            ×
        </button>
        <h2 id="modalTitle" class="text-2xl font-bold mb-3">{{ __('Donor registration') }}</h2>
        <p id="modalDesc" class="text-gray-500 text-sm mb-5">{{ __('Complete the details below') }}</p>
        <form id="form" class="donation-modal-form">
            <label class="modal-field">
                <span>{{ __('Full name') }}</span>
                <input name="name" required class="field" autocomplete="name" placeholder="{{ __('Full name') }}">
            </label>
            <label class="modal-field">
                <span>{{ __('Mobile number') }}</span>
                <input name="phone" type="tel" required class="field" autocomplete="tel"
                    placeholder="{{ __('Mobile number') }}" inputmode="tel">
            </label>
            <label class="modal-field" data-registration-field>
                <span>{{ __('Blood group') }}</span>
                <select name="blood_group_id" required class="field">
                    <option value="">{{ __('Blood group') }}</option>
                    <option>A+</option>
                    <option>A-</option>
                    <option>B+</option>
                    <option>B-</option>
                    <option>AB+</option>
                    <option>AB-</option>
                    <option>O+</option>
                    <option>O-</option>
                </select>
            </label>
            <label class="modal-field" data-registration-field>
                <span>{{ __('Address / area') }}</span>
                <input name="area" required class="field" autocomplete="street-address"
                    placeholder="{{ __('Address / area') }}">
            </label>
            <label class="modal-field" data-contact-field hidden>
                <span>{{ __('Reason for contact') }}</span>
                <textarea name="message" class="field" placeholder="{{ __('Reason for contact') }}" hidden></textarea>
            </label>
            <label class="modal-consent">
                <input name="consent_to_contact" value="1" required type="checkbox">
                {{ __('I consent to data collection and contact requests.') }}
            </label>
            <button class="btn w-full" type="submit">
                {{ __('Submit') }}
            </button>
        </form>
        <p id="message" class="hidden text-sm text-green-700 bg-green-50 rounded-lg p-3 mt-4">
        </p>
    </div>
</div>
