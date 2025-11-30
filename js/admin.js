/**
 * Admin Dashboard JavaScript
 * js/admin.js
 * Handles chart rendering and admin interactions
 */

// Initialize Chart.js defaults
if (typeof Chart !== 'undefined') {
    Chart.defaults.color = '#666';
    Chart.defaults.font.family = "'Montserrat', sans-serif";
}

/**
 * Format currency value
 */
function formatCurrency(amount) {
    return '₵' + parseFloat(amount).toFixed(2);
}

/**
 * Format date to readable format
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Format time to readable format
 */
function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Get status badge class
 */
function getStatusClass(status, type = 'order') {
    const statusMap = {
        order: {
            'pending': 'status-pending',
            'paid': 'status-paid',
            'failed': 'status-failed',
            'refunded': 'status-refunded'
        },
        booking: {
            'pending': 'status-pending',
            'confirmed': 'status-confirmed',
            'completed': 'status-completed',
            'cancelled': 'status-cancelled',
            'rejected': 'status-cancelled'
        },
        role: {
            'admin': 'role-admin',
            'photographer': 'role-photographer',
            'vendor': 'role-vendor',
            'customer': 'role-customer'
        }
    };

    return statusMap[type][status] || 'status-pending';
}

/**
 * Create a simple bar chart
 */
function createBarChart(canvasId, labels, data, title) {
    const ctx = document.getElementById(canvasId).getContext('2d');
    if (!ctx) return;

    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: title,
                data: data,
                backgroundColor: 'rgba(92, 154, 173, 0.7)',
                borderColor: 'rgba(92, 154, 173, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

/**
 * Create a line chart
 */
function createLineChart(canvasId, labels, data, title) {
    const ctx = document.getElementById(canvasId).getContext('2d');
    if (!ctx) return;

    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: title,
                data: data,
                borderColor: 'rgba(92, 154, 173, 1)',
                backgroundColor: 'rgba(92, 154, 173, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

/**
 * Create a doughnut/pie chart
 */
function createDoughnutChart(canvasId, labels, data, title) {
    const ctx = document.getElementById(canvasId).getContext('2d');
    if (!ctx) return;

    const colors = [
        '#ff9800',
        '#4caf50',
        '#f44336',
        '#2196f3',
        '#ff5722',
        '#9c27b0'
    ];

    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors.slice(0, labels.length),
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

/**
 * Animate a number from 0 to target value
 */
function animateNumber(element, target, duration = 1000) {
    const startValue = 0;
    const startTime = Date.now();

    function update() {
        const currentTime = Date.now();
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const value = Math.floor(startValue + (target - startValue) * progress);

        element.textContent = value;

        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }

    update();
}

/**
 * Show notification
 */
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 4px;
        background: ${type === 'success' ? '#4caf50' : '#f44336'};
        color: white;
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

/**
 * Add notification styles
 */
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

/**
 * Export table to CSV
 */
function exportTableToCSV(tableId, filename = 'export.csv') {
    const table = document.getElementById(tableId);
    if (!table) return;

    let csv = [];
    const rows = table.querySelectorAll('tr');

    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = Array.from(cols).map(col => {
            // Remove any HTML and trim
            return col.textContent.trim().replace(/"/g, '""');
        });
        csv.push('"' + rowData.join('","') + '"');
    });

    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
}

/**
 * Print table
 */
function printTable(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const printWindow = window.open('', '_blank');
    printWindow.document.write(table.outerHTML);
    printWindow.print();
}

/**
 * Confirm action dialog
 */
function confirmAction(message, onConfirm, onCancel = null) {
    if (confirm(message)) {
        if (typeof onConfirm === 'function') {
            onConfirm();
        }
    } else {
        if (typeof onCancel === 'function') {
            onCancel();
        }
    }
}

/**
 * Debounce function for search/filter
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
