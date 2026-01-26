@props(['id' => 'dataTable', 'headers' => []])

<div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden">
    <!-- Table Header -->
    <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <h3 class="font-bold text-lg text-primary" id="{{ $id }}-title">{{ $title ?? 'الجدول' }}</h3>
            <span class="bg-accent/10 text-accent text-xs font-bold px-2 py-1 rounded-full border border-accent/20" id="{{ $id }}-count">0 عنصر</span>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <div class="relative group w-full sm:w-64">
                <i class="fa-solid fa-magnifying-glass absolute right-3 top-3 text-gray-400 group-focus-within:text-accent transition-colors"></i>
                <input 
                    type="text" 
                    id="{{ $id }}-search"
                    placeholder="بحث..." 
                    class="w-full bg-gray-50 border border-gray-200 text-sm rounded-xl py-2.5 pr-10 pl-4 focus:outline-none focus:border-accent focus:bg-white transition-all"
                >
            </div>
            
            <button class="flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-primary transition-colors">
                <i class="fa-solid fa-filter"></i>
                <span>تصفية</span>
            </button>
            
            <button class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white rounded-xl text-sm font-medium hover:bg-primary-light shadow-lg shadow-primary/20 transition-all">
                <i class="fa-solid fa-download"></i>
                <span>تصدير</span>
            </button>
        </div>
    </div>
    
    <!-- Table Content -->
    <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse" id="{{ $id }}">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-bold">
                    @if(count($headers) > 0)
                        @foreach($headers as $header)
                            <th class="{{ $header['class'] ?? '' }} px-6 py-4">
                                {{ $header['name'] }}
                            </th>
                        @endforeach
                    @else
                        <th class="px-6 py-4 w-10">
                            <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-accent focus:ring-accent cursor-pointer" id="{{ $id }}-select-all">
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody id="{{ $id }}-tbody" class="divide-y divide-gray-100 bg-white">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="hidden p-12 text-center text-gray-400">
        <i class="fas fa-circle-notch fa-spin text-2xl mb-3 text-accent"></i>
        <p>جاري تحميل البيانات...</p>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="hidden p-12 text-center">
        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
            <i class="fas fa-search text-xl text-gray-400"></i>
        </div>
        <h3 class="text-base font-bold text-gray-800 mb-1">لا توجد نتائج</h3>
        <p class="text-sm text-gray-500">لم يتم العثور على بيانات تطابق بحثك.</p>
    </div>

    <!-- Table Footer / Pagination -->
    <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/50">
        <div class="text-sm text-gray-500" id="{{ $id }}-info">
            عرض <span class="font-bold text-gray-800">0</span> إلى <span class="font-bold text-gray-800">0</span> من أصل <span class="font-bold text-gray-800">0</span> سجل
        </div>
        
        <div class="flex items-center gap-2" id="{{ $id }}-pagination">
            <button id="{{ $id }}-prev" class="px-3 py-1 text-sm border border-gray-200 rounded-lg text-gray-500 bg-white hover:bg-gray-50 hover:text-primary disabled:opacity-50 disabled:cursor-not-allowed transition-colors" disabled>
                السابق
            </button>
            <div class="flex items-center gap-1" id="{{ $id }}-pages">
                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-accent text-white text-sm font-bold shadow-sm">1</button>
            </div>
            <button id="{{ $id }}-next" class="px-3 py-1 text-sm border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 hover:text-primary disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                التالي
            </button>
        </div>
    </div>
</div>

<style>
    #{{ $id }} tbody tr {
        transition: background-color 0.2s ease;
    }
    
    #{{ $id }} tbody tr:hover {
        background-color: rgba(59, 130, 246, 0.03);
    }
</style>

