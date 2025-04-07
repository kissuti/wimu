const { addToCart } = require('../utils/cart');

describe('addToCart', () => {
    let mockCart;
    let mockStock;

    beforeEach(() => {
        mockCart = [
            { productId: 1, quantity: 2 },
            { productId: 2, quantity: 1 }
        ];
        mockStock = {
            1: 10, // Product 1 has 10 units in stock
            2: 5,  // Product 2 has 5 units in stock
            3: 8   // Product 3 has 8 units in stock
        };
    });

    // Black-box testing: Focus on input-output behavior
    describe('Black-box testing', () => {
        test('should add a new product to the cart', () => {
            addToCart(3, 2, mockCart, mockStock);
            expect(mockCart).toContainEqual({ productId: 3, quantity: 2 });
        });

        test('should throw an error if the requested quantity exceeds stock', () => {
            expect(() => addToCart(1, 20, mockCart, mockStock)).toThrow('Requested quantity exceeds stock.');
        });

        test('should throw an error if quantity is less than 1', () => {
            expect(() => addToCart(1, 0, mockCart, mockStock)).toThrow('Quantity must be at least 1.');
        });
    });

    // White-box testing: Focus on internal logic and paths
    describe('White-box testing', () => {
        test('should increase the quantity of an existing product in the cart', () => {
            addToCart(1, 3, mockCart, mockStock);
            expect(mockCart.find(item => item.productId === 1).quantity).toBe(5);
        });

        test('should handle adding a product not already in the cart', () => {
            addToCart(3, 4, mockCart, mockStock);
            expect(mockCart).toContainEqual({ productId: 3, quantity: 4 });
        });

        test('should throw an error if the product ID is invalid', () => {
            expect(() => addToCart(99, 1, mockCart, mockStock)).toThrow('Invalid product ID.');
        });
    });

    // Edge cases testing
    describe('addToCart - edge cases', () => {
        test('should handle adding zero quantity gracefully', () => {
            expect(() => addToCart(1, 0, mockCart, mockStock)).toThrow('Quantity must be at least 1.');
        });

        test('should handle adding maximum stock quantity', () => {
            addToCart(1, 8, mockCart, mockStock); // 2 already in cart, 8 more to reach 10
            expect(mockCart.find(item => item.productId === 1).quantity).toBe(10);
        });

        test('should handle empty cart gracefully', () => {
            const emptyCart = [];
            addToCart(3, 2, emptyCart, mockStock);
            expect(emptyCart).toContainEqual({ productId: 3, quantity: 2 });
        });

        test('should throw an error for invalid stock object', () => {
            expect(() => addToCart(1, 1, mockCart, null)).toThrow();
        });
    });
});
