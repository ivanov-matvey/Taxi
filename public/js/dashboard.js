document.addEventListener('DOMContentLoaded', async function () {
    try {
        const response = await fetch('/api/auth/check-auth');
        const data = await response.json();

        const pathParts = window.location.pathname.split('/');
        const userRole = pathParts[1];

        if (!data.success || !data.role) {
            window.location.href = `/login.html`;
        } else if (data.role !== userRole) {
            window.location.href = `/${data.role}/dashboard.html`
        }
    } catch (error) {
        console.error(error);
    }
})
