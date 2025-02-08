// Function to check if the user has already accepted cookies
function checkCookieConsent() {
    if (localStorage.getItem('cookies_accepted')) {
        // If cookies are accepted, hide the consent pop-up
        document.getElementById('cookie-consent').style.display = 'none';
    } else {
        // If cookies are not accepted, show the pop-up
        document.getElementById('cookie-consent').style.display = 'block';
    }
}

// When the user clicks "I Accept"
document.getElementById('accept-cookies').addEventListener('click', function() {
    // Store the user's consent in localStorage
    localStorage.setItem('cookies_accepted', 'true');
    // Hide the pop-up
    document.getElementById('cookie-consent').style.display = 'none';
});

// When the user clicks "Decline"
document.getElementById('decline-cookies').addEventListener('click', function() {
    // Hide the pop-up, but do not store consent
    document.getElementById('cookie-consent').style.display = 'none';
});

// Check cookie consent status on page load
window.onload = checkCookieConsent;
