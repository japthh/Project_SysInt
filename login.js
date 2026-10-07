const loginForm = document.getElementById("loginForm");
const loginMessage = document.getElementById("loginMessage");

fetch("auth.php", { cache: "no-store" })
    .then(response => {
        if (!response.ok) throw new Error("Unable to check login status.");
        return response.json();
    })
    .then(data => {
        if (data.authenticated) {
            window.location.replace("index.html");
        }
    })
    .catch(error => {
        loginMessage.textContent = error.message;
    });

loginForm.addEventListener("submit", async event => {
    event.preventDefault();
    loginMessage.textContent = "";

    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    try {
        const response = await fetch("login.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ username, password })
        });
        const data = await response.json();

        if (!response.ok || !data.success) {
            loginMessage.textContent = data.message || "Unable to log in.";
            return;
        }

        window.location.replace("index.html");
    } catch (error) {
        loginMessage.textContent = "Unable to reach the server. Please try again.";
    }
});
