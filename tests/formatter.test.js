const { szampontos } = require('../utils/formatter');

describe('szampontos', () => {
    test('should format numbers with dots', () => {
        expect(szampontos(1000)).toBe('1.000');
        expect(szampontos(1234567)).toBe('1.234.567');
        expect(szampontos(9876543210)).toBe('9.876.543.210');
    });

    test('should handle small numbers', () => {
        expect(szampontos(0)).toBe('0');
        expect(szampontos(5)).toBe('5');
    });

    test('should handle invalid inputs gracefully', () => {
        expect(() => szampontos(null)).toThrow();
        expect(() => szampontos(undefined)).toThrow();
    });
});
