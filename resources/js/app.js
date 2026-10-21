import './bootstrap';

document.querySelector('[data-menu-toggle]')?.addEventListener('click', () => {
    document.querySelector('[id="sidebar"]')?.classList.toggle('open');
});

document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

document.querySelector('[data-print]')?.addEventListener('click', () => window.print());

const tenantSelect = document.querySelector('[data-tenant-select]');
const invoiceSelect = document.querySelector('[data-invoice-select]');

if (tenantSelect && invoiceSelect) {
    const filterInvoices = () => {
        Array.from(invoiceSelect.options).forEach((option) => {
            option.hidden = Boolean(option.dataset.tenant) && option.dataset.tenant !== tenantSelect.value;
        });
        if (invoiceSelect.selectedOptions[0]?.hidden) {
            invoiceSelect.value = '';
        }
    };
    tenantSelect.addEventListener('change', filterInvoices);
    filterInvoices();
}

const serviceSelect = document.querySelector('select[name="servicio_id"]');
const invoiceAmount = document.querySelector('input[name="monto"]');

if (serviceSelect && invoiceAmount) {
    serviceSelect.addEventListener('change', () => {
        const amount = serviceSelect.selectedOptions[0]?.dataset.amount;
        if (amount && !invoiceAmount.value) {
            invoiceAmount.value = amount;
        }
    });
}
