class UIService {
    static handleLoginFormSubmit(form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = Object.fromEntries(new FormData(this).entries());
            await AuthService.login(formData);
        });
    }

    static handleRegisterFormSubmit(form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = Object.fromEntries(new FormData(this).entries());
            await AuthService.register(formData);
        });
    }

    static handleLogoutButtonClick(button) {
        button.addEventListener('click', async function () {
            await AuthService.logout();
        });
    }

    static handleOrderFormSubmit(form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const orderData = Object.fromEntries(formData.entries());

            if (!orderData.price || !orderData.date || !orderData.car_id || !orderData.driver_id || !orderData.client_id) {
                alert('Заполните все поля!');
                return;
            }

            orderData.price = parseFloat(orderData.price);
            orderData.baby = orderData.baby ? 1 : 0;
            orderData.car_id = parseInt(orderData.car_id, 10);
            orderData.driver_id = parseInt(orderData.driver_id, 10);
            orderData.client_id = parseInt(orderData.client_id, 10);

            await OrderService.addOrder(orderData);
            form.reset();
            await OrderService.fetchOrders();
        });
    }
}
