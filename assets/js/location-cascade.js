/**
 * ─────────────────────────────────────────────────────────────────────────────
 * Location Cascade — Reusable Module
 * Usage: Call initLocationCascade(config) on DOMContentLoaded.
 *
 * Config options:
 *   ajaxUrl   : path to get_locations.php  (default: auto-detected)
 *   stateEl   : CSS selector for state <select>
 *   districtEl: CSS selector for district <select>
 *   cityEl    : CSS selector for city <select>   (optional)
 *   countryId : numeric country_id for states fetch (default: 1 = India)
 *
 * Example:
 *   initLocationCascade({
 *       stateEl   : '#state',
 *       districtEl: '#district',
 *       cityEl    : '#city'
 *   });
 * ─────────────────────────────────────────────────────────────────────────────
 */

/**
 * Detect the base URL to the ajax folder automatically.
 * Works from any depth of admin pages (admin/*, admin/sub/*, etc.)
 */
function _locationAjaxBase() {
    // Try to detect from a meta tag first (set in header.php if needed)
    const meta = document.querySelector('meta[name="base-url"]');
    if (meta) return meta.content.replace(/\/$/, '') + '/ajax/get_locations.php';

    // Fallback: walk up path segments to find root
    const parts = window.location.pathname.split('/').filter(Boolean);
    const dirs = ['admin', 'student', 'franchise', 'partner'];
    let idx = -1;
    for (let i = 0; i < dirs.length; i++) {
        idx = parts.indexOf(dirs[i]);
        if (idx >= 0) break;
    }

    let root = '';
    if (idx > 0) {
        root = '/' + parts.slice(0, idx).join('/');
    }
    
    return root + '/ajax/get_locations.php';
}

/**
 * Build a <select> option HTML string.
 */
function _buildOptions(items, valueKey, textKey, placeholder) {
    let html = `<option value="">${placeholder}</option>`;
    items.forEach(item => {
        html += `<option value="${item[valueKey]}">${item[textKey]}</option>`;
    });
    return html;
}

/**
 * Set a select to loading state.
 */
function _setLoading(el, msg) {
    el.innerHTML = `<option value="" disabled selected>${msg}</option>`;
    el.disabled = true;
}

/**
 * Reset a select to empty/placeholder state.
 */
function _resetSelect(el, placeholder) {
    el.innerHTML = `<option value="">${placeholder}</option>`;
    el.disabled = true;
}

/**
 * Fetch JSON from get_locations.php with given params.
 */
async function _fetchLocation(url, params) {
    const qs = new URLSearchParams(params).toString();
    const res = await fetch(`${url}?${qs}`);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
}

/**
 * Main initializer — call once per form.
 */
function initLocationCascade(config) {
    const ajaxUrl   = config.ajaxUrl   || _locationAjaxBase();
    const countryId = config.countryId || 1;

    const stateEl    = document.querySelector(config.stateEl);
    const districtEl = document.querySelector(config.districtEl);
    const cityEl     = config.cityEl ? document.querySelector(config.cityEl) : null;

    if (!stateEl || !districtEl) {
        console.warn('[Location Cascade] stateEl or districtEl not found.');
        return;
    }

    // ── State → Districts ────────────────────────────────────────────────────
    stateEl.addEventListener('change', async function () {
        const stateId = this.value;

        _resetSelect(districtEl, 'Select District');
        if (cityEl) _resetSelect(cityEl, 'Select City / Town');

        if (!stateId) return;

        _setLoading(districtEl, 'Loading districts…');
        try {
            const districts = await _fetchLocation(ajaxUrl, { type: 'districts', state_id: stateId });
            districtEl.innerHTML = _buildOptions(districts, 'id', 'name', 'Select District');
            districtEl.disabled = false;
        } catch (e) {
            districtEl.innerHTML = '<option value="">Failed to load</option>';
            console.error('[Location Cascade] Districts:', e);
        }
    });

    // ── District → Cities ────────────────────────────────────────────────────
    if (cityEl) {
        districtEl.addEventListener('change', async function () {
            const districtId = this.value;

            _resetSelect(cityEl, 'Select City / Town');
            if (!districtId) return;

            _setLoading(cityEl, 'Loading cities…');
            try {
                const cities = await _fetchLocation(ajaxUrl, { type: 'cities', district_id: districtId });
                cityEl.innerHTML = _buildOptions(cities, 'id', 'name', 'Select City / Town');
                cityEl.disabled = false;
            } catch (e) {
                cityEl.innerHTML = '<option value="">Failed to load</option>';
                console.error('[Location Cascade] Cities:', e);
            }
        });
    }

    // ── Load States on init ──────────────────────────────────────────────────
    return new Promise((resolve) => {
        (async () => {
            _setLoading(stateEl, 'Loading states…');
            try {
                const states = await _fetchLocation(ajaxUrl, { type: 'states', country_id: countryId });
                stateEl.innerHTML = _buildOptions(states, 'id', 'name', 'Select State');
                stateEl.disabled = false;
            } catch (e) {
                stateEl.innerHTML = '<option value="">Failed to load states</option>';
                console.error('[Location Cascade] States:', e);
            }
            resolve();
        })();
    });
}
