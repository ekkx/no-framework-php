import {client} from "../api/client.js";

export class Signup {
    constructor() {
        const signupButton = document.getElementById("signup-button");

        signupButton && signupButton.addEventListener("click", this.handleSignup);
    }

    handleSignup = async () => {
        const username = document.getElementById("username").value;
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;
        const passwordConfirm = document.getElementById("password-confirm").value;

        const response = await client.user.create(username, email, password, passwordConfirm);
        if (response.ok) {
            window.location.href = "/auth/login";
            return
        }

        if (typeof response.message === "string") {
            alert(response.message);
        } else {
            let alertMessage = "";
            for (let key in response.message) {
                alertMessage += response.message[key].join(", ") + "\n";
            }
            alert(alertMessage);
        }
    }
}
