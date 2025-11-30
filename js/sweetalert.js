/**
 * SweetAlert2 Utility Functions
 * js/sweetalert.js
 * Wrapper functions for common SweetAlert2 patterns
 */

/**
 * Show success alert
 */
function showSuccessAlert(title = 'Success', message = '', redirectUrl = null) {
    Swal.fire({
        title: title,
        text: message,
        icon: 'success',
        confirmButtonColor: '#5c9ead',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed && redirectUrl) {
            window.location.href = redirectUrl;
        }
    });
}

/**
 * Show error alert
 */
function showErrorAlert(title = 'Error', message = '', redirectUrl = null) {
    Swal.fire({
        title: title,
        text: message,
        icon: 'error',
        confirmButtonColor: '#d32f2f',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed && redirectUrl) {
            window.location.href = redirectUrl;
        }
    });
}

/**
 * Show info alert
 */
function showInfoAlert(title = 'Info', message = '', redirectUrl = null) {
    Swal.fire({
        title: title,
        text: message,
        icon: 'info',
        confirmButtonColor: '#5c9ead',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed && redirectUrl) {
            window.location.href = redirectUrl;
        }
    });
}

/**
 * Show warning alert
 */
function showWarningAlert(title = 'Warning', message = '', redirectUrl = null) {
    Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        confirmButtonColor: '#ff9800',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed && redirectUrl) {
            window.location.href = redirectUrl;
        }
    });
}

/**
 * Show confirmation dialog
 */
function showConfirmAlert(title = 'Confirm', message = '', onConfirm = null, onCancel = null) {
    Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#5c9ead',
        cancelButtonColor: '#999',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
        } else if (result.isDismissed && typeof onCancel === 'function') {
            onCancel();
        }
    });
}

/**
 * Show alert and redirect
 */
function showAlertAndRedirect(title = 'Success', message = '', redirectUrl) {
    Swal.fire({
        title: title,
        text: message,
        icon: 'success',
        confirmButtonColor: '#5c9ead',
        confirmButtonText: 'OK',
        allowOutsideClick: false,
        allowEscapeKey: false
    }).then(() => {
        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    });
}

/**
 * Show temporary success toast (top-right corner)
 */
function showToastSuccess(message = 'Success') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: 'success',
        title: message
    });
}

/**
 * Show temporary error toast (top-right corner)
 */
function showToastError(message = 'Error') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: 'error',
        title: message
    });
}

/**
 * Show temporary info toast (top-right corner)
 */
function showToastInfo(message = 'Info') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: 'info',
        title: message
    });
}
