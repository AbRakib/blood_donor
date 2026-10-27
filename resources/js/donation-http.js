export const routes = window.donationRoutes;
export const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]));
export async function request(url, options = {}) {
    const response = await fetch(url, {
        credentials: 'same-origin', ...options,
        headers: {Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, ...options.headers},
    });
    if (response.status === 204) return null;
    const data = await response.json();
    if (!response.ok) {
        if (response.status === 401) throw new Error('অ্যাডমিন লগইন করুন।');
        throw new Error(data.errors ? Object.values(data.errors).flat().join('\n') : data.message || 'অনুরোধ ব্যর্থ হয়েছে।');
    }
    return data;
}
export const send = (url, data, method = 'POST') => request(url, {method, body: JSON.stringify(data)});
