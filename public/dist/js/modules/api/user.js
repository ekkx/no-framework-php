export const user = {
    async login(email, password) {
        const response = await fetch("/api/users/login", {
            method: "POST",
            body: JSON.stringify({
                email: email,
                password: password,
            })
        });

        return response.json();
    },

    async create(username, email, password, passwordConfirm) {
        const response = await fetch("/api/users/create", {
            method: "POST",
            body: JSON.stringify({
                username: username,
                email: email,
                password: password,
                passwordConfirm: passwordConfirm
            })
        });

        return response.json();
    },
}
