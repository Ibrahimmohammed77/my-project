export class CardRedemptionView {
    /**
     * Render card redemption form
     */
    renderRedemptionForm(container) {
        if (!container) return;

        container.innerHTML = `
            <div class="card-redemption-container">
                <div class="redemption-form-wrapper">
                    <h2>استخدام الكرت</h2>
                    <p class="text-muted">أدخل رقم الكرت للحصول على الوصول إلى الألبوم</p>
                    
                    <form id="redemption-form">
                        <div class="form-group">
                            <label>رقم الكرت</label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   name="card_number" 
                                   placeholder="أدخل رقم الكرت"
                                   required
                                   autocomplete="off">
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-check"></i> استخدام الكرت
                        </button>
                    </form>
                </div>

                <div id="redemption-result" class="redemption-result"></div>
            </div>
        `;
    }

    /**
     * Show redemption success message
     */
    showSuccess(data, container) {
        if (!container) return;

        const card = data.card;
        const library = data.storage_library;
        const album = data.hidden_album;

        container.innerHTML = `
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <h4>تم استخدام الكرت بنجاح!</h4>
                <p>يمكنك الآن الوصول إلى الألبوم</p>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h5>معلومات الكرت</h5>
                    <ul class="info-list">
                        <li><strong>رقم الكرت:</strong> ${card.card_number}</li>
                        <li><strong>مكتبة التخزين:</strong> ${library?.name || 'غير محدد'}</li>
                        <li><strong>الألبوم:</strong> ${album?.name || 'غير محدد'}</li>
                        <li><strong>تاريخ الاستخدام:</strong> ${new Date(card.activation_date).toLocaleDateString('ar')}</li>
                    </ul>
                    
                    <a href="/my-cards" class="btn btn-primary mt-3">
                        عرض كروتي
                    </a>
                </div>
            </div>
        `;
    }

    /**
     * Render user's cards
     */
    renderMyCards(cards, container) {
        if (!container) return;

        if (!cards || cards.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-credit-card fa-3x mb-3"></i>
                    <p>لا توجد كروت مستخدمة</p>
                    <a href="/cards/redeem" class="btn btn-primary">
                        <i class="fas fa-plus"></i> استخدام كرت جديد
                    </a>
                </div>
            `;
            return;
        }

        const html = `
            <div class="my-cards-grid">
                ${cards.map(card => `
                    <div class="card-item" data-id="${card.card_id}">
                        <div class="card-header">
                            <h5>${card.card_number}</h5>
                            <span class="badge badge-${this.getStatusBadgeClass(card.status?.code)}">
                                ${card.status?.name || 'نشط'}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="card-info">
                                <div class="info-row">
                                    <i class="fas fa-folder"></i>
                                    <span>${card.storage_library?.name || 'غير محدد'}</span>
                                </div>
                                <div class="info-row">
                                    <i class="fas fa-images"></i>
                                    <span>${card.storage_library?.hidden_album?.name || 'غير محدد'}</span>
                                </div>
                                <div class="info-row">
                                    <i class="fas fa-calendar"></i>
                                    <span>تاريخ الاستخدام: ${new Date(card.activation_date).toLocaleDateString('ar')}</span>
                                </div>
                                ${card.expiry_date ? `
                                    <div class="info-row">
                                        <i class="fas fa-clock"></i>
                                        <span>ينتهي في: ${new Date(card.expiry_date).toLocaleDateString('ar')}</span>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-sm btn-outline-primary view-album" 
                                    data-album-id="${card.storage_library?.hidden_album?.album_id}">
                                <i class="fas fa-eye"></i> عرض الألبوم
                            </button>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;

        container.innerHTML = html;
    }

    /**
     * Get badge class based on status
     */
    getStatusBadgeClass(statusCode) {
        switch(statusCode) {
            case 'ACTIVE': return 'success';
            case 'EXPIRED': return 'danger';
            case 'USED': return 'info';
            default: return 'secondary';
        }
    }
}

export default CardRedemptionView;
