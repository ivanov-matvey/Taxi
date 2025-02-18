document.addEventListener('DOMContentLoaded', async function () {
    try {
        const response = await fetch('/api/auth/check-auth');
        const data = await response.json();

        if (data.success) {
            if (data.role === 'client') {
                alert('Already authenticated.')
                window.location.href = `/client/dashboard.html`;
            } else if (data.role === 'driver') {
                alert('Already authenticated.')
                window.location.href = `/driver/dashboard.html`;
            }
        }
    } catch (error) {
        console.error(error);
    }
})

document.getElementById('registerForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const formObject = {};

    formData.forEach((value, key) => {
        formObject[key] = value;
    });

    try {
        const response = await fetch('api/user/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formObject),
        });

        console.log('Response Status:', response.status);
        const data = await response.json();

        if (data.success) {
            console.log('Registration successful.');

            if (data.redirect) {
                window.location.href = data.redirect;
            }
        }
    } catch (error) {
        console.error(error);
    }
});
