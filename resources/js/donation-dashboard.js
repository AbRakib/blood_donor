import {locationFields, bindLocations} from './donation-locations.js';
import {routes, request, send, escapeHtml as esc} from './donation-http.js';
const $ = id => document.getElementById(id);
let groups = [], donors = [], summary = null, currentPage = 1, formAction, filterTimer;
const donorMarkup = $('donorForm').innerHTML;
const sections = {dashboard:'ড্যাশবোর্ড',donors:'রক্তদাতা',requests:'রক্তের অনুরোধ',groups:'ব্লাড গ্রুপ',areas:'এলাকা',reports:'রিপোর্ট',settings:'সেটিংস',contacts:'যোগাযোগের অনুরোধ',donations:'রক্তদানের ইতিহাস',activity:'অ্যাডমিন কার্যক্রম'};
const extra = document.createElement('section'); extra.className = 'panel panel-pad'; extra.hidden = true; $('directory').after(extra);
for (const [page, label] of Object.entries(sections).filter(([page]) => ['contacts','donations','activity'].includes(page))) {
    const button = document.createElement('button'); button.dataset.page = page; button.textContent = label; $('nav').append(button);
}
function showToast(text) {$('toast').textContent = text; $('toast').classList.add('show'); clearTimeout(window.toastTimeout); window.toastTimeout = setTimeout(() => $('toast').classList.remove('show'), 6000);}
function closeModal() {$('modalOverlay').classList.remove('show');}
function modal(title, markup, action) {
    $('modalOverlay').querySelector('h2').textContent = title;
    $('donorForm').innerHTML = markup + '<p id="formError" role="alert" style="color:#c92d3b;white-space:pre-line"></p><div class="modal-actions"><button type="button" class="btn" onclick="closeModal()">বাতিল</button><button type="submit" class="btn primary">সংরক্ষণ</button></div>';
    formAction = action; $('modalOverlay').classList.add('show');
}
const field = (label, name, type = 'text', value = '', required = true) => `<div class="field"><label>${esc(label)}</label><input name="${name}" type="${type}" value="${esc(value)}" ${required ? 'required' : ''}></div>`;
const groupSelect = selected => `<div class="field"><label>ব্লাড গ্রুপ</label><select name="blood_group_id" required>${groups.map(group => `<option value="${group.id}" ${Number(selected) === group.id ? 'selected' : ''}>${esc(group.name)}</option>`).join('')}</select></div>`;
async function refreshSummary() {
    summary = (await request(routes.admin)).data;
    const stats = [['মোট সক্রিয় রক্তদাতা',summary.donors],['উপলব্ধ রক্তদাতা',summary.available_donors],['অপেক্ষমাণ অনুরোধ',summary.pending_requests],['জরুরি অনুরোধ',summary.urgent_requests],['সম্পন্ন রক্তদান',summary.donations]];
    $('stats').innerHTML = stats.map(([label, value]) => `<div class="stat"><div class="stat-label">${label}</div><div class="stat-value">${value}</div></div>`).join('');
    $('openRequestCount').textContent = summary.open_requests;
    const max = Math.max(...summary.blood_groups.map(group => group.donors_count), 1);
    $('bloodBars').innerHTML = summary.blood_groups.map(group => `<div class="bloodrow"><strong>${esc(group.name)}</strong><div class="progress"><div style="width:${group.donors_count/max*100}%"></div></div><small>${group.donors_count} জন</small></div>`).join('');
    drawChart();
}
function drawChart() {
    if (!summary) return;
    const data = summary.monthly_donations.slice(-Number($('chartPeriod').value));
    $('chart').innerHTML = '<div style="display:flex;align-items:end;gap:18px;height:190px">' + data.map(month => `<div style="flex:1;text-align:center"><b>${month.donations}</b><div style="height:${month.donations/Math.max(...data.map(x => x.donations),1)*140}px;min-height:2px;background:#e94956;border-radius:5px"></div><small>${esc(month.month)}</small></div>`).join('') + '</div>';
}
async function loadDonors(page = 1) {
    const params = new URLSearchParams({page, q: $('search').value, area: $('areaFilter').value, availability: $('statusFilter').value});
    const group = groups.find(group => group.name === $('groupFilter').value); if (group) params.set('blood_group_id', group.id);
    const result = await request(`${routes.adminDonors}?${params}`); donors = result.data; currentPage = page;
    $('donorRows').innerHTML = donors.map(donor => `<tr><td><strong>${esc(donor.name)}</strong><small>#${donor.id} · ${donor.status ? 'সক্রিয়' : 'যাচাই অপেক্ষমাণ'}</small></td><td><span class="bloodpill">${esc(donor.bloodGroup?.name)}</span></td><td>${esc(donor.area)}<small>${esc(donor.address)}</small></td><td>${esc(donor.last_donation_date?.slice(0,10) || 'তথ্য নেই')}</td><td>${donor.is_available ? 'উপলব্ধ' : 'অনুপলব্ধ'}</td><td><button class="row-btn" data-action="edit-donor" data-id="${donor.id}">সম্পাদনা</button><button class="row-btn" data-action="toggle-donor" data-id="${donor.id}">${donor.is_available ? 'অনুপলব্ধ করুন' : 'উপলব্ধ করুন'}</button><button class="row-btn" data-action="approve-donor" data-id="${donor.id}">${donor.status ? 'নিষ্ক্রিয়' : 'অনুমোদন'}</button><button class="row-btn" data-action="delete-donor" data-id="${donor.id}">মুছুন</button><small>${esc(donor.phone || 'সম্মতি নেই')} · ${donor.total_donations} বার যাচাইকৃত রক্তদান</small><button class="row-btn" data-action="donor-history" data-id="${donor.id}">ইতিহাস</button></td></tr>`).join('') || '<tr><td colspan="6">কোনো রক্তদাতা নেই।</td></tr>';
    $('tableCount').textContent = `${result.meta.from || 0}–${result.meta.to || 0} / ${result.meta.total}`;
    $('pagination').innerHTML = `<button data-donor-page="${page-1}" ${page <= 1 ? 'disabled' : ''}>‹</button><span>${page} / ${result.meta.last_page}</span><button data-donor-page="${page+1}" ${page >= result.meta.last_page ? 'disabled' : ''}>›</button>`;
    const areas = [...new Set(donors.map(donor => donor.area).filter(Boolean))]; const selected = $('areaFilter').value;
    $('areaFilter').innerHTML = '<option value="">সকল এলাকা</option>' + [...new Set([selected, ...areas].filter(Boolean))].map(area => `<option ${area === selected ? 'selected' : ''}>${esc(area)}</option>`).join('');
}
function renderTable() {clearTimeout(filterTimer); filterTimer = setTimeout(() => loadDonors().catch(error => showToast(error.message)), 250);}
function openModal() {
    modal('নতুন রক্তদাতা', donorMarkup + locationFields(), async values => {
        const group = groups.find(group => group.name === values.group); if (!group) throw new Error('ব্লাড গ্রুপ নির্বাচন করুন।');
        await send(routes.adminDonors, {name:values.name, phone:values.phone, blood_group_id:group.id, area:values.area, address:values.address, is_available:values.status === 'available', consent_to_contact:true, district_id:values.district_id || null, upazila_id:values.upazila_id || null, union_id:values.union_id || null});
        await loadDonors(); await refreshSummary();
    });
    bindLocations($('donorForm')).catch(error => showToast(error.message));
    const actions = $('donorForm').querySelectorAll('.modal-actions'); if (actions.length > 1) actions[0].remove();
}
async function editDonor(id) {
    const donor = (await request(`${routes.adminDonors}/${id}`)).data;
    modal('রক্তদাতা সম্পাদনা', field('নাম','name','text',donor.name)+field('ফোন (নতুন নম্বর দিতে চাইলে লিখুন)','phone','tel',donor.phone || '',false)+groupSelect(donor.blood_group_id)+field('এলাকা','area','text',donor.area,false)+field('ঠিকানা','address','text',donor.address,false)+locationFields()+`<label><input type="checkbox" name="consent_to_contact" ${donor.consent_to_contact ? 'checked' : ''}> যোগাযোগের সম্মতি আছে</label>`, async values => {
        if (!values.phone) delete values.phone;
        await send(`${routes.adminDonors}/${id}`, {...values, consent_to_contact:values.consent_to_contact === 'on'}, 'PATCH'); await loadDonors(currentPage); await refreshSummary();
    });
    await bindLocations($('donorForm'),donor);
}
function openRequestForm() {
    modal('রক্তের অনুরোধ', groupSelect()+locationFields()+field('রোগীর নাম','patient_name')+field('যোগাযোগের নাম','contact_name')+field('যোগাযোগের ফোন','contact_phone','tel')+field('হাসপাতাল','hospital_name')+field('হাসপাতালের ঠিকানা','hospital_address','text','',false)+field('কত ব্যাগ','bags_needed','number',1)+field('কখন প্রয়োজন','needed_at','datetime-local')+field('মেয়াদ শেষ (ঐচ্ছিক)','expires_at','datetime-local','',false)+'<div class="field"><label>জরুরিতা</label><select name="urgency"><option value="normal">সাধারণ</option><option value="urgent">জরুরি</option><option value="emergency">অতি জরুরি</option><option value="critical">সংকটাপন্ন</option></select></div>', async values => {
        await send(routes.requests, {...values, needed_at:new Date(values.needed_at).toISOString(), expires_at:values.expires_at ? new Date(values.expires_at).toISOString() : null}); await loadRequests(); await refreshSummary();
    });
    bindLocations($('donorForm')).catch(error => showToast(error.message));
}
async function loadRequests(page = 1) {
    const result = await request(`${routes.requests}?page=${page}`);
    $('requestList').innerHTML = result.data.map(record => `<div class="request-item"><div><span class="bloodpill">${esc(record.bloodGroup?.name)}</span><strong>${esc(record.patient_name)}</strong><p>${esc(record.hospital_name)} · ${record.bags_needed} ব্যাগ · ${esc(record.request_status)} · ${esc(record.urgency)}</p><p>${esc(record.contact_name)} · ${esc(record.contact_phone)}</p></div><div><button class="btn" data-action="request-details" data-id="${record.id}">বিস্তারিত</button>${record.request_status === 'pending' ? `<button class="btn" data-action="verify-request" data-id="${record.id}">যাচাই করুন</button><button class="btn" data-action="cancel-request" data-id="${record.id}">বাতিল</button>` : ['verified','in_progress'].includes(record.request_status) ? `<button class="btn" data-action="assign-donor" data-id="${record.id}">দাতা অ্যাসাইন</button><button class="btn" data-action="match" data-id="${record.id}">দাতা ম্যাচ করুন</button><button class="btn" data-action="cancel-request" data-id="${record.id}">বাতিল</button>` : ''}</div></div>`).join('') || '<p>কোনো অনুরোধ নেই।</p>';
    $('requestList').insertAdjacentHTML('beforeend', pageButtons(result.meta, 'requests-page'));
}
const pageButtons = (meta, action) => `<div><button class="btn" data-action="${action}" data-id="${meta.current_page-1}" ${meta.current_page <= 1 ? 'disabled' : ''}>আগের পৃষ্ঠা</button> ${meta.current_page} / ${meta.last_page} <button class="btn" data-action="${action}" data-id="${meta.current_page+1}" ${meta.current_page >= meta.last_page ? 'disabled' : ''}>পরের পৃষ্ঠা</button></div>`;
async function requestDetails(id) {
    const record = (await request(`${routes.requests}/${id}`)).data;
    const open = ['verified','in_progress'].includes(record.request_status);
    modal('অনুরোধের বিস্তারিত', `<p>${esc(record.patient_name)} · ${esc(record.hospital_name)} · ${record.bags_needed} ব্যাগ · ${esc(record.request_status)} · ${esc(record.urgency)}</p><h3>ম্যাচ করা রক্তদাতা</h3>` + record.request_donors.map(match => `<p>${esc(match.blood_donor?.name)} · ${esc(match.blood_donor?.phone)} · ${esc(match.response_status)} ${open && match.response_status !== 'donated' ? `<button type="button" class="btn" data-action="respond" data-request="${id}" data-id="${match.id}">প্রতিক্রিয়া</button><button type="button" class="btn" data-action="record-donation" data-request="${id}" data-id="${match.blood_donor_id}">রক্তদান লিখুন</button>` : ''}</p>`).join('') + '<h3>স্ট্যাটাস হিস্ট্রি</h3>' + record.status_logs.map(log => `<p>${esc(log.from_status || 'নতুন')} → ${esc(log.to_status)} · ${esc(log.created_at)}</p>`).join('') + '<h3>রক্তদানের ইতিহাস</h3>' + record.donations.map(donation => `<p>#${donation.blood_donor_id} · ${esc(donation.donated_at?.slice(0,10))} · ${donation.bags} ব্যাগ</p>`).join(''), async () => {});
    $('donorForm').querySelector('[type=submit]').hidden = true;
}
function recordDonation(donorId = '', requestId = '') {
    modal('রক্তদান রেকর্ড',field('রক্তদাতার ID','blood_donor_id','number',donorId)+field('রিকোয়েস্ট ID (ঐচ্ছিক)','blood_request_id','number',requestId,false)+field('রক্তদানের তারিখ','donated_at','date',new Intl.DateTimeFormat('en-CA',{timeZone:'Asia/Dhaka',year:'numeric',month:'2-digit',day:'2-digit'}).format(new Date()))+field('হাসপাতাল','hospital_name','text','',false), async values => {
        await send(routes.donations,{...values,blood_request_id:values.blood_request_id || null,bags:1}); await refreshSummary(); await loadDonors(); await loadRequests(); if (!extra.hidden) await loadDonations();
    });
}
async function loadSettings() {
    const result = await request(routes.settings);
    $('settingsList').innerHTML = result.data.map(setting => `<form data-setting="${setting.id}" style="margin:16px 0"><label>${esc(setting.key)} <input class="filter" name="value" value="${esc(setting.value)}"></label><button class="btn" type="submit">সংরক্ষণ</button></form>`).join('');
}
async function loadContacts(page = 1) {
    const result = await request(`${routes.contacts}?page=${page}`);
    extra.innerHTML = '<h2>যোগাযোগের অনুরোধ</h2>' + result.data.map(contact => `<div class="request-item"><div><b>${esc(contact.requester_name)} · ${esc(contact.requester_phone)}</b><p>${esc(contact.message)}</p><p>দাতা: ${esc(contact.blood_donor?.name)} · ${esc(contact.blood_donor?.phone)} · ${esc(contact.contact_status)}</p></div><div>${contact.contact_status === 'pending' ? `<button class="btn" data-action="contact-approved" data-id="${contact.id}">অনুমোদন</button><button class="btn" data-action="contact-rejected" data-id="${contact.id}">প্রত্যাখ্যান</button>` : contact.contact_status === 'approved' ? `<button class="btn" data-action="contact-completed" data-id="${contact.id}">সম্পন্ন</button><button class="btn" data-action="contact-rejected" data-id="${contact.id}">প্রত্যাখ্যান</button>` : ''}</div></div>`).join('') + pageButtons({current_page:result.current_page,last_page:result.last_page},'contacts-page');
}
async function loadDonations(page = 1) {
    const result = await request(`${routes.donations}?page=${page}`);
    extra.innerHTML = '<h2>রক্তদানের ইতিহাস</h2><button class="btn primary" data-action="new-donation">+ রক্তদান লিখুন</button>' + result.data.map(donation => `<div class="request-item"><b>${esc(donation.bloodDonor?.name)}</b><span>${esc(donation.donated_at?.slice(0,10))} · ${donation.bags} ব্যাগ · ${esc(donation.hospital_name)} · ${esc(donation.donation_status)}</span>${donation.donation_status === 'pending' ? `<button class="btn" data-action="verify-donation" data-id="${donation.id}">রক্তদান যাচাই করুন</button>` : 'যাচাইকৃত'}</div>`).join('') + pageButtons(result.meta,'donations-page');
}
async function loadActivity(page = 1) {
    const result = await request(`${routes.activity}?page=${page}`);
    extra.innerHTML = '<h2>অ্যাডমিন কার্যক্রম</h2>' + result.data.map(log => `<p>${esc(log.created_at)} · User #${log.user_id} · ${esc(log.action)} · ${esc(log.subject_type)} #${log.subject_id}</p>`).join('') + pageButtons({current_page:result.current_page,last_page:result.last_page},'activity-page');
}
async function loadLocations() {
    const result = await request(routes.locations);
    extra.innerHTML = '<h2>জেলা → উপজেলা → ইউনিয়ন</h2><button class="btn" data-action="new-location" data-id="districts">জেলা যোগ করুন</button><button class="btn" data-action="new-location" data-id="upazilas">উপজেলা যোগ করুন</button><button class="btn" data-action="new-location" data-id="unions">ইউনিয়ন যোগ করুন</button><div id="locationTree"></div>';
    $('locationTree').innerHTML = result.data.map(location => `<p><button class="btn" data-action="expand-district" data-id="${location.id}">${esc(location.name_bn || location.name)} (#${location.id})</button><span id="district-${location.id}"></span></p>`).join('');
}
async function loadGroups() {
    groups = (await request(routes.groups)).data;
    await refreshSummary();
    extra.innerHTML = '<h2>ব্লাড গ্রুপ</h2><p>ব্লাড গ্রুপ ও নিবন্ধিত রক্তদাতার তালিকা</p><div class="tablewrap"><table><thead><tr><th>ব্লাড গ্রুপ</th><th>রক্তদাতার সংখ্যা</th></tr></thead><tbody>' + groups.map(group => {
        const donorCount = summary.blood_groups.find(record => record.id === group.id)?.donors_count ?? 0;
        return `<tr><td><span class="bloodpill">${esc(group.name)}</span></td><td>${donorCount} জন</td></tr>`;
    }).join('') + (groups.length ? '' : '<tr><td colspan="2">কোনো ব্লাড গ্রুপ নেই।</td></tr>') + '</tbody></table></div>';
}
async function newLocation(type) {
    let parent = '';
    if (type === 'upazilas') {const result = await request(routes.locations); parent = `<div class="field"><label>জেলা</label><select name="district_id">${result.data.map(row => `<option value="${row.id}">${esc(row.name)}</option>`).join('')}</select></div>`;}
    if (type === 'unions') parent = field('উপজেলার ID (তালিকা থেকে দেখুন)','upazila_id','number');
    modal('এলাকা যোগ করুন',parent+field('ইংরেজি নাম','name')+field('বাংলা নাম','name_bn','text','',false)+(type === 'districts' ? field('কোড','code','text','',false) : ''), async values => {
        await send(routes.locationStore.replace(/districts$/,type), values); await loadLocations();
    });
}
async function selectPage(page) {
    $('pageTitle').textContent = sections[page]; $('crumb').textContent = sections[page];
    document.querySelectorAll('.nav button').forEach(button => button.classList.toggle('active',button.dataset.page === page));
    $('dashboardExtra').classList.toggle('active',['dashboard','reports'].includes(page));
    $('directory').style.display = ['dashboard','donors'].includes(page) ? 'block' : 'none';
    $('requestsView').classList.toggle('active',page === 'requests'); $('settingsView').classList.toggle('active',page === 'settings');
    extra.hidden = !['contacts','donations','activity','areas','groups'].includes(page);
    $('sidebar').classList.remove('open');
    try {
        if (page === 'requests') await loadRequests();
        if (page === 'settings') await loadSettings();
        if (page === 'contacts') await loadContacts();
        if (page === 'donations') await loadDonations();
        if (page === 'activity') await loadActivity();
        if (page === 'areas') await loadLocations();
        if (page === 'groups') await loadGroups();
        if (page === 'reports') {await refreshSummary(); $('pageSubtitle').textContent = `${summary.donations} রক্তদান · ${summary.bags_donated} ব্যাগ রক্ত`;}
    } catch (error) {showToast(error.message);}
}
async function exportCSV() {
    try {
        const rows = []; let page = 1, lastPage = 1;
        do {const result = await request(`${routes.adminDonors}?page=${page}`); rows.push(...result.data); lastPage = result.meta.last_page; page++;} while (page <= lastPage);
        const cell = value => '"' + String(value ?? '').replace(/^[=+@\-\t\r]/, "'$&").replaceAll('"','""') + '"';
        const csv = 'Name,Blood Group,Area,Phone,Active,Available\n' + rows.map(row => [row.name,row.bloodGroup?.name,row.area,row.phone,row.status,row.is_available].map(cell).join(',')).join('\n');
        const url = URL.createObjectURL(new Blob(['\ufeff',csv],{type:'text/csv;charset=utf-8'})); const anchor = document.createElement('a'); anchor.href = url; anchor.download = 'blood-donors.csv'; anchor.click(); URL.revokeObjectURL(url);
    } catch (error) {showToast(error.message);}
}
$('donorForm').addEventListener('submit',async event => {
    event.preventDefault(); const button = event.submitter; if (!formAction) return; if (button) button.disabled = true;
    try {await formAction(Object.fromEntries(new FormData(event.target))); closeModal(); showToast('সংরক্ষিত হয়েছে।');}
    catch (error) {$('formError').textContent = error.message;} finally {if (button) button.disabled = false;}
});
document.addEventListener('submit',async event => {
    const form = event.target.closest('[data-setting]'); if (!form) return; event.preventDefault();
    try {await send(`${routes.settings}/${form.dataset.setting}`,{value:new FormData(form).get('value')},'PATCH'); showToast('সেটিংস সংরক্ষিত হয়েছে।');} catch (error) {showToast(error.message);}
});
document.addEventListener('click',async event => {
    const nav = event.target.closest('[data-page]'); if (nav) {selectPage(nav.dataset.page); return;}
    const page = event.target.closest('[data-donor-page]'); if (page) {loadDonors(Number(page.dataset.donorPage)).catch(error => showToast(error.message)); return;}
    const button = event.target.closest('[data-action]'); if (!button) return;
    const {action,id} = button.dataset; button.disabled = true;
    try {
        if (action === 'donor-history') {const result = await request(`${routes.adminDonors}/${id}/history`); modal('যাচাইকৃত রক্তদানের ইতিহাস',result.data.map(row => `<p>${esc(row.donated_at?.slice(0,10))} · ${row.bags} ব্যাগ · ${esc(row.hospital_name)}</p>`).join('') || '<p>কোনো যাচাইকৃত রক্তদান নেই।</p>',async () => {}); $('donorForm').querySelector('[type=submit]').hidden = true;}
        if (action === 'edit-donor') await editDonor(id);
        if (action === 'toggle-donor' || action === 'approve-donor') {
            const donor = donors.find(donor => donor.id === Number(id));
            await send(`${routes.adminDonors}/${id}${action === 'toggle-donor' ? '/availability' : ''}`,action === 'toggle-donor' ? {is_available:!donor.is_available} : {status:donor.status ? 0 : 1},'PATCH'); await loadDonors(currentPage); await refreshSummary();
        }
        if (action === 'delete-donor' && confirm('এই রক্তদাতাকে মুছতে চান?')) {await request(`${routes.adminDonors}/${id}`,{method:'DELETE'}); await loadDonors(); await refreshSummary();}
        if (action === 'verify-request') {await send(`${routes.requests}/${id}/verify`,{}); await loadRequests(); await refreshSummary();}
        if (action === 'verify-donation') {await send(`${routes.donations}/${id}/verify`,{}); await loadDonations(); await loadDonors(); await loadRequests(); await refreshSummary();}
        if (action === 'assign-donor') {modal('দাতা অ্যাসাইন করুন',field('রক্তদাতার ID','blood_donor_id','number'),async values => {await send(`${routes.requests}/${id}/assign-donor`,values); await loadRequests(); await refreshSummary();});}
        if (action === 'match') {await send(`${routes.requests}/${id}/matches`,{}); await refreshSummary(); await loadRequests(); await requestDetails(id);}
        if (action === 'request-details') await requestDetails(id);
        if (action === 'cancel-request' && confirm('অনুরোধ বাতিল করবেন?')) {await send(`${routes.requests}/${id}`,{request_status:'cancelled'},'PATCH'); await loadRequests(); await refreshSummary();}
        if (action === 'record-donation') recordDonation(id,button.dataset.request);
        if (action === 'new-donation') recordDonation();
        if (action === 'respond') {
            const requestId = button.dataset.request;
            modal('দাতার প্রতিক্রিয়া','<div class="field"><select name="response_status"><option value="contacted">যোগাযোগ হয়েছে</option><option value="accepted">সম্মত</option><option value="declined">অসম্মত</option></select></div>'+field('নোট','notes','text','',false),async values => {await send(`${routes.requests}/${requestId}/matches/${id}`,values,'PATCH');});
        }
        if (action.startsWith('contact-')) {await send(`${routes.contacts}/${id}`,{contact_status:action.slice(8)},'PATCH'); await loadContacts();}
        if (action === 'requests-page') await loadRequests(Number(id));
        if (action === 'contacts-page') await loadContacts(Number(id));
        if (action === 'donations-page') await loadDonations(Number(id));
        if (action === 'activity-page') await loadActivity(Number(id));
        if (action === 'new-location') await newLocation(id);
        if (action === 'expand-district') {
            const result = await request(`${routes.locations}?district_id=${id}`);
            $(`district-${id}`).innerHTML = result.data.map(row => `<p style="margin-left:30px"><button class="btn" data-action="expand-upazila" data-id="${row.id}">${esc(row.name_bn || row.name)} (#${row.id})</button><span id="upazila-${row.id}"></span></p>`).join('');
        }
        if (action === 'expand-upazila') {const result = await request(`${routes.locations}?upazila_id=${id}`); $(`upazila-${id}`).textContent = result.data.map(row => row.name_bn || row.name).join(' · ');}
    } catch (error) {showToast(error.message);} finally {button.disabled = false;}
});
Object.assign(window,{showToast,closeModal,openModal,selectPage,renderTable,drawChart,exportCSV,openRequestForm});
$('today').textContent = new Intl.DateTimeFormat('bn-BD',{timeZone:'Asia/Dhaka',day:'numeric',month:'long',year:'numeric'}).format(new Date());
window.lucide?.createIcons();
try {groups = (await request(routes.groups)).data; await refreshSummary(); await loadDonors();}
catch (error) {showToast(error.message); $('donorRows').innerHTML = `<tr><td colspan="6"><a href="${esc(routes.login)}">অ্যাডমিন লগইন করুন</a></td></tr>`;}
