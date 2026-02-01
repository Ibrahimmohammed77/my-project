@props(['id' => 'upload-modal'])

<x-modal id="{{ $id }}" title="رفع صور للألبوم">
    <form id="upload-form" class="space-y-4" enctype="multipart/form-data">
        <input type="hidden" id="upload-album-id" name="album_id">

        <!-- File Upload -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">اختر الصور</label>
            <input type="file" name="photos[]" multiple accept="image/*" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-accent/10 file:text-accent hover:file:bg-accent/20">
            <p class="text-xs text-gray-400 mt-1">يمكنك اختيار صور متعددة. الحد الأقصى 10 ميجابايت لكل صورة.</p>
        </div>

        <!-- Caption -->
        <x-form.input name="caption" label="تعليق (اختياري)" icon="fa-comment-alt" placeholder="تعليق عام للصور..." />

    </form>

    <x-slot name="footer">
        <x-button type="submit" form="upload-form" variant="primary">
            <i class="fas fa-cloud-upload-alt ms-2"></i>
            <span>بدء الرفع</span>
        </x-button>
        <x-button type="button" onclick="closeUploadModal()" variant="secondary">إلغاء</x-button>
    </x-slot>
</x-modal>
