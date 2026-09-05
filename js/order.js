



document.getElementById("checkoutForm") .addEventListener("submit", function (e) {
    e.preventDefault();

    const btn = document.getElementById("placeOrderBtn");
    const originalBtnContent = btn.innerHTML;
    const errorContainer = document.getElementById("ajaxErrorContainer");
    const errorList = document.getElementById("ajaxErrorList");

    // Loading status
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Placing Order…';
    errorContainer.style.display = "none";
    errorList.innerHTML = "";

    const formData = new FormData(this);

    fetch("checkout.php", {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
        
          document.getElementById("modalCustomerName").innerText =
            data.customer_name;
          document.getElementById("modalOrderId").innerText = data.order_id;

          const modal = document.getElementById("successModal");
          modal.style.display = "flex";
          setTimeout(() => {
            modal.classList.add("show");
          }, 10);

          setTimeout(() => {
            modal.classList.remove("show");
            setTimeout(() => {
              modal.style.display = "none";
              window.location.href = "my-orders.php";
            }, 300);
          }, 2000);

          const asideSummary = document.querySelector(".order-summary");
          if (asideSummary) {
            asideSummary.innerHTML = `<div class="empty-state"><i class="fas fa-shopping-cart"></i><p>Your cart is empty.</p></div>`;
          }
        } else {
          if (data.errors) {
            data.errors.forEach((err) => {
              const li = document.createElement("li");
              li.innerText = err;
              errorList.appendChild(li);
            });
            errorContainer.style.display = "block";
            window.scrollTo({
              top: 0,
              behavior: "smooth",
            });
          }
          btn.disabled = false;
          btn.innerHTML = originalBtnContent;
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("An error occurred. Please try again.");
        btn.disabled = false;
        btn.innerHTML = originalBtnContent;
      });
  });
