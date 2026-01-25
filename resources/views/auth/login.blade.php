@extends('layouts.guest')

@section('title', 'تسجيل الدخول')

@section('content')
<div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 shadow-2xl border border-white/20">
    <h2 class="text-2xl font-bold text-white text-center mb-6">تسجيل الدخول</h2>
    
    <div id="error-message" class="hidden mb-4 p-4 bg-red-500/20 border border-red-400/30 rounded-xl text-white text-sm"></div>
    
    <form id="login-form" class="space-y-4">
        <div>
            <label class="block text-sm font-semibold text-white mb-2">اسم المستخدم أو البريد أو الهاتف</label>
            <input 
                type="text" 
                id="login" 
                name="login"
                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200 focus:border-white focus:ring-2 focus:ring-white/20 outline-none transition-all backdrop-blur-sm" 
                placeholder="admin أو admin@example.com"
                required
            >
        </div>
        
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-semibold text-white">كلمة المرور</label>
                <a href="{{ route('password.request') }}" class="text-xs text-blue-200 hover:text-white transition-colors">نسيت كلمة المرور؟</a>
            </div>
            <input 
                type="password" 
                id="password"
                name="password"
                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200 focus:border-white focus:ring-2 focus:ring-white/20 outline-none transition-all backdrop-blur-sm" 
                placeholder="••••••••"
                required
            >
        </div>
        
        <div class="flex items-center">
            <input 
                type="checkbox" 
                name="remember" 
                id="remember" 
                class="w-4 h-4 rounded border-white/20 bg-white/10 text-accent focus:ring-2 focus:ring-white/20"
            >
            <label for="remember" class="mr-2 text-sm text-blue-100">تذكرني على هذا الجهاز</label>
        </div>
        
        <button 
            type="submit" 
            id="submit-btn"
            class="w-full bg-white hover:bg-blue-50 text-primary px-6 py-3 rounded-xl font-bold shadow-lg transition-all active:scale-95"
        >
            تسجيل الدخول
        </button>
    </form>
</div>

<div class="text-center mt-6">
    <p class="text-blue-100 text-sm">
        ليس لديك حساب؟ 
        <a href="{{ route('register') }}" class="text-white font-semibold hover:underline">إنشاء حساب جديد</a>
    </p>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
// Setup Axios
axios.defaults.baseURL = '/api';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

const loginForm = document.getElementById('login-form');
const submitBtn = document.getElementById('submit-btn');
const errorMessage = document.getElementById('error-message');

loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Disable button
    submitBtn.disabled = true;
    submitBtn.textContent = 'جاري تسجيل الدخول...';
    errorMessage.classList.add('hidden');
    
    const formData = {
        login: document.getElementById('login').value,
        password: document.getElementById('password').value
    };
    
    try {
        const response = await axios.post('/auth/login', formData);
        
        if (response.data.success) {
            // Store token
            localStorage.setItem('auth_token', response.data.data.token);
            
            // Redirect to dashboard
            window.location.href = '/spa/accounts';
        }
    } catch (error) {
        // Show error
        let errorText = 'حدث خطأ أثناء تسجيل الدخول';
        
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
        submitBtn.textContent = 'تسجيل الدخول';
    }
});
</script>
@endpush
@endsection
