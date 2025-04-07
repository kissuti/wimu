/**
 * Creates a new order and adds it to the orders list.
 * @param {Object} orderData - The data for the new order.
 * @param {Array} orders - The existing list of orders.
 * @returns {Object} - The newly created order.
 * @throws {Error} - If required fields are missing.
 */
function createOrder(orderData, orders) {
    const { customerId, total } = orderData;

    if (!customerId) {
        throw new Error('Customer ID is required.');
    }

    if (!total) {
        throw new Error('Total amount is required.');
    }

    const newOrder = {
        id: orders.length + 1,
        customerId,
        total,
        status: 'pending'
    };

    orders.push(newOrder);
    return newOrder;
}

module.exports = { createOrder };
