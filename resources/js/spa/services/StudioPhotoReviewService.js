import axios from 'axios';

export class StudioPhotoReviewService {
    static async getPending() {
        try {
            const response = await axios.get('/studio/photo-review/pending');
            return response.data.data;
        } catch (error) {
            console.error('Error fetching pending photos:', error);
            throw error;
        }
    }

    static async review(photoId, status, rejectionReason = null) {
        try {
            const response = await axios.post(`/studio/photo-review/${photoId}/review`, {
                status,
                rejection_reason: rejectionReason
            });
            return response.data;
        } catch (error) {
            console.error('Error reviewing photo:', error);
            throw error;
        }
    }
}
