@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6">الملف الشخصي</h1>
        
        <form action="{{ url('/profile') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Context: Profile Image -->
            <div class="mb-6 flex flex-col items-center">
                <div class="relative w-32 h-32 mb-4">
                    @if($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile" class="w-full h-full rounded-full object-cover border-4 border-gray-100 shadow-sm">
                    @else
                        <div class="w-full h-full rounded-full bg-gray-100 flex items-center justify-center border-4 border-gray-50 text-gray-400">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                    <label for="profile_image" class="absolute bottom-0 right-0 bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-full cursor-pointer shadow-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <input type="file" id="profile_image" name="profile_image" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </label>
                </div>
                <p class="text-sm text-gray-500">اختر صورة (JPEG, PNG, JPG, GIF) بحد أقصى 2MB</p>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">الاسم</label>
                <input type="text" name="name" value="{{ $user->name }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">اسم المستخدم</label>
                <input type="text" name="username" value="{{ $user->username }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">البريد الإلكتروني</label>
                <input type="email" name="email" value="{{ $user->email }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                حفظ التغييرات
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                // Find potential image element or placeholder
                var container = input.closest('.relative');
                var img = container.querySelector('img');
                
                if (img) {
                    img.src = e.target.result;
                } else {
                    // Replace placeholder with new image
                    var placeholder = container.querySelector('div.bg-gray-100');
                    if (placeholder) {
                        var newImg = document.createElement('img');
                        newImg.src = e.target.result;
                        newImg.alt = "Profile";
                        newImg.className = "w-full h-full rounded-full object-cover border-4 border-gray-100 shadow-sm";
                        placeholder.parentNode.replaceChild(newImg, placeholder);
                    }
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
