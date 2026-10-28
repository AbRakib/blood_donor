import {enhanceSearchSelect} from './donation-select.js';
import {showSuccess, showError} from './donation-alerts.js';
import {t} from './donation-http.js';
import {locationFields, bindLocations} from './donation-locations.js';
import {routes, request, send, escapeHtml as esc} from './donation-http.js';
let groups = [], donors = [], mode = 'register', donorId = null, currentPage = 1, searchSequence = 0;
for (const id of ['blood', 'area']) enhanceSearchSelect(document.getElementById(id));
const form = document.getElementById('form');
const locationContainer = document.createElement('div'); locationContainer.innerHTML = locationFields(); form.elements.area.after(locationContainer);
const message = document.getElementById('message');
const grid = document.getElementById('grid');
const pager = document.createElement('div'); pager.className = 'donor-pagination'; grid.after(pager);
function showMessage(text) {message.textContent = text; message.classList.remove('hidden');}
function showModal(type, index = -1) {
    mode = type; donorId = donors[index]?.id; form.reset(); message.classList.add('hidden');
    locationContainer.hidden = type !== 'register';
    locationContainer.querySelectorAll('select').forEach(field => {field.disabled = type !== 'register';});
    if (type === 'register') bindLocations(form).catch(error => showMessage(error.message));
    document.getElementById('modalTitle').textContent = type === 'register' ? t("Donor registration") : t("Contact requests");
    document.getElementById('modalDesc').textContent = type === 'register' ? t("Registration will be published after administrator verification.") : `${donors[index]?.name || ''} — ${t("An administrator will review your request.")}`;
    for (const field of ['blood_group_id', 'area']) {
        form.elements[field].hidden = type !== 'register'; form.elements[field].disabled = type !== 'register';
    }
    form.elements.message.hidden = type === 'register'; form.elements.message.disabled = type === 'register'; form.elements.message.required = type !== 'register';
    document.getElementById('modal').classList.remove('hidden'); document.getElementById('modal').classList.add('flex');
}
function hideModal() {document.getElementById('modal').classList.add('hidden'); document.getElementById('modal').classList.remove('flex');}
async function render(page = 1) {
    currentPage = page; const sequence = ++searchSequence;
    const params = new URLSearchParams({page, q: document.getElementById('query').value, district_id: document.getElementById('area').value});
    const group = groups.find(group => group.name === document.getElementById('blood').value);
    if (group) params.set('blood_group_id', group.id);
    try {
        const result = await request(`${routes.donors}?${params}`);
        if (sequence !== searchSequence) return;
        donors = result.data;
        document.getElementById('result').textContent = `${result.meta.total} ${t("people found")}`;
        grid.innerHTML = donors.map((donor, index) => {
            const initials = donor.name.trim().split(/\s+/).slice(0, 2).map(part => part[0]).join('');
            return `<article class="card"><div class="donor-card-top"><span class="blood-badge">${esc(donor.blood_group)}</span><span class="availability ${donor.is_available ? '' : 'unavailable'}">${t(donor.is_available ? "Available" : "Unavailable")}</span></div><div class="donor-details"><span class="donor-avatar" aria-hidden="true">${esc(initials)}</span><div><h3>${esc(donor.name)}</h3><p>${esc([donor.union, donor.upazila, donor.district].filter(Boolean).join(', ') || t("No location information"))}</p></div></div><button data-contact="${index}" class="contact-button"><i class="fi fi-rr-paper-plane" aria-hidden="true"></i><span>${t("Contact requests")}</span><span aria-hidden="true">›</span></button></article>`;
        }).join('') || t("<p>No donors found.</p>");
        pager.innerHTML = `<button class="btn" data-page="${page-1}" ${page <= 1 ? 'disabled' : ''}>${t("Previous page")}</button> ${page} / ${result.meta.last_page} <button class="btn" data-page="${page+1}" ${page >= result.meta.last_page ? 'disabled' : ''}>${t("Next page")}</button>`;
    } catch (error) {grid.textContent = error.message;}
}
grid.addEventListener('click', event => {const button = event.target.closest('[data-contact]'); if (button) showModal('contact', Number(button.dataset.contact));});
pager.addEventListener('click', event => {const button = event.target.closest('[data-page]'); if (button) render(Number(button.dataset.page));});
form.addEventListener('submit', async event => {
    event.preventDefault(); const button = form.querySelector('[type=submit]'); button.disabled = true;
    const values = Object.fromEntries(new FormData(form));
    try {
        const data = mode === 'register' ? {...values, consent_to_contact: true} : {blood_donor_id: donorId, requester_name: values.name, requester_phone: values.phone, message: values.message};
        await send(mode === 'register' ? routes.register : routes.contact, data);
        hideModal(); showSuccess(mode === 'register' ? t("Registration submitted. An administrator will verify it.") : t("Contact request submitted.")); form.reset();
    } catch (error) {showMessage(error.message); showError(error.message);} finally {button.disabled = false;}
});
let timer;
for (const id of ['query', 'blood', 'area']) document.getElementById(id).addEventListener(id === 'query' ? 'input' : 'change', () => {clearTimeout(timer); timer = setTimeout(() => render(), 250);});
document.getElementById('searchBtn').addEventListener('click', () => render());
document.addEventListener('keydown', event => {if (event.key === 'Escape') hideModal();});
document.getElementById('modal').addEventListener('click', event => {if (event.target.id === 'modal') hideModal();});
Object.assign(window, {showModal, hideModal});
try {
    groups = (await request(routes.groups)).data;
    form.elements.blood_group_id.innerHTML = t("<option value=\"\">Blood group</option>") + groups.map(group => `<option value="${group.id}">${esc(group.name)}</option>`).join('');
    document.getElementById('blood').innerHTML = t("<option value=\"\">All blood groups</option>") + groups.map(group => `<option>${esc(group.name)}</option>`).join('');
    document.getElementById('groupTotal').textContent = groups.length;
    const totals = await request(routes.donors); document.getElementById('donorTotal').textContent = totals.meta.total;
    const districts = (await request(routes.locations)).data;
    document.getElementById('area').innerHTML = t("<option value=\"\">All districts</option>") + districts.map(row => `<option value="${row.id}">${esc((document.documentElement.lang === 'bn' ? row.name_bn || row.name : row.name))}</option>`).join('');
    await render();
} catch (error) {grid.textContent = error.message;}
