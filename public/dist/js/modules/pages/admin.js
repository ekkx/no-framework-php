import {client} from "../api/client.js";

export class Admin {
    constructor() {
        const logoutButton = document.getElementById("logout-button")

        logoutButton && logoutButton.addEventListener("click", this.handleLogout);
    }

    handleLogout = async () => {
        document.cookie = `access_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
        window.location.href = "/auth/login";
    }
}
