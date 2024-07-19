import {signup} from "./pages/signup.js";
import {login} from "./pages/login.js";

export const app = {
    init: () => {
        document.addEventListener("DOMContentLoaded", () => {
            signup.init();
            login.init();
        });
    }
}
