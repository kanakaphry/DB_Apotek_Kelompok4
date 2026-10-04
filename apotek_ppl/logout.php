<?php
require_once __DIR__ . '/model/Auth.php';

Auth::logout();
echo "<script>alert('Berhasil Logout'); window.location.href = 'login.php'</script>";
