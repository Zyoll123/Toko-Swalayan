<?php
include '../aksi/koneksi.php';
session_start();
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
}

$adminName = $_SESSION['Name'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Name = trim($_POST['Name'] ?? '');
    $Email = trim($_POST['Email'] ?? '');
    $Password = trim($_POST['Password'] ?? '');
    $ConfirmPassword = trim($_POST['ConfirmPassword'] ?? '');

    $errors = [];
    if (empty($Name))
        $errors[] = "Nama harus diisi";
    if (empty($Email))
        $errors[] = "Email harus diisi";
    if (!filter_var($Email, FILTER_VALIDATE_EMAIL))
        $errors[] = "Format email tidak valid.";
    if (empty($Password))
        $errors[] = "Password harus diisi";
    if ($Password !== $ConfirmPassword)
        $errors[] = "Password dan konfirmasi password tidak sama";
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $Password))
        $errors[] = "Password harus minimal 8 karakter, mengandung 1 huruf besar, 1 huruf kecil, dan 1 angka";

    if (empty($errors)) {
        $query = "INSERT INTO accounts (Name, Email, Role, Password)
        VALUES (?, ?, 'Cashier', ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sss", $Name, $Email, $Password);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: users.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADD NEW CASHIER</title>
    <link rel="stylesheet" href="style/add_new_users.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
    <div class="container">
        <?php include 'sidebar.php' ?>
        <div class="content">
            <div class="title">
                <h1>TAMBAH USER KASIR</h1>
            </div>
            <form action="" method="post">
                <div class="form-group">
                    <input type="text" class="form-input" id="name" name="Name" placeholder="Enter Username"
                        oninput="toggleLabel(this)" required>
                </div>
                <div class="form-group">
                    <input type="email" class="form-input" name="Email" id="email" placeholder="Enter Email"
                        oninput="toggleLabel(this)" required>
                </div>
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="password" class="form-input" name="Password" id="password"
                            placeholder="Enter Password" oninput="validatePasswordStrenght(this)" required>
                        <button type="button" class="toggle-password"
                            onclick="togglePasswordVisibility('password', this)"><i
                                class="fa-solid fa-eye"></i></button>

                    </div>
                    <div id="password-strenght-message" class="error-message"></div>
                </div>
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="password" name="ConfirmPassword" id="confirmPassword" class="form-input"
                            placeholder="Confirm Password" oninput="checkPasswordMatch()" required>
                        <button type="button" class="toggle-password"
                            onclick="togglePasswordVisibility('confirmPassword', this)"><i
                                class="fa-solid fa-eye"></i></button>
                    </div>
                    <div id="password-match-message" class="error-message"></div>
                </div>
                <button type="submit">ADD</button>
            </form>
        </div>
    </div>

    <script src="js/add_new_product.js"></script>
    <script>
        function togglePasswordVisibility(fieldId, button) {
            const passwordField = document.getElementById(fieldId)
            const icon = button.querySelector('i')

            if (passwordField.type === "password") {
                passwordField.type = "text"
                icon.classList.remove('fa-eye')
                icon.classList.add('fa-eye-slash')
            } else {
                passwordField.type = "password"
                icon.classList.remove('fa-eye-slash')
                icon.classList.add('fa-eye')
            }
        }

        function validatePasswordStrenght(input) {
            const password = input.value
            const message = document.getElementById('password-strenght-message')

            if (password.lenght === 0) {
                message.textContent = ''
                return false
            }

            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const isLongEnough = password.length >= 8;

            let errors = [];
            if (!hasUpperCase) errors.push('1 huruf besar');
            if (!hasLowerCase) errors.push('1 huruf kecil');
            if (!hasNumber) errors.push('1 angka');
            if (!isLongEnough) errors.push('minimal 8 karakter');

            if (errors.length > 0) {
                message.textContent = 'Password harus mengandung: ' + errors.join(', ');
                message.style.color = 'red';
                return false;
            } else {
                message.textContent = 'Password memenuhi syarat';
                message.style.color = 'green';
                return true;
            }
        }

        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const message = document.getElementById('password-match-message');

            if (confirmPassword.length === 0) {
                message.textContent = '';
                return false;
            }

            if (password !== confirmPassword) {
                message.textContent = 'Password tidak sama';
                message.style.color = 'red';
                return false;
            } else {
                message.textContent = 'Password sama';
                message.style.color = 'green';
                return true;
            }
        }

        function validatePassword() {
            const isStrong = validatePasswordStrength(document.getElementById('password'));
            const isMatch = checkPasswordMatch();

            return isStrong && isMatch;
        }
    </script>
</body>

</html>