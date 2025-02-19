class AuthService {
    static async checkAuth() {
        try {
            const data = await ApiService.get('/api/auth/check-auth');
            if (!data.success || !data.role) {
                window.location.href = `/login.html`;
                return false;
            }

            const currentPath = window.location.pathname.split('/');
            if (data.role !== currentPath[1]) {
                window.location.href = `/${data.role}/dashboard.html`;
                return false;
            }

            return true;
        } catch (error) {
            console.error('Error checking auth:', error);
            window.location.href = `/login.html`;
            return false;
        }
    }

    static async login(formData) {
        try {
            const data = await ApiService.post('/api/user/login', formData);
            if (data.success && data.redirect) {
                window.location.href = data.redirect;
            }
        } catch (error) {
            console.error('Login failed:', error);
        }
    }

    static async register(formData) {
        try {
            const data = await ApiService.post('/api/user/register', formData);
            if (data.success && data.redirect) {
                window.location.href = data.redirect;
            }
        } catch (error) {
            console.error('Registration failed:', error);
        }
    }

    static async logout() {
        try {
            const data = await ApiService.post('/api/user/logout');
            if (data.success && data.redirect) {
                window.location.href = data.redirect;
            }
        } catch (error) {
            console.error('Logout failed:', error);
        }
    }
}
