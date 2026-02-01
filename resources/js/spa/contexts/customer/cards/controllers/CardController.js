import CustomerCardService from '../services/CustomerCardService.js';
import CardView from '../views/CardView.js';

export default class CardController {
    constructor() {
        this.service = new CustomerCardService();
        this.view = new CardView();
        this.currentPage = 1;
        this.searchTerm = '';
    }

    init() {
        this.loadCards();
        this.bindEvents();

        // Register global functions
        window.showCreateModal = () => this.openCreate();
        window.editCard = (id) => this.openEdit(id);
        window.deleteCard = (id) => this.delete(id);
        window.linkAlbums = (id) => this.openLinkAlbums(id);
        window.closeModal = () => this.view.closeModal();
        window.closeLinkModal = () => this.view.closeLinkModal();
    }

    bindEvents() {
        // Search
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchTerm = e.target.value;
                this.currentPage = 1;
                this.loadCards();
            });
        }

        // Form submission
        const form = document.getElementById('card-form');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.save();
            });
        }

        // Link albums form
        const linkForm = document.getElementById('link-albums-form');
        if (linkForm) {
            linkForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveLinkAlbums();
            });
        }
    }

    async loadCards() {
        try {
            const response = await this.service.getCards(this.currentPage, this.searchTerm);
            this.view.renderTable(response.data.cards);
            this.view.renderPagination(response.data.pagination, (page) => {
                this.currentPage = page;
                this.loadCards();
            });
        } catch (error) {
            this.view.showToast('حدث خطأ أثناء تحميل الكروت', 'error');
        }
    }

    openCreate() {
        this.view.openModal('إنشاء كرت جديد');
        document.getElementById('card-form').reset();
        document.getElementById('card-id').value = '';
    }

    async openEdit(id) {
        try {
            const response = await this.service.getCard(id);
            const card = response.data.card;

            this.view.openModal('تعديل الكرت');
            document.getElementById('card-id').value = card.card_id;
            document.getElementById('title').value = card.title;
            document.getElementById('is_active').checked = card.is_active;
        } catch (error) {
            this.view.showToast('حدث خطأ أثناء تحميل بيانات الكرت', 'error');
        }
    }

    async save() {
        const cardId = document.getElementById('card-id').value;
        const data = {
            title: document.getElementById('title').value,
            is_active: document.getElementById('is_active').checked ? 1 : 0,
        };

        try {
            if (cardId) {
                await this.service.updateCard(cardId, data);
                this.view.showToast('تم تحديث الكرت بنجاح', 'success');
            } else {
                await this.service.createCard(data);
                this.view.showToast('تم إنشاء الكرت بنجاح', 'success');
            }

            this.view.closeModal();
            this.loadCards();
        } catch (error) {
            this.view.showToast(error.message || 'حدث خطأ أثناء حفظ الكرت', 'error');
        }
    }

    async delete(id) {
        if (!confirm('هل أنت متأكد من حذف هذا الكرت؟')) {
            return;
        }

        try {
            await this.service.deleteCard(id);
            this.view.showToast('تم حذف الكرت بنجاح', 'success');
            this.loadCards();
        } catch (error) {
            this.view.showToast('حدث خطأ أثناء حذف الكرت', 'error');
        }
    }

    async openLinkAlbums(id) {
        try {
            // Load card and available albums
            const [cardResponse, albumsResponse] = await Promise.all([
                this.service.getCard(id),
                this.service.getAvailableAlbums()
            ]);

            const card = cardResponse.data.card;
            const albums = albumsResponse.data.albums;

            this.view.openLinkModal(card, albums);
            document.getElementById('link-card-id').value = id;
        } catch (error) {
            this.view.showToast('حدث خطأ أثناء تحميل البيانات', 'error');
        }
    }

    async saveLinkAlbums() {
        const cardId = document.getElementById('link-card-id').value;
        const selectedAlbums = Array.from(
            document.querySelectorAll('input[name="album_ids[]"]:checked')
        ).map(cb => parseInt(cb.value));

        if (selectedAlbums.length === 0) {
            this.view.showToast('الرجاء اختيار ألبوم واحد على الأقل', 'error');
            return;
        }

        try {
            await this.service.linkAlbums(cardId, selectedAlbums);
            this.view.showToast('تم ربط الألبومات بالكرت بنجاح', 'success');
            this.view.closeLinkModal();
            this.loadCards();
        } catch (error) {
            this.view.showToast('حدث خطأ أثناء ربط الألبومات', 'error');
        }
    }
}
