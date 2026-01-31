export class CardDetailView {
    constructor() {
        this.form = document.getElementById('link-albums-form');
        this.submitBtn = this.form?.querySelector('button[type="submit"]');
        this.originalContent = this.submitBtn?.innerHTML;
    }

    bindSubmit(handler) {
        if (!this.form) return;
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(this.form);
            const albumIds = formData.getAll('album_ids[]');
            handler(albumIds);
        });
    }

    setLoading(loading) {
        if (!this.submitBtn) return;
        if (loading) {
            this.submitBtn.disabled = true;
            this.submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin ms-2"></i> جاري الحفظ...';
            this.submitBtn.classList.add('opacity-75');
        } else {
            this.submitBtn.disabled = false;
            this.submitBtn.innerHTML = this.originalContent;
            this.submitBtn.classList.remove('opacity-75');
        }
    }
}
