document.addEventListener('DOMContentLoaded', async function () {
    try {
        const data = await ApiService.get('/api/auth/check-auth');
        if (data.success && data.role) {
            window.location.href = `/${data.role}/dashboard.html`;
        }
    } catch (error) {
        console.error(error);
    }
});

const loginForm = document.getElementById('loginForm');
if (loginForm) UIService.handleLoginFormSubmit(loginForm);

const registerForm = document.getElementById('registerForm');
if (registerForm) UIService.handleRegisterFormSubmit(registerForm);
