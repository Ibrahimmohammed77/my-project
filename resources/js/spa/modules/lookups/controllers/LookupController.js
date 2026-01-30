import { LookupService } from '../services/LookupService.js';
import { LookupView } from '../views/LookupView.js';
import { Toast } from '../../../core/ui/Toast.js';

export class LookupController {
    constructor() {
        this.view = new LookupView();
        this.masters = [];
        this.init();
    }

    init() {
        if (this.view.searchInput) this.view.searchInput.addEventListener('input', () => this.filterAndRender());
        if (this.view.valueForm) {
            this.view.valueForm.addEventListener('submit', (e) => this.handleValueSubmit(e));
        }
        this.loadLookups();
    }

    async loadLookups() {
        this.view.showLoading();
        try {
            this.masters = await LookupService.getAll();
            this.view.hideLoading();
            this.filterAndRender();
        } catch (error) {
            this.view.showError();
        }
    }

    filterAndRender() {
        const searchTerm = this.view.searchInput?.value.toLowerCase() || '';
        const filtered = this.masters.filter(m => 
            m.name.toLowerCase().includes(searchTerm) || 
            m.code.toLowerCase().includes(searchTerm)
        );
        this.view.renderLookups(filtered);
    }

    editMaster(id) {
        const master = this.masters.find(m => m.id === id);
        if (!master) return;

        this.view.openModal(`إدارة القيم: ${master.name}`, master.id);
        this.view.renderValuesTable(master.values || []);
    }

    editValue(valId) {
        const masterId = parseInt(this.view.masterIdInput.value);
        const master = this.masters.find(m => m.id === masterId);
        if (!master) return;

        const val = master.values.find(v => v.id === valId);
        if (val) this.view.populateValueForm(val);
    }

    async deleteValue(valId) {
        if (!confirm('هل أنت متأكد من حذف هذه القيمة؟')) return;
        Toast.info('جاري الحذف...');
        try {
            await LookupService.deleteValue(valId);
            await this.loadLookups();
            
            // Refresh modal table
            const masterId = parseInt(this.view.masterIdInput.value);
            const master = this.masters.find(m => m.id === masterId);
            if (master) this.view.renderValuesTable(master.values || []);
            
            Toast.success('تم الحذف بنجاح');
        } catch (error) {
            Toast.error('حدث خطأ أثناء الحذف');
        }
    }

    async handleValueSubmit(e) {
        e.preventDefault();
        const valId = this.view.valueIdInput.value;
        const masterId = this.view.masterIdInput.value;

        const formData = {
            lookup_master_id: masterId,
            name: document.getElementById('value-name').value,
            code: document.getElementById('value-code').value,
            description: document.getElementById('value-description').value,
            is_active: true
        };

        try {
            if (valId) {
                await LookupService.updateValue(valId, formData);
                Toast.success('تم التحديث بنجاح');
            } else {
                await LookupService.createValue(formData);
                Toast.success('تم الإضافة بنجاح');
            }
            
            this.view.resetValueForm();
            await this.loadLookups();
            
            // Refresh modal table
            const master = this.masters.find(m => m.id === parseInt(masterId));
            if (master) this.view.renderValuesTable(master.values || []);
            
        } catch (error) {
            Toast.error(error.response?.data?.message || 'حدث خطأ أثناء الحفظ');
        }
    }

    closeModal() {
        this.view.closeModal();
    }
}

export default LookupController;
