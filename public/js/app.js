document.addEventListener('DOMContentLoaded', async function () {
    if (await AuthService.checkAuth()) {
        await OrderService.fetchOrders();
        await OrderService.fetchFormData();
    }

    const orderForm = document.getElementById('orderForm');
    if (orderForm) UIService.handleOrderFormSubmit(orderForm);

    const loginForm = document.getElementById('loginForm');
    if (loginForm) UIService.handleLoginFormSubmit(loginForm);

    const registerForm = document.getElementById('registerForm');
    if (registerForm) UIService.handleRegisterFormSubmit(registerForm);

    const logoutButton = document.getElementById('logoutButton');
    if (logoutButton) UIService.handleLogoutButtonClick(logoutButton);
});
