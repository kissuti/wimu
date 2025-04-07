const { createOrder } = require('../utils/order');

describe('createOrder', () => {
    let mockOrders;

    beforeEach(() => {
        mockOrders = [
            { id: 1, customerId: 101, total: 5000, status: 'pending' },
            { id: 2, customerId: 102, total: 3000, status: 'completed' }
        ];
    });

    test('should create a new order successfully', () => {
        const newOrder = createOrder({ customerId: 103, total: 4500 }, mockOrders);
        expect(newOrder).toEqual({ id: 3, customerId: 103, total: 4500, status: 'pending' });
        expect(mockOrders).toContainEqual(newOrder);
    });

    test('should throw an error if customerId is missing', () => {
        expect(() => createOrder({ total: 4500 }, mockOrders)).toThrow('Customer ID is required.');
    });

    test('should throw an error if total is missing', () => {
        expect(() => createOrder({ customerId: 103 }, mockOrders)).toThrow('Total amount is required.');
    });
});
