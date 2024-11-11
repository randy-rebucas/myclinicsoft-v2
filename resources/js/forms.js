// Form validation and submission
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.querySelector('#contact-form');
    
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Basic validation
            const name = this.querySelector('#name').value.trim();
            const email = this.querySelector('#email').value.trim();
            const subject = this.querySelector('#subject').value;
            const message = this.querySelector('#message').value.trim();
            
            let errors = [];
            
            if (name.length < 2) errors.push('Name is required');
            if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) errors.push('Valid email is required');
            if (!subject) errors.push('Please select a subject');
            if (message.length < 10) errors.push('Message must be at least 10 characters');
            
            if (errors.length > 0) {
                showFormErrors(errors);
                return;
            }
            
            try {
                const response = await submitForm(this);
                showSuccess('Message sent successfully!');
                this.reset();
            } catch (error) {
                showFormErrors(['Failed to send message. Please try again.']);
            }
        });
    }
});

function showFormErrors(errors) {
    const errorDiv = document.querySelector('#form-errors');
    errorDiv.innerHTML = errors.map(error => `<p class="text-red-600">${error}</p>`).join('');
    errorDiv.classList.remove('hidden');
}

function showSuccess(message) {
    const successDiv = document.querySelector('#form-success');
    successDiv.textContent = message;
    successDiv.classList.remove('hidden');
    setTimeout(() => successDiv.classList.add('hidden'), 5000);
}

async function submitForm(form) {
    const formData = new FormData(form);
    const response = await fetch('/api/contact', {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });
    
    if (!response.ok) throw new Error('Network response was not ok');
    return response.json();
} 