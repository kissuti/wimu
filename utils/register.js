/**
 * Simulates a user registration function.
 * @param {string} email - The user's email.
 * @param {string} name - The user's name.
 * @param {string} password - The user's password.
 * @param {Array} users - A mock database of users.
 * @returns {string} - A success message or an error.
 */
function register(email, name, password, users) {
    if (!email || !name || !password) {
        throw new Error('All fields are required.');
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        throw new Error('Invalid email format.');
    }

    if (users.some(u => u.email === email)) {
        throw new Error('Email is already registered.');
    }

    if (!/^(?=.*[A-Z])(?=.*\d).{8,}$/.test(password)) {
        throw new Error('Password must be at least 8 characters long, contain one uppercase letter, and one number.');
    }

    users.push({ email, name, password });
    return 'Registration successful.';
}

module.exports = { register };
