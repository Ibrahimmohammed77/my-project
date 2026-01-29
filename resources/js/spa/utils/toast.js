export function showToast(message, type = 'success') {
    // Create toast container if it doesn't exist
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed bottom-4 left-4 z-50 flex flex-col gap-2 pointer-events-none';
        document.body.appendChild(container); // Append to body to ensure it's on top
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `
        pointer-events-auto
        flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg transform transition-all duration-300 translate-y-10 opacity-0
        ${type === 'success' ? 'bg-green-600 text-white' : ''}
        ${type === 'error' ? 'bg-red-600 text-white' : ''}
        ${type === 'info' ? 'bg-blue-600 text-white' : ''}
        ${type === 'warning' ? 'bg-yellow-500 text-white' : ''}
        min-w-[300px] max-w-md
    `;

    // Icon based on type
    const iconClass = type === 'success' ? 'fa-check-circle' :
                      type === 'error' ? 'fa-exclamation-circle' :
                      type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';

    toast.innerHTML = `
        <i class="fas ${iconClass} text-lg"></i>
        <p class="text-sm font-medium flex-1">${message}</p>
        <button onclick="this.parentElement.remove()" class="text-white/70 hover:text-white transition-colors">
            <i class="fas fa-times"></i>
        </button>
    `;

    container.appendChild(toast);

    // Animation In
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-10', 'opacity-0');
    });

    // Auto remove
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-4');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

export function clearErrors() {
    document.querySelectorAll('.field-error').forEach(el => {
        el.textContent = '';
        el.classList.add('hidden');
    });
    
    // Also remove error styles from inputs if any (optional, can implementation later)
    document.querySelectorAll('input, select').forEach(el => {
        el.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500/20');
    });
}

export function showErrors(errors) {
    clearErrors();
    
    // errors object from Laravel resource: { field_name: ['Error message 1'] }
    for (const [field, messages] of Object.entries(errors)) {
        // Find error element by ID pattern
        const errorEl = document.getElementById(`${field}-error`);
        if (errorEl) {
            errorEl.textContent = messages[0];
            errorEl.classList.remove('hidden');
        }

        // Highlight input
        const inputEl = document.getElementById(field);
        if (inputEl) {
            inputEl.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500/20');
            
            // Add input listener to clear error on typing
            inputEl.addEventListener('input', function() {
                this.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500/20');
                if (errorEl) {
                    errorEl.classList.add('hidden');
                    errorEl.textContent = '';
                }
            }, { once: true });
        }
    }
}
