// Delete confirmation for product items
document.addEventListener("DOMContentLoaded", function () {
    const deleteButtons = document.querySelectorAll(".delete-btn");

    deleteButtons.forEach(button => {
      button.addEventListener("click", function (e) {
        const productName = this.closest(".product-row").querySelector("h4").textContent;
        const confirmDelete = confirm(`Are you sure you want to delete this product "${productName}"?`);
        if (!confirmDelete) {
          e.preventDefault();
        }
      });
    });
  });

// Update confirmation for product items
document.addEventListener("DOMContentLoaded", function () {
    const updateForm = document.querySelector("form button[name='edit_product']");
    if (updateForm) {
      updateForm.addEventListener("click", function (e) {
        const confirmUpdate = confirm("Are you sure you want to update this product?");
        if (!confirmUpdate) {
          e.preventDefault();
        }
      });
    }
  });


// Edit product fuction
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const price = button.getAttribute('data-price');
        const description = button.getAttribute('data-description');

        document.getElementById('edit-product-id').value = id;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-price').value = price;
        document.getElementById('edit-description').value = description;
    });
});
