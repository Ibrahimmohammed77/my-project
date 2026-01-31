import { CardService } from '../services/CardService.js';
import { CardView } from '../views/CardView.js';
import { Toast } from '../../../core/ui/Toast.js';
import { DOM } from '../../../core/utils/dom.js';
import { debounce } from '../../../core/utils/performance.js';
import { showErrors, clearErrors } from '../../../utils/toast.js';

export class CardController {
    constructor() {
        this.items = [];
        this.view = new CardView();
        this.currentItem = null;
        this.isGroupContext = false;

        this.init();
    }

    init() {
        const groupIdInput = document.getElementById('group-id');
        this.isGroupContext = !!groupIdInput;
        this.groupId = groupIdInput?.value;

        this.attachEventListeners();
        
        if (this.isGroupContext && this.groupId) {
            this.loadGroupCards(this.groupId);
        } else if (!this.isGroupContext) {
            this.loadGroups();
        }
    }

    attachEventListeners() {
        const searchInput = DOM.query('#search');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(() => {
                this.filterAndRender();
            }, 300));
        }

        if (this.view.form) {
            this.view.form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit();
            });
        }
    }

    async loadGroups() {
        this.view.showLoading();
        try {
            this.items = await CardService.getAllGroups();
            this.filterAndRender();
        } catch (error) {
            console.error('Failed to load groups:', error);
            Toast.error('فشل تحميل مجموعات الكروت');
        } finally {
            this.view.hideLoading();
        }
    }

    async loadGroupCards(groupId) {
        this.view.showLoading();
        try {
            this.items = await CardService.getGroupCards(groupId);
            this.filterAndRender();
        } catch (error) {
            console.error('Failed to load cards:', error);
            Toast.error('فشل تحميل الكروت');
        } finally {
            this.view.hideLoading();
        }
    }

    filterAndRender() {
        const searchTerm = (DOM.query('#search')?.value || '').toLowerCase();
        
        const filtered = this.items.filter(item => {
            if (this.isGroupContext) {
                return !searchTerm || 
                    (item.card_number && item.card_number.toLowerCase().includes(searchTerm)) ||
                    (item.card_uuid && item.card_uuid.toLowerCase().includes(searchTerm));
            } else {
                return !searchTerm || 
                    (item.name && item.name.toLowerCase().includes(searchTerm)) ||
                    (item.description && item.description.toLowerCase().includes(searchTerm));
            }
        });

        if (this.isGroupContext) {
            this.view.renderCards(filtered);
        } else {
            this.view.renderGroups(filtered);
        }
    }

    showCreateModal() {
        this.currentItem = null;
        this.view.clearForm();
        this.view.openModal(this.isGroupContext ? 'إضافة كرت جديد' : 'إضافة مجموعة جديدة');
        clearErrors();
    }

    async editGroup(id) {
        const group = this.items.find(g => (g.id || g.group_id) == id);
        if (!group) return;

        this.currentItem = group;
        this.view.populateForm(group);
        this.view.openModal('تعديل مجموعة الكروت');
        clearErrors();
    }

    async editCard(id) {
        const card = this.items.find(c => (c.id || c.card_id) == id);
        if (!card) return;

        this.currentItem = card;
        this.view.populateForm(card);
        this.view.openModal('تعديل بيانات الكرت');
        clearErrors();
    }

    async deleteGroup(id) {
        if (!confirm('هل أنت متأكد من حذف هذه المجموعة؟')) return;

        try {
            await CardService.deleteGroup(id);
            this.items = this.items.filter(g => (g.id || g.group_id) != id);
            this.filterAndRender();
            Toast.success('تم حذف المجموعة بنجاح');
        } catch (error) {
            Toast.error('فشل حذف المجموعة');
        }
    }

    async deleteCard(id) {
        if (!confirm('هل أنت متأكد من حذف هذا الكرت؟')) return;

        try {
            await CardService.deleteCard(this.groupId, id);
            this.items = this.items.filter(c => (c.id || c.card_id) != id);
            this.filterAndRender();
            Toast.success('تم حذف الكرت بنجاح');
        } catch (error) {
            Toast.error('فشل حذف الكرت');
        }
    }

    async handleFormSubmit() {
        clearErrors();
        const formData = DOM.getFormData(this.view.form);
        this.view.disableForm();

        try {
            let result;
            if (this.isGroupContext) {
                // Individual Card
                if (this.currentItem) {
                    result = await CardService.updateCard(this.groupId, this.currentItem.id || this.currentItem.card_id, formData);
                    const idx = this.items.findIndex(c => (c.id || c.card_id) == (this.currentItem.id || this.currentItem.card_id));
                    if (idx !== -1) this.items[idx] = result;
                } else {
                    result = await CardService.createCard(this.groupId, formData);
                    this.items.unshift(result);
                }
            } else {
                // Card Group
                if (this.currentItem) {
                    result = await CardService.updateGroup(this.currentItem.id || this.currentItem.group_id, formData);
                    const idx = this.items.findIndex(g => (g.id || g.group_id) == (this.currentItem.id || this.currentItem.group_id));
                    if (idx !== -1) this.items[idx] = result;
                } else {
                    result = await CardService.createGroup(formData);
                    this.items.unshift(result);
                }
            }

            this.filterAndRender();
            this.view.closeModal();
            Toast.success('تم حفظ البيانات بنجاح');
        } catch (error) {
            console.error('Submit error:', error);
            if (error.response?.status === 422) {
                showErrors(error.response.data.errors);
            } else {
                Toast.error('حدث خطأ أثناء حفظ البيانات');
            }
        } finally {
            this.view.enableForm();
        }
    }
}

export default CardController;


