import {client} from "../api/client.js";

export class Login {
    constructor() {
        const loginButton = document.getElementById("login-button")

        loginButton && loginButton.addEventListener("click", this.handleLogin);
    }

    handleLogin = async () => {
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;

        const response = await client.user.login(email, password);
        if (response.ok) {
            document.cookie = `access_token=${response.accessToken}; path=/`;
            window.location.href = "/admin";
            return
        }

        alert(response.message);
    }
}
