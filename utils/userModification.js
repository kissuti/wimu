/**
 * Modifies user details in the mock database.
 * @param {number} userId - The ID of the user to modify.
 * @param {Object} updates - The updates to apply.
 * @param {Array} users - The mock database of users.
 * @returns {Object} - The updated user object.
 */
function modifyUser(userId, updates, users) {
    const user = users.find(u => u.id === userId);
    if (!user) {
        throw new Error('User not found.');
    }

    if (Object.keys(updates).length === 0) {
        throw new Error('No updates provided.');
    }

    Object.assign(user, updates);
    return user;
}

module.exports = { modifyUser };
