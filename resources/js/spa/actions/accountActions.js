import { AccountService } from '../services/AccountService';

/**
 * Handles the creation or update of an account.
 * 
 * @param {string|null} id - The account ID if updating, or null if creating.
 * @param {Object} accountData - The account data object.
 * @param {string|null} password - The password value (if provided).
 * @param {string|null} passwordConfirmation - The password confirmation value (if provided).
 * @returns {Promise<void>}
 */
export async function saveAccount(id, accountData, password = null, passwordConfirmation = null) {
    // Add password to data if provided
    if (password) {
        accountData.password = password;
        accountData.password_confirmation = passwordConfirmation;
    }

    if (id) {
        // Update logic
        await AccountService.update(id, accountData);
    } else {
        // Create logic
        if (!password) {
            throw new Error('كلمة المرور مطلوبة عند إنشاء حساب جديد');
        }
        await AccountService.create(accountData);
    }
}
