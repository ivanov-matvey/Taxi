document.addEventListener('DOMContentLoaded', async function () {
    const isAuthenticated = await AuthService.checkAuth();
    if (!isAuthenticated) return;

    await OrderService.fetchOrders();
    await OrderService.fetchFormData();

    const orderForm = document.getElementById('orderForm');
    if (orderForm) UIService.handleOrderFormSubmit(orderForm);

    const logoutButton = document.getElementById('logoutButton');
    if (logoutButton) UIService.handleLogoutButtonClick(logoutButton);
});
