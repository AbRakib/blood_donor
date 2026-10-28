import {showSuccess, showError, showInfo, confirmAction} from './donation-alerts.js';
import {t} from './donation-http.js';
import {locationFields, bindLocations} from './donation-locations.js';
import {routes, request, send, escapeHtml as esc} from './donation-http.js';
const $ = id => document.getElementById(id);
let groups = [], donors = [], summary = null, currentPage = 1, formAction, filterTimer, formSuccessMessage;
const donorMarkup = $('donorForm').innerHTML;
const sections = {dashboard:t("Dashboard"),donors:t("Blood donors"),requests:t("Blood requests"),groups:t("Blood group"),reports:t("Reports"),settings:t("Settings"),contacts:t("Contact requests"),donations:t("Donation history")};
const extra = document.createElement('section'); extra.className = 'panel panel-pad'; extra.hidden = true; $('directory').after(extra);
for (const [page, label] of Object.entries(sections).filter(([page]) => ['contacts','donations'].includes(page))) {
    const button = document.createElement('button'); button.dataset.page = page; button.innerHTML = `<i class="fi fi-rr-${({contacts:'comments',donations:'calendar'})[page]}" aria-hidden="true"></i>${esc(label)}`; $('nav').append(button);
}
function showToast(text) {return showInfo(text);}
function closeModal() {$('modalOverlay').classList.remove('show');}
function modal(title, markup, action) {
    formSuccessMessage = ({
        [t('New donor')]: t('Donor created successfully.'),
        [t('Edit donor')]: t('Donor updated successfully.'),
        [t('Blood requests')]: t('Blood request created successfully.'),
        [t('Record donation')]: t('Donation recorded successfully.'),
        [t('Assign donor')]: t('Donor assigned successfully.'),
        [t('Donor response')]: t('Donor response updated successfully.'),
    })[title] || t('Saved successfully.');
    $('modalOverlay').querySelector('h2').textContent = title;
    $('donorForm').innerHTML = markup + t("<p id=\"formError\" role=\"alert\" style=\"color:#c92d3b;white-space:pre-line\"></p><div class=\"modal-actions\"><button type=\"button\" class=\"btn\" onclick=\"closeModal()\">Cancel</button><button type=\"submit\" class=\"btn primary\">Save</button></div>");
    formAction = action; $('modalOverlay').classList.add('show');
}
const field = (label, name, type = 'text', value = '', required = true) => `<div class="field"><label>${esc(label)}</label><input name="${name}" type="${type}" value="${esc(value)}" ${required ? 'required' : ''}></div>`;
const groupSelect = selected => `<div class="field"><label>${t("Blood group")}</label><select name="blood_group_id" required>${groups.map(group => `<option value="${group.id}" ${Number(selected) === group.id ? 'selected' : ''}>${esc(group.name)}</option>`).join('')}</select></div>`;
async function refreshSummary() {
    summary = (await request(routes.admin)).data;
    const stats = [[t("Total active donors"),summary.donors,'users','r'],[t("Available donors"),summary.available_donors,'check-circle','g'],[t("Pending requests"),summary.pending_requests,'clock','o'],[t("Urgent requests"),summary.urgent_requests,'triangle-warning','r'],[t("Completed donations"),summary.donations,'heart','r']];
    $('stats').innerHTML = stats.map(([label, value, icon, color]) => `<div class="stat"><div class="stat-icon ${color}"><i class="fi fi-rr-${icon}" aria-hidden="true"></i></div><div><div class="stat-label">${label}</div><div class="stat-value">${value}</div></div></div>`).join('');
    $('openRequestCount').textContent = summary.open_requests;
    const max = Math.max(...summary.blood_groups.map(group => group.donors_count), 1);
    $('bloodBars').innerHTML = summary.blood_groups.map(group => `<div class="bloodrow"><strong>${esc(group.name)}</strong><div class="progress"><div style="width:${group.donors_count/max*100}%"></div></div><small>${group.donors_count}</small></div>`).join('');
    drawChart();
}
function drawChart() {
    if (!summary) return;
    const data = summary.monthly_donations.slice(-Number($('chartPeriod').value));
    $('chart').innerHTML = '<div style="display:flex;align-items:end;gap:6px;height:190px">' + data.map(month => `<div style="flex:1;text-align:center"><b class="chart-value">${month.donations}</b><div style="height:${month.donations/Math.max(...data.map(x => x.donations),1)*140}px;min-height:2px;background:#ed1638;border-radius:5px;position:relative"></div><small class="chart-month">${esc(month.month)}</small></div>`).join('') + '</div>';
}
async function loadDonors(page = 1) {
    const params = new URLSearchParams({page, q: $('search').value, area: $('areaFilter').value, availability: $('statusFilter').value});
    const group = groups.find(group => group.name === $('groupFilter').value); if (group) params.set('blood_group_id', group.id);
    const result = await request(`${routes.adminDonors}?${params}`); donors = result.data; currentPage = page;
    $('donorRows').innerHTML = donors.map((donor, index) => `<tr><td>${result.meta.from + index}.</td><td><div class="donorcell"><span class="donoravatar" aria-hidden="true">${esc(donor.name.trim().split(/\s+/u).slice(0,2).map(part => Array.from(part)[0]).join('').toLocaleUpperCase())}</span><div><strong>${esc(donor.name)}</strong><small class="donor-state ${donor.status ? 'active' : ''}">${donor.status ? t("Active") : t("Awaiting verification")}</small></div></div></td><td><span class="bloodpill">${esc(donor.bloodGroup?.name)}</span></td><td>${esc(donor.area)}<small>${esc(donor.address)}</small></td><td><span class="table-date">${esc(donor.last_donation_date?.slice(0,10) || t("No information"))}</span></td><td><span class="status ${donor.is_available ? '' : 'busy'}">${donor.is_available ? t("Available") : t("Unavailable")}</span></td><td><a class="phone-link" href="tel:${esc(donor.phone || '')}">${esc(donor.phone || t("No consent"))}</a><small>${donor.total_donations} ${t("verified donations")}</small></td><td class="donor-actions"><details class="row-menu"><summary aria-label="${t("Actions")}"><i class="fi fi-rr-angle-small-down menu-chevron" aria-hidden="true"></i></summary><div><button class="row-btn" data-action="donor-history" data-id="${donor.id}">${t("History")}</button><button class="row-btn" data-action="edit-donor" data-id="${donor.id}">${t("Edit")}</button><button class="row-btn" data-action="delete-donor" data-id="${donor.id}">${t("Delete")}</button><button class="row-btn menu-toggle" role="switch" aria-checked="${Boolean(donor.is_available)}" data-action="toggle-donor" data-id="${donor.id}"><span>${t("Available")}</span><span class="menu-switch" aria-hidden="true"></span></button><button class="row-btn menu-toggle" role="switch" aria-checked="${Boolean(donor.status)}" data-action="approve-donor" data-id="${donor.id}"><span>${t("Active")}</span><span class="menu-switch" aria-hidden="true"></span></button></div></details></td></tr>`).join('') || t("<tr><td colspan=\"8\">No donors.</td></tr>");
    $('tableCount').textContent = `${result.meta.from || 0}–${result.meta.to || 0} / ${result.meta.total}`;
    $('pagination').innerHTML = `<button data-donor-page="${page-1}" ${page <= 1 ? 'disabled' : ''}>‹</button><button class="current" aria-current="page">${page}</button><button data-donor-page="${page+1}" ${page >= result.meta.last_page ? 'disabled' : ''}>›</button>`;
    const areas = [...new Set(donors.map(donor => donor.area).filter(Boolean))]; const selected = $('areaFilter').value;
    $('areaFilter').innerHTML = t("<option value=\"\">All areas</option>") + [...new Set([selected, ...areas].filter(Boolean))].map(area => `<option ${area === selected ? 'selected' : ''}>${esc(area)}</option>`).join('');
}
function renderTable() {clearTimeout(filterTimer); filterTimer = setTimeout(() => loadDonors().catch(error => showError(error.message)), 250);}
function openModal() {
    modal(t("New donor"), donorMarkup + locationFields(), async values => {
        const group = groups.find(group => group.name === values.group); if (!group) throw new Error(t("Select a blood group."));
        await send(routes.adminDonors, {name:values.name, phone:values.phone, blood_group_id:group.id, area:values.area, address:values.address, is_available:values.status === 'available', consent_to_contact:true, district_id:values.district_id || null, upazila_id:values.upazila_id || null, union_id:values.union_id || null});
        await loadDonors(); await refreshSummary();
    });
    bindLocations($('donorForm')).catch(error => showError(error.message));
    const actions = $('donorForm').querySelectorAll('.modal-actions'); if (actions.length > 1) actions[0].remove();
}
async function editDonor(id) {
    const donor = (await request(`${routes.adminDonors}/${id}`)).data;
    modal(t("Edit donor"), field(t("Name"),'name','text',donor.name)+field(t("Phone (enter to change number)"),'phone','tel',donor.phone || '',false)+groupSelect(donor.blood_group_id)+field(t("Area"),'area','text',donor.area,false)+field(t("Address"),'address','text',donor.address,false)+locationFields()+`<label><input type="checkbox" name="consent_to_contact" ${donor.consent_to_contact ? 'checked' : ''}> ${t("Consent to contact given")}</label>`, async values => {
        if (!values.phone) delete values.phone;
        await send(`${routes.adminDonors}/${id}`, {...values, consent_to_contact:values.consent_to_contact === 'on'}, 'PATCH'); await loadDonors(currentPage); await refreshSummary();
    });
    await bindLocations($('donorForm'),donor);
}
function openRequestForm() {
    modal(t("Blood requests"), groupSelect()+locationFields()+field(t("Patient name"),'patient_name')+field(t("Contact name"),'contact_name')+field(t("Contact phone"),'contact_phone','tel')+field(t("Hospital"),'hospital_name')+field(t("Hospital address"),'hospital_address','text','',false)+field(t("Number of bags"),'bags_needed','number',1)+field(t("Needed at"),'needed_at','datetime-local')+field(t("Expires at (optional)"),'expires_at','datetime-local','',false)+t("<div class=\"field\"><label>Urgency</label><select name=\"urgency\"><option value=\"normal\">Normal</option><option value=\"urgent\">Urgent</option><option value=\"emergency\">Very urgent</option><option value=\"critical\">Critical</option></select></div>"), async values => {
        await send(routes.requests, {...values, needed_at:new Date(values.needed_at).toISOString(), expires_at:values.expires_at ? new Date(values.expires_at).toISOString() : null}); await loadRequests(); await refreshSummary();
    });
    bindLocations($('donorForm')).catch(error => showError(error.message));
}
async function loadRequests(page = 1) {
    const result = await request(`${routes.requests}?page=${page}`);
    $('requestList').innerHTML = result.data.map((record, index) => {
        const initials = record.patient_name.trim().split(/\s+/u).slice(0,2).map(part => Array.from(part)[0]).join('').toLocaleUpperCase();
        const actions = `<button class="row-btn" data-action="request-details" data-id="${record.id}">${t("Details")}</button>` + (record.request_status === 'pending' ? `<button class="row-btn" data-action="verify-request" data-id="${record.id}">${t("Verify")}</button><button class="row-btn" data-action="cancel-request" data-id="${record.id}">${t("Cancel")}</button>` : ['verified','in_progress'].includes(record.request_status) ? `<button class="row-btn" data-action="assign-donor" data-id="${record.id}">${t("Assign donor")}</button><button class="row-btn" data-action="match" data-id="${record.id}">${t("Match donors")}</button><button class="row-btn" data-action="cancel-request" data-id="${record.id}">${t("Cancel")}</button>` : '');
        return `<tr><td>${result.meta.from + index}.</td><td><div class="donorcell"><span class="donoravatar" aria-hidden="true">${esc(initials)}</span><strong>${esc(record.patient_name)}</strong></div></td><td><span class="bloodpill">${esc(record.bloodGroup?.name)}</span></td><td>${esc(record.hospital_name)}<small>${esc(record.hospital_address || '')}</small></td><td>${record.bags_needed}</td><td><span class="status ${['verified','in_progress','completed'].includes(record.request_status) ? '' : 'busy'}">${esc(record.request_status.replaceAll('_',' '))}</span></td><td><span class="bloodpill request-urgency ${record.urgency === 'normal' ? 'normal' : ''}">${esc(record.urgency)}</span></td><td><a class="phone-link" href="tel:${esc(record.contact_phone)}">${esc(record.contact_phone)}</a><small>${esc(record.contact_name)}</small></td><td class="donor-actions"><details class="row-menu"><summary aria-label="${t("Actions")}"><i class="fi fi-rr-angle-small-down menu-chevron" aria-hidden="true"></i></summary><div>${actions}</div></details></td></tr>`;
    }).join('') || `<tr><td colspan="9" class="empty">${t("No requests.")}</td></tr>`;
    $('requestTableCount').textContent = `${result.meta.from || 0}–${result.meta.to || 0} / ${result.meta.total}`;
    $('requestPagination').innerHTML = `<button data-action="requests-page" data-id="${page-1}" aria-label="${t("Previous page")}" ${page <= 1 ? 'disabled' : ''}>‹</button><button class="current" aria-current="page">${page}</button><button data-action="requests-page" data-id="${page+1}" aria-label="${t("Next page")}" ${page >= result.meta.last_page ? 'disabled' : ''}>›</button>`;
}
const pageButtons = (meta, action) => `<div><button class="btn" data-action="${action}" data-id="${meta.current_page-1}" ${meta.current_page <= 1 ? 'disabled' : ''}>${t("Previous page")}</button> ${meta.current_page} / ${meta.last_page} <button class="btn" data-action="${action}" data-id="${meta.current_page+1}" ${meta.current_page >= meta.last_page ? 'disabled' : ''}>${t("Next page")}</button></div>`;
async function requestDetails(id) {
    const record = (await request(`${routes.requests}/${id}`)).data;
    const open = ['verified','in_progress'].includes(record.request_status);
    modal(t("Request details"), `<p>${esc(record.patient_name)} · ${esc(record.hospital_name)} · ${record.bags_needed} ${t("bags ·")} ${esc(record.request_status)} · ${esc(record.urgency)}</p><h3>${t("Matched donors")}</h3>` + record.request_donors.map(match => `<p>${esc(match.blood_donor?.name)} · ${esc(match.blood_donor?.phone)} · ${esc(match.response_status)} ${open && match.response_status !== 'donated' ? `<button type="button" class="btn" data-action="respond" data-request="${id}" data-id="${match.id}">${t("Response")}</button><button type="button" class="btn" data-action="record-donation" data-request="${id}" data-id="${match.blood_donor_id}">${t("Record donation")}</button>` : ''}</p>`).join('') + t("<h3>Status history</h3>") + record.status_logs.map(log => `<p>${esc(log.from_status || t("New"))} → ${esc(log.to_status)} · ${esc(log.created_at)}</p>`).join('') + t("<h3>Donation history</h3>") + record.donations.map(donation => `<p>#${donation.blood_donor_id} · ${esc(donation.donated_at?.slice(0,10))} · ${donation.bags} ${t("bags")}</p>`).join(''), async () => {});
    $('donorForm').querySelector('[type=submit]').hidden = true;
}
function recordDonation(donorId = '', requestId = '') {
    modal(t("Record donation"),field(t("Donor ID"),'blood_donor_id','number',donorId)+field(t("Requests ID (Optional)"),'blood_request_id','number',requestId,false)+field(t("Donation date"),'donated_at','date',new Intl.DateTimeFormat('en-CA',{timeZone:'Asia/Dhaka',year:'numeric',month:'2-digit',day:'2-digit'}).format(new Date()))+field(t("Hospital"),'hospital_name','text','',false), async values => {
        await send(routes.donations,{...values,blood_request_id:values.blood_request_id || null,bags:1}); await refreshSummary(); await loadDonors(); await loadRequests(); if (!extra.hidden) await loadDonations();
    });
}
async function loadSettings() {
    const result = await request(routes.settings);
    $('settingsList').innerHTML = result.data.map(setting => `<form data-setting="${setting.id}" style="margin:16px 0"><label>${esc(setting.key)} <input class="filter" name="value" value="${esc(setting.value)}"></label><button class="btn" type="submit">${t("Save")}</button></form>`).join('');
}
async function loadContacts(page = 1) {
    const result = await request(`${routes.contacts}?page=${page}`);
    const rows = result.data.map((contact, index) => {
        const requesterName = contact.requester_name || t("No information");
        const initials = requesterName.trim().split(/\s+/u).slice(0,2).map(part => Array.from(part)[0]).join('').toLocaleUpperCase();
        const actions = contact.contact_status === 'pending' ? `<button class="row-btn" data-action="contact-approved" data-id="${contact.id}">${t("Approve")}</button><button class="row-btn" data-action="contact-rejected" data-id="${contact.id}">${t("Reject")}</button>` : contact.contact_status === 'approved' ? `<button class="row-btn" data-action="contact-completed" data-id="${contact.id}">${t("Completed")}</button><button class="row-btn" data-action="contact-rejected" data-id="${contact.id}">${t("Reject")}</button>` : '';
        return `<tr><td>${result.from + index}.</td><td><div class="donorcell"><span class="donoravatar" aria-hidden="true">${esc(initials)}</span><strong>${esc(requesterName)}</strong></div></td><td><a class="phone-link" href="tel:${esc(contact.requester_phone || '')}">${esc(contact.requester_phone || t("No information"))}</a></td><td>${esc(contact.blood_donor?.name || t("No information"))}<small>${esc(contact.blood_donor?.phone || '')}</small></td><td>${esc(contact.message || t("No information"))}</td><td><span class="status ${['approved','completed'].includes(contact.contact_status) ? '' : 'busy'}">${esc(t(({pending:'Pending',approved:'Approved',rejected:'Rejected',completed:'Completed'})[contact.contact_status] || contact.contact_status))}</span></td><td class="donor-actions">${actions ? `<details class="row-menu"><summary aria-label="${t("Actions")}"><i class="fi fi-rr-angle-small-down menu-chevron" aria-hidden="true"></i></summary><div>${actions}</div></details>` : '—'}</td></tr>`;
    }).join('');
    extra.innerHTML = `<div class="dirheader"><div><h2>${t("Contact requests")}</h2></div></div><div class="tablewrap"><table><thead><tr><th scope="col">${t("Sl No.")}</th><th scope="col">${t("Contact name")}</th><th scope="col">${t("Contact phone")}</th><th scope="col">${t("Donor name")}</th><th scope="col">${t("Message")}</th><th scope="col">${t("Status")}</th><th scope="col">${t("Actions")}</th></tr></thead><tbody>${rows || `<tr><td colspan="7" class="empty">${t("No information")}</td></tr>`}</tbody></table></div><div class="tablefooter"><span>${result.from || 0}–${result.to || 0} / ${result.total}</span><div class="pagination"><button data-action="contacts-page" data-id="${result.current_page-1}" aria-label="${t("Previous page")}" ${result.current_page <= 1 ? 'disabled' : ''}>‹</button><button class="current" aria-current="page">${result.current_page}</button><button data-action="contacts-page" data-id="${result.current_page+1}" aria-label="${t("Next page")}" ${result.current_page >= result.last_page ? 'disabled' : ''}>›</button></div></div>`;
}
async function loadDonations(page = 1) {
    const result = await request(`${routes.donations}?page=${page}`);
    const rows = result.data.map((donation, index) => {
        const donorName = donation.bloodDonor?.name || t("No information");
        const initials = donorName.trim().split(/\s+/u).slice(0,2).map(part => Array.from(part)[0]).join('').toLocaleUpperCase();
        const isVerified = donation.donation_status === 'verified';
        return `<tr><td>${result.meta.from + index}.</td><td><div class="donorcell"><span class="donoravatar" aria-hidden="true">${esc(initials)}</span><strong>${esc(donorName)}</strong></div></td><td><span class="table-date">${esc(donation.donated_at?.slice(0,10) || t("No information"))}</span></td><td>${esc(donation.hospital_name || t("No information"))}</td><td>${esc(donation.bags)}</td><td><span class="status ${isVerified ? '' : 'busy'}">${isVerified ? t("Verified") : esc(donation.donation_status)}</span></td><td class="donor-actions">${donation.donation_status === 'pending' ? `<button class="row-btn" data-action="verify-donation" data-id="${donation.id}">${t("Verify donation")}</button>` : '—'}</td></tr>`;
    }).join('');
    extra.innerHTML = `<div class="dirheader"><div><h2>${t("Donation history")}</h2></div><button class="btn primary" data-action="new-donation"><i class="fi fi-rr-plus" aria-hidden="true"></i>${t("Record donation")}</button></div><div class="tablewrap"><table><thead><tr><th scope="col">${t("Sl No.")}</th><th scope="col">${t("Donor name")}</th><th scope="col">${t("Donation date")}</th><th scope="col">${t("Hospital")}</th><th scope="col">${t("Number of bags")}</th><th scope="col">${t("Status")}</th><th scope="col">${t("Actions")}</th></tr></thead><tbody>${rows || `<tr><td colspan="7" class="empty">${t("No information")}</td></tr>`}</tbody></table></div><div class="tablefooter"><span>${result.meta.from || 0}–${result.meta.to || 0} / ${result.meta.total}</span><div class="pagination"><button data-action="donations-page" data-id="${page-1}" ${page <= 1 ? 'disabled' : ''}>‹</button><button class="current" aria-current="page">${page}</button><button data-action="donations-page" data-id="${page+1}" ${page >= result.meta.last_page ? 'disabled' : ''}>›</button></div></div>`;
}
async function loadGroups() {
    groups = (await request(routes.groups)).data;
    await refreshSummary();
    extra.innerHTML = `<div class="tablewrap"><table><thead><tr><th scope="col">${t("Sl No.")}</th><th scope="col">${t("Blood group")}</th><th scope="col">${t("Number of donors")}</th><th scope="col">${t("Created date")}</th><th scope="col">${t("Created by")}</th></tr></thead><tbody>` + groups.map((group, index) => {
        const donorCount = summary.blood_groups.find(record => record.id === group.id)?.donors_count ?? 0;
        return `<tr><td>${index + 1}.</td><td><span class="bloodpill">${esc(group.name)}</span></td><td>${donorCount} ${t("people")}</td><td><span class="table-date">${esc(group.created_at ? new Intl.DateTimeFormat(document.documentElement.lang === "bn" ? "bn-BD" : "en-GB", {timeZone: "Asia/Dhaka", day: "2-digit", month: "short", year: "numeric"}).format(new Date(group.created_at)) : t("No information"))}</span></td><td>${esc(group.creator?.name || (group.created_by ? t("No information") : t("System")))}</td></tr>`;
    }).join('') + (groups.length ? '' : `<tr><td colspan="5">${t("No blood groups.")}</td></tr>`) + '</tbody></table></div>';
}
async function selectPage(page) {
    $('headingActions').hidden = ['dashboard','requests','groups','donations'].includes(page);
    $('requestHeadingActions').hidden = page !== 'requests';
    $('pageSubtitle').hidden = ['donors','requests','groups','donations'].includes(page);
    $('stats').style.display = ['donors','requests','groups','donations'].includes(page) ? 'none' : '';
    $('exportDonors').style.display = page === 'donors' ? 'none' : '';
    $('pageTitle').textContent = sections[page]; $('crumb').textContent = sections[page];
    document.querySelectorAll('.nav button').forEach(button => button.classList.toggle('active',button.dataset.page === page));
    $('dashboardExtra').classList.toggle('active',['dashboard','reports'].includes(page));
    $('directory').style.display = ['dashboard','donors'].includes(page) ? 'block' : 'none';
    $('requestsView').classList.toggle('active',page === 'requests'); $('settingsView').classList.toggle('active',page === 'settings');
    extra.hidden = !['contacts','donations','groups'].includes(page);
    extra.className = ['contacts','donations'].includes(page) ? 'panel directory' : 'panel panel-pad';
    $('sidebar').classList.remove('open');
    try {
        if (page === 'requests') await loadRequests();
        if (page === 'settings') await loadSettings();
        if (page === 'contacts') await loadContacts();
        if (page === 'donations') await loadDonations();
        if (page === 'groups') await loadGroups();
        if (page === 'reports') {await refreshSummary(); $('pageSubtitle').textContent = `${summary.donations} ${t("donations ·")} ${summary.bags_donated} ${t("bags of blood")}`;}
    } catch (error) {showError(error.message);}
}
async function exportCSV() {
    try {
        const rows = []; let page = 1, lastPage = 1;
        do {const result = await request(`${routes.adminDonors}?page=${page}`); rows.push(...result.data); lastPage = result.meta.last_page; page++;} while (page <= lastPage);
        const cell = value => '"' + String(value ?? '').replace(/^[=+@\-\t\r]/, "'$&").replaceAll('"','""') + '"';
        const csv = 'Name,Blood Group,Area,Phone,Active,Available\n' + rows.map(row => [row.name,row.bloodGroup?.name,row.area,row.phone,row.status,row.is_available].map(cell).join(',')).join('\n');
        const url = URL.createObjectURL(new Blob(['\ufeff',csv],{type:'text/csv;charset=utf-8'})); const anchor = document.createElement('a'); anchor.href = url; anchor.download = 'blood-donors.csv'; anchor.click(); URL.revokeObjectURL(url);
    } catch (error) {showError(error.message);}
}
$('donorForm').addEventListener('submit',async event => {
    event.preventDefault(); const button = event.submitter; if (!formAction) return; if (button) button.disabled = true;
    try {await formAction(Object.fromEntries(new FormData(event.target))); closeModal(); showSuccess(formSuccessMessage);}
    catch (error) {$('formError').textContent = error.message; showError(error.message);} finally {if (button) button.disabled = false;}
});
document.addEventListener('submit',async event => {
    const form = event.target.closest('[data-setting]'); if (!form) return; event.preventDefault();
    try {await send(`${routes.settings}/${form.dataset.setting}`,{value:new FormData(form).get('value')},'PATCH'); showSuccess(t("Settings saved."));} catch (error) {showError(error.message);}
});
document.addEventListener('click',async event => {
    const nav = event.target.closest('[data-page]'); if (nav) {selectPage(nav.dataset.page); return;}
    const page = event.target.closest('[data-donor-page]'); if (page) {loadDonors(Number(page.dataset.donorPage)).catch(error => showError(error.message)); return;}
    const button = event.target.closest('[data-action]'); if (!button) return;
    const {action,id} = button.dataset; button.disabled = true;
    try {
        if (action === 'donor-history') {const result = await request(`${routes.adminDonors}/${id}/history`); modal(t("Verified donation history"),result.data.map(row => `<p>${esc(row.donated_at?.slice(0,10))} · ${row.bags} ${t("bags ·")} ${esc(row.hospital_name)}</p>`).join('') || t("<p>No verified donations.</p>"),async () => {}); $('donorForm').querySelector('[type=submit]').hidden = true;}
        if (action === 'edit-donor') await editDonor(id);
        if (action === 'toggle-donor' || action === 'approve-donor') {
            const donor = donors.find(donor => donor.id === Number(id));
            await send(`${routes.adminDonors}/${id}${action === 'toggle-donor' ? '/availability' : ''}`,action === 'toggle-donor' ? {is_available:!donor.is_available} : {status:donor.status ? 0 : 1},'PATCH'); showSuccess(t(action === 'toggle-donor' ? 'Availability updated successfully.' : donor.status ? 'Donor marked inactive successfully.' : 'Donor approved successfully.')); await loadDonors(currentPage); await refreshSummary();
        }
        if (action === 'delete-donor' && await confirmAction(t("Delete this donor?"))) {await request(`${routes.adminDonors}/${id}`,{method:'DELETE'}); showSuccess(t('Donor deleted successfully.')); await loadDonors(); await refreshSummary();}
        if (action === 'verify-request') {await send(`${routes.requests}/${id}/verify`,{}); showSuccess(t('Blood request verified successfully.')); await loadRequests(); await refreshSummary();}
        if (action === 'verify-donation') {await send(`${routes.donations}/${id}/verify`,{}); showSuccess(t('Donation verified successfully.')); await loadDonations(); await loadDonors(); await loadRequests(); await refreshSummary();}
        if (action === 'assign-donor') {modal(t("Assign donor"),field(t("Donor ID"),'blood_donor_id','number'),async values => {await send(`${routes.requests}/${id}/assign-donor`,values); await loadRequests(); await refreshSummary();});}
        if (action === 'match') {await send(`${routes.requests}/${id}/matches`,{}); showSuccess(t('Donor matching completed successfully.')); await refreshSummary(); await loadRequests(); await requestDetails(id);}
        if (action === 'request-details') await requestDetails(id);
        if (action === 'cancel-request' && await confirmAction(t("Cancel this request?"))) {await send(`${routes.requests}/${id}`,{request_status:'cancelled'},'PATCH'); showSuccess(t('Blood request cancelled successfully.')); await loadRequests(); await refreshSummary();}
        if (action === 'record-donation') recordDonation(id,button.dataset.request);
        if (action === 'new-donation') recordDonation();
        if (action === 'respond') {
            const requestId = button.dataset.request;
            modal(t("Donor response"),t("<div class=\"field\"><select name=\"response_status\"><option value=\"contacted\">Contacted</option><option value=\"accepted\">Accepted</option><option value=\"declined\">Declined</option></select></div>")+field(t("Notes"),'notes','text','',false),async values => {await send(`${routes.requests}/${requestId}/matches/${id}`,values,'PATCH');});
        }
        if (action.startsWith('contact-')) {await send(`${routes.contacts}/${id}`,{contact_status:action.slice(8)},'PATCH'); showSuccess(t('Contact request status updated successfully.')); await loadContacts();}
        if (action === 'requests-page') await loadRequests(Number(id));
        if (action === 'contacts-page') await loadContacts(Number(id));
        if (action === 'donations-page') await loadDonations(Number(id));
    } catch (error) {showError(error.message);} finally {button.disabled = false;}
});
Object.assign(window,{showToast,closeModal,openModal,selectPage,renderTable,drawChart,exportCSV,openRequestForm});
$('today').textContent = new Intl.DateTimeFormat(document.documentElement.lang === 'bn' ? 'bn-BD' : 'en-GB',{timeZone:'Asia/Dhaka',day:'numeric',month:'long',year:'numeric'}).format(new Date());
$('weekday').textContent = new Intl.DateTimeFormat(document.documentElement.lang === 'bn' ? 'bn-BD' : 'en-GB',{timeZone:'Asia/Dhaka',weekday:'long'}).format(new Date());
$('globalSearch').addEventListener('input', event => { $('search').value = event.target.value; selectPage('donors'); renderTable(); });
try {groups = (await request(routes.groups)).data; await refreshSummary(); await loadDonors();}
catch (error) {showError(error.message); $('donorRows').innerHTML = `<tr><td colspan="8"><a href="${esc(routes.login)}">${t("Log in as administrator")}</a></td></tr>`;}

