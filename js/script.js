// Basic client-side validation and enhancements for the land registry UI
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form');

    forms.forEach((form) => {
        form.addEventListener('submit', function (event) {
            const inputs = Array.from(form.querySelectorAll('input[required]'));
            const isValid = inputs.every((input) => input.value.trim().length > 0);

            if (!isValid) {
                event.preventDefault();
                alert('Please fill out all required fields before submitting.');
            }
        });
    });
});
