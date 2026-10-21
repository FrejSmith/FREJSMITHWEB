const resolveApiUrl = (path) => {
    const base = (window.API_BASE_URL || '').replace(/\/$/, '');
    const normalizedPath = path.startsWith('/') ? path : `/${path}`;
    return `${base}${normalizedPath}`;
};

const reviewForm = document.getElementById('reviewForm');
if (reviewForm) {
    reviewForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const messageDiv = document.getElementById('formMessage');

        messageDiv.style.padding = '12px';
        messageDiv.style.marginBottom = '16px';
        messageDiv.style.borderRadius = '6px';
        messageDiv.style.display = 'none';

        fetch(resolveApiUrl('api.php?action=submit-review'), {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            messageDiv.style.display = 'block';
            if (data.success) {
                messageDiv.style.backgroundColor = '#d1fae5';
                messageDiv.style.color = '#065f46';
                messageDiv.style.border = '1px solid #a7f3d0';
                messageDiv.textContent = data.message;
                reviewForm.reset();
            } else {
                messageDiv.style.backgroundColor = '#fee2e2';
                messageDiv.style.color = '#991b1b';
                messageDiv.style.border = '1px solid #fecaca';
                messageDiv.textContent = data.message;
            }
        })
        .catch(error => {
            messageDiv.style.display = 'block';
            messageDiv.style.backgroundColor = '#fee2e2';
            messageDiv.style.color = '#991b1b';
            messageDiv.style.border = '1px solid #fecaca';
            messageDiv.textContent = 'Erreur lors de l\'envoi: ' + error;
        });
    });
}
