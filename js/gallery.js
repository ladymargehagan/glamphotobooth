// Gallery Filtering and Interaction

document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.gallery-filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');
    const loadMoreBtn = document.getElementById('loadMoreBtn');

    // Filter functionality
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));

            // Add active class to clicked button
            this.classList.add('active');

            const filterValue = this.getAttribute('data-filter');
            let visibleCount = 0;

            galleryItems.forEach(item => {
                if (filterValue === 'all') {
                    item.classList.remove('hidden');
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    const category = item.getAttribute('data-category');
                    if (category === filterValue) {
                        item.classList.remove('hidden');
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.classList.add('hidden');
                        item.style.display = 'none';
                    }
                }
            });

            // Show/hide load more button based on visible items
            if (visibleCount > 12) {
                loadMoreBtn.style.display = 'inline-block';
            } else {
                loadMoreBtn.style.display = 'none';
            }
        });
    });

    // Load more functionality (simplified - in real app would load from server)
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            // Simulate loading more items
            this.innerHTML = '<span class="spinner"></span> Loading...';

            setTimeout(() => {
                this.textContent = 'Load More Events';
                if (window.showNotification) {
                    showNotification('All events loaded!', 'info');
                }
                // In production, this would trigger an AJAX call to load more items
            }, 1000);
        });
    }

    // View gallery button functionality
    const viewGalleryBtns = document.querySelectorAll('.view-gallery-btn');
    viewGalleryBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const galleryCard = this.closest('.gallery-card');
            const title = galleryCard.querySelector('h4').textContent;

            // In production, this would open a modal or navigate to a detailed gallery page
            if (window.showNotification) {
                showNotification(`Opening gallery: ${title}`, 'info');
            }

            // Simulate navigation to event details
            // window.location.href = `event-gallery.php?event=${encodeURIComponent(title)}`;
        });
    });

    // Animation on scroll for gallery items
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 50);
            }
        });
    }, observerOptions);

    galleryItems.forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(item);
    });
});
