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
        const data = JSON.parse(await response.text());

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
