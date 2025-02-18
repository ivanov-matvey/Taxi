document.getElementById('logoutButton').addEventListener('click', async function (e) {
    try {
        const response = await fetch('/api/user/logout');
        const data = await response.json();

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
