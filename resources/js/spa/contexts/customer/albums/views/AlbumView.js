export default class AlbumView {
    renderTable(albums) {
        const tbody = document.getElementById('albumsTableBody');
        if (!tbody) return;

        if (!albums || albums.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <i class="fa-solid fa-images text-4xl mb-3 opacity-30"></i>
                        <p>لا توجد ألبومات بعد</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = albums.map(album => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-accent/10 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-images text-accent"></i>
                        </div>
                        <span class="font-medium text-gray-900">${album.name}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-600 max-w-xs truncate">
                    ${album.description || '-'}
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm">
                        <i class="fa-solid fa-image text-xs"></i>
                        ${album.photos_count || 0}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    ${album.is_visible
                ? '<span class="inline-flex px-3 py-1 bg-green-50 text-green-700 rounded-full text-sm">مرئي</span>'
                : '<span class="inline-flex px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm">مخفي</span>'
            }
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        <button onclick="openUploadModal(${album.album_id})" 
                                class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                title="رفع صور">
                            <i class="fa-solid fa-cloud-upload-alt"></i>
                        </button>
                        <button onclick="editAlbum(${album.album_id})" 
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                title="تعديل">
                            <i class="fa-solid fa-edit"></i>
                        </button>
                        <button onclick="deleteAlbum(${album.album_id})" 
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
        const modalTitle = document.querySelector('#album-modal h3');
        if (modalTitle) modalTitle.textContent = title;
    }

    closeModal() {
        const modal = document.querySelector('[x-data]');
        if (modal) {
            modal.__x.$data.showModal = false;
        }
    }

    openUploadModal(albumId) {
        document.getElementById('upload-album-id').value = albumId;
        document.getElementById('upload-form').reset();
        const modal = document.querySelector('[x-data]');
        if (modal) {
            modal.__x.$data.showUploadModal = true;
        }
    }

    closeUploadModal() {
        const modal = document.querySelector('[x-data]');
        if (modal) {
            modal.__x.$data.showUploadModal = false;
        }
    }

    showToast(message, type = 'success') {
        // Simple toast implementation
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
