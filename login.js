// This file handles the login page behavior.
const loginForm = document.getElementById("loginForm");
const loginMessage = document.getElementById("loginMessage");

// Before showing the login page, check if the user is already logged in.
fetch("auth.php", { cache: "no-store" })
    .then(response => response.json())
    .then(data => {
        if (data.authenticated) {
            window.location.replace("index.html");
        }
    });

// When the login form is submitted, send the credentials to the backend.
loginForm.addEventListener("submit", async event => {
    event.preventDefault();
    loginMessage.textContent = "";

    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    const response = await fetch("login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password })
    });

    const data = await response.json();

    // If the login succeeds, redirect the user to the dashboard.
    if (data.success) {
        window.location.replace("index.html");
    } else {
        loginMessage.textContent = data.message || "Login failed";
    }
});
