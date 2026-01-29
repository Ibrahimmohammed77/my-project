import '../../bootstrap';
import { AuthService } from '../services/AuthService';

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    const submitBtn = document.getElementById('submit-btn');
    const errorMessage = document.getElementById('error-message');
    const errorText = errorMessage?.querySelector('.error-text');

    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Reset state
            if(submitBtn) {
                submitBtn.disabled = true;
                // Store original content
                if (!submitBtn.dataset.originalContent) {
                    submitBtn.dataset.originalContent = submitBtn.innerHTML;
                }
                submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';
            }
            if(errorMessage) errorMessage.classList.add('hidden');
            
            const formData = {
                login: document.getElementById('login').value,
                password: document.getElementById('password').value,
                remember: document.getElementById('remember').checked
            };
            
            try {
                const response = await AuthService.login(formData);
                
                if (response.success) {
                    if(submitBtn) {
                        submitBtn.innerHTML = '<i class="fa-solid fa-check ml-2"></i> تم بنجاح';
                        submitBtn.classList.remove('bg-primary');
                        submitBtn.classList.add('bg-green-600');
                    }
                    
                    setTimeout(() => {
                        window.location.href = response.redirect || '/dashboard';
                    }, 500);
                }
            } catch (error) {
                // Show error
                let errorMsg = 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.';
                
                if (error.response?.data?.message) {
                    errorMsg = error.response.data.message;
                } else if (error.response?.data?.errors) {
                    errorMsg = Object.values(error.response.data.errors).flat().join('<br>');
                }
                
                if(errorText) errorText.innerHTML = errorMsg;
                if(errorMessage) errorMessage.classList.remove('hidden');
                
                // Reset button
                if(submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.dataset.originalContent;
                }
            }
        });
    }
});
