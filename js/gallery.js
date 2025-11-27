/**
 * Gallery JavaScript
 * js/gallery.js
 * Handles drag-and-drop upload and photo management
 */

document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    const photoInput = document.getElementById('photoInput');
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadProgress = document.getElementById('uploadProgress');
    const errorMsg = document.getElementById('errorMessage');
    const successMsg = document.getElementById('successMessage');

    if (!dropZone || !photoInput) return;

    let selectedFiles = [];

    // Click to select
    dropZone.addEventListener('click', function() {
        photoInput.click();
    });

    // File input change
    photoInput.addEventListener('change', function(e) {
        selectedFiles = Array.from(e.target.files);
        updateUploadButton();
    });

    // Drag and drop
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', function() {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        selectedFiles = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
        updateUploadButton();
    });

    function updateUploadButton() {
        uploadBtn.disabled = selectedFiles.length === 0;
        if (selectedFiles.length > 0) {
            uploadBtn.textContent = 'Upload ' + selectedFiles.length + ' Photo' + (selectedFiles.length !== 1 ? 's' : '');
        } else {
            uploadBtn.textContent = 'Upload Selected Photos';
        }
    }

    // Upload handler
    uploadBtn.addEventListener('click', function() {
        if (selectedFiles.length === 0) return;

        uploadPhotos(selectedFiles);
    });

    function uploadPhotos(files) {
        const formData = new FormData();
        formData.append('gallery_id', window.galleryId);
        formData.append('csrf_token', window.csrfToken);

        // Add files
        files.forEach(file => {
            formData.append('photos[]', file);
        });

        uploadBtn.disabled = true;
        uploadBtn.textContent = 'Uploading...';
        uploadProgress.innerHTML = '';
        hideMessage(errorMsg);
        hideMessage(successMsg);

        // Create file list in UI
        files.forEach((file, index) => {
            const progressItem = document.createElement('div');
            progressItem.className = 'progress-item';
            progressItem.id = 'progress-' + index;
            progressItem.innerHTML = `
                <span>${file.name}</span>
                <div class="progress-bar">
                    <div class="progress-bar-fill" style="width: 0%"></div>
                </div>
            `;
            uploadProgress.appendChild(progressItem);
        });

        // Upload
        const xhr = new XMLHttpRequest();

        // Progress tracking
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                const avgProgress = percentComplete;

                files.forEach((file, index) => {
                    const progressBar = document.querySelector('#progress-' + index + ' .progress-bar-fill');
                    if (progressBar) {
                        progressBar.style.width = Math.round(avgProgress) + '%';
                    }
                });
            }
        });

        xhr.addEventListener('load', function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        showMessage(successMsg, `Successfully uploaded ${response.uploaded_count} photo${response.uploaded_count !== 1 ? 's' : ''}!`);
                        selectedFiles = [];
                        photoInput.value = '';
                        updateUploadButton();

                        // Reload page to show new photos
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showMessage(errorMsg, response.message || 'Upload failed');
                        updateUploadButton();
                    }
                } catch (e) {
                    showMessage(errorMsg, 'Error processing response');
                    updateUploadButton();
                }
            } else {
                showMessage(errorMsg, 'Upload failed (HTTP ' + xhr.status + ')');
                updateUploadButton();
            }
        });

        xhr.addEventListener('error', function() {
            showMessage(errorMsg, 'Network error during upload');
            updateUploadButton();
        });

        xhr.open('POST', window.siteUrl + '/actions/upload_photos_action.php');
        xhr.send(formData);
    }

    function showMessage(element, text) {
        if (!element) return;
        element.textContent = text;
        element.classList.add('show');
    }

    function hideMessage(element) {
        if (!element) return;
        element.classList.remove('show');
    }
});

/**
 * Delete photo function
 */
function deletePhoto(photoId) {
    showConfirmAlert(
        'Delete Photo',
        'Are you sure you want to delete this photo?',
        function() {
            const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || window.csrfToken;
            const formData = new FormData();
            formData.append('photo_id', photoId);
            formData.append('csrf_token', csrfToken);

            fetch(window.siteUrl + '/actions/delete_photo_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove photo element
                    event.target.closest('.photo-item').style.animation = 'fadeOut 0.3s';
                    setTimeout(() => {
                        event.target.closest('.photo-item').remove();
                        // Reload if no photos left
                        const items = document.querySelectorAll('.photo-item');
                        if (items.length === 0) {
                            location.reload();
                        }
                    }, 300);
                } else {
                    showErrorAlert('Error', data.message || 'Failed to delete photo');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorAlert('Error', 'Error deleting photo');
            });
        }
    );
}
