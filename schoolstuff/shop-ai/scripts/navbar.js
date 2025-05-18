// Hamburger Menu
document.getElementById('hamburger-menu').addEventListener('click', function () {
    const navLinks = document.getElementById('nav-links');
    navLinks.classList.toggle('show');
});

// Scroll to Product
function scrollToProduct() {
    const input = document.getElementById("searchInput").value.trim().toLowerCase();
    const productImage = document.getElementById(input);
    if (productImage) {
      productImage.scrollIntoView({ behavior: "smooth", block: "center" });
      productImage.classList.add("border", "border-success", "shadow");
      setTimeout(() => {
        productImage.classList.remove("border", "border-success", "shadow");
      }, 1500);
    } else {
      alert("Product not found!");
    }
  }

// Cart functionality
let cart = [];
let total = 0;

function addToCart() {
    const modalImage = document.getElementById('modalImage').src;
    const modalTitle = document.getElementById('modalTitle').textContent;
    const modalPrice = parseFloat(document.getElementById('modalPrice').textContent.replace('₱', ''));
    cart.push({ name: modalTitle, price: modalPrice, img: modalImage });
    total += modalPrice;
    updateCartUI();
}

function updateCartUI() {
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const cartCount = document.getElementById('cartCount');
    cartItems.innerHTML = '';
    cart.forEach((item, index) => {
        const li = document.createElement('li');
        li.innerHTML = `
            <img src="${item.img}" alt="${item.name}" class="cart-img">
            <span>${item.name}</span> - <span>&#8369;${item.price.toFixed(2)}</span>
            <button class="cart-button" onclick="removeFromCart(${index})">Remove</button>
        `;
        cartItems.appendChild(li);
    });
    cartTotal.textContent = `Total: ₱${total.toFixed(2)}`;
    cartCount.textContent = cart.length;
}

function removeFromCart(index) {
    total -= cart[index].price;
    cart.splice(index, 1);
    updateCartUI();
}

function clearCart() {
    cart = [];
    total = 0;
    updateCartUI();
}