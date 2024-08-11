document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    form.addEventListener('submit', function (event) {
        const username = document.getElementById('username').value;
        if (username.trim() === '') {
            alert('Username is required');
            event.preventDefault(); 
        }
    });
});
