/**
 * Számot pontokkal elválasztva formáz.
 * @param {number} num - A formázandó szám.
 * @returns {string} - A formázott szám.
 */
function szampontos(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

module.exports = { szampontos };
