import { showToast } from '../components/toast';

document.addEventListener('DOMContentLoaded', () => {
    const profileForm = document.querySelector('#school-profile-form');

    if (profileForm) {
        profileForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(profileForm);
            // Since we're using PUT but Laravel needs POST with _method or actual PUT
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('/school/profile', {
                    method: 'POST', // Use POST with _method: PUT for easier handling or check controller
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ ...data, _method: 'PUT' })
                });

                const result = await response.json();
                if (result.success) {
                    showToast(result.message || 'تم تحديث بيانات المدرسة بنجاح');
                } else {
                    showToast(result.message || 'فشل تحديث البيانات', 'error');
                }
            } catch (error) {
                showToast('خطأ في الاتصال بالخادم', 'error');
            }
        });
    }
});
