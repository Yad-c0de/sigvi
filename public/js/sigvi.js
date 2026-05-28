/* ================================================
   SIGVI – Install D | sigvi.js
   Sidebar, Modales, Toast, Table Search
   ================================================ */

/* ---- Sidebar toggle ---- */
function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebarOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('show');
    document.body.style.overflow = '';
}

/* ---- Modal SIGVI ---- */
function openModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('open');
    document.body.style.overflow = '';
    // Si el modal tiene una función de cierre personalizada definida en data-close-fn, ejecutarla
    const closeFn = el.getAttribute('data-close-fn');
    if (closeFn && typeof window[closeFn] === 'function') {
        window[closeFn]();
    }
}

/* ❌ Ya NO cerramos el modal al hacer clic en el overlay (se eliminó el event listener) */

/* Escape cierra modales abiertos (ejecutando la función personalizada si existe) */
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.sigvi-modal-overlay.open').forEach(m => {
            closeModal(m.id); // Usamos closeModal para que respete data-close-fn
        });
    }
});

/* ---- Toast ---- */
function showToast(msg, type = 'success') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    const icons = {
        success: 'bi-check-circle-fill',
        error:   'bi-x-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info:    'bi-info-circle-fill'
    };
    const colors = {
        success: '#16a34a', error: '#dc2626',
        warning: '#d97706', info:  '#0284c7'
    };
    const t = document.createElement('div');
    t.className = `toast-msg ${type}`;
    t.innerHTML = `
        <i class="bi ${icons[type]||icons.success}"
           style="color:${colors[type]||colors.success};font-size:18px;flex-shrink:0"></i>
        <span style="flex:1">${msg}</span>
        <button onclick="this.parentElement.remove()"
            style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:18px;padding:0 0 0 8px;line-height:1">&times;</button>`;
    container.appendChild(t);
    setTimeout(() => { t.style.transition='opacity .4s'; t.style.opacity='0'; }, 3500);
    setTimeout(() => t.remove(), 3900);
}

/* ---- Búsqueda en tabla ---- */
function tableSearch(inputId, tableId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll(`#${tableId} tbody tr`).forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}

/* ---- Confirmar eliminación ---- */
function confirmDelete(form, name) {
    if (confirm(`¿Eliminar "${name}"?\n\nEsta acción no se puede deshacer.`)) {
        form.submit();
    }
}

/* ---- Init ---- */
document.addEventListener('DOMContentLoaded', function() {
    /* Auto-cerrar alertas después de 5s */
    document.querySelectorAll('.alert:not(.alert-permanent)').forEach(a => {
        setTimeout(() => {
            a.style.transition = 'opacity .4s';
            a.style.opacity = '0';
            setTimeout(() => a.remove(), 400);
        }, 5000);
    });

    /* Table search: si hay elementos search+table en la página */
    if (document.getElementById('searchInput') && document.getElementById('mainTable')) {
        tableSearch('searchInput', 'mainTable');
    }
    if (document.getElementById('searchInputMobile') && document.getElementById('mainTable')) {
        tableSearch('searchInputMobile', 'mainTable');
    }
});
