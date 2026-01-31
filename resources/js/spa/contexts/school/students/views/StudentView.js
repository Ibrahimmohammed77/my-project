export class StudentView {
    constructor() {
        this.tableBody = document.querySelector('#students-table tbody');
        this.searchField = document.getElementById('search');
    }

    bindSearch(handler) {
        if (!this.searchField) return;
        this.searchField.addEventListener('input', (e) => handler(e.target.value));
    }

    renderTable(students) {
        if (!this.tableBody) return;
        this.tableBody.innerHTML = '';
        
        if (students.length === 0) {
            this.renderEmpty();
            return;
        }

        students.forEach(student => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/50 transition-colors border-b border-gray-100';
            
            tr.innerHTML = `
                <td class="px-6 py-4 font-medium text-gray-900"></td>
                <td class="px-6 py-4 text-gray-600"></td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        ${student.card?.card_number || 'بدون كارت'}
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-500">${new Date(student.created_at).toLocaleDateString('ar-EG')}</td>
            `;

            tr.children[0].textContent = student.name;
            tr.children[1].textContent = student.email || '-';

            this.tableBody.appendChild(tr);
        });
    }

    renderEmpty() {
        this.tableBody.innerHTML = `
            <tr>
                <td colspan="4" class="py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <i class="far fa-user text-3xl mb-3 text-gray-300"></i>
                        <p>لا يوجد طلاب مطابقين للبحث</p>
                    </div>
                </td>
            </tr>
        `;
    }
}
