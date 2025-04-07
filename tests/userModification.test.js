const { modifyUser } = require('../utils/userModification');

describe('modifyUser', () => {
    let mockUsers;

    beforeEach(() => {
        mockUsers = [
            { id: 1, email: 'user1@example.com', name: 'User One', role: 'user' },
            { id: 2, email: 'admin@example.com', name: 'Admin User', role: 'admin' }
        ];
    });

    test('should modify user details successfully', () => {
        const updatedUser = modifyUser(1, { name: 'Updated User One', role: 'admin' }, mockUsers);
        expect(updatedUser).toEqual({ id: 1, email: 'user1@example.com', name: 'Updated User One', role: 'admin' });
        expect(mockUsers[0]).toEqual(updatedUser);
    });

    test('should throw an error if user ID is not found', () => {
        expect(() => modifyUser(3, { name: 'Nonexistent User' }, mockUsers)).toThrow('User not found.');
    });

    test('should throw an error if no updates are provided', () => {
        expect(() => modifyUser(1, {}, mockUsers)).toThrow('No updates provided.');
    });
});
