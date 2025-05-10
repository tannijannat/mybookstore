<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BookBuddy Footer</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
    }

    .footer {
      background-color: rgb(24, 1, 153);
      color: white;
      padding: 40px 20px;
    }

    .footer-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 30px;
      max-width: 1200px;
      margin: auto;
    }

    .footer-logo img {
      width: 120px;
      height: auto;
      margin-bottom: 10px;
    }

    .footer p {
      font-size: 14px;
      line-height: 1.6;
      margin: 5px 0;
    }

    .footer h4 {
      margin-bottom: 10px;
      font-size: 18px;
      border-bottom: 1px solid white;
      padding-bottom: 5px;
    }

    .footer a {
      color: white;
      text-decoration: none;
      margin-right: 15px;
      font-size: 14px;
    }

    .footer a:hover {
      text-decoration: underline;
    }

    .bottom-footer {
      background-color: black;
      color: white;
      text-align: center;
      font-size: 13px;
      padding: 10px;
    }

    @media (max-width: 600px) {
      .footer-container {
        text-align: center;
      }
    }
  </style>
</head>
<body>

  <!-- Footer Section -->
  <div class="footer">
    <div class="footer-container">
      
      <!-- Left Column -->
      <div class="footer-logo">
        <a href="#"><img src="images/sitelogo.png" alt="BookBuddy Logo" /></a>
        <p>
          BookBuddy is your trusted online bookstore, offering a wide collection
          of academic, fiction, non-fiction, and exam books.
          <br>Read anywhere, learn anytime — because knowledge should never wait.
        </p>
      </div>

      <!-- Right Column -->
      <div>
        <h4>Contact</h4>
        <p>📍 Address: House-500, Road-24, Dhaka</p>
        <p>📞 Phone: +880 188 555 222</p>
        <p>📧 Email: bookbuddy@gmail.com</p>
        <div style="margin-top: 10px;">
          <a href="#">Facebook</a>
          <a href="#">Instagram</a>
          <a href="#">Twitter</a>
        </div>
      </div>

    </div>
  </div>

  <!-- Bottom Footer -->
  <footer class="bottom-footer">
    <p>&copy; BookBuddy. All rights reserved.</p>
  </footer>

</body>
</html>
