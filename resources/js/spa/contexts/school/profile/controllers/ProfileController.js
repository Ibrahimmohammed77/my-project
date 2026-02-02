import { ProfileService } from '../services/ProfileService.js';
import { ProfileView } from '../views/ProfileView.js';
import { Toast } from '../../../../core/ui/Toast.js';
import { InputValidator } from '../../../../core/security/InputValidator.js';

export class ProfileController {
    constructor() {
        this.view = new ProfileView();
        this.init();
    }

    init() {
        this.view.bindSubmit(this.handleSubmit.bind(this));
    }

    async handleSubmit(data) {
        // Basic Validation
        const errors = {};
        const name = data instanceof FormData ? data.get('name') : data.name;

        if (!InputValidator.validateRequired(name)) {
            errors.name = ['اسم المدرسة مطلوب'];
        }

        if (Object.keys(errors).length > 0) {
            import('../../../../utils/toast.js').then(({ showErrors }) => showErrors(errors));
            return;
        }

        try {
            this.view.setLoading(true);

            // Handle Method Spoofing for File Uploads in PHP
            if (data instanceof FormData) {
                data.append('_method', 'PUT');
            } else {
                // Should use FormData ideally, but fallback just in case
                data = { ...data, _method: 'PUT' };
            }

            const response = await ProfileService.update(data);

            if (response.data.success) {
                Toast.success(response.data.message || 'تم تحديث الملف الشخصي بنجاح');
            }
        } catch (error) {
            if (error.response?.status === 422) {
                import('../../../../utils/toast.js').then(({ showErrors }) => showErrors(error.response.data.errors));
            } else {
                Toast.error(error.response?.data?.message || 'خطأ في تحديث البيانات');
            }
        } finally {
            this.view.setLoading(false);
        }
    }
}

