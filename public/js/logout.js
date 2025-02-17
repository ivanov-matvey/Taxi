document.getElementById('logoutButton').addEventListener('click', async function (e) {
    try {
        const response = await fetch('api/user/logout', {
            method: 'POST',
        });

        console.log('Response Status:', response.status);
        const data = JSON.parse(await response.text());

        if (data.success) {
            console.log('Logout successful.');

            if (data.redirect) {
                window.location.href = data.redirect;
            }
        }
    } catch (error) {
        console.error(error);
    }
});
