<?php
session_start();
include "db.php";
include "header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

   
    <title>Book Store</title>
</head>
 <style>
    
    .height_image{
        height:400px
    }

    </style>
<body>

<!-- Carousel -->


    <!-- Image Slider -->
    <div id="carouselExampleSlidesOnly" class="carousel slide pt-1 height_image" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/slider1.jpg" class="d-block w-100 " alt="Slider 1">
            </div>
            <div class="carousel-item">
                <img src="images/slider2.jpg" class="d-block w-100" alt="Slider 2">
            </div>
            <div class="carousel-item">
                <img src="images/slider3.jpg" class="d-block w-100" alt="Slider 3">
            </div>
        </div>
    </div>


<!-- Team  Start-->
<div class="container">
<div class="row ">
  <div class="col-4">
    <div class="card">
      <img src="images/shirin.png" class="shirin">
      <div class="card-body">
        <h5 class="card-title"><b>Name : </b>Mst. Alif Jan Nur Shire</h5>
        <p class="card-text"><b>ID : </b>221400048</p>
        <p class="card-text"><b>Department : </b>CSE</p>
      </div>
    </div>
  </div>
  <div class="col-4">
    <div class="card">
      <img src="images/jannat.png" class="shirin">
      <div class="card-body">
        <h5 class="card-title"><b>Name : </b>Jannatul Ferdousy Tanni
        </h5>
        <p class="card-text"><b>ID : </b>221400015</p>
        <p class="card-text"><b>Department : </b>CSE</p>
      </div>
    </div>
  </div>
  <div class="col-4">
    <div class="card">
      <img src="images/Noor-Alom.png" class="shirin">
      <div class="card-body">
        <h5 class="card-title"><b>Name : </b>Noor Alom Islam Manik</h5>
        <p class="card-text"><b>ID : </b>221400044</p>
        <p class="card-text"><b>Department  : </b>CSE</p>
      </div>
    </div>
  </div>
  
</div>
</div>


<?php include "footer.php"; ?>
</body>
</html>