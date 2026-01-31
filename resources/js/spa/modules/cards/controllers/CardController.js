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
        this.hasCardsTable = false;
        this.hasGroupsGrid = false;

        this.init();
    }

    init() {
        const groupIdInput = document.getElementById('group-id');
        this.isGroupContext = !!groupIdInput;
        this.groupId = groupIdInput?.value;

        // Detect View Type
        this.hasCardsTable = !!document.getElementById('cards-tbody');
        this.hasGroupsGrid = !!document.getElementById('sub-branches-grid');

        this.attachEventListeners();
        
        if (this.hasGroupsGrid && !this.hasCardsTable) {
            // Groups Index View
            this.loadGroups();
        } else if (this.hasCardsTable) {
            // Cards Index View (Group or All)
            if (this.isGroupContext && this.groupId) {
                this.loadGroupCards(this.groupId);
            } else {
                this.loadCards(); 
            }
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
            if (this.hasGroupsGrid && !this.hasCardsTable) {
                 // Filtering Groups
                const name = String(item.name || '').toLowerCase();
                const description = String(item.description || '').toLowerCase();
                return !searchTerm || name.includes(searchTerm) || description.includes(searchTerm);
            } else {
                 // Filtering Cards
                const cardNumber = String(item.card_number || '').toLowerCase();
                const cardUuid = String(item.card_uuid || '').toLowerCase();
                return !searchTerm || cardNumber.includes(searchTerm) || cardUuid.includes(searchTerm);
            }
        });

        if (this.hasGroupsGrid && !this.hasCardsTable) {
            this.view.renderSubBranches(filtered);
        } else {
            this.view.renderCards(filtered);
        }
    }

    showCreateModal() {
        this.currentItem = null;
        this.view.clearForm();
        this.view.openModal(this.isGroupContext ? 'إضافة كرت فرعي جديد' : 'إضافة كرت جديد');
        clearErrors();
    }

    showCreateGroupModal() {
        this.currentItem = null;
        if (this.view.groupForm) this.view.groupForm.reset();
        // If we don't have a separate group modal in this view, we'll use the main one but change title
        if (this.view.groupModal) {
            this.view.openGroupModal('إضافة فرع فرعي جديد');
        } else {
            // Re-use main modal for group creation if separate one doesn't exist
            this.view.openModal('إضافة فرع فرعي جديد');
        }
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
        clearErrors();
        
        // Handle explicit Group Modal (if used separately, e.g. for sub-branches inside card view? not currently used)
        const isExplicitGroupForm = e && e.target && e.target.id === 'group-modal-form';
        if (isExplicitGroupForm) {
            this.handleGroupFormSubmit();
            return;
        }

        // Main Modal (#modal-form) Handling
        // Determine context based on View
        if (this.hasGroupsGrid && !this.hasCardsTable) {
            // Groups Index View -> creating/updating Global Groups
            await this.handleGroupFormSubmit(this.view.form);
        } else {
            // Cards Index View -> creating/updating Cards
            await this.handleCardFormSubmit(this.view.form);
        }
    }

    async handleGroupFormSubmit(formElement = null) {
        const form = formElement || this.view.groupForm;
        const formData = DOM.getFormData(form);
        
        if (form === this.view.form) this.view.disableForm();

        try {
            let result;
            const id = this.currentItem ? (this.currentItem.id || this.currentItem.group_id) : null;
            
            if (id) {
                 result = await CardService.updateGroup(id, formData);
            } else {
                 result = await CardService.createGroup(formData);
            }

            // Reload Groups
            this.loadGroups();

            if (form === this.view.form) {
                this.view.closeModal();
            } else {
                this.view.closeGroupModal();
            }
            Toast.success(id ? 'تم تحديث المجموعة بنجاح' : 'تم إضافة المجموعة بنجاح');
        } catch (error) {
             console.error('Submit group error:', error);
             if (error.response?.status === 422) {
                showErrors(error.response.data.errors);
             } else {
                Toast.error('حدث خطأ أثناء حفظ المجموعة');
             }
        } finally {
            if (form === this.view.form) this.view.enableForm();
        }
    }

    async handleCardFormSubmit(form) {
        const formData = DOM.getFormData(form);
        this.view.disableForm();

        try {
            let result;
            const id = this.currentItem ? (this.currentItem.id || this.currentItem.card_id) : null;

            if (id) {
                // Update
                if (this.isGroupContext) {
                    result = await CardService.updateCard(this.groupId, id, formData);
                } else {
                     // Update in All Cards view - assuming card carries its group_id or we use a generic update if available
                     // But CardService.updateCard requires groupId. 
                     const gId = this.currentItem.card_group_id || this.currentItem.group?.id || this.currentItem.group?.group_id;
                     if (gId) {
                        result = await CardService.updateCard(gId, id, formData);
                     } else {
                         throw new Error("Card Group ID missing for update");
                     }
                }
            } else {
                // Create
                if (this.isGroupContext) {
                    result = await CardService.createCard(this.groupId, formData);
                } else {
                     Toast.warning("Creating card from All Cards view require selecting a group (Feature pending UI update)");
                     return;
                }
            }
            
            // Reload list
            if (this.isGroupContext) {
                this.loadGroupCards(this.groupId);
            } else {
                this.loadCards();
            }

            this.view.closeModal();
            // Success message handled by Toast inside service or here
            Toast.success(id ? 'تم تحديث الكرت بنجاح' : 'تم إضافة الكرت بنجاح');
        } catch (error) {
            console.error('Submit card error:', error);
            if (error.response?.status === 422) {
                showErrors(error.response.data.errors);
            } else {
                Toast.error('حدث خطأ أثناء حفظ الكرت');
            }
        } finally {
            this.view.enableForm();
        }
    }
}

export default CardController;
