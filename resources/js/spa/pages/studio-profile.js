import ApiClient from '../core/api/ApiClient.js';
import { Toast } from '../components/Toast.js';

document.getElementById('profile-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await ApiClient.put('/studio/profile', data);
        if (response.data.success) {
            Toast.success(response.data.message);
        }
    } catch (error) {
        Toast.error(error.response?.data?.message || 'خطأ في تحديث البيانات');
    }
});
