document.addEventListener("DOMContentLoaded", () => {
    // Select the edit and submit buttons
    const edit_button = document.getElementById('edit_button');
    const submit_button = document.getElementById('submit_profile_change');

    // Add a click event listener to the edit button
    edit_button.addEventListener("click", () => {
        // Hide the edit button
        edit_button.style.display = "none";
        
        // Show the submit button
        submit_button.style.display = "inline-block";

        // Enable all input fields except the disabled ones (email and phone)
        const formInputs = document.querySelectorAll('.updatable');
        formInputs.forEach(input => {
            input.removeAttribute('disabled');
        });
    });
});
