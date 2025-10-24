<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>J&T Express Shipping Label</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f0f0;
            padding: 20px;
        }

        .label-container {
            width: 600px;
            background: white;
            border: 3px solid black;
        }

        .header {
            display: flex;
            border-bottom: 3px solid black;
        }

        .logo-section {
            width: 43%;
            border-right: 3px solid black;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #e31e24;
        }

        .header-info {
            flex: 1;
            padding: 15px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .imus {
            font-size: 18px;
            font-weight: bold;
        }

        .tracking-number-large {
            font-size: 28px;
            font-weight: bold;
            text-align: right;
        }

        .send-date {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .tracking-number {
            font-size: 48px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 2px;
        }

        .order-id-section {
            border-bottom: 3px solid black;
            display: flex;
        }

        .order-id-label {
            width: 43%;
            border-right: 3px solid black;
            padding: 15px;
            text-align: center;
        }

        .order-id-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .order-id-number {
            font-size: 14px;
        }

        .order-id-value {
            flex: 1;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 52px;
            font-weight: bold;
        }

        .barcode-section {
            border-bottom: 3px solid black;
            padding: 20px;
            text-align: center;
        }

        .barcode {
            height: 80px;
            background: repeating-linear-gradient(
                90deg,
                black 0px,
                black 3px,
                white 3px,
                white 5px,
                black 5px,
                black 6px,
                white 6px,
                white 10px,
                black 10px,
                black 14px,
                white 14px,
                white 16px,
                black 16px,
                black 18px,
                white 18px,
                white 19px,
                black 19px,
                black 22px,
                white 22px,
                white 26px
            );
            margin-bottom: 10px;
        }

        .barcode-number {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 3px;
        }

        .buyer-seller-section {
            display: flex;
        }

        .buyer-section {
            width: 43%;
            border-right: 3px solid black;
            border-bottom: 3px solid black;
            padding: 15px;
        }

        .section-label {
            writing-mode: vertical-lr;
            transform: rotate(180deg);
            font-size: 18px;
            font-weight: bold;
            position: absolute;
            left: 5px;
            top: 50%;
            transform: translateY(-50%) rotate(180deg);
        }

        .buyer-section, .seller-section {
            position: relative;
            padding-left: 40px;
        }

        .buyer-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .buyer-phone {
            font-size: 14px;
            margin-bottom: 8px;
        }

        .buyer-address {
            font-size: 13px;
            line-height: 1.4;
            margin-bottom: 15px;
        }

        .cod-watermark {
            position: relative;
            text-align: center;
            margin: 10px 0;
        }

        .cod-text {
            font-size: 80px;
            font-weight: bold;
            color: #d0d0d0;
            opacity: 0.3;
        }

        .location-info {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: bold;
        }

        .location-left {
            flex: 1;
        }

        .location-right {
            text-align: right;
        }

        .home-code {
            font-size: 20px;
            font-weight: bold;
        }

        .seller-section {
            flex: 1;
            border-bottom: 3px solid black;
            padding: 15px;
        }

        .seller-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .seller-phone {
            font-size: 13px;
            margin-bottom: 3px;
        }

        .seller-address {
            font-size: 12px;
            line-height: 1.3;
            margin-bottom: 10px;
        }

        .delivery-info {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-top: 10px;
        }

        .sbd {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
        }

        .date-info {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: bold;
            margin-top: 5px;
        }

        .bottom-section {
            display: flex;
        }

        .qr-shopee-section {
            width: 43%;
            border-right: 3px solid black;
            display: flex;
            flex-direction: column;
        }

        .qr-code {
            padding: 20px;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-placeholder {
            width: 120px;
            height: 120px;
            background: white;
            border: 3px solid black;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            grid-template-rows: repeat(5, 1fr);
            gap: 2px;
            padding: 5px;
        }

        .qr-block {
            background: black;
        }

        .qr-block.white {
            background: white;
        }

        .shopee-section {
            padding: 10px 15px;
            border-top: 3px solid black;
            display: flex;
            align-items: center;
        }

        .shopee-logo {
            width: 30px;
            height: 30px;
            background: #ee4d2d;
            border-radius: 3px;
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .shopee-text {
            font-size: 11px;
            color: #666;
            line-height: 1.3;
        }

        .product-delivery-section {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .barcode-product {
            border-bottom: 3px solid black;
            padding: 15px;
            text-align: center;
        }

        .product-barcode {
            height: 60px;
            background: repeating-linear-gradient(
                90deg,
                black 0px,
                black 2px,
                white 2px,
                white 4px,
                black 4px,
                black 7px,
                white 7px,
                white 9px
            );
            margin-bottom: 10px;
        }

        .product-info {
            display: flex;
            border-bottom: 3px solid black;
        }

        .product-details {
            flex: 1;
            padding: 15px;
            border-right: 3px solid black;
        }

        .product-line {
            font-size: 13px;
            margin-bottom: 5px;
        }

        .cod-amount {
            flex: 1;
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .cod-label {
            font-size: 12px;
            margin-bottom: 3px;
        }

        .cod-value {
            font-size: 20px;
            font-weight: bold;
        }

        .attempt-section {
            display: flex;
        }

        .attempt-box {
            flex: 1;
            padding: 10px;
            text-align: center;
            border-right: 3px solid black;
        }

        .attempt-box:last-child {
            border-right: none;
        }

        .attempt-label {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .attempt-numbers {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .attempt-number {
            width: 40px;
            height: 40px;
            border: 2px solid black;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="label-container">
        <!-- Header Section -->
        <div class="header">
            <div class="logo-section">
                <div class="logo">J&T<span style="font-size: 24px;">EXPRESS</span></div>
            </div>
            <div class="header-info">
                <div class="header-top">
                    <div>
                        <div class="imus">Imus</div>
                        <div class="send-date">Send Date: 2021-04-04</div>
                    </div>
                    <div class="tracking-number-large">029</div>
                </div>
                <div class="tracking-number">460-410301</div>
            </div>
        </div>

        <!-- Order ID Section -->
        <div class="order-id-section">
            <div class="order-id-label">
                <div class="order-id-title">Order ID</div>
                <div class="order-id-number">2104034PNYXKJB</div>
            </div>
            <div class="order-id-value">460-410301</div>
        </div>

        <!-- Barcode Section -->
        <div class="barcode-section">
            <div class="barcode"></div>
            <div class="barcode-number">770180382589</div>
        </div>

        <!-- Buyer and Seller Section -->
        <div class="buyer-seller-section">
            <div class="buyer-section">
                <div class="section-label">BUYER</div>
                <div class="buyer-name">April Joy Legaspi</div>
                <div class="buyer-phone">639054941145</div>
                <div class="buyer-address">046 Brgy. Anabu 2C Imus, Cavite, Imus, Cavite, South Luzon</div>
                <div class="cod-watermark">
                    <div class="cod-text">COD</div>
                </div>
                <div class="location-info">
                    <div class="location-left">
                        <div>Imus</div>
                        <div>Anabu II-C</div>
                    </div>
                    <div class="location-right">
                        <div>Cavite</div>
                        <div>South Luzon</div>
                        <div class="home-code">HOME<br>4103</div>
                    </div>
                </div>
            </div>

            <div class="seller-section">
                <div class="section-label">SELLER</div>
                <div class="seller-name">Gracefully.ph</div>
                <div class="seller-phone">639952843840</div>
                <div class="seller-address">260 F MLQuezon St. Hagonoy Taguig City</div>
                <div class="delivery-info">
                    <div>
                        <div>Taguig City</div>
                        <div>Hagonoy</div>
                    </div>
                    <div>
                        <div>Metro Manila</div>
                        <div>Metro Manila</div>
                    </div>
                    <div class="sbd">
                        <div>SBD</div>
                        <div style="font-size: 14px; font-weight: bold;">2021-04-10</div>
                        <div style="font-size: 14px; font-weight: bold;">1630</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="bottom-section">
            <div class="qr-shopee-section">
                <div class="qr-code">
                    <div class="qr-placeholder">
                        <div class="qr-block"></div>
                        <div class="qr-block white"></div>
                        <div class="qr-block"></div>
                        <div class="qr-block white"></div>
                        <div class="qr-block"></div>
                        <div class="qr-block white"></div>
                        <div class="qr-block"></div>
                        <div class="qr-block"></div>
                        <div class="qr-block white"></div>
                        <div class="qr-block"></div>
                        <div class="qr-block"></div>
                        <div class="qr-block white"></div>
                        <div class="qr-block"></div>
                        <div class="qr-block"></div>
                        <div class="qr-block white"></div>
                        <div class="qr-block white"></div>
                        <div class="qr-block"></div>
                        <div class="qr-block white"></div>
                        <div class="qr-block"></div>
                        <div class="qr-block"></div>
                        <div class="qr-block"></div>
                        <div class="qr-block white"></div>
                        <div class="qr-block"></div>
                        <div class="qr-block white"></div>
                        <div class="qr-block"></div>
                    </div>
                </div>
                <div class="shopee-section">
                    <div class="shopee-logo">S</div>
                    <div class="shopee-text">
                        Thank you for shipping with Shopee!<br>
                        Please click "Order Received and rate this<br>
                        product!!
                    </div>
                </div>
            </div>

            <div class="product-delivery-section">
                <div class="barcode-product">
                    <div class="product-barcode"></div>
                </div>
                <div class="product-info">
                    <div class="product-details">
                        <div class="product-line">Product Quantity: 1</div>
                        <div class="product-line">Weight: 2.0 kg</div>
                    </div>
                    <div class="cod-amount">
                        <div class="cod-label">COD Amount:</div>
                        <div class="cod-value">228.0</div>
                    </div>
                </div>
                <div class="attempt-section">
                    <div class="attempt-box">
                        <div class="attempt-label">Delivery Attempt</div>
                        <div class="attempt-numbers">
                            <div class="attempt-number">1</div>
                            <div class="attempt-number">2</div>
                        </div>
                    </div>
                    <div class="attempt-box">
                        <div class="attempt-label">Return Attempt</div>
                        <div class="attempt-numbers">
                            <div class="attempt-number">1</div>
                            <div class="attempt-number">2</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>