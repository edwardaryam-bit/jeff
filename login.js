document.addEventListener('DOMContentLoaded', () => {
    const authForm = document.getElementById('auth-form');
    const authTitle = document.getElementById('auth-title');
    const authSubmit = document.getElementById('auth-submit');
    const signupFields = document.getElementById('signup-fields');
    const forgotFields = document.getElementById('forgot-fields');
    const toggleContainer = document.getElementById('toggle-container');
    const forgotToggleContainer = document.getElementById('forgot-toggle-container');
    const authError = document.getElementById('auth-error');

    let mode = window.location.pathname.includes('signup.html') ? 'signup' : 'login';

    const updateUI = () => {
        authError.textContent = '';
        if (mode === 'login') {
            authTitle.textContent = 'User Login';
            document.getElementById('auth-desc').textContent = 'Welcome back! Please enter your details to access your account and manage your pickups.';
            authSubmit.textContent = 'Login';
            signupFields.style.display = 'none';
            forgotFields.style.display = 'none';
            document.getElementById('user-password').placeholder = 'Password';
            toggleContainer.innerHTML = 'Don\'t have an account? <a href="#" id="toggle-link">Sign Up</a>';
            forgotToggleContainer.style.display = 'block';
            
            document.getElementById('toggle-link').addEventListener('click', (e) => {
                e.preventDefault();
                mode = 'signup';
                updateUI();
            });
            document.getElementById('forgot-link').addEventListener('click', (e) => {
                e.preventDefault();
                mode = 'forgot';
                updateUI();
            });
        } else if (mode === 'signup') {
            authTitle.textContent = 'User Sign Up';
            document.getElementById('auth-desc').textContent = 'Join our community today! Create an account to start scheduling your e-waste pickups.';
            authSubmit.textContent = 'Sign Up';
            signupFields.style.display = 'block';
            forgotFields.style.display = 'none';
            document.getElementById('user-password').placeholder = 'Password';
            toggleContainer.innerHTML = 'Already have an account? <a href="#" id="toggle-link">Login</a>';
            forgotToggleContainer.style.display = 'none';
            
            document.getElementById('toggle-link').addEventListener('click', (e) => {
                e.preventDefault();
                mode = 'login';
                updateUI();
            });
        } else if (mode === 'forgot') {
            authTitle.textContent = 'Reset Password';
            document.getElementById('auth-desc').textContent = 'Enter your email and your new desired password to reset your access credentials.';
            authSubmit.textContent = 'Reset Password';
            signupFields.style.display = 'none';
            forgotFields.style.display = 'block';
            document.getElementById('user-password').placeholder = 'New Password';
            toggleContainer.innerHTML = '<a href="#" id="toggle-link">Back to Login</a>';
            forgotToggleContainer.style.display = 'none';
            
            document.getElementById('toggle-link').addEventListener('click', (e) => {
                e.preventDefault();
                mode = 'login';
                updateUI();
            });
        }
    };

    updateUI();

    const userRole = document.getElementById('user-role');
    const driverFields = document.getElementById('driver-fields');
    if (userRole) {
        userRole.addEventListener('change', () => {
            if (userRole.value === 'driver') {
                driverFields.style.display = 'block';
            } else {
                driverFields.style.display = 'none';
            }
        });
    }

    authForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = document.getElementById('user-email').value;
        const password = document.getElementById('user-password').value;
        
        const payload = {
            email,
            password,
            action: mode
        };

        if (mode === 'signup') {
            payload.name = document.getElementById('user-name').value;
            payload.role = document.getElementById('user-role').value;
            if (!payload.name) {
                authError.textContent = 'Please enter your name!';
                return;
            }
            if (payload.role === 'driver') {
                payload.vehicle_type = document.getElementById('vehicle-type').value;
            }
        } else if (mode === 'forgot') {
            const confirmPassword = document.getElementById('user-confirm-password').value;
            if (password !== confirmPassword) {
                authError.textContent = 'Passwords do not match!';
                return;
            }
        }

        try {
            const response = await fetch('/api/auth', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await response.json();

            if (result.success) {
                if (mode === 'forgot') {
                    authError.innerHTML = '<p style="color: #31df88; font-weight: bold; text-align: center;">Password reset successfully! Redirecting to login...</p>';
                    setTimeout(() => {
                        mode = 'login';
                        updateUI();
                        document.getElementById('user-password').value = '';
                        document.getElementById('user-confirm-password').value = '';
                    }, 2000);
                } else {
                    localStorage.setItem('userLoggedIn', 'true');
                    localStorage.setItem('userData', JSON.stringify(result.user));
                    if (mode === 'login') {
                        window.location.href = 'index.html';
                    } else {
                        window.location.href = 'welcome.html';
                    }
                }
            } else {
                authError.textContent = result.message;
            }
        } catch (error) {
            authError.textContent = 'An error occurred. Please try again later.';
            console.error('Auth error:', error);
        }
    });
});