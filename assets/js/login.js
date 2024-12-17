// coded by Efe

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');

    loginForm.addEventListener('submit', function(event) {
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();

        if (email === '' || password === '') {
            event.preventDefault();
            alert('Please fill in all required fields.');
        }

    });
});
