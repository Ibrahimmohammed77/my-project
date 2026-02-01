<div x-show="showLinkModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="link-modal-title" 
     role="dialog" 
     aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="showLinkModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/50 transition-opacity" 
             @click="showLinkModal = false"></div>

        <!-- Modal panel -->
        <div x-show="showLinkModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white rounded-2xl text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-purple-500 px-6 py-4">
                <h3 id="link-modal-title" class="text-xl font-bold text-white">
                    <i class="fa-solid fa-link ml-2"></i>
                    ربط الألبومات بالكرت
                </h3>
            </div>

            <!-- Form -->
            <form id="link-albums-form" class="p-6">
                <input type="hidden" id="link-card-id">

                <!-- Albums List -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        اختر الألبومات المراد ربطها:
                    </label>
                    <div id="albums-list" class="space-y-2 max-h-96 overflow-y-auto">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-4 border-t border-gray-200">
                    <button type="submit" 
                            class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fa-solid fa-link ml-2"></i>
                        ربط الألبومات
                    </button>
                    <button type="button" 
                            @click="showLinkModal = false"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fa-solid fa-times ml-2"></i>
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
