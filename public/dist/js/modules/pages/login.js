import {client} from "../api/client.js";

export const login = {
    loginButton: document.getElementById("login-button"),

    handleLogin: async () => {
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;

        await client.user.login(email, password);
    },

    init: () => {
        this.loginButton && this.loginButton.addEventListener("click", this.handleLogin);
    }
}
