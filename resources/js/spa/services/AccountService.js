import axios from 'axios';
import { Account } from '../models/Account';

export class AccountService {
    static async getAll() {
        try {
            const response = await axios.get('/accounts');
            return response.data.data.accounts.map(accountData => Account.fromJson(accountData));
        } catch (error) {
            console.error('Error fetching accounts:', error);
            throw error;
        }
    }

    static async create(account) {
        try {
            const response = await axios.post('/accounts', account.toJson());
            return response.data;
        } catch (error) {
            console.error('Error creating account:', error);
            throw error;
        }
    }

    static async update(id, account) {
        try {
            const response = await axios.put(`/accounts/${id}`, account.toJson());
            return response.data;
        } catch (error) {
            console.error('Error updating account:', error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            await axios.delete(`/accounts/${id}`);
        } catch (error) {
            console.error('Error deleting account:', error);
            throw error;
        }
    }
}
