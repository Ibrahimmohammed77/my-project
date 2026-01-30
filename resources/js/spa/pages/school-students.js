import { showToast } from '../utils/toast.js';
import { setupSearch } from '../components/search.js';

document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.querySelector('#students-table tbody');
    let students = [];

    // Fetch Students
    const fetchStudents = async () => {
        try {
            const response = await fetch('/school/students', {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (result.success) {
                students = result.data.students;
                renderStudents(students);
            }
        } catch (error) {
            showToast('خطأ في تحميل قائمة الطلاب', 'error');
        }
    };

    // Render Students
    const renderStudents = (data) => {
        if (!tableBody) return;
        tableBody.innerHTML = '';
        
        data.forEach(student => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50/50 transition-colors border-b border-gray-100';
            row.innerHTML = `
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-sm">
                            ${student.name ? student.name.charAt(0) : 'S'}
                        </div>
                        <div>
                            <p class="font-bold text-gray-700">${student.name}</p>
                            <p class="text-[10px] text-gray-400">@${student.username}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">${student.email}</td>
                <td class="px-6 py-4 text-sm text-gray-600">${student.phone || '<span class="text-gray-300">غير متوفر</span>'}</td>
                <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-1">
                        ${student.cards.map(card => `
                            <span class="px-2 py-0.5 bg-accent/5 text-accent border border-accent/10 rounded text-[10px] font-bold">
                                ${card.card_number}
                            </span>
                        `).join('')}
                    </div>
                </td>
                <td class="px-6 py-4 text-xs text-gray-500">${new Date(student.created_at).toLocaleDateString('ar-EG')}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        <button onclick="viewStudentDetails(${student.id})" class="w-8 h-8 rounded-lg bg-gray-50 text-gray-500 hover:text-accent hover:bg-accent/5 transition-all flex items-center justify-center" title="عرض التفاصيل">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                    </div>
                </td>
            `;
            tableBody.appendChild(row);
        });
    };

    window.viewStudentDetails = (id) => {
        // Mock or implement actual redirection/modal if needed
        showToast('سيتم إضافة تفاصيل الطالب قريباً', 'info');
    };

    setupSearch('#search', (query) => {
        const filtered = students.filter(s => 
            s.name.toLowerCase().includes(query.toLowerCase()) || 
            s.email.toLowerCase().includes(query.toLowerCase()) ||
            s.username.toLowerCase().includes(query.toLowerCase())
        );
        renderStudents(filtered);
    });

    fetchStudents();
});
