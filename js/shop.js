const loader = document.getElementById("loader");
const productUI = document.getElementById("product-ui");
const mainImg = document.getElementById("main-img");
const gallery = document.getElementById("gallery");
const relatedGrid = document.querySelector(".product-grid");

async function getProducts() {
  const res = await fetch("data.json");
  return await res.json();
}

async function initProductPage() {
  const params = new URLSearchParams(window.location.search);
  const pid = params.get("id");

  if (!pid) {
    loader.innerHTML = "<h2>Invalid product ID</h2>";
    return;
  }

  try {
    const data = await getProducts();
    const product = data.find((p) => p.id == pid);

    if (!product) {
      loader.innerHTML = "<h2>Product not found</h2>";
      return;
    }

    displayProduct(product);

    displayRelatedProducts(data, pid);

    window.scrollTo({ top: 0, behavior: "smooth" });
  } catch (err) {
    console.error("Error:", err);
    loader.innerHTML = "<h2>Error loading product</h2>";
  }
}

function displayProduct(p) {
  loader.style.display = "none";
  productUI.style.display = "grid";

  document.getElementById("p-brand").innerText = p.brand;
  document.getElementById("p-name").innerText = p.name;
  document.getElementById("p-desc").innerText = p.description;
  document.getElementById("p-price").innerText = `${p.price} ${p.currency}`;
  document.getElementById("full_name").innerHTML = p.full_title;

  mainImg.src = p.main_image;

  const images = [p.main_image, ...p.gallery];
  gallery.innerHTML = images
    .map((img) => `<img src="${img}" class="thumb">`)
    .join("");

  document.querySelectorAll(".thumb").forEach((img) => {
    img.addEventListener("click", () => {
      mainImg.src = img.src;
    });
  });
}

function displayRelatedProducts(allProducts, currentId) {
  const otherProducts = allProducts.filter((p) => p.id != currentId);

  const shuffled = otherProducts.sort(() => 0.5 - Math.random());

  const selection = shuffled.slice(0, 8);

  relatedGrid.innerHTML = selection
    .map(
      (p) => `
    <a href="shop-details.html?id=${p.id}" class="related-link">
      <div class="p-card">
        <div class="p-img">
          <img src="${p.main_image}" class="main-img" alt="${p.name}" />
          <img src="${p.gallery[0] || p.main_image}" class="hover-img" alt="${p.name}" />
        </div>
        <div class="p-details">
          <span class="p-brand">${p.brand}</span>
          <h3 class="full_name">${p.name}</h3>
        
          <div class="p-shop">
            <div class="p-price">${p.price} ${p.currency}</div>
            <i class="fa-solid fa-bag-shopping"></i>
          </div>
          <div class="p-stock"><span class="dott"></span> In stock.</div>
        </div>
      </div>
    </a>
  `,
    )
    .join("");

  document.querySelectorAll(".related-link").forEach((link) => {
    link.addEventListener("click", () => {
      setTimeout(() => {
        initProductPage();
      }, 50);
    });
  });
}

window.addEventListener("popstate", initProductPage);

initProductPage();
