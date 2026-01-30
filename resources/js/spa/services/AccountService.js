/**
 * Account Service
 * Handles API calls for account management using new ApiClient
 */

import ApiClient from '../core/api/ApiClient.js';
import { API_ENDPOINTS, getEndpoint } from '../core/api/endpoints.js';
import { Account } from '../models/Account.js';

export class AccountService {
    /**
     * Get all accounts
     * @returns {Promise<Array<Account>>} - List of accounts
     */
    static async getAll() {
        const response = await ApiClient.get(API_ENDPOINTS.ACCOUNTS.LIST);
        
        if (response.data && response.data.data && response.data.data.accounts) {
            return response.data.data.accounts.map(accountData => Account.fromJson(accountData));
        }
        
        return [];
    }

    /**
     * Create new account
     * @param {Object} accountData - Account data
     * @returns {Promise<Account>} - Created account
     */
    static async create(accountData) {
        const data = accountData instanceof Account ? accountData.toJson() : accountData;
        
        const response = await ApiClient.post(API_ENDPOINTS.ACCOUNTS.CREATE, data);
        
        if (response.data && response.data.data && response.data.data.account) {
            return Account.fromJson(response.data.data.account);
        }
        
        return response.data;
    }

    /**
     * Update existing account
     * @param {number} id - Account ID
     * @param {Object} accountData - Account data
     * @returns {Promise<Account>} - Updated account
     */
    static async update(id, accountData) {
        const data = accountData instanceof Account ? accountData.toJson() : accountData;
        
        const response = await ApiClient.put(getEndpoint(API_ENDPOINTS.ACCOUNTS.UPDATE, id), data);
        
        if (response.data && response.data.data && response.data.data.account) {
            return Account.fromJson(response.data.data.account);
        }
        
        return response.data;
    }

    /**
     * Delete account
     * @param {number} id - Account ID
     * @returns {Promise<void>}
     */
    static async delete(id) {
        await ApiClient.delete(getEndpoint(API_ENDPOINTS.ACCOUNTS.DELETE, id));
    }

    /**
     * Get single account
     * @param {number} id - Account ID
     * @returns {Promise<Account>} - Account
     */
    static async getById(id) {
        const response = await ApiClient.get(getEndpoint(API_ENDPOINTS.ACCOUNTS.SHOW, id));
        
        if (response.data && response.data.data && response.data.data.account) {
            return Account.fromJson(response.data.data.account);
        }
        
        return null;
    }
}

export default AccountService;

