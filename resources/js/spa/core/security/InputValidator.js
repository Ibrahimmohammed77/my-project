/**
 * Input Validator Module
 * Client-side validation with localized error messages
 */

export class InputValidator {
    constructor(rules = {}) {
        this.rules = rules;
        this.errors = {};
        this.messages = {
            required: 'هذا الحقل مطلوب',
            email: 'البريد الإلكتروني غير صالح',
            min: 'الحد الأدنى :min أحرف',
            max: 'الحد الأقصى :max أحرف',
            numeric: 'يجب أن يكون رقمًا',
            alphanumeric: 'يجب أن يحتوي على حروف وأرقام فقط',
            alpha: 'يجب أن يحتوي على حروف فقط',
            url: 'الرابط غير صالح',
            phone: 'رقم الهاتف غير صالح',
            strong: 'كلمة المرور يجب أن تحتوي على حروف كبيرة وصغيرة وأرقام ورموز',
            confirmed: 'التأكيد غير مطابق',
            between: 'يجب أن يكون بين :min و :max',
            in: 'القيمة غير صالحة',
            unique: 'القيمة موجودة مسبقًا',
            regex: 'التنسيق غير صالح'
        };
    }

    /**
     * Validate data against rules
     * @param {Object} data - Data to validate
     * @returns {boolean} - Whether validation passed
     */
    validate(data) {
        this.errors = {};
        let isValid = true;

        for (const [field, fieldRules] of Object.entries(this.rules)) {
            const value = data[field];
            const rules = Array.isArray(fieldRules) ? fieldRules : [fieldRules];

            for (const rule of rules) {
                const result = this.validateRule(field, value, rule, data);
                
                if (!result.valid) {
                    if (!this.errors[field]) {
                        this.errors[field] = [];
                    }
                    this.errors[field].push(result.message);
                    isValid = false;
                }
            }
        }

        return isValid;
    }

    /**
     * Validate a single rule
     * @param {string} field - Field name
     * @param {*} value - Field value
     * @param {string|Object} rule - Validation rule
     * @param {Object} data - All data (for confirmed validation)
     * @returns {Object} - Validation result
     */
    validateRule(field, value, rule, data = {}) {
        // Parse rule (e.g., "min:3" or "between:5,10")
        let ruleName, params = [];
        
        if (typeof rule === 'string') {
            const parts = rule.split(':');
            ruleName = parts[0];
            params = parts[1] ? parts[1].split(',') : [];
        } else if (typeof rule === 'object') {
            ruleName = rule.name;
            params = rule.params || [];
        } else {
            ruleName = rule;
        }

        // Execute validation
        const validator = this.validators[ruleName];
        
        if (!validator) {
            console.warn(`Unknown validation rule: ${ruleName}`);
            return { valid: true };
        }

        const isValid = validator.call(this, value, params, data);
        
        return {
            valid: isValid,
            message: isValid ? '' : this.formatMessage(ruleName, params, field)
        };
    }

    /**
     * Format error message with parameters
     * @param {string} ruleName - Rule name
     * @param {Array} params - Rule parameters
     * @param {string} field - Field name
     * @returns {string} - Formatted message
     */
    formatMessage(ruleName, params, field) {
        let message = this.messages[ruleName] || 'حقل غير صالح';
        
        params.forEach((param, index) => {
            message = message.replace(`:${index === 0 ? 'min' : 'max'}`, param);
        });
        
        return message.replace(':field', field);
    }

    /**
     * Get validation errors
     * @returns {Object} - Errors object
     */
    getErrors() {
        return this.errors;
    }

    /**
     * Get first error for a field
     * @param {string} field - Field name
     * @returns {string|null} - First error message
     */
    getFirstError(field) {
        return this.errors[field] ? this.errors[field][0] : null;
    }

    /**
     * Check if field has errors
     * @param {string} field - Field name
     * @returns {boolean} - Whether field has errors
     */
    hasError(field) {
        return !!this.errors[field];
    }

    /**
     * Clear all errors
     */
    clearErrors() {
        this.errors = {};
    }

    /**
     * Validation rules
     */
    validators = {
        required(value) {
            if (value === null || value === undefined) return false;
            if (typeof value === 'string') return value.trim().length > 0;
            if (Array.isArray(value)) return value.length > 0;
            return true;
        },

        email(value) {
            if (!value) return true;
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(value);
        },

        min(value, [minLength]) {
            if (!value) return true;
            return String(value).length >= parseInt(minLength, 10);
        },

        max(value, [maxLength]) {
            if (!value) return true;
            return String(value).length <= parseInt(maxLength, 10);
        },

        numeric(value) {
            if (!value) return true;
            return !isNaN(value) && !isNaN(parseFloat(value));
        },

        alphanumeric(value) {
            if (!value) return true;
            return /^[a-zA-Z0-9_]+$/.test(value);
        },

        alpha(value) {
            if (!value) return true;
            return /^[a-zA-Z]+$/.test(value);
        },

        url(value) {
            if (!value) return true;
            try {
                new URL(value);
                return true;
            } catch {
                return false;
            }
        },

        phone(value) {
            if (!value) return true;
            // Yemeni phone format
            return /^(009677|9677|\+9677|07)([0-9]{8})$/.test(value);
        },

        strong(value) {
            if (!value) return true;
            // At least 8 chars, uppercase, lowercase, number, special char
            return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/.test(value);
        },

        confirmed(value, params, data) {
            if (!value) return true;
            const confirmField = params[0] || 'password_confirmation';
            return value === data[confirmField];
        },

        between(value, [min, max]) {
            if (!value) return true;
            const length = String(value).length;
            return length >= parseInt(min, 10) && length <= parseInt(max, 10);
        },

        in(value, allowedValues) {
            if (!value) return true;
            return allowedValues.includes(String(value));
        },

        regex(value, [pattern]) {
            if (!value) return true;
            const regex = new RegExp(pattern);
            return regex.test(value);
        }
    };

    /**
     * Static helper for required validation
     */
    static validateRequired(value) {
        if (value === null || value === undefined) return false;
        if (typeof value === 'string') return value.trim().length > 0;
        if (Array.isArray(value)) return value.length > 0;
        return true;
    }

    /**
     * Static helper for email validation
     */
    static validateEmail(value) {
        if (!value) return true;
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(value);
    }

    /**
     * Static helper for length validation
     */
    static validateLength(value, min = 0, max = Infinity) {
        if (!value) return min === 0;
        const length = String(value).length;
        return length >= min && length <= max;
    }
}

// Make it globally accessible
if (typeof window !== 'undefined') {
    window.InputValidator = InputValidator;
}
