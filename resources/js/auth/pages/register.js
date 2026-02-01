import '../../bootstrap';
import { AuthService } from '../services/AuthService';

document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.getElementById('register-form');
    const submitBtn = document.getElementById('submit-btn');
    const errorMessage = document.getElementById('error-message');
    const errorText = errorMessage?.querySelector('.error-text');

    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Reset UI
            if (submitBtn) {
                submitBtn.disabled = true;
                const btnContent = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-xl"></i>';
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');

                // Store original content to restore later if needed
                submitBtn.dataset.originalContent = btnContent;
            }

            if (errorMessage) {
                errorMessage.classList.add('hidden');
                errorMessage.classList.remove('opacity-100', 'translate-y-0');
                errorMessage.classList.add('translate-y-2', 'opacity-0');
            }

            // Gather data
            const formData = new FormData(registerForm);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await AuthService.register(data);

                if (response.success) {
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="fa-solid fa-check ml-2 text-xl"></i> <span class="text-lg">تم بنجاح!</span>';
                        submitBtn.classList.remove('from-primary', 'to-primary-light');
                        submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                    }

                    setTimeout(() => {
                        window.location.href = response.redirect || '/dashboard';
                    }, 800);
                } else {
                    throw { response: { data: response } };
                }
            } catch (error) {
                // Show error
                let errorMsg = 'حدث خطأ في إنشاء الحساب';

                if (error.response?.data?.message) {
                    errorMsg = error.response.data.message;
                } else if (error.response?.data?.errors) {
                    errorMsg = Object.values(error.response.data.errors).flat().join('<br>');
                }

                if (errorText) errorText.innerHTML = errorMsg;
                if (errorMessage) {
                    errorMessage.classList.remove('hidden');

                    // Small delay for animation
                    setTimeout(() => {
                        errorMessage.classList.remove('translate-y-2', 'opacity-0');
                        errorMessage.classList.add('translate-y-0', 'opacity-100');
                    }, 10);
                }

                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.dataset.originalContent;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
            }
        });
    }
});
