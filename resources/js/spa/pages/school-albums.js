import { showToast } from '../components/toast';
import { setupSearch } from '../components/search';

document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.querySelector('#albums-table tbody');
    const albumForm = document.querySelector('#album-form');
    let albums = [];

    // Fetch Albums
    const fetchAlbums = async () => {
        try {
            const response = await fetch('/school/albums', {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (result.success) {
                albums = result.data.albums;
                renderAlbums(albums);
            }
        } catch (error) {
            showToast('خطأ في تحميل الألبومات', 'error');
        }
    };

    // Render Albums
    const renderAlbums = (data) => {
        if (!tableBody) return;
        tableBody.innerHTML = '';
        
        data.forEach(album => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50/50 transition-colors border-b border-gray-100';
            row.innerHTML = `
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-accent/10 flex items-center justify-center">
                            <i class="fas fa-images text-accent"></i>
                        </div>
                        <span class="font-bold text-gray-700">${album.name}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">${album.description || '<span class="text-gray-300">لا يوجد وصف</span>'}</td>
                <td class="px-6 py-4">
                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-lg text-xs font-bold">
                        ${album.photos_count || 0} صورة
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium ${album.is_visible ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'}">
                        <span class="w-1.5 h-1.5 rounded-full ${album.is_visible ? 'bg-green-500' : 'bg-gray-500'}"></span>
                        ${album.is_visible ? 'مرئي للطلاب' : 'مخفي'}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        <button onclick="editAlbum(${album.album_id})" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors flex items-center justify-center" title="تعديل">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                        <button onclick="deleteAlbum(${album.album_id})" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors flex items-center justify-center" title="حذف">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                </td>
            `;
            tableBody.appendChild(row);
        });
    };

    // Global Handlers
    window.showCreateModal = () => {
        albumForm.reset();
        document.querySelector('#album-id').value = '';
        document.querySelector('#album-modal-title').textContent = 'ألبوم مدرسي جديد';
        window.openModal('album-modal');
    };

    window.editAlbum = (id) => {
        const album = albums.find(a => a.album_id === id);
        if (!album) return;

        document.querySelector('#album-id').value = album.album_id;
        document.querySelector('#name').value = album.name;
        document.querySelector('#description').value = album.description || '';
        document.querySelector('#is_visible').checked = album.is_visible;
        
        document.querySelector('#album-modal-title').textContent = 'تعديل ألبوم المدرسي';
        window.openModal('album-modal');
    };

    window.deleteAlbum = async (id) => {
        if (!confirm('هل أنت متأكد من حذف هذا الألبوم؟')) return;

        try {
            const response = await fetch(`/school/albums/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            const result = await response.json();
            if (result.success) {
                showToast(result.message);
                fetchAlbums();
            }
        } catch (error) {
            showToast('خطأ في عملية الحذف', 'error');
        }
    };

    albumForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.querySelector('#album-id').value;
        const formData = new FormData(albumForm);
        const data = Object.fromEntries(formData.entries());
        data.is_visible = document.querySelector('#is_visible').checked ? 1 : 0;

        const url = id ? `/school/albums/${id}` : '/school/albums';
        const method = id ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: id ? 'POST' : 'POST', // Use POST with _method for PUT in Laravel if needed, or structured PUT
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(id ? { ...data, _method: 'PUT' } : data)
            });

            const result = await response.json();
            if (result.success) {
                showToast(result.message);
                window.closeModal('album-modal');
                fetchAlbums();
            } else {
                showToast(result.message, 'error');
            }
        } catch (error) {
            showToast('خطأ في حفظ البيانات', 'error');
        }
    });

    setupSearch('#search', (query) => {
        const filtered = albums.filter(a => 
            a.name.toLowerCase().includes(query.toLowerCase()) || 
            (a.description && a.description.toLowerCase().includes(query.toLowerCase()))
        );
        renderAlbums(filtered);
    });

    fetchAlbums();
});
