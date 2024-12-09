// assets/js/register.js

document.addEventListener('DOMContentLoaded', function () {
    const registrationForm = document.querySelector('.register-form');

    registrationForm.addEventListener('submit', function (e) {
        const username = document.getElementById('username').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        const requiredDomain = '@mail.mcgill.ca';

        // Username validation (e.g., length, allowed characters)
        if (username.length < 3) {
            e.preventDefault();
            alert('Username must be at least 3 characters long.');
            return;
        }

        // Email domain validation
        if (!email.endsWith(requiredDomain)) {
            e.preventDefault();
            alert('Email must end with ' + requiredDomain);
            return;
        }

        // Password strength validation
        if (password.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long.');
            return;
        }

        // Password match validation
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match.');
            return;
        }

    });
});
