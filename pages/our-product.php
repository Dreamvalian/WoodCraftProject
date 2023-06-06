<?php

@include('../components/Header.php')

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Woodcraft - Our Product</title>
  <link rel="stylesheet" href="../styles/product.css" />
  <link rel="stylesheet" href="../styles/components/header.css" />
  <link rel="stylesheet" href="../styles/components/footer.css" />
  <link rel="stylesheet" href="../assets/lib/owl.carousel.min.css">
  <link rel="stylesheet" href="../assets/lib/owl.theme.default.min.css">
</head>

<body>
  <section class="hero">
    <div class="hero-container">
      <h2>Nature Mastered the
        Art of Regeneration</h2>
      <h4>we are master of the are and wood</h4>
    </div>
  </section>

  <section class="product">
    <div class="left-product">
      <h2>Our
        Products</h2>
      <p>Introducing our exceptional Forest Wood Craft collection, a testament to the timeless beauty and craftsmanship of artisan traditions. Each piece in this collection is meticulously handcrafted by skilled artisans who are deeply connected to nature and passionate about their craft.
        </br>
        </br>
        Our Forest Wood Craft products are created using carefully selected and sustainable wood sourced from responsibly managed forests. We prioritize the use of reclaimed or salvaged wood, giving new life to materials and promoting environmental conservation.

    </div>
    <div class="right-product">
      <div class="slider owl-carousel">
        <div class="slide">
          <img class="product-image" src="../assets/images/product-1.jpg" alt="product-slider-1">
          <h4 class="slide-title">Teak</h4>
          <p class="slide-subtitle">Teak wood has high density and good strength</p>
        </div>
        <div class="slide">
          <img class="product-image" src="../assets/images/product-2.jpg" alt="product-slider-2">
          <h4 class="slide-title">Ulin</h4>
          <p class="slide-subtitle">Ulin wood has a high density</p>
        </div>
        <div class="slide">
          <img class="product-image" src="../assets/images/product-3.jpg" alt="product-slider-3">
          <h4 class="slide-title">Bengkirai</h4>
          <p class="slide-subtitle">Bengkirai wood has good strength and attractive color and appearance.</p>
        </div>
        <div class="slide">
          <img class="product-image" src="../assets/images/product-7.jpg" alt="product-slider-3">
          <h4 class="slide-title">Mahoni</h4>
          <p class="slide-subtitle">has straight fibers, is resistant to
            shrinkage, and has an attractive red
            brownish color.</p>
        </div>
      </div>
  </section>

  <section class="works">
    <h2>Our Works</h2>
    <h5>Hasil pekerjaan Wood Craft dan digunakan untuk desain interior rumah clients kita</h5>
    <div class="slider owl-carousel">
      <div class="slide">
        <img class="slider-image" src="../assets/images/product-4.jpg" alt="product-slider-4">
      </div>
      <div class="slide">
        <img class="slider-image" src="../assets/images/product-5.jpg" alt="product-slider-5">
      </div>
      <div class="slide">
        <img class="slider-image" src="../assets/images/product-6.jpg" alt="product-slider-6">
      </div>
    </div>
  </section>

  <script src="../assets/lib/jquery-3.7.0.min.js"></script>
  <script src="../assets/lib/owl.carousel.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.slider.owl-carousel').owlCarousel({
        margin: 8,
        autoplay: true,
        loop: true,
        nav: true,
        items: 3,
        autoplayTimeout: 5000

      });
    });
  </script>

</body>

<?php

@include('../components/Footer.php')

?>

</html>