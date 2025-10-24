<?php
session_start();
include 'includes/db.php';
if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit();
}

$email = $_SESSION['email'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_code = implode('', $_POST['code']); // combine 6 inputs

    $stmt = $conn->prepare("SELECT verification_code FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($db_code);
    $stmt->fetch();
    $stmt->close();

    if ($input_code === $db_code) {
        $update = $conn->prepare("UPDATE users SET is_verified=1 WHERE email=?");
        $update->bind_param("s", $email);
        $update->execute();
        $update->close();

        unset($_SESSION['email']);
        $_SESSION['success'] = "Your account has been verified. Please log in.";
        header("Location: login.php");
        exit();
    } else {
        $message = "<div class='alert alert-danger mt-3'>Invalid code. Please try again.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verify Email - Shophub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #ffecd2, #fcb69f);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .verify-card {
            background: #fff;
            border-radius: 20px;
            padding: 3rem 2rem;
            width: 400px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .verify-card img {
            width: 120px;
            margin-bottom: 20px;
        }

        .code-inputs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }

        .code-inputs input {
            width: 45px;
            height: 55px;
            text-align: center;
            font-size: 1.5rem;
            border-radius: 10px;
            border: 2px solid #ddd;
            transition: all 0.2s ease;
        }

        .code-inputs input:focus {
            border-color: #ff914d;
            outline: none;
        }

        .btn-primary {
            background-color: #ff914d;
            border: none;
            border-radius: 12px;
            padding: 0.8rem;
            font-size: 1rem;
            width: 100%;
        }

        .btn-primary:disabled {
            background-color: #ffb28b;
        }

        .btn-primary:hover {
            background-color: #ff7f32;
        }

        h3 {
            font-weight: 600;
            margin-bottom: 10px;
        }

        p.text-muted {
            color: #666;
            font-size: 0.95rem;
        }
    </style>
</head>

<body>
    <div class="verify-card">
        <img src="Shophub.png" alt="Shophub Logo">
        <h3>Enter Verification Code</h3>
        <p class="text-muted">We sent a 6-digit code to <b><?= htmlspecialchars($email) ?></b></p>
        <?= $message ?>
        <form method="POST" id="verifyForm">
            <div class="code-inputs">
                <?php for ($i = 0; $i < 6; $i++): ?>
                    <input type="text" name="code[]" maxlength="1" pattern="[0-9]" required>
                <?php endfor; ?>
            </div>
            <button type="submit" class="btn btn-primary" id="verifyBtn" disabled>Verify</button>
        </form>
        <p class="mt-3 text-muted">Didn’t get the code? <a href="resend_code.php" class="text-primary fw-bold">Resend</a></p>
    </div>

    <script>
        const inputs = document.querySelectorAll('.code-inputs input');
        const verifyBtn = document.getElementById('verifyBtn');

        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                checkFilled();
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        function checkFilled() {
            const allFilled = Array.from(inputs).every(input => input.value.trim() !== '');
            verifyBtn.disabled = !allFilled;
        }
    </script>
</body>

</html>