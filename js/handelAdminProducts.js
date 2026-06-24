


function editProduct(product) {
  document.getElementById("form-title").innerHTML =
    '<i class="fas fa-edit"></i> Edit Product: ' + product.name;
  document.getElementById("btn-submit-form").name = "update_product";
  document.getElementById("btn-submit-form").innerText = "Save Changes";
  document.getElementById("btn-cancel").style.display = "inline-block";

  document.getElementById("prod-id").value = product.id;
  document.getElementById("prod-name").value = product.name;
  document.getElementById("prod-brand").value = product.brand;
  document.getElementById("prod-price").value = product.price;
  document.getElementById("prod-description").value = product.description;

  document.getElementById("main-img-hint").innerText =
    "Current: " + (product.main_image ? product.main_image : "None");
  document.getElementById("hover-img-hint").innerText =
    "Current: " + (product.hover_image ? product.hover_image : "None");
  document.getElementById("shop-img-hint").innerText =
    "Leave blank to keep existing shop images.";

  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
}

document.getElementById("btn-cancel").addEventListener("click", function () {
  document.getElementById("form-title").innerHTML =
    '<i class="fas fa-plus-circle"></i> Add Product to <?= strtoupper($current_category) ?>';
  document.getElementById("btn-submit-form").name = "add_product";
  document.getElementById("btn-submit-form").innerText = "Add Product";
  document.getElementById("btn-cancel").style.display = "none";
  document.getElementById("product-form").reset();
  document.getElementById("prod-id").value = "";
  document.getElementById("main-img-hint").innerText = "";
  document.getElementById("hover-img-hint").innerText = "";
  document.getElementById("shop-img-hint").innerText = "";
});
