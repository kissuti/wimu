/**
 * Adds a product to the cart or updates its quantity.
 * @param {number} productId - The ID of the product to add.
 * @param {number} quantity - The quantity to add.
 * @param {Array} cart - The current cart.
 * @param {Object} stock - The available stock for each product.
 * @throws {Error} - If the product ID is invalid or quantity exceeds stock.
 */
function addToCart(productId, quantity, cart, stock) {
    if (!stock[productId]) {
        throw new Error('Invalid product ID.');
    }

    if (quantity < 1) {
        throw new Error('Quantity must be at least 1.');
    }

    const availableStock = stock[productId];
    const existingItem = cart.find(item => item.productId === productId);
    const currentQuantity = existingItem ? existingItem.quantity : 0;

    if (currentQuantity + quantity > availableStock) {
        throw new Error('Requested quantity exceeds stock.');
    }

    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({ productId, quantity });
    }
}

module.exports = { addToCart };