function closeDonorMenus() {
    document.querySelectorAll('.row-menu[open]').forEach(menu => {menu.open = false;});
}
document.addEventListener('click', event => {
    const trigger = event.target.closest('.row-menu > summary');
    if (!trigger) {
        closeDonorMenus();
        return;
    }
    event.preventDefault();
    const menu = trigger.parentElement;
    const wasOpen = menu.open;
    closeDonorMenus();
    if (wasOpen) return;
    menu.open = true;
    const dropdown = menu.querySelector(':scope > div');
    const anchor = trigger.getBoundingClientRect();
    const bounds = dropdown.getBoundingClientRect();
    const left = Math.max(8, Math.min(anchor.right - bounds.width, window.innerWidth - bounds.width - 8));
    const top = anchor.bottom + bounds.height + 8 <= window.innerHeight
        ? anchor.bottom + 6
        : Math.max(8, anchor.top - bounds.height - 6);
    dropdown.style.left = `${left}px`;
    dropdown.style.top = `${top}px`;
});
document.addEventListener('keydown', event => {if (event.key === 'Escape') closeDonorMenus();});
document.addEventListener('scroll', event => {
    if (!(event.target instanceof Element) || !event.target.closest('.row-menu > div')) closeDonorMenus();
}, true);
window.addEventListener('resize', closeDonorMenus);
