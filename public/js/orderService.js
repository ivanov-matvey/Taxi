class OrderService {
    static async fetchOrders() {
        try {
            const data = await ApiService.get('/api/order/list');
            const tableBody = document.getElementById('ordersTableBody');
            if (!tableBody) return;

            tableBody.innerHTML = '';
            data.data.forEach(order => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${order['id']}</td>
                    <td>${order['price']}</td>
                    <td>${order['date']}</td>
                    <td>${order['baby'] ? 'Да' : 'Нет'}</td>
                    <td>${order['car_number']}</td>
                    <td>${order['driver_phone']}</td>
                    <td>${order['client_phone']}</td>
                `;
                tableBody.appendChild(row);
            });
        } catch (error) {
            console.error('Error fetching orders:', error);
        }
    }

    static async fetchFormData() {
        try {
            const data = await ApiService.get('/api/order/form-data');
            const carSelect = document.getElementById('car_id');
            const driverSelect = document.getElementById('driver_id');
            const clientSelect = document.getElementById('client_id');

            if (!carSelect || !driverSelect || !clientSelect) return;

            carSelect.innerHTML = data['cars'].map(car => `<option value="${car.id}">${car.number}</option>`).join('');
            driverSelect.innerHTML = data['drivers'].map(driver => `<option value="${driver.id}">${driver.phone}</option>`).join('');
            clientSelect.innerHTML = data['clients'].map(client => `<option value="${client.id}">${client.phone}</option>`).join('');
        } catch (error) {
            console.error('Error fetching form data:', error);
        }
    }

    static async addOrder(orderData) {
        try {
            return await ApiService.post('/api/order/add', orderData);
        } catch (error) {
            console.error('Error adding order:', error);
        }
    }
}
