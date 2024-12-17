// coded by William Rodzen-Staff
document.addEventListener('DOMContentLoaded', function () {



    const settingsForm = document.querySelector('.settings-form');
    const majorSelect = document.getElementById('major');
    const minorSelect = document.getElementById('minor');

    if (!majorSelect || !minorSelect || !settingsForm) {
        return;
    }

    function disable_minor() {
        const selectedMajorName = majorSelect.options[majorSelect.selectedIndex].getAttribute('data-name') || '';

        Array.from(minorSelect.options).forEach(option => {
            const minorName = option.getAttribute('data-name') || '';
            if (minorName === selectedMajorName && option.value !== "") {
                option.disabled = true;
            } else {
                option.disabled = false;
            }
        });

        const currentMinorName = minorSelect.options[minorSelect.selectedIndex].getAttribute('data-name') || '';
        if (currentMinorName === selectedMajorName) {
            minorSelect.value = '';
        }
    }

    disable_minor();

    majorSelect.addEventListener('change', disable_minor);




    settingsForm.addEventListener('submit', function (e) {

        const major = majorSelect.value;
        const minor = minorSelect.value;
        const newUsername = document.getElementById('new_username').value.trim();

        const usernameRegex = /^[A-Za-z0-9_.]{3,20}$/;

        if (newUsername && !usernameRegex.test(newUsername)) {
            e.preventDefault();
            alert('Username must be 3-20 characters long and can include letters, numbers, underscores, and periods.');
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
