import {Signup} from "./pages/signup.js";
import {Login} from "./pages/login.js";
import {Admin} from "./pages/admin.js";

export const app = {
    init: () => {
        document.addEventListener("DOMContentLoaded", () => {
            new Signup();
            new Login();
            new Admin();
        });
    }
}
