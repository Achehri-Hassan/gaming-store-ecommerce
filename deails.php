<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- title page  -->
    <title>Document</title>

    <!-- link font icon -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    />

    <!-- link css style -->
    <link rel="stylesheet" href="css/deails.css" />
  </head>
  <body>

    <!-- Header -->
  
     <?php  include "src/views/layouts/header.php" ?>

    <!-- End Header -->

    <!-- start main content -->

    <main>
       <!-- add cart to  -->
   

      <div class="hero">
        <!-- hero section -->
        <section class="hero-container">
          <!-- Left Content -->
          <div class="hero-content">
            <h1 class="title">Special Offers</h1>
            <p class="subtitle">Save on your favorite products</p>

            <div class="countdown">
              <div class="time-box">2</div>
              <div class="time-box">15</div>
              <div class="time-box">20</div>
            </div>

            <button class="btn-explore">Explore Deals</button>

            <!-- Bottom Navbar -->
            <nav class="filter-nav">
              <a href="#" class="active">All</a>
              <a href="#">Chair</a>
              <a href="#">Mous</a>
              <a href="#">Desk</a>
              <a href="#">Pc</a>
              <a href="#">Cask</a>
            </nav>
          </div>

          <!-- Right Image -->
          <div class="hero-image">
            <img
              src="src/assets/banners_hero_section/woman_deails.jpg"
              alt="Gaming Setup"
            />
          </div>
        </section>
      </div>
    </main>

     
     <!-- footer -->
      <?php  include "src/views/layouts/footer.php" ?>

    <script src="js/header.js"></script>
  </body>
</html>
