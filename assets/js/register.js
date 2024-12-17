// coded by Efe

document.addEventListener('DOMContentLoaded', function () {
    const registrationForm = document.querySelector('.register-form');
    const majorSelect = document.getElementById('major');
    const minorSelect = document.getElementById('minor');

    if (!majorSelect || !minorSelect || !registrationForm) {
        console.error("Major, Minor select elements or registration form not found.");
        return;
    }

    function disable_minor() {
        const selectedMajorName = majorSelect.options[majorSelect.selectedIndex].getAttribute('data-name') || '';

        Array.from(minorSelect.options).forEach(option => {
            const minorName = option.getAttribute('data-name') || '';
            if (minorName === selectedMajorName && option.value !== "") {
                option.disabled = true;
                console.log(`Disabled minor option: ${minorName}`);
            } else {
                option.disabled = false;
            }
        });

        const currentMinorName = minorSelect.options[minorSelect.selectedIndex].getAttribute('data-name') || '';
        if (currentMinorName === selectedMajorName) {
            minorSelect.value = '';
            console.log("Minor reset because it matched the major.");
        }
    }

    disable_minor();



    majorSelect.addEventListener('change', disable_minor);



    registrationForm.addEventListener('submit', function (e) {
        const username = document.getElementById('username').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const major = majorSelect.value;
        const minor = minorSelect.value;

        const requiredDomain = '@mail.mcgill.ca';

        if (username.length < 3) {
            e.preventDefault();
            alert('Username must be at least 3 characters long.');
            return;
        }

        if (!email.endsWith(requiredDomain)) {
            e.preventDefault();
            alert('Email must end with ' + requiredDomain);
            return;
        }

        if (password.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long.');
            return;
        }

        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match.');
            return;
        }

        const selectedMajorName = majorSelect.options[majorSelect.selectedIndex].getAttribute('data-name') || '';
        const selectedMinorName = minorSelect.options[minorSelect.selectedIndex].getAttribute('data-name') || '';

        if (selectedMinorName && selectedMinorName === selectedMajorName) {
            e.preventDefault();
            alert('Minor cannot be the same as Major.');
            return;
        }
    });
});
