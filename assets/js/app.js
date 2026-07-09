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
