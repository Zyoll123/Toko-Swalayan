<?php
include '../aksi/koneksi.php';
session_start();
if (!isset($_SESSION['Name'])) {
    header("Location: ../login/login.html");
    exit();
}

$adminName = $_SESSION['Name'];

if (!isset($_GET['Id'])) {
    echo "<p>Id user tidak ditemukan.</p>";
    exit();
}

$Id = (int)$_GET['Id'];

$query_users = "SELECT * FROM accounts WHERE Id = ?";
$stmt = mysqli_prepare($conn, $query_users);
mysqli_stmt_bind_param($stmt, "i", $Id);
mysqli_stmt_execute($stmt);
$result_users = mysqli_stmt_get_result($stmt);

if (!$result_users || $result_users->num_rows === 0) {
    echo "<p>Data user tidak ditemukan.</p>";
    exit();
}

$d = mysqli_fetch_assoc($result_users);

$query_roles = "SELECT DISTINCT Role FROM accounts";
$result_roles = mysqli_query($conn, $query_roles);
$roles = [];
while ($row = mysqli_fetch_assoc($result_roles)) {
    $roles[] = $row['Role'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Name = trim($_POST['Name'] ?? '');
    $Email = trim($_POST['Email'] ?? '');
    $Password = $_POST['Password'] ?? '';
    $ConfirmPassword = $_POST['ConfirmPassword'] ?? '';
    $Role = trim($_POST['Role'] ?? '');

    $errors = [];
    if (empty($Name)) $errors[] = "Nama harus diisi";
    if (empty($Email)) $errors[] = "Email harus diisi";
    if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid";
    if (empty($Role)) $errors[] = "Role harus dipilih";

    // Validasi password hanya jika salah satu password atau confirm diisi
    if (!empty($Password) || !empty($ConfirmPassword)) {
        if ($Password !== $ConfirmPassword) $errors[] = "Password dan Confirm Password tidak sama";
        // Bisa tambahkan validasi kekuatan password di sini jika diperlukan
    }

    if (empty($errors)) {
        if (!empty($Password)) {
            // Update dengan password baru
            $query = "UPDATE accounts SET Name = ?, Email = ?, Role = ?, Password = ? WHERE Id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssssi", $Name, $Email, $Role, $Password, $Id);
        } else {
            // Update tanpa ubah password
            $query = "UPDATE accounts SET Name = ?, Email = ?, Role = ? WHERE Id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssi", $Name, $Email, $Role, $Id);
        }

        if (mysqli_stmt_execute($stmt)) {
            header("Location: users.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>UPDATE USER</title>
<link rel="stylesheet" href="style/update_users.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
<style>
  .input-wrapper {
    position: relative;
  }
  .toggle-password {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
  }
  .error-message {
    font-size: 0.9em;
    margin-top: 5px;
  }
</style>
</head>
<body>
<div class="container">
    <?php include 'sidebar.php' ?>
    <div class="content">
        <div class="title">
            <h1>Update User</h1>
        </div>
        <form action="" method="post" onsubmit="return validateForm()">
            <div class="form-group">
                <input type="text" class="form-input" name="Name" id="name" placeholder="Enter new Name"
                    value="<?= htmlspecialchars($d['Name']) ?>" required>
            </div>
            <div class="form-group">
                <input type="email" class="form-input" name="Email" id="email" placeholder="Enter new Email"
                    value="<?= htmlspecialchars($d['Email']) ?>" required>
            </div>
            <div class="form-group input-wrapper">
                <input type="password" class="form-input" name="Password" id="password"
                    placeholder="Enter new password (optional)" oninput="validatePasswordStrength(this)">
                <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password', this)">
                    <i class="fa-solid fa-eye"></i>
                </button>
                <div id="password-strength-message" class="error-message"></div>
            </div>
            <div class="form-group input-wrapper">
                <input type="password" class="form-input" name="ConfirmPassword" id="confirmPassword"
                    placeholder="Confirm new password (optional)" oninput="checkPasswordMatch()">
                <button type="button" class="toggle-password" onclick="togglePasswordVisibility('confirmPassword', this)">
                    <i class="fa-solid fa-eye"></i> 
                </button>
                <div id="password-match-message" class="error-message"></div>
            </div>
            <div class="form-group">
                <label for="Role">Choose Role</label>
                <select name="Role" id="Role" required>
                    <option value="">--Choose Role--</option>
                    <?php foreach ($roles as $role): ?>
                    <option value="<?= htmlspecialchars($role) ?>"
                        <?= ($role === $d['Role']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit">Update</button>
        </form>
    </div>
</div>

<script>
function togglePasswordVisibility(fieldId, button) {
    const passwordField = document.getElementById(fieldId);
    const icon = button.querySelector('i');

    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = "password";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function validatePasswordStrength(input) {
    const password = input.value;
    const message = document.getElementById('password-strength-message');

    if (password.length === 0) {
        message.textContent = '';
        return true; // Password optional
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
        return true;
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

function validateForm() {
    const password = document.getElementById('password').value.trim();
    const confirmPassword = document.getElementById('confirmPassword').value.trim();

    if ((password !== '' || confirmPassword !== '')) {
        if (!validatePasswordStrength(document.getElementById('password'))) {
            alert("Password tidak memenuhi syarat.");
            return false;
        }
        if (password !== confirmPassword) {
            alert("Password dan Confirm Password harus sama.");
            return false;
        }
    }
    return true;
}
</script>
</body>
</html>
