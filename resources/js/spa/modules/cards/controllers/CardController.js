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
        
        // Always load sub-branches (groups) logic if elements exist
        if (this.view.subBranchesContainer) {
            this.loadSubBranches();
        }

        if (this.isGroupContext && this.groupId) {
            this.loadGroupCards(this.groupId);
        } else {
            // "All Cards" view
            this.loadCards(); 
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
                this.handleFormSubmit(e);
            });
        }

        if (this.view.groupForm) {
            this.view.groupForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit(e);
            });
        }
    }

    async loadSubBranches() {
        // Reuse loadGroups logic but target the sub-branches view
        try {
            const groups = await CardService.getAllGroups();
            this.view.renderSubBranches(groups);
        } catch (error) {
            console.error('Failed to load sub-branches:', error);
            // Silent error or toast?
        }
    }

    async loadCards() {
        this.view.showLoading();
        try {
            const result = await CardService.getAllCards(); // Ensure this method exists or use generic list
            // If getAllCards doesn't exist, use listCards with empty params
            // Assuming listCards exists based on CardService usage elsewhere
            this.items = result.data || result; 
            this.filterAndRender();
        } catch (error) {
            console.error('Failed to load cards:', error);
            Toast.error('فشل تحميل الكروت');
        } finally {
            this.view.hideLoading();
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

    // ...

    async loadCards() {
        this.view.showLoading();
        try {
            const result = await CardService.getAllCards(); 
            this.items = result.data || result; 
            this.filterAndRender();
        } catch (error) {
            console.error('Failed to load cards:', error);
            Toast.error('فشل تحميل الكروت');
        } finally {
            this.view.hideLoading();
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
                 // Filtering Cards
                const cardNumber = String(item.card_number || '').toLowerCase();
                const cardUuid = String(item.card_uuid || '').toLowerCase();
                return !searchTerm || cardNumber.includes(searchTerm) || cardUuid.includes(searchTerm);
            } else {
                // Filtering Groups
                const name = String(item.name || '').toLowerCase();
                const description = String(item.description || '').toLowerCase();
                return !searchTerm || name.includes(searchTerm) || description.includes(searchTerm);
            }
        });

        if (this.isGroupContext) {
            this.view.renderCards(filtered);
        } else {
            // Use Grid Renderer for Groups
            this.view.renderSubBranches(filtered);
        }
    }

    showCreateModal() {
        this.currentItem = null;
        this.view.clearForm();
        this.view.openModal(this.isGroupContext ? 'إضافة كرت جديد' : 'إضافة مجموعة جديدة');
        clearErrors();
    }

    showCreateGroupModal() {
        this.currentItem = null;
        if (this.view.groupForm) this.view.groupForm.reset();
        this.view.openGroupModal('إضافة فرع فرعي جديد');
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

    async handleFormSubmit(e) { 
        // We need to know which form triggered submit, or we can check the active context/modal
        // Ideally, we attach separate listeners. But if we share one handler:
        
        clearErrors();
        // Check if group modal is open or if the event target is group form
        const isGroupForm = e && e.target && e.target.id === 'group-modal-form';
        
        if (isGroupForm) {
            const formData = DOM.getFormData(this.view.groupForm);
            // Disable group form... logic
            try {
                let result;
                if (this.currentItem) {
                     result = await CardService.updateGroup(this.currentItem.id || this.currentItem.group_id, formData);
                     // Update item in list if reusing same list, or reload sub-branches
                     this.loadSubBranches(); 
                } else {
                     result = await CardService.createGroup(formData);
                     // Reload sub-branches
                     this.loadSubBranches();
                }
                this.view.closeGroupModal();
                Toast.success('تم حفظ الفرع بنجاح');
            } catch (error) {
                 console.error('Submit group error:', error);
                 if (error.response?.status === 422) {
                    showErrors(error.response.data.errors);
                 } else {
                    Toast.error('حدث خطأ أثناء حفظ الفرع');
                 }
            } finally {
                // Enable form
            }
            return;
        }

        // Existing Card/Group Logic for main modal
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
                // Main modal used for Group? Not anymore if we have showCreateGroupModal
                // But keeping logic just in case
                if (this.currentItem) {
                    result = await CardService.updateGroup(this.currentItem.id || this.currentItem.group_id, formData);
                    // Update main list if this view serves groups
                } else {
                    result = await CardService.createGroup(formData);
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


