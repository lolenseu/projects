// Hamburger Menu
document.getElementById('hamburger-menu').addEventListener('click', function () {
    const navLinks = document.getElementById('nav-links');
    navLinks.classList.toggle('show');
});

// Ensure all popovers are hidden on page load
window.onload = function () {
    const loginPopover = document.getElementById('loginPopover');
    const signupPopover = document.getElementById('signupPopover');
    const userPopover = document.getElementById('userPopover');
    const cartPopover = document.getElementById('cartPopover');

    // Hide all popovers
    loginPopover.style.display = 'none';
    signupPopover.style.display = 'none';
    userPopover.classList.add('hidden');
    cartPopover.classList.add('hidden');
};

// Popover
function closeLoginPopover() {
    const loginPopover = document.getElementById('loginPopover');
    loginPopover.style.display = 'none';
}

function closeSignupPopover() {
    const signupPopover = document.getElementById('signupPopover');
    signupPopover.style.display = 'none';
}

function hideAllPopovers() {
    const loginPopover = document.getElementById('loginPopover');
    const signupPopover = document.getElementById('signupPopover');
    const userPopover = document.getElementById('userPopover');
    const cartPopover = document.getElementById('cartPopover');

    // Hide all popovers
    loginPopover.style.display = 'none';
    signupPopover.style.display = 'none';
    userPopover.classList.add('hidden');
    cartPopover.classList.add('hidden');
}

function showLoginPopover() {
    hideAllPopovers();
    const loginPopover = document.getElementById('loginPopover');
    loginPopover.style.display = 'flex';
}

function showSignupPopover() {
    hideAllPopovers();
    const signupPopover = document.getElementById('signupPopover');
    signupPopover.style.display = 'flex';
}

function toggleUserPopover() {
    hideAllPopovers();
    const userPopover = document.getElementById('userPopover');
    userPopover.classList.toggle('hidden');
}

function toggleCartPopover() {
    hideAllPopovers();
    const cartPopover = document.getElementById('cartPopover');
    cartPopover.classList.toggle('hidden');
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
            <button onclick="removeFromCart(${index})">Remove</button>
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

// Slideshow and Dots
let slideIndex = 0;
let slideInterval;
showSlides();

function showSlides() {
    let i;
    let slides = document.getElementsByClassName("mySlides");
    let dots = document.getElementsByClassName("dot");
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";  
    }
    slideIndex++;
    if (slideIndex > slides.length) {slideIndex = 1}    
    for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
    }
    slides[slideIndex-1].style.display = "block";  
    dots[slideIndex-1].className += " active";
    slideInterval = setTimeout(showSlides, 2000);
}

function currentSlide(n) {
    clearTimeout(slideInterval);
    slideIndex = n;
    let slides = document.getElementsByClassName("mySlides");
    let dots = document.getElementsByClassName("dot");
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";  
    }
    for (let i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
    }
    slides[slideIndex-1].style.display = "block";  
    dots[slideIndex-1].className += " active";
    slideInterval = setTimeout(showSlides, 5000);
}

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

// Open modal with product info
document.querySelectorAll('.product img').forEach(img => {
    img.addEventListener('click', function () {
      const productDiv = this.parentElement;
      const name = productDiv.querySelector('h3').innerText;
      const price = productDiv.querySelector('p').innerText;
      const imgSrc = this.getAttribute('src');

      document.getElementById('modalImage').src = imgSrc;
      document.getElementById('modalTitle').innerText = name;
      document.getElementById('modalPrice').innerHTML = price;

      document.getElementById('productModal').style.display = 'flex';
    });
  });

  function closeModal() {
    document.getElementById('productModal').style.display = 'none';
  }

  // Optional: close modal on outside click
  window.addEventListener('click', function (e) {
    const modal = document.getElementById('productModal');
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  });

// Backtotop Button
window.onscroll = function() {
    var backToTopButton = document.getElementById("back-to-top");
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        backToTopButton.style.display = "block";
    } else {
        backToTopButton.style.display = "none";
    }
};

function scrollToTop() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}