import { showToast } from '../components/toast';
import { setupSearch } from '../components/search';

document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.querySelector('#cards-table tbody');
    const linkForm = document.querySelector('#link-albums-form');
    const albumsList = document.querySelector('#school-albums-list');
    let cards = [];
    let allAlbums = [];

    // Fetch Cards
    const fetchCards = async () => {
        try {
            const response = await fetch('/school/cards', {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (result.success) {
                cards = result.data.cards;
                renderCards(cards);
            }
        } catch (error) {
            showToast('خطأ في تحميل الكروت', 'error');
        }
    };

    // Fetch Albums for linking
    const fetchAllAlbums = async () => {
        try {
            const response = await fetch('/school/albums', {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (result.success) {
                allAlbums = result.data.albums;
            }
        } catch (error) {
            console.error('Failed to pre-fetch albums');
        }
    };

    // Render Cards
    const renderCards = (data) => {
        if (!tableBody) return;
        tableBody.innerHTML = '';
        
        data.forEach(card => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50/50 transition-colors border-b border-gray-100';
            row.innerHTML = `
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-accent/10 flex items-center justify-center">
                            <i class="fas fa-id-card text-accent"></i>
                        </div>
                        <span class="font-bold text-gray-700">${card.title || 'بدون عنوان'}</span>
                    </div>
                </td>
                <td class="px-6 py-4 font-mono text-sm">${card.card_number}</td>
                <td class="px-6 py-4">
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">
                        ${card.card_type?.name || 'افتراضي'}
                    </span>
                </td>
                <td class="px-6 py-4 text-xs text-gray-500">${new Date(card.created_at).toLocaleDateString('ar-EG')}</td>
                <td class="px-6 py-4">
                    <span class="text-xs font-bold ${card.users_count > 0 ? 'text-accent' : 'text-gray-400'}">
                        ${card.users_count || 0} طلاب
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <button onclick="openLinkModal(${card.card_id})" class="inline-flex items-center gap-2 px-3 py-1.5 bg-accent/5 text-accent hover:bg-accent/10 rounded-lg transition-all text-xs font-bold">
                        <i class="fas fa-link"></i>
                        <span>ربط الألبومات (${card.albums_count})</span>
                    </button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    };

    window.openLinkModal = (cardId) => {
        const card = cards.find(c => c.card_id === cardId);
        if (!card) return;

        document.querySelector('#card-id-to-link').value = cardId;
        const linkedAlbumIds = card.albums.map(a => a.album_id);

        albumsList.innerHTML = '';
        if (allAlbums.length === 0) {
            albumsList.innerHTML = '<div class="col-span-full py-4 text-center text-gray-400 text-sm">لا توجد ألبومات للربط</div>';
        } else {
            allAlbums.forEach(album => {
                const isChecked = linkedAlbumIds.includes(album.album_id);
                const div = document.createElement('label');
                div.className = `flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all ${isChecked ? 'border-accent bg-accent/5' : 'border-gray-100 bg-gray-50/50 hover:border-gray-200'}`;
                div.innerHTML = `
                    <input type="checkbox" name="album_ids[]" value="${album.album_id}" ${isChecked ? 'checked' : ''} class="w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent">
                    <div class="flex-1">
                        <p class="text-xs font-bold text-gray-700">${album.name}</p>
                        <p class="text-[10px] text-gray-400">${album.photos_count} صورة</p>
                    </div>
                `;
                // Simple toggle visual
                div.querySelector('input').addEventListener('change', (e) => {
                    if (e.target.checked) {
                        div.className = 'flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all border-accent bg-accent/5';
                    } else {
                        div.className = 'flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all border-gray-100 bg-gray-50/50 hover:border-gray-200';
                    }
                });
                albumsList.appendChild(div);
            });
        }

        window.openModal('link-albums-modal');
    };

    linkForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const cardId = document.querySelector('#card-id-to-link').value;
        const selectedAlbums = Array.from(linkForm.querySelectorAll('input[name="album_ids[]"]:checked')).map(cb => cb.value);

        try {
            const response = await fetch(`/school/cards/${cardId}/link-albums`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ album_ids: selectedAlbums })
            });

            const result = await response.json();
            if (result.success) {
                showToast(result.message);
                window.closeModal('link-albums-modal');
                fetchCards();
            } else {
                showToast(result.message, 'error');
            }
        } catch (error) {
            showToast('خطأ في حفظ الربط', 'error');
        }
    });

    setupSearch('#search', (query) => {
        const filtered = cards.filter(c => 
            c.card_number.toLowerCase().includes(query.toLowerCase()) || 
            (c.title && c.title.toLowerCase().includes(query.toLowerCase()))
        );
        renderCards(filtered);
    });

    fetchCards();
    fetchAllAlbums();
});
