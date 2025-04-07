const { register } = require('../utils/register');

describe('register', () => {
    let mockUsers;

    beforeEach(() => {
        mockUsers = [
            { email: 'existing@example.com', name: 'Existing User', password: 'Password123' }
        ];
    });

    test('should register successfully with valid inputs', () => {
        expect(register('newuser@example.com', 'New User', 'Password123', mockUsers)).toBe('Registration successful.');
        expect(mockUsers).toContainEqual({ email: 'newuser@example.com', name: 'New User', password: 'Password123' });
    });

    test('should throw an error if any field is missing', () => {
        expect(() => register('', 'New User', 'Password123', mockUsers)).toThrow('All fields are required.');
        expect(() => register('newuser@example.com', '', 'Password123', mockUsers)).toThrow('All fields are required.');
        expect(() => register('newuser@example.com', 'New User', '', mockUsers)).toThrow('All fields are required.');
    });

    test('should throw an error for invalid email format', () => {
        expect(() => register('invalid-email', 'New User', 'Password123', mockUsers)).toThrow('Invalid email format.');
    });

    test('should throw an error if email is already registered', () => {
        expect(() => register('existing@example.com', 'New User', 'Password123', mockUsers)).toThrow('Email is already registered.');
    });

    test('should throw an error for weak passwords', () => {
        expect(() => register('newuser@example.com', 'New User', 'weak', mockUsers)).toThrow('Password must be at least 8 characters long, contain one uppercase letter, and one number.');
    });
});
