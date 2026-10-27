import {t} from './donation-http.js';
import {routes, request, escapeHtml as esc} from './donation-http.js';
export const locationFields = () => ['district_id','upazila_id','union_id'].map((name,index) => `<div class="field"><label>${[t("District"),t("Upazila"),t("Union")][index]}</label><select name="${name}" data-location="${name}"><option value="">${t("Select (optional)")}</option></select></div>`).join('');
export async function bindLocations(form, values = {}) {
    const district = form.querySelector('[data-location="district_id"]');
    const upazila = form.querySelector('[data-location="upazila_id"]');
    const union = form.querySelector('[data-location="union_id"]');
    if (!district) return;
    const fill = (field, rows, selected) => {field.innerHTML = t("<option value=\"\">Select (optional)</option>") + rows.map(row => `<option value="${row.id}" ${Number(selected) === row.id ? 'selected' : ''}>${esc((document.documentElement.lang === 'bn' ? row.name_bn || row.name : row.name))}</option>`).join('');};
    fill(upazila,[]); fill(union,[]);
    fill(district,(await request(routes.locations)).data,values.district_id);
    if (values.district_id) fill(upazila,(await request(`${routes.locations}?district_id=${values.district_id}`)).data,values.upazila_id);
    if (values.upazila_id) fill(union,(await request(`${routes.locations}?upazila_id=${values.upazila_id}`)).data,values.union_id);
    district.onchange = async () => {district.setCustomValidity('');fill(upazila,[]); fill(union,[]); if (district.value) {try {const id = district.value; const result = await request(`${routes.locations}?district_id=${id}`); if (district.value === id) fill(upazila,result.data);} catch (error) {district.setCustomValidity(error.message); district.reportValidity();}}};
    upazila.onchange = async () => {upazila.setCustomValidity('');fill(union,[]); if (upazila.value) {try {const id = upazila.value; const result = await request(`${routes.locations}?upazila_id=${id}`); if (upazila.value === id) fill(union,result.data);} catch (error) {upazila.setCustomValidity(error.message); upazila.reportValidity();}}};
}
