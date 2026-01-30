/**
 * Formatters
 * Utility functions for formatting dates, numbers, etc.
 */

export const Formatters = {
    /**
     * Format date to Arabic locale
     * @param {string|Date} date - Date to format
     * @param {string} format - Format type (short|long|full)
     * @returns {string} - Formatted date
     */
    date(date, format = 'short') {
        if (!date) return '';

        const dateObj = date instanceof Date ? date : new Date(date);
        
        if (isNaN(dateObj.getTime())) {
            return '';
        }

        const options = {
            short: { year: 'numeric', month: '2-digit', day: '2-digit' },
            long: { year: 'numeric', month: 'long', day: 'numeric' },
            full: { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }
        };

        return dateObj.toLocaleDateString('ar-YE', options[format] || options.short);
    },

    /**
     * Format time to Arabic locale
     * @param {string|Date} date - Date/time to format
     * @returns {string} - Formatted time
     */
    time(date) {
        if (!date) return '';

        const dateObj = date instanceof Date ? date : new Date(date);
        
        if (isNaN(dateObj.getTime())) {
            return '';
        }

        return dateObj.toLocaleTimeString('ar-YE', {
            hour: '2-digit',
            minute: '2-digit'
        });
    },

    /**
     * Format datetime to Arabic locale
     * @param {string|Date} date - DateTime to format
     * @returns {string} - Formatted datetime
     */
    datetime(date) {
        if (!date) return '';
        
        return `${this.date(date, 'long')} - ${this.time(date)}`;
    },

    /**
     * Format number with thousands separator
     * @param {number} number - Number to format
     * @param {number} decimals - Number of decimal places
     * @returns {string} - Formatted number
     */
    number(number, decimals = 0) {
        if (number === null || number === undefined || isNaN(number)) {
            return '0';
        }

        return parseFloat(number).toLocaleString('ar-YE', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    },

    /**
     * Format currency
     * @param {number} amount - Amount to format
     * @param {string} currency - Currency code
     * @returns {string} - Formatted currency
     */
    currency(amount, currency = 'YER') {
        if (amount === null || amount === undefined || isNaN(amount)) {
            return '0 ريال';
        }

        const formatted = this.number(amount, 2);
        
        const symbols = {
            YER: 'ريال',
            USD: '$',
            SAR: 'ريال سعودي'
        };

        return `${formatted} ${symbols[currency] || currency}`;
    },

    /**
     * Format file size
     * @param {number} bytes - Size in bytes
     * @returns {string} - Formatted size
     */
    fileSize(bytes) {
        if (!bytes || bytes === 0) return '0 بايت';

        const units = ['بايت', 'كيلوبايت', 'ميجابايت', 'جيجابايت', 'تيرابايت'];
        const i = Math.floor(Math.log(bytes) / Math.log(1024));
        
        return `${(bytes / Math.pow(1024, i)).toFixed(2)} ${units[i]}`;
    },

    /**
     * Format percentage
     * @param {number} value - Value (0-100)
     * @param {number} decimals - Decimal places
     * @returns {string} - Formatted percentage
     */
    percentage(value, decimals = 0) {
        if (value === null || value === undefined || isNaN(value)) {
            return '0%';
        }

        return `${this.number(value, decimals)}%`;
    },

    /**
     * Format phone number (Yemeni format)
     * @param {string} phone - Phone number
     * @returns {string} - Formatted phone
     */
    phone(phone) {
        if (!phone) return '';

        // Remove non-digits
        const cleaned = phone.replace(/\D/g, '');

        // Format as: 077X XXX XXXX
        if (cleaned.length === 9 && cleaned.startsWith('7')) {
            return `0${cleaned.substr(0, 3)} ${cleaned.substr(3, 3)} ${cleaned.substr(6)}`;
        }

        return phone;
    },

    /**
     * Truncate text
     * @param {string} text - Text to truncate
     * @param {number} length - Max length
     * @param {string} suffix - Suffix (e.g., '...')
     * @returns {string} - Truncated text
     */
    truncate(text, length = 50, suffix = '...') {
        if (!text || text.length <= length) {
            return text || '';
        }

        return text.substring(0, length).trim() + suffix;
    },

    /**
     * Capitalize first letter
     * @param {string} text - Text to capitalize
     * @returns {string} - Capitalized text
     */
    capitalize(text) {
        if (!text) return '';
        return text.charAt(0).toUpperCase() + text.slice(1);
    },

    /**
     * Format relative time (e.g., "منذ ساعتين")
     * @param {string|Date} date - Date to format
     * @returns {string} - Relative time
     */
    relativeTime(date) {
        if (!date) return '';

        const dateObj = date instanceof Date ? date : new Date(date);
        if (isNaN(dateObj.getTime())) return '';

        const now = new Date();
        const diff = now - dateObj;
        const seconds = Math.floor(diff / 1000);
        const minutes = Math.floor(seconds / 60);
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);
        const months = Math.floor(days / 30);
        const years = Math.floor(days / 365);

        if (seconds < 60) return 'الآن';
        if (minutes < 60) return `منذ ${minutes} دقيقة`;
        if (hours < 24) return `منذ ${hours} ساعة`;
        if (days < 30) return `منذ ${days} يوم`;
        if (months < 12) return `منذ ${months} شهر`;
        return `منذ ${years} سنة`;
    }
};

export default Formatters;
