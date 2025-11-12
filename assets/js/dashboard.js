// Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Mobile sidebar toggle
    const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    const dashboardSidebar = document.getElementById('dashboardSidebar');

    if (mobileSidebarToggle && dashboardSidebar) {
        mobileSidebarToggle.addEventListener('click', function() {
            dashboardSidebar.classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 1024) {
                if (!event.target.closest('.dashboard-sidebar') &&
                    !event.target.closest('.mobile-sidebar-toggle')) {
                    dashboardSidebar.classList.remove('active');
                }
            }
        });
    }

    // Active navigation highlighting
    const currentPath = window.location.pathname.split('/').pop();
    document.querySelectorAll('.sidebar-nav a').forEach(link => {
        const linkPath = link.getAttribute('href');
        if (linkPath === currentPath) {
            // Remove active class from all links
            document.querySelectorAll('.sidebar-nav a').forEach(l => l.classList.remove('active'));
            // Add active class to current link
            link.classList.add('active');
        }
    });

    // Notification dropdown (simplified)
    const notificationBtn = document.querySelector('.notification-btn');
    if (notificationBtn) {
        notificationBtn.addEventListener('click', function() {
            // In production, this would open a notification dropdown
            if (window.showNotification) {
                showNotification('You have 5 new notifications!', 'info');
            }
        });
    }

    // Animate stats on load
    animateStats();

    // Table row interactions
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on a button or link
            if (!e.target.closest('button') && !e.target.closest('a')) {
                const bookingId = this.querySelector('strong')?.textContent;
                if (bookingId && window.showNotification) {
                    showNotification(`Opening details for ${bookingId}`, 'info');
                }
            }
        });
    });

    // Auto-refresh data every 30 seconds (simplified)
    // In production, this would fetch fresh data from the server
    setInterval(function() {
        console.log('Checking for updates...');
        // updateDashboardData();
    }, 30000);
});

// Animate stat numbers
function animateStats() {
    const statNumbers = document.querySelectorAll('.stat-details h3');

    statNumbers.forEach(stat => {
        const finalValue = parseInt(stat.textContent.replace(/,/g, ''));
        if (isNaN(finalValue)) return;

        let currentValue = 0;
        const increment = finalValue / 30; // 30 frames
        const duration = 1000; // 1 second
        const frameTime = duration / 30;

        const counter = setInterval(() => {
            currentValue += increment;
            if (currentValue >= finalValue) {
                stat.textContent = finalValue.toLocaleString();
                clearInterval(counter);
            } else {
                stat.textContent = Math.floor(currentValue).toLocaleString();
            }
        }, frameTime);
    });
}

// Helper function to format dates
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// Helper function to format currency
function formatCurrency(amount) {
    return `GH₵ ${parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })}`;
}

// Export functionality for reports (placeholder)
function exportData(format) {
    if (window.showNotification) {
        showNotification(`Exporting data as ${format.toUpperCase()}...`, 'info');
    }
    // In production, this would trigger actual data export
}

// Filter table data (placeholder)
function filterTableData(filterValue) {
    console.log('Filtering by:', filterValue);
    // In production, this would filter the table rows
}

// Confirm before canceling booking
function confirmCancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking?')) {
        // In production, send cancellation request to server
        if (window.showNotification) {
            showNotification('Booking cancellation request sent', 'success');
        }
    }
}
