import {client} from "../api/client.js";

export const signup = {
    signupButton: document.getElementById("signup-button"),

    handleSignup: async () => {
        const username = document.getElementById("username").value;
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;
        const passwordConfirm = document.getElementById("password-confirm").value;

        await client.user.create(username, email, password, passwordConfirm);
    },

    init() {
        this.signupButton && this.signupButton.addEventListener("click", this.handleSignup);
    }
}