<script>
(function() {
    const tableId = '{{ $id }}';
    let currentPage = 1;
    let itemsPerPage = 10;
    let allRows = [];
    
    // Initialize pagination after data is loaded
    window['{{ $id }}_initPagination'] = function() {
        const tbody = document.getElementById(tableId + '-tbody');
        if (!tbody) return;
        
        // Store all rows
        allRows = Array.from(tbody.querySelectorAll('tr'));
        updatePagination();
    };
    
    function updatePagination() {
        const totalItems = allRows.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        
        // Update info
        const start = totalItems > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0;
        const end = Math.min(currentPage * itemsPerPage, totalItems);
        
        const infoElement = document.getElementById(tableId + '-info');
        if (infoElement) {
            infoElement.innerHTML = `عرض <span class="font-bold text-gray-800">${start}</span> إلى <span class="font-bold text-gray-800">${end}</span> من أصل <span class="font-bold text-gray-800">${totalItems}</span> سجل`;
        }
        
        const countElement = document.getElementById(tableId + '-count');
        if (countElement) {
            countElement.textContent = `${totalItems} عنصر`;
        }
        
        // Update pagination buttons
        const prevBtn = document.getElementById(tableId + '-prev');
        const nextBtn = document.getElementById(tableId + '-next');
        
        if (prevBtn) {
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderPage();
                }
            };
        }
        
        if (nextBtn) {
            nextBtn.disabled = currentPage === totalPages || totalPages === 0;
            nextBtn.onclick = () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderPage();
                }
            };
        }
        
        // Render page numbers
        renderPageNumbers(totalPages);
        
        // Render current page
        renderPage();
    }
    
    function renderPageNumbers(totalPages) {
        const pagesContainer = document.getElementById(tableId + '-pages');
        if (!pagesContainer) return;
        
        pagesContainer.innerHTML = '';
        
        if (totalPages === 0) {
            pagesContainer.innerHTML = '<button class="w-8 h-8 flex items-center justify-center rounded-lg bg-accent text-white text-sm font-bold shadow-sm">1</button>';
            return;
        }
        
        const maxVisible = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);
        
        if (endPage - startPage < maxVisible - 1) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }
        
        // First page
        if (startPage > 1) {
            pagesContainer.appendChild(createPageButton(1));
            if (startPage > 2) {
                const dots = document.createElement('span');
                dots.className = 'text-gray-400 px-1';
                dots.textContent = '...';
                pagesContainer.appendChild(dots);
            }
        }
        
        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            pagesContainer.appendChild(createPageButton(i));
        }
        
        // Last page
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const dots = document.createElement('span');
                dots.className = 'text-gray-400 px-1';
                dots.textContent = '...';
                pagesContainer.appendChild(dots);
            }
            pagesContainer.appendChild(createPageButton(totalPages));
        }
    }
    
    function createPageButton(pageNum) {
        const btn = document.createElement('button');
        btn.className = pageNum === currentPage 
            ? 'w-8 h-8 flex items-center justify-center rounded-lg bg-accent text-white text-sm font-bold shadow-sm'
            : 'w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600 text-sm transition-colors';
        btn.textContent = pageNum;
        btn.onclick = () => {
            currentPage = pageNum;
            renderPage();
        };
        return btn;
    }
    
    function renderPage() {
        const tbody = document.getElementById(tableId + '-tbody');
        if (!tbody) return;
        
        // Hide all rows
        allRows.forEach(row => row.style.display = 'none');
        
        // Show current page rows
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        
        for (let i = start; i < end && i < allRows.length; i++) {
            allRows[i].style.display = '';
        }
        
        updatePagination();
    }
    
    // Re-initialize when rows change
    const observer = new MutationObserver(() => {
        const tbody = document.getElementById(tableId + '-tbody');
        if (tbody && tbody.children.length > 0) {
            allRows = Array.from(tbody.querySelectorAll('tr'));
            currentPage = 1; // Reset to first page
            updatePagination();
        }
    });
    
    // Start observing
    setTimeout(() => {
        const tbody = document.getElementById(tableId + '-tbody');
        if (tbody) {
            observer.observe(tbody, { childList: true });
        }
    }, 100);
})();
</script>
