// Utility function to hide all popovers
function hideAllPopovers() {
    const loginPopover = document.getElementById('loginPopover');
    const signupPopover = document.getElementById('signupPopover');
    const userPopover = document.getElementById('userPopover');
    const cartPopover = document.getElementById('cartPopover');

    // Hide all popovers
    loginPopover.style.display = 'none';
    signupPopover.style.display = 'none';
    userPopover.classList.add('hidden');
    cartPopover.classList.add('hidden');
}

// Toggle User Popover
function toggleUserPopover() {
    const userPopover = document.getElementById('userPopover');
    const isHidden = userPopover.classList.contains('hidden');

    // Hide all other popovers
    hideAllPopovers();

    // Toggle the user popover
    if (isHidden) {
        userPopover.classList.remove('hidden');
    }
}

// Toggle Cart Popover
function toggleCartPopover() {
    const cartPopover = document.getElementById('cartPopover');
    const isHidden = cartPopover.classList.contains('hidden');

    // Hide all other popovers
    hideAllPopovers();

    // Toggle the cart popover
    if (isHidden) {
        cartPopover.classList.remove('hidden');
    }
}

// Show Login Popover
function showLoginPopover() {
    hideAllPopovers();
    const loginPopover = document.getElementById('loginPopover');
    loginPopover.style.display = 'flex';
}

// Close Login Popover
function closeLoginPopover() {
    const loginPopover = document.getElementById('loginPopover');
    loginPopover.style.display = 'none';
}

// Show Signup Popover
function showSignupPopover() {
    hideAllPopovers();
    const signupPopover = document.getElementById('signupPopover');
    signupPopover.style.display = 'flex';
}

// Close Signup Popover
function closeSignupPopover() {
    const signupPopover = document.getElementById('signupPopover');
    signupPopover.style.display = 'none';
}