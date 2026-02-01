<div x-show="showModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="showModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/50 transition-opacity" 
             @click="showModal = false"></div>

        <!-- Modal panel -->
        <div x-show="showModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white rounded-2xl text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-accent to-accent/80 px-6 py-4">
                <h3 id="modal-title" class="text-xl font-bold text-white">إدارة الألبوم</h3>
            </div>

            <!-- Form -->
            <form id="album-form" class="p-6 space-y-4">
                <input type="hidden" id="album-id">

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fa-solid fa-images text-accent ml-1"></i>
                        اسم الألبوم
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           required
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent transition-all"
                           placeholder="مثال: ألبوم العائلة 2026">
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fa-solid fa-align-right text-accent ml-1"></i>
                        الوصف
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent transition-all"
                              placeholder="وصف مختصر للألبوم..."></textarea>
                </div>

                <!-- Visibility -->
                <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                    <input type="checkbox" 
                           id="is_visible" 
                           name="is_visible" 
                           checked
                           class="w-5 h-5 text-accent border-gray-300 rounded focus:ring-accent">
                    <label for="is_visible" class="text-sm font-medium text-gray-700">
                        جعل الألبوم مرئياً للجميع
                    </label>
                </div>

                <!-- Actions -->
                <div class="flex gap-3 pt-4">
                    <button type="submit" 
                            class="flex-1 bg-accent hover:bg-accent/90 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fa-solid fa-save ml-2"></i>
                        حفظ
                    </button>
                    <button type="button" 
                            @click="showModal = false"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fa-solid fa-times ml-2"></i>
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
