class ApiService {
    static async request(url, options = {}) {
        try {
            const response = await fetch(url, {
                ...options,
                headers: {
                    'Content-Type': 'application/json',
                    ...options.headers,
                },
            });

            return await response.json();
        } catch (error) {
            console.error('API Request failed:', error);
        }
    }

    static get(url) {
        return this.request(url, { method: 'GET' });
    }

    static post(url, body) {
        return this.request(url, {
            method: 'POST',
            body: JSON.stringify(body),
        });
    }
}
