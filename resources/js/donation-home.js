import {locationFields, bindLocations} from './donation-locations.js';
import {routes, request, send, escapeHtml as esc} from './donation-http.js';
let groups = [], donors = [], mode = 'register', donorId = null, currentPage = 1, searchSequence = 0;
const form = document.getElementById('form');
const locationContainer = document.createElement('div'); locationContainer.innerHTML = locationFields(); form.elements.area.after(locationContainer);
const message = document.getElementById('message');
const grid = document.getElementById('grid');
const pager = document.createElement('div'); grid.after(pager);
function showMessage(text) {message.textContent = text; message.classList.remove('hidden');}
function showModal(type, index = -1) {
    mode = type; donorId = donors[index]?.id; form.reset(); message.classList.add('hidden');
    locationContainer.hidden = type !== 'register';
    locationContainer.querySelectorAll('select').forEach(field => {field.disabled = type !== 'register';});
    if (type === 'register') bindLocations(form).catch(error => showMessage(error.message));
    document.getElementById('modalTitle').textContent = type === 'register' ? 'রক্তদাতা নিবন্ধন' : 'যোগাযোগের অনুরোধ';
    document.getElementById('modalDesc').textContent = type === 'register' ? 'নিবন্ধন অ্যাডমিন যাচাইয়ের পর প্রকাশিত হবে।' : `${donors[index]?.name || ''} — অ্যাডমিন আপনার অনুরোধ পর্যালোচনা করবেন।`;
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
        document.getElementById('result').textContent = `${result.meta.total} জন পাওয়া গেছে`;
        grid.innerHTML = donors.map((donor, index) => `<article class="card"><div class="red text-2xl font-bold">${esc(donor.blood_group)}</div><h3 class="font-bold text-xl mt-5">${esc(donor.name)}</h3><p class="text-gray-500">${esc([donor.union, donor.upazila, donor.district].filter(Boolean).join(', ') || 'এলাকার তথ্য নেই')}</p><button data-contact="${index}" class="btn mt-4">যোগাযোগের অনুরোধ</button></article>`).join('') || '<p>কোনো রক্তদাতা পাওয়া যায়নি।</p>';
        pager.innerHTML = `<button class="btn" data-page="${page-1}" ${page <= 1 ? 'disabled' : ''}>আগের পৃষ্ঠা</button> ${page} / ${result.meta.last_page} <button class="btn" data-page="${page+1}" ${page >= result.meta.last_page ? 'disabled' : ''}>পরের পৃষ্ঠা</button>`;
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
        showMessage(mode === 'register' ? 'নিবন্ধন জমা হয়েছে। অ্যাডমিন যাচাই করবেন।' : 'যোগাযোগের অনুরোধ জমা হয়েছে।'); form.reset();
    } catch (error) {showMessage(error.message);} finally {button.disabled = false;}
});
let timer;
for (const id of ['query', 'blood', 'area']) document.getElementById(id).addEventListener(id === 'query' ? 'input' : 'change', () => {clearTimeout(timer); timer = setTimeout(() => render(), 250);});
document.getElementById('searchBtn').addEventListener('click', () => render());
document.getElementById('modal').addEventListener('click', event => {if (event.target.id === 'modal') hideModal();});
Object.assign(window, {showModal, hideModal});
try {
    groups = (await request(routes.groups)).data;
    form.elements.blood_group_id.innerHTML = '<option value="">রক্তের গ্রুপ</option>' + groups.map(group => `<option value="${group.id}">${esc(group.name)}</option>`).join('');
    document.getElementById('blood').innerHTML = '<option value="">সব রক্তের গ্রুপ</option>' + groups.map(group => `<option>${esc(group.name)}</option>`).join('');
    document.getElementById('groupTotal').textContent = groups.length;
    const totals = await request(routes.donors); document.getElementById('donorTotal').textContent = totals.meta.total;
    const districts = (await request(routes.locations)).data;
    document.getElementById('area').innerHTML = '<option value="">সব জেলা</option>' + districts.map(row => `<option value="${row.id}">${esc(row.name_bn || row.name)}</option>`).join('');
    await render();
} catch (error) {grid.textContent = error.message;}
