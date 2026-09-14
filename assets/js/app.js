const storedTheme = localStorage.getItem('dms-theme');
if (storedTheme) {
    document.documentElement.dataset.theme = storedTheme;
}

document.querySelector('[data-theme-toggle]')?.addEventListener('click', () => {
    const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.theme = nextTheme;
    localStorage.setItem('dms-theme', nextTheme);
});

const roleSelect = document.querySelector('[data-role-select]');
if (roleSelect) {
    const syncRoleFields = () => {
        const isStaff = roleSelect.value === 'staff';
        document.querySelectorAll('[data-student-field]').forEach((field) => field.hidden = isStaff);
        document.querySelectorAll('[data-staff-field]').forEach((field) => field.hidden = !isStaff);
    };
    roleSelect.addEventListener('change', syncRoleFields);
    syncRoleFields();
}

document.querySelectorAll('.timetable-board article[draggable="true"]').forEach((card) => {
    card.addEventListener('dragstart', () => card.classList.add('is-dragging'));
    card.addEventListener('dragend', () => card.classList.remove('is-dragging'));
});

const chart = document.getElementById('financeChart');
if (chart && window.Chart) {
    new Chart(chart, {
        type: 'bar',
        data: {
            labels: ['Revenue', 'Expenses'],
            datasets: [{
                label: 'Amount',
                data: [Number(chart.dataset.revenue || 0), Number(chart.dataset.expenses || 0)],
                backgroundColor: ['#E85D36', '#737373'],
                borderWidth: 0,
            }],
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } },
        },
    });
}

// Global password visibility toggle
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('input[type="password"]').forEach(input => {
        // Skip if already wrapped or has a toggle button (like in paystack_integration.php)
        if (input.nextElementSibling && input.nextElementSibling.tagName === 'BUTTON') return;
        
        // Wrap input
        const wrapper = document.createElement('div');
        wrapper.style.position = 'relative';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        
        // Add padding to input so text doesn't hide behind button
        input.style.paddingRight = '48px';
        
        // Create toggle button
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.setAttribute('aria-label', 'Toggle password visibility');
        btn.style.cssText = 'position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--muted); padding: 4px; display: flex; align-items: center; justify-content: center;';
        
        btn.innerHTML = `
            <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>
        `;
        
        btn.addEventListener('click', () => {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.querySelector('.eye-open').style.display = isPassword ? 'none' : 'block';
            btn.querySelector('.eye-closed').style.display = isPassword ? 'block' : 'none';
        });
        
        wrapper.appendChild(btn);
    });
});
