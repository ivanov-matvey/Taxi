document.getElementById("registerForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    // Получаем данные формы
    const formData = new FormData(this);

    try {
        const response = await fetch('server/register.php', {
            method: 'POST',
            body: formData,
        });

        console.log('Статус ответа:', response.status);

        if (!response.ok) {
            throw new Error("Ошибка при запросе. Статус: ${response.status}");
        }

        const text = await response.text();

        // Парсим JSON-ответ
        const data = JSON.parse(text);

        if (data.success) {
            console.log("Регистрация успешна");

            if (data.redirect) {
                window.location.href = data.redirect;
            }
        } else {
            console.log("Ошибка: " + data.error);
        }
    } catch (error) {
        // Логирование ошибок, если что-то пошло не так
        console.error("Ошибка при отправке запроса или парсинге JSON:", error);
        alert("Произошла ошибка при отправке запроса.");
    }
});