const { login } = require('../utils/login');

describe('login', () => {
    const mockUsers = [
        { email: 'test@example.com', password: 'password123', name: 'Test User' },
        { email: 'admin@example.com', password: 'adminpass', name: 'Admin User' }
    ];

    test('should login successfully with valid credentials', () => {
        expect(login('test@example.com', 'password123', mockUsers)).toBe('Welcome, Test User!');
    });

    test('should throw an error if email is missing', () => {
        expect(() => login('', 'password123', mockUsers)).toThrow('Email and password are required.');
    });

    test('should throw an error if password is missing', () => {
        expect(() => login('test@example.com', '', mockUsers)).toThrow('Email and password are required.');
    });

    test('should throw an error if user is not found', () => {
        expect(() => login('unknown@example.com', 'password123', mockUsers)).toThrow('User not found.');
    });

    test('should throw an error if password is incorrect', () => {
        expect(() => login('test@example.com', 'wrongpassword', mockUsers)).toThrow('Invalid password.');
    });
});
