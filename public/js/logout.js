document.getElementById("logoutButton").addEventListener("click", async function () {
    try {
        const response = await fetch('/server/logout.php', {
            method: 'POST',
        });

        const data = await response.json();

        if (data.success) {
            console.log("Выход успешен.");
            window.location.href = '/login.html';
        } else {
            console.error("Ошибка при выходе: " + data.error);
        }
    } catch (error) {
        console.error("Ошибка при отправке запроса на выход:", error);
    }
});