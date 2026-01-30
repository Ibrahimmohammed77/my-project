@props(['id' => 'dataTable', 'headers' => []])

<div class="bg-white rounded-[20px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100/50 overflow-hidden transition-all duration-300 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)]">
    <!-- Table Header -->
    <div class="px-6 py-5 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white">
        <div class="flex items-center gap-3">
            <h3 class="font-bold text-xl text-gray-800 tracking-tight" id="{{ $id }}-title">{{ $title ?? 'الجدول' }}</h3>
            <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2.5 py-1 rounded-full border border-blue-100" id="{{ $id }}-count">0 عنصر</span>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <div class="relative group w-full sm:w-72">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                     <i class="fa-solid fa-magnifying-glass text-gray-300 group-focus-within:text-blue-500 transition-colors"></i>
                </div>
                <input 
                    type="text" 
                    id="{{ $id }}-search"
                    placeholder="بحث في القائمة..." 
                    class="block w-full p-2.5 pr-10 text-sm text-gray-900 border border-gray-200 rounded-xl bg-gray-50/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all outline-none placeholder-gray-400"
                >
            </div>
            
            <button class="flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-50 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm">
                <i class="fa-solid fa-filter"></i>
                <span>تصفية</span>
            </button>
            
            <button class="flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-900 text-white rounded-xl text-xs font-bold hover:bg-gray-800 shadow-lg shadow-gray-900/20 transition-all transform hover:-translate-y-0.5">
                <i class="fa-solid fa-download"></i>
                <span>تصدير</span>
            </button>
        </div>
    </div>
    
    <!-- Table Content -->
    <div class="overflow-x-auto">
        <table class="w-full text-right border-collapse" id="{{ $id }}">
            <thead class="bg-gray-50/80 uppercase">
                <tr class="text-xs text-gray-500 font-bold tracking-wider border-b border-gray-100">
                    @if(count($headers) > 0)
                        @foreach($headers as $header)
                            <th class="{{ $header['class'] ?? '' }} px-6 py-4 first:rounded-tr-lg last:rounded-tl-lg">
                                {{ $header['name'] }}
                            </th>
                        @endforeach
                    @else
                        <th class="px-6 py-4 w-10">
                            <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer" id="{{ $id }}-select-all">
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody id="{{ $id }}-tbody" class="divide-y divide-gray-50 bg-white">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="hidden p-20 text-center">
        <div class="inline-flex relative">
            <div class="absolute inset-0 bg-blue-500 opacity-20 rounded-full animate-ping"></div>
            <div class="relative bg-white p-4 rounded-full shadow-xl border border-gray-100">
                <i class="fas fa-circle-notch fa-spin text-2xl text-blue-500"></i>
            </div>
        </div>
        <p class="mt-4 text-sm font-medium text-gray-500 animate-pulse">جاري تحميل البيانات...</p>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="hidden py-16 px-6 text-center">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 border-2 border-dashed border-gray-200">
            <i class="fas fa-search text-3xl text-gray-300"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-2">لا توجد نتائج</h3>
        <p class="text-sm text-gray-500 max-w-sm mx-auto">لم نعثر على أي بيانات تطابق معايير البحث الخاصة بك. حاول تغيير مصطلحات البحث.</p>
    </div>

    <!-- Table Footer / Pagination -->
    <div class="px-6 py-4 border-t border-gray-50 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/30">
        <div class="text-xs font-medium text-gray-500" id="{{ $id }}-info">
            عرض <span class="text-gray-900 font-bold">0</span> إلى <span class="text-gray-900 font-bold">0</span> من أصل <span class="text-gray-900 font-bold">0</span> سجل
        </div>
        
        <div class="flex items-center gap-2" id="{{ $id }}-pagination">
            <button id="{{ $id }}-prev" class="px-3 py-1.5 text-xs font-bold border border-gray-200 rounded-lg text-gray-500 bg-white hover:bg-gray-50 hover:text-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-sm" disabled>
                السابق
            </button>
            <div class="flex items-center gap-1.5" id="{{ $id }}-pages">
                <!-- Pages injected here -->
            </div>
            <button id="{{ $id }}-next" class="px-3 py-1.5 text-xs font-bold border border-gray-200 rounded-lg text-gray-500 bg-white hover:bg-gray-50 hover:text-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-sm">
                التالي
            </button>
        </div>
    </div>
</div>

<style>
    #{{ $id }} tbody tr {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    #{{ $id }} tbody tr:hover {
        background-color: #F8FAFC;
        transform: scale-[1.002] translateZ(0);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
        z-index: 10;
        position: relative;
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
            infoElement.innerHTML = `عرض <span class="text-gray-900 font-bold">${start}</span> إلى <span class="text-gray-900 font-bold">${end}</span> من أصل <span class="text-gray-900 font-bold">${totalItems}</span> سجل`;
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
            pagesContainer.innerHTML = '<button class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-900 text-white text-xs font-bold shadow-md transform scale-105">1</button>';
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
                dots.className = 'text-gray-300 text-xs px-1 font-bold';
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
                dots.className = 'text-gray-300 text-xs px-1 font-bold';
                dots.textContent = '...';
                pagesContainer.appendChild(dots);
            }
            pagesContainer.appendChild(createPageButton(totalPages));
        }
    }
    
    function createPageButton(pageNum) {
        const btn = document.createElement('button');
        const isCurrent = pageNum === currentPage;
        
        btn.className = isCurrent 
            ? 'w-8 h-8 flex items-center justify-center rounded-lg bg-gray-900 text-white text-xs font-bold shadow-md transform scale-105 transition-all'
            : 'w-8 h-8 flex items-center justify-center rounded-lg border border-gray-100 hover:bg-gray-50 text-gray-500 text-xs font-medium hover:text-blue-600 transition-colors';
            
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
    }
    
    // Re-initialize when rows change
    const observer = new MutationObserver(() => {
        const tbody = document.getElementById(tableId + '-tbody');
        if (tbody && tbody.children.length > 0) {
            allRows = Array.from(tbody.querySelectorAll('tr'));
            // Keep current page if possible, otherwise reset
            const totalPages = Math.ceil(allRows.length / itemsPerPage);
            if (currentPage > totalPages) currentPage = 1;
            
            updatePagination();
        } else {
             const infoElement = document.getElementById(tableId + '-info');
             const countElement = document.getElementById(tableId + '-count');
             if(infoElement) infoElement.innerHTML = 'عرض 0 إلى 0';
             if(countElement) countElement.textContent = '0 عنصر';
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
