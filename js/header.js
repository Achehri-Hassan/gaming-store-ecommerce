
// Hamburger menu toggle
const hamburger = document.getElementById("hamburger");
const navLinks = document.getElementById("nav-links");

hamburger.addEventListener("click", () => {
  hamburger.classList.toggle("open");
  navLinks.classList.toggle("open");
});

// Close nav when a link is clicked
navLinks.querySelectorAll("a").forEach((link) => {
  link.addEventListener("click", () => {
    hamburger.classList.remove("open");
    navLinks.classList.remove("open");
  });
});










let cartIcon = document.querySelector("#cart");
let cart = document.querySelector(".carts");
let cartClose = document.querySelector("#cart-close");

cartIcon.onclick = () => {
  cart.classList.add("active");
};

cartClose.onclick = () => {
  cart.classList.remove("active");
};


let cartContent = document.querySelector(".cart-content");
let badge = document.getElementById("cart-badge");

let count = 0;


let addBtns = document.querySelectorAll(".add-to-cart");

addBtns.forEach((btn) => {
  btn.addEventListener("click", () => {
    let product = btn.closest(".p-card");

    let title = product.querySelector("h3").innerText;
    let price = product.querySelector(".p-price").innerText;
    let img = product.querySelector(".main-img").src;

    addToCart(title, price, img);
  });
});

function addToCart(title, price, img) {
  let cartBox = document.createElement("div");
  cartBox.classList.add("cart-box");

  cartBox.innerHTML = `
    <img src="${img}" width="80">

    <div class="cart-detail">
      <h4>${title}</h4>
      <span class="cart-price">${price}</span>

      <div class="cart-quantity">
        <button class="decrement">-</button>
        <span class="number">1</span>
        <button class="increment">+</button>
      </div>
    </div>

    <i class="fa-solid fa-trash cart-remove"></i>
  `;

  cartContent.appendChild(cartBox);

  count++;
  badge.innerText = count;

  updateTotal();
}


document.addEventListener("click", function (e) {
  if (e.target.classList.contains("cart-remove")) {
    e.target.closest(".cart-box").remove();

    count--;
    badge.innerText = count;

    updateTotal();
  }
});


document.addEventListener("click", function (e) {
 
  if (e.target.classList.contains("increment")) {
    let qty = e.target.parentElement.querySelector(".number");
    qty.innerText = parseInt(qty.innerText) + 1;

    updateTotal();
  }

  
  if (e.target.classList.contains("decrement")) {
    let qty = e.target.parentElement.querySelector(".number");

    if (parseInt(qty.innerText) > 1) {
      qty.innerText = parseInt(qty.innerText) - 1;
    }

    updateTotal();
  }
});


function updateTotal() {
  let total = 0;

  let cartBoxes = document.querySelectorAll(".cart-box");

  cartBoxes.forEach((box) => {
    let price = box.querySelector(".cart-price").innerText;
    let qty = box.querySelector(".number").innerText;

    price = parseFloat(price); // remove dh

    total += price * qty;
  });

  document.querySelector(".total-price").innerText = total + " dh";
}
