// script.js
document.addEventListener("DOMContentLoaded", function () {
    const signUpButton = document.getElementById('signUpButton');
    const signInButton = document.getElementById('signInButton');
    const signupForm = document.getElementById('signup');
    const signInForm = document.getElementById('signIn');

    signUpButton.addEventListener('click', () => {
        signInForm.style.display = 'none';
        signupForm.style.display = 'block';
    });

    signInButton.addEventListener('click', () => {
        signupForm.style.display = 'none';
        signInForm.style.display = 'block';
    });
});