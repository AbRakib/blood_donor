import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/dist/sweetalert2.css';
import {t} from './donation-http.js';

const alert = options => Swal.fire({
    confirmButtonColor: '#e91335',
    cancelButtonColor: '#64748b',
    confirmButtonText: t('OK'),
    heightAuto: false,
    returnFocus: false,
    ...options,
});

export const showSuccess = text => alert({icon: 'success', titleText: t('Success'), text});
export const showError = text => alert({icon: 'error', titleText: t('Action failed'), text});
export const showInfo = text => alert({icon: 'info', titleText: t('Notice'), text});
export async function confirmAction(text) {
    const result = await alert({
        icon: 'warning',
        titleText: t('Are you sure?'),
        text,
        showCancelButton: true,
        confirmButtonText: t('Confirm'),
        cancelButtonText: t('Cancel'),
        focusCancel: true,
    });
    return result.isConfirmed;
}
