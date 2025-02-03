<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Methods - TogetherA+</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .payment-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .payment-container h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
        }

        .payment-container p {
            font-size: 16px;
            color: #555;
            margin-bottom: 30px;
        }

        .payment-methods {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .payment-method {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px;
            width: 150px;
            height: 120px;
            background-color: #f4f4f4;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .payment-method:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .payment-method img {
            width: 60px;
            height: auto;
            margin-bottom: 10px;
        }

        .payment-method span {
            font-size: 14px;
            color: #333;
        }

        .back-btn {
            display: inline-block;
            margin-top: 30px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h1>Select Payment Method</h1>
        <p>Choose a preferred payment method to complete your payment:</p>

        <!-- Payment Methods Section -->
        <div class="payment-methods">
            <div class="payment-method">
                <img src="img/visa.png" alt="Visa">
                <span>Visa</span>
            </div>
            <div class="payment-method">
                <img src="img/mastercard.png" alt="Mastercard">
                <span>Mastercard</span>
            </div>
            <div class="payment-method">
                <img src="img/paypal.png" alt="PayPal">
                <span>PayPal</span>
            </div>
            <div class="payment-method">
                <img src="img/mobile_banking.png" alt="Mobile Banking">
                <span>Mobile Banking</span>
            </div>
            <div class="payment-method">
                <img src="img/bkash.png" alt="bKash">
                <span>bKash</span>
            </div>
            <div class="payment-method">
                <img src="img/rocket.png" alt="Rocket">
                <span>Rocket</span>
            </div>
        </div>

        <!-- Back Button -->
        <a href="payment.php" class="back-btn">Go Back to Payment Page</a>
    </div>
</body>
</html>