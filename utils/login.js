/**
 * Simulates a login function.
 * @param {string} email - The user's email.
 * @param {string} password - The user's password.
 * @param {Array} users - A mock database of users.
 * @returns {string} - A success message or an error.
 */
function login(email, password, users) {
    if (!email || !password) {
        throw new Error('Email and password are required.');
    }

    const user = users.find(u => u.email === email);
    if (!user) {
        throw new Error('User not found.');
    }

    if (user.password !== password) {
        throw new Error('Invalid password.');
    }

    return `Welcome, ${user.name}!`;
}

module.exports = { login };
