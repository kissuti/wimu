const { szampontos } = require('./formatter');

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

describe('szampontos - edge cases', () => {
    test('should handle negative numbers', () => {
        expect(szampontos(-1234567)).toBe('-1.234.567');
    });

    test('should handle floating-point numbers', () => {
        expect(szampontos(1234.56)).toBe('1.234.56');
    });

    test('should handle very large numbers', () => {
        expect(szampontos(123456789012345)).toBe('123.456.789.012.345');
    });

    test('should throw an error for non-numeric inputs', () => {
        expect(() => szampontos('abc')).toThrow();
        expect(() => szampontos({})).toThrow();
    });
});
