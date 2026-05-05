/* assets/js/admin.js */
(function() {
    "use strict";

    /**
     * Helper functions
     */
    const select = (el, all = false) => {
        el = el.trim()
        if (all) {
            return [...document.querySelectorAll(el)]
        } else {
            return document.querySelector(el)
        }
    }

    const on = (type, el, listener, all = false) => {
        if (all) {
            select(el, all).forEach(e => e.addEventListener(type, listener))
        } else {
            let element = select(el, all);
            if(element) element.addEventListener(type, listener)
        }
    }

    /**
     * Back to top button
     */
    let backtotop = document.querySelector('.back-to-top')
    if (backtotop) {
        const toggleBacktotop = () => {
            if (window.scrollY > 100) {
                backtotop.classList.add('active')
            } else {
                backtotop.classList.remove('active')
            }
        }
        window.addEventListener('load', toggleBacktotop)
        document.addEventListener('scroll', toggleBacktotop)
    }

    /**
     * Sidebar toggle
     */
    if (select('.toggle-sidebar-btn')) {
        on('click', '.toggle-sidebar-btn', function(e) {
            select('body').classList.toggle('toggle-sidebar')
        })
    }

    /**
     * Search bar toggle
     */
    if (select('.search-bar-toggle')) {
        on('click', '.search-bar-toggle', function(e) {
            select('.search-bar').classList.toggle('search-bar-show')
        })
    }

    /**
     * Smart Active Link & Menu Auto-Expansion
     */
    const currentPath = window.location.pathname.split('/').pop() || 'index.php';
    const navLinks = document.querySelectorAll('.sidebar-nav .nav-link, .sidebar-nav .nav-content a');
    
    navLinks.forEach(link => {
        const linkPath = link.getAttribute('href').split('/').pop();
        if (linkPath === currentPath) {
            link.classList.add('active');
            link.classList.remove('collapsed');
            
            // Switch sub-menu icon to solid
            const icon = link.querySelector('.nav-icon-sub');
            if (icon) {
                icon.classList.replace('far', 'fas');
            }

            // Expand parent menu
            const parentUl = link.closest('.nav-content');
            if (parentUl) {
                parentUl.classList.add('show');
                const toggleBtn = document.querySelector(`[data-bs-target="#${parentUl.id}"]`);
                if (toggleBtn) {
                    toggleBtn.classList.remove('collapsed');
                }
            }
        }
    });

    /**
     * Global Delete Confirmation (Standardized Premium UI)
     */
    window.confirmDelete = function(options) {
        const modalEl = document.getElementById('globalDeleteModal');
        if (!modalEl) return;
        
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        const confirmBtn = document.getElementById('globalDeleteConfirmBtn');
        const targetText = document.getElementById('globalDeleteTarget');
        const warningText = document.getElementById('globalDeleteText');

        // Reset button state BEFORE cloning
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = 'Yes, Delete Now';
        
        targetText.textContent = options.target || 'this record';
        warningText.textContent = options.message || 'You are about to delete this record. This action might be irreversible.';
        
        // Remove old listeners by replacing the button
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        newConfirmBtn.addEventListener('click', function() {
            newConfirmBtn.disabled = true;
            newConfirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';
            if (typeof options.onConfirm === 'function') {
                options.onConfirm(modal, newConfirmBtn);
            }
        });
        
        modal.show();
    };

    document.addEventListener('click', function(e) {
        const button = e.target.closest('.btn-delete');
        if (button) {
            e.preventDefault();
            const url = button.getAttribute('href');
            const target = button.getAttribute('data-name') || 'this item';
            
            window.confirmDelete({
                target: target,
                onConfirm: function(modal, btn) {
                    window.location.href = url;
                }
            });
        }
    });

})();

/**
 * ─────────────────────────────────────────────────────────────
 * Global Table Sorting Engine
 * Usage: add class "table-sortable" to any <table>
 * Skip a column from sorting: add data-no-sort to the <th>
 * ─────────────────────────────────────────────────────────────
 */
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('table.table-sortable').forEach(function (table) {
        const headers = table.querySelectorAll('thead th');

        headers.forEach(function (th, colIndex) {
            // Skip S.No. (first col) and Action (last col) and any data-no-sort col
            if (th.hasAttribute('data-no-sort') || colIndex === 0 || colIndex === headers.length - 1) {
                return;
            }

            th.classList.add('sortable-col');
            th.setAttribute('data-sort', 'none');

            // Add sort icon wrapper — inline style prevents th's uppercase/letter-spacing from breaking FA glyphs
            const icon = document.createElement('span');
            icon.classList.add('sort-icon');
            icon.setAttribute('aria-hidden', 'true');
            icon.style.cssText = 'text-transform:none!important;letter-spacing:0!important;font-family:"Font Awesome 6 Free"!important;';
            icon.innerHTML = '<i class="fas fa-sort" aria-hidden="true"></i>';
            th.appendChild(icon);

            th.addEventListener('click', function () {
                const currentSort = th.getAttribute('data-sort');
                const newSort = currentSort === 'asc' ? 'desc' : 'asc';

                // Reset all other headers in this table
                headers.forEach(function (h) {
                    if (h !== th) {
                        h.setAttribute('data-sort', 'none');
                        const si = h.querySelector('.sort-icon');
                        if (si) si.innerHTML = '<i class="fas fa-sort"></i>';
                    }
                });

                th.setAttribute('data-sort', newSort);
                icon.innerHTML = newSort === 'asc'
                    ? '<i class="fas fa-sort-up"></i>'
                    : '<i class="fas fa-sort-down"></i>';

                sortTable(table, colIndex, newSort);
            });
        });
    });

    function sortTable(table, colIndex, direction) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        const sorted = rows.sort(function (a, b) {
            const aText = getCellText(a, colIndex);
            const bText = getCellText(b, colIndex);

            // Try numeric sort
            const aNum = parseFloat(aText.replace(/[^0-9.-]/g, ''));
            const bNum = parseFloat(bText.replace(/[^0-9.-]/g, ''));

            if (!isNaN(aNum) && !isNaN(bNum)) {
                return direction === 'asc' ? aNum - bNum : bNum - aNum;
            }

            // Try date sort
            const aDate = new Date(aText);
            const bDate = new Date(bText);
            if (!isNaN(aDate) && !isNaN(bDate)) {
                return direction === 'asc' ? aDate - bDate : bDate - aDate;
            }

            // Default string sort
            return direction === 'asc'
                ? aText.localeCompare(bText)
                : bText.localeCompare(aText);
        });

        // Re-append sorted rows
        sorted.forEach(function (row, i) {
            // Update S.No. in first cell if it's numeric
            const firstCell = row.querySelector('td:first-child');
            if (firstCell && /^\d+$/.test(firstCell.textContent.trim())) {
                firstCell.textContent = i + 1;
            }
            tbody.appendChild(row);
        });
    }

    function getCellText(row, colIndex) {
        const cell = row.querySelectorAll('td')[colIndex];
        if (!cell) return '';
        // Get text but ignore icons/badges if plain text exists
        return cell.innerText.trim().toLowerCase();
    }
});
