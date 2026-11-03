import {t, routes, request, escapeHtml as esc} from './donation-http.js';

export const locationFields = () => ['division_id', 'district_id', 'upazila_id', 'union_id'].map((name, index) => `<div class="field"><label>${[t("Division"), t("District"), t("Upazila"), t("Union")][index]}</label><select name="${name}" data-location="${name}" ${index ? 'disabled' : ''}><option value="">${t("Select (optional)")}</option></select></div>`).join('');

export async function bindLocations(form, values = {}) {
    const names = ['division_id', 'district_id', 'upazila_id', 'union_id'];
    const fields = names.map(name => form.querySelector(`[data-location="${name}"]`));
    if (fields.some(field => !field)) return;
    const fill = (field, rows = [], selected = '') => {
        field.setCustomValidity('');
        field.innerHTML = `<option value="">${t("Select (optional)")}</option>` + rows.map(row => `<option value="${row.id}" ${String(selected) === String(row.id) ? 'selected' : ''}>${esc(document.documentElement.lang === 'bn' ? row.name_bn || row.name : row.name)}</option>`).join('');
        field.disabled = rows.length === 0;
    };
    let revision = 0;
    fields.forEach(field => fill(field));
    fields.forEach((field, index) => {
        field.onchange = async () => {
            const currentRevision = ++revision;
            field.setCustomValidity('');
            fields.slice(index + 1).forEach(child => fill(child));
            if (!field.value || index === fields.length - 1) return;
            try {
                const result = await request(`${routes.locations}?${names[index]}=${encodeURIComponent(field.value)}`);
                if (currentRevision === revision) fill(fields[index + 1], result.data);
            } catch (error) {
                if (currentRevision !== revision) return;
                field.setCustomValidity(error.message);
                field.reportValidity();
            }
        };
    });
    const initialRevision = revision;
    const divisions = (await request(`${routes.locations}?type=divisions`)).data;
    if (revision !== initialRevision) return;
    let divisionId = values.division_id;
    if (!divisionId && values.district_id) {
        const districts = (await request(routes.locations)).data;
        if (revision !== initialRevision) return;
        divisionId = districts.find(district => String(district.id) === String(values.district_id))?.division_id;
    }
    fill(fields[0], divisions, divisionId);
    const selections = [divisionId, values.district_id, values.upazila_id, values.union_id];
    for (let index = 0; index < fields.length - 1; index++) {
        if (!fields[index].value) break;
        const result = await request(`${routes.locations}?${names[index]}=${encodeURIComponent(fields[index].value)}`);
        if (revision !== initialRevision) return;
        fill(fields[index + 1], result.data, selections[index + 1]);
    }
}
