document.addEventListener('DOMContentLoaded', async function () {
    try {
        const response = await fetch('/api/auth/check-auth');
        const data = await response.json();

        if (data.success) {
            if (data.role === 'client') {
                window.location.href = `/client/dashboard.html`;
            } else if (data.role === 'driver') {
                window.location.href = `/driver/dashboard.html`;
            }
        }
    } catch (error) {
        console.error(error);
    }
})

document.getElementById('loginForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const formObject = {};

    formData.forEach((value, key) => {
        formObject[key] = value;
    });

    try {
        const response = await fetch('api/user/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formObject),
        });

        console.log('Response Status:', response.status);
        const data = await response.json();

        if (data.success) {
            console.log('Authorization successful.');

            if (data.redirect) {
                window.location.href = data.redirect;
            }
        }
    } catch (error) {
        console.error(error);
    }
});
