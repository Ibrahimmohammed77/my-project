export default class CardView {
    renderTable(cards) {
        const tbody = document.getElementById('cardsTableBody');
        if (!tbody) return;

        if (!cards || cards.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i class="fa-solid fa-qrcode text-4xl mb-3 opacity-30"></i>
                        <p>لا توجد كروت بعد</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = cards.map(card => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-qrcode text-purple-600"></i>
                        </div>
                        <span class="font-medium text-gray-900">${card.title}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-600 font-mono text-sm">
                    ${card.qr_code}
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm">
                        <i class="fa-solid fa-images text-xs"></i>
                        ${card.albums_count || 0}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    ${card.is_active
                ? '<span class="inline-flex px-3 py-1 bg-green-50 text-green-700 rounded-full text-sm">نشط</span>'
                : '<span class="inline-flex px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm">غير نشط</span>'
            }
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        <button onclick="linkAlbums(${card.card_id})" 
                                class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors"
                                title="ربط ألبومات">
                            <i class="fa-solid fa-link"></i>
                        </button>
                        <button onclick="editCard(${card.card_id})" 
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                title="تعديل">
                            <i class="fa-solid fa-edit"></i>
                        </button>
                        <button onclick="deleteCard(${card.card_id})" 
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                title="حذف">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    renderPagination(pagination, onPageChange) {
        const container = document.getElementById('paginationContainer');
        if (!container || !pagination) return;

        const { current_page, last_page, from, to, total } = pagination;

        if (last_page <= 1) {
            container.innerHTML = '';
            return;
        }

        let pages = [];
        for (let i = 1; i <= last_page; i++) {
            if (i === 1 || i === last_page || (i >= current_page - 1 && i <= current_page + 1)) {
                pages.push(i);
            } else if (pages[pages.length - 1] !== '...') {
                pages.push('...');
            }
        }

        container.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    عرض ${from} إلى ${to} من ${total} نتيجة
                </div>
                <div class="flex gap-1">
                    ${pages.map(page => {
            if (page === '...') {
                return '<span class="px-3 py-1 text-gray-400">...</span>';
            }
            return `
                            <button 
                                onclick="(${onPageChange})(${page})"
                                class="px-3 py-1 rounded ${page === current_page
                    ? 'bg-accent text-white'
                    : 'text-gray-600 hover:bg-gray-100'} transition-colors">
                                ${page}
                            </button>
                        `;
        }).join('')}
                </div>
            </div>
        `;
    }

    openModal(title) {
        const modal = document.querySelector('[x-data]');
        if (modal) {
            modal.__x.$data.showModal = true;
        }
        const modalTitle = document.querySelector('#card-modal h3');
        if (modalTitle) modalTitle.textContent = title;
    }

    closeModal() {
        const modal = document.querySelector('[x-data]');
        if (modal) {
            modal.__x.$data.showModal = false;
        }
    }

    openLinkModal(card, albums) {
        const modal = document.querySelector('[x-data]');
        if (modal) {
            modal.__x.$data.showLinkModal = true;
        }

        // Render albums checkboxes
        const container = document.getElementById('albums-list');
        if (container && albums) {
            const linkedAlbumIds = card.albums ? card.albums.map(a => a.album_id) : [];
            container.innerHTML = albums.map(album => `
                <label class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="album_ids[]" value="${album.album_id}" 
                           ${linkedAlbumIds.includes(album.album_id) ? 'checked' : ''}
                           class="w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent">
                    <span class="flex-1">${album.name}</span>
                    <span class="text-sm text-gray-500">${album.photos_count || 0} صورة</span>
                </label>
            `).join('');
        }
    }

    closeLinkModal() {
        const modal = document.querySelector('[x-data]');
        if (modal) {
            modal.__x.$data.showLinkModal = false;
        }
    }

    showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 left-1/2 -translate-x-1/2 px-6 py-3 rounded-lg shadow-lg z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } text-white`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
}
