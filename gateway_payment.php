<?php
session_start();
include "header.php";

// Check if checkout data is set
if (!isset($_SESSION['checkout_data'])) {
    echo "<div class='container mt-5 alert alert-danger text-center'>No payment session found.</div>";
    include "footer.php";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulated Payment Gateway</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General Body Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        /* Main Container */
        .container {
            max-width: 600px;
            margin: 50px auto;
        }

        /* Heading Styles */
        h3 {
            font-size: 24px;
            color: #2c6f3f;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Form Container */
        form {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        /* Form Element Styles */
        .form-label {
            font-size: 16px;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .form-control:focus {
            border-color: #2c6f3f;
            outline: none;
        }

        .select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        /* Stripe Form Styles */
        #stripe-form {
            display: none;
        }

        #card-element {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            background-color: #f9f9f9;
        }

        #card-errors {
            color: #e85746;
            margin-top: 10px;
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            background-color: #28a745;
            color: white;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #218838;
        }

        .btn:disabled {
            background-color: #aaa;
            cursor: not-allowed;
        }

        /* Payment Method Section */
        .mb-3 {
            margin-bottom: 20px;
        }

        select.form-select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        #payment-form {
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }
    </style>
</head>
<body>

<div class="container mt-5" style="max-width: 600px;">
    <h3 class="text-center mb-4">Simulated Payment Gateway</h3>

    <form action="payment_success.php" method="post" id="payment-form" class="border p-4 rounded bg-light">
        <!-- Mobile Number -->
        <div class="mb-3">
            <label for="mobile" class="form-label">Enter Mobile Number</label>
            <input type="text" name="mobile" class="form-control" required placeholder="e.g. 01XXXXXXXXX">
        </div>

        <!-- Transaction ID -->
        <div class="mb-3">
            <label for="trxid" class="form-label">Transaction ID</label>
            <input type="text" name="trxid" class="form-control" required placeholder="Dummy Transaction ID">
        </div>

        <!-- Payment Method Selection -->
        <div class="mb-3">
            <label class="form-label">Choose Payment Method</label>
            <select name="payment_method" id="payment_method" class="form-select" required>
                <option value="">Select Payment Method</option>
                <option value="bkash">bKash</option>
                <option value="nagad">Nagad</option>
                <option value="stripe">Stripe</option>
            </select>
        </div>

        <!-- Stripe Payment Form -->
        <div id="stripe-form" style="display:none;">
            <div class="mb-3">
                <label for="card-element" class="form-label">Credit or Debit Card</label>
                <div id="card-element" class="form-control p-2"></div>
                <div id="card-errors" class="text-danger mt-2"></div>
            </div>
            <button type="button" id="stripe-submit" class="btn btn-success w-100">Pay with Stripe</button>
        </div>

        <!-- Normal Pay Now button -->
        <button type="submit" id="normal-submit" class="btn btn-success w-100">Pay Now</button>
    </form>
</div>

<!-- Include Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>

<script>
    var stripe = Stripe('pk_test_51RLpl6Q1NZRl7ka9Jk91jguiySsFciINA8mXEm7trSDr3EFYAQmYdaNo0F8tG6EOXMxg5Ea1YlfVXHwJCotNJCzz00hdtqKsyz');
    var elements = stripe.elements();

    var style = {
        base: {
            iconColor: '#666EE8',
            color: '#31325F',
            fontWeight: '400',
            fontFamily: 'Helvetica Neue, Helvetica, sans-serif',
            fontSize: '16px',
            '::placeholder': {
                color: '#CFD7E0'
            }
        },
        invalid: {
            iconColor: '#e85746',
            color: '#e85746'
        }
    };

    var card = elements.create('card', { style: style, hidePostalCode: true });
    card.mount('#card-element');

    // Show/hide Stripe form based on payment method selection
    document.getElementById('payment_method').addEventListener('change', function () {
        var stripeForm = document.getElementById('stripe-form');
        var normalSubmit = document.getElementById('normal-submit');

        if (this.value === 'stripe') {
            stripeForm.style.display = 'block';
            normalSubmit.style.display = 'none';
        } else {
            stripeForm.style.display = 'none';
            normalSubmit.style.display = 'block';
        }
    });

    // Handle Stripe payment button click
    document.getElementById('stripe-submit').addEventListener('click', function (event) {
        event.preventDefault();

        stripe.createPaymentMethod({
            type: 'card',
            card: card,
        }).then(function (result) {
            if (result.error) {
                // Show error
                document.getElementById('card-errors').textContent = result.error.message;
            } else {
                // Create hidden input with PaymentMethod ID
                var form = document.getElementById('payment-form');
                var paymentMethodInput = document.createElement('input');
                paymentMethodInput.type = 'hidden';
                paymentMethodInput.name = 'payment_method_id';
                paymentMethodInput.value = result.paymentMethod.id;
                form.appendChild(paymentMethodInput);

                // Submit form
                form.submit();
            }
        });
    });
</script>

<?php include "footer.php"; ?>

</body>
</html>
