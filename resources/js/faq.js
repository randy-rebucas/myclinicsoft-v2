document.addEventListener('DOMContentLoaded', function() {
    const faqButtons = document.querySelectorAll('[data-faq-button]');
    
    faqButtons.forEach(button => {
        button.addEventListener('click', () => {
            const answer = button.querySelector('[data-faq-answer]');
            const icon = button.querySelector('[data-faq-icon]');
            
            // Toggle current FAQ
            answer.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
            
            // Close other FAQs
            faqButtons.forEach(otherButton => {
                if (otherButton !== button) {
                    const otherAnswer = otherButton.querySelector('[data-faq-answer]');
                    const otherIcon = otherButton.querySelector('[data-faq-icon]');
                    otherAnswer.classList.add('hidden');
                    otherIcon.classList.remove('rotate-180');
                }
            });
        });
    });
}); 