@extends('layouts.guest')

@section('title', 'إنشاء حساب جديد')

@section('content')
<div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 shadow-2xl border border-white/20">
    <h2 class="text-2xl font-bold text-white text-center mb-6">إنشاء حساب جديد</h2>
    
    <div id="error-message" class="hidden mb-4 p-4 bg-red-500/20 border border-red-400/30 rounded-xl text-white text-sm"></div>
    
    <form id="register-form" class="space-y-4">
        <div>
            <label class="block text-sm font-semibold text-white mb-2">اسم المستخدم</label>
            <input 
                type="text" 
                id="username"
                name="username"
                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200 focus:border-white focus:ring-2 focus:ring-white/20 outline-none transition-all backdrop-blur-sm" 
                placeholder="اسم مستخدم فريد"
                required
            >
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-white mb-2">الاسم الكامل</label>
            <input 
                type="text" 
                id="full_name"
                name="full_name"
                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200 focus:border-white focus:ring-2 focus:ring-white/20 outline-none transition-all backdrop-blur-sm" 
                placeholder="أدخل اسمك الكامل"
                required
            >
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-white mb-2">البريد الإلكتروني (اختياري)</label>
            <input 
                type="email" 
                id="email"
                name="email"
                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200 focus:border-white focus:ring-2 focus:ring-white/20 outline-none transition-all backdrop-blur-sm" 
                placeholder="example@domain.com"
            >
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-white mb-2">رقم الهاتف</label>
            <input 
                type="text" 
                id="phone"
                name="phone"
                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200 focus:border-white focus:ring-2 focus:ring-white/20 outline-none transition-all backdrop-blur-sm" 
                placeholder="+966500000000"
                required
            >
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-white mb-2">كلمة المرور</label>
            <input 
                type="password" 
                id="password"
                name="password"
                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200 focus:border-white focus:ring-2 focus:ring-white/20 outline-none transition-all backdrop-blur-sm" 
                placeholder="••••••••"
                required
            >
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-white mb-2">تأكيد كلمة المرور</label>
            <input 
                type="password" 
                id="password_confirmation"
                name="password_confirmation"
                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200 focus:border-white focus:ring-2 focus:ring-white/20 outline-none transition-all backdrop-blur-sm" 
                placeholder="••••••••"
                required
            >
        </div>
        
        <div class="flex items-start">
            <input 
                type="checkbox" 
                name="terms" 
                id="terms" 
                class="w-4 h-4 mt-1 rounded border-white/20 bg-white/10 text-accent focus:ring-2 focus:ring-white/20"
                required
            >
            <label for="terms" class="mr-2 text-sm text-blue-100">
                أوافق على <a href="#" class="text-white font-semibold hover:underline">الشروط والأحكام</a>
            </label>
        </div>
        
        <button 
            type="submit" 
            id="submit-btn"
            class="w-full bg-white hover:bg-blue-50 text-primary px-6 py-3 rounded-xl font-bold shadow-lg transition-all active:scale-95"
        >
            إنشاء الحساب
        </button>
    </form>
</div>

<div class="text-center mt-6">
    <p class="text-blue-100 text-sm">
        لديك حساب بالفعل؟ 
        <a href="{{ route('login') }}" class="text-white font-semibold hover:underline">تسجيل الدخول</a>
    </p>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
// Setup Axios for web requests (not API)
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

// Get CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

const registerForm = document.getElementById('register-form');
const submitBtn = document.getElementById('submit-btn');
const errorMessage = document.getElementById('error-message');

registerForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Disable button
    submitBtn.disabled = true;
    submitBtn.textContent = 'جاري إنشاء الحساب...';
    errorMessage.classList.add('hidden');
    
    const formData = {
        username: document.getElementById('username').value,
        full_name: document.getElementById('full_name').value,
        email: document.getElementById('email').value || null,
        phone: document.getElementById('phone').value,
        password: document.getElementById('password').value,
        password_confirmation: document.getElementById('password_confirmation').value
    };
    
    try {
        const response = await axios.post('/register', formData);
        
        if (response.data.success) {
            // Redirect to dashboard or specified redirect
            window.location.href = response.data.redirect || '/dashboard';
        }
    } catch (error) {
        // Show error
        let errorText = 'حدث خطأ أثناء إنشاء الحساب';
        
        if (error.response?.data?.message) {
            errorText = error.response.data.message;
        } else if (error.response?.data?.errors) {
            const errors = Object.values(error.response.data.errors).flat();
            errorText = errors.join('<br>');
        }
        
        errorMessage.innerHTML = errorText;
        errorMessage.classList.remove('hidden');
        
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.textContent = 'إنشاء الحساب';
    }
});
</script>
@endpush
@endsection
