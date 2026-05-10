



document.querySelectorAll('.dot').forEach(dot => {
    dot.addEventListener('click', function() {
        const newSrc = this.getAttribute('data-image');
        const productCard = this.closest('.product-card');
        const mainImg = productCard.querySelector('.main-product-img');
        
        if(newSrc) {
            mainImg.src = newSrc;
        }
    });
});




// swiper Hom page 
var swiper = new Swiper(".myHeroSwiper", {
  loop: true,
  effect: "fade", 
  autoplay: {
    delay: 5000,
    disableOnInteraction: false,
  }
 
});



new Swiper(".productSwiper", {
  slidesPerView: 4,
  spaceBetween: 20,
  navigation: {
    nextEl: ".custom-next",
    prevEl: ".custom-prev",
  },
  breakpoints: {
    320: {
      slidesPerView: 1,
    },
    620: {
      slidesPerView: 2,
    },
    1024: {
      slidesPerView: 3,
    },
  },
});


var swiper = new Swiper(".mySwiper", {
    slidesPerView: 1,
    spaceBetween: 20,
    loop: true,

    navigation: {
        nextEl: ".next",
        prevEl: ".prev",
    },
    breakpoints: {
     
        530: { slidesPerView: 2 },
        786 : {slidesPerView:2.5},
        1200: { slidesPerView: 3 }
    }
});








const deskSwiper = new Swiper(".deskSwiper", {
  slidesPerView: 1,
  spaceBetween: 20,
  loop: true,

  navigation: {
    nextEl: ".desk-next",
    prevEl: ".desk-prev",
  },

  breakpoints: {
    530: { slidesPerView: 2 },
        786 : {slidesPerView:3},
        900:{slidesPerView:3},
        960:{slidesPerView:3.5},
        1200: { slidesPerView: 4 }
  }
});




const ControllerSwiper = new Swiper(".ControllerSwiper", {
  slidesPerView: 1,
  spaceBetween: 20,
  loop: true,

  navigation: {
    nextEl: ".Controller-next",
    prevEl: ".Controller-prev",
  },

 breakpoints: {
    530: { slidesPerView: 2 },
        786 : {slidesPerView:3},
        900:{slidesPerView:3},
        960:{slidesPerView:3.5},
        1200: { slidesPerView: 4 }
  }
});



const Playstaion = new Swiper(".playstation-swiper", {
  slidesPerView: 1,
  spaceBetween: 20,
  loop: true,

  navigation: {
    
    nextEl: ".playstation-next", 
    prevEl: ".playstation-prev",
  },

  breakpoints: {
    530: { slidesPerView: 2 },
        786 : {slidesPerView:3},
        900:{slidesPerView:3},
        960:{slidesPerView:3.5},
        1200: { slidesPerView: 4 }
  }
});





const mous= new Swiper(".mous-swiper", {
  slidesPerView: 1,
  spaceBetween: 20,
  loop: true,

  navigation: {
    
    nextEl: ".mous-next", 
    prevEl: ".mous-prev",
  },

  breakpoints: {
    530: { slidesPerView: 2 },
        786 : {slidesPerView:3},
        900:{slidesPerView:3},
        960:{slidesPerView:3.5},
        1200: { slidesPerView: 4 }
  }
});







const ecran= new Swiper(".ecran-swiper", {
  slidesPerView: 1,
  spaceBetween: 20,
  loop: true,

  navigation: {
    
    nextEl: ".ecran-next", 
    prevEl: ".ecran-prev",
  },

  breakpoints: {
    530: { slidesPerView: 2 },
        786 : {slidesPerView:3},
        900:{slidesPerView:3},
        960:{slidesPerView:3.5},
        1200: { slidesPerView: 4 }
  }
});