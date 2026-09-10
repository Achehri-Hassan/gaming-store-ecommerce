// Escape a value before it is interpolated into innerHTML. Product fields
// are admin-entered, so this is defense-in-depth rather than a fix for an
// exploitable-by-anyone bug — but it's the same class of mistake as the
// storefront cart XSS (see js/main.js), so it gets the same treatment here.
function escapeHtml(value) {
  return String(value ?? "").replace(/[&<>"']/g, (ch) => ({
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#39;",
  }[ch]));
}

function editProduct(product) {
  document.getElementById("form-title").innerHTML =
    '<i class="fas fa-edit"></i> Edit Product: ' + escapeHtml(product.name);
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
  // Read the current category from the hidden form field instead of
  // embedding a PHP tag directly in this file: this file has a .js
  // extension, so the web server serves it as a static asset and PHP
  // never parses it — a `<?= ... ?>` tag here would previously be sent
  // to the browser as literal, unrendered text instead of the category
  // name every time an admin clicked "Cancel".
  const category = document.getElementById("prod-category")?.value || "";
  document.getElementById("form-title").innerHTML =
    '<i class="fas fa-plus-circle"></i> Add Product to ' +
    escapeHtml(category.toUpperCase());
  document.getElementById("btn-submit-form").name = "add_product";
  document.getElementById("btn-submit-form").innerText = "Add Product";
  document.getElementById("btn-cancel").style.display = "none";
  document.getElementById("product-form").reset();
  document.getElementById("prod-id").value = "";
  document.getElementById("main-img-hint").innerText = "";
  document.getElementById("hover-img-hint").innerText = "";
  document.getElementById("shop-img-hint").innerText = "";
});