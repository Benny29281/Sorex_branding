// Data pengguna (Hard-coded)
const users = {
    user: { username: "user", password: "user123", role: "user" },
    admin: { username: "admin", password: "admin123", role: "admin" },
    administrator: { username: "administrator", password: "admin1234", role: "administrator" },
    user2: { username: "user2", password: "user12345", role: "user2" } // Tambahan user baru
};

// Menangani submit form login
document.getElementById('login-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorMessage = document.getElementById('error-message');

    // Cek apakah username dan password cocok
    if (checkLogin(username, password)) {
        // Redirect ke halaman sesuai role
        const role = users[username].role;
        if (role === "user2") {
            window.location.href = "user2.html"; // Redirect ke user2.html
        } else {
            window.location.href = `${role}.html`;
        }
    } else {
        errorMessage.style.display = 'block'; // Tampilkan pesan error jika login gagal
    }
});

// Fungsi untuk cek login
function checkLogin(username, password) {
    return users[username] && users[username].password === password;
}

// Menangani klik "Lupa Password?"
document.getElementById('forgot-password-link').addEventListener('click', function(event) {
    event.preventDefault();
    // Menyembunyikan form login dan menampilkan form forgot password
    document.getElementById('login-form').style.display = 'none';
    document.getElementById('forgot-password-form').style.display = 'block';
    document.getElementById('form-title').innerText = 'Lupa Password';
});

// Menangani klik "Kembali ke Halaman Login"
document.getElementById('back-to-login-link').addEventListener('click', function(event) {
    event.preventDefault();
    // Menyembunyikan form forgot password dan menampilkan form login
    document.getElementById('forgot-password-form').style.display = 'none';
    document.getElementById('login-form').style.display = 'block';
    document.getElementById('form-title').innerText = 'Login';
});

// Menangani submit form forgot password
document.getElementById('forgot-password-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const email = document.getElementById('email').value;
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');

    // Cek apakah email valid (simple check)
    if (validateEmail(email)) {
        // Simulasi pengiriman email pemulihan
        setTimeout(function() {
            successMessage.style.display = 'block';
            errorMessage.style.display = 'none';
            document.getElementById('forgot-password-form').reset();
        }, 1000);
    } else {
        successMessage.style.display = 'none';
        errorMessage.style.display = 'block';
    }
});

// Fungsi untuk validasi email (simple regex)
function validateEmail(email) {
    const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    return regex.test(email);
}
