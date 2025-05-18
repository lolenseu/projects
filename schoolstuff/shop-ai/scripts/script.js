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

// Open modal with product info
document.querySelectorAll('.product img').forEach(img => {
    img.addEventListener('click', function () {
        const productDiv = this.parentElement;
        const name = productDiv.querySelector('h3').innerText;
        const price = productDiv.querySelector('p').innerText;
        const imgSrc = this.getAttribute('src');
        const description = productDiv.getAttribute('data-description'); // Fetch description from data attribute

        // Populate modal with product data
        document.getElementById('modalImage').src = imgSrc;
        document.getElementById('modalTitle').innerText = name;
        document.getElementById('modalPrice').innerHTML = price;
        document.getElementById('modalDescription').innerText = description;
        document.getElementById('modalDescription').style.color = '#555';

        // Show the modal
        const modal = document.getElementById('productModal');
        modal.style.display = 'flex';
        modal.style.justifyContent = 'center'; // Center horizontally
        modal.style.alignItems = 'center'; // Center vertically
    });
});

// Close modal
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