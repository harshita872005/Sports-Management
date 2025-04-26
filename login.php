<?php
// login.php
session_start();
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // $role = $_POST['role'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $role);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['role'] = $role;
        if($role === 'admin') {
            header("Location: admin_dashboard.php");
            exit();
        } 
        else if($role === 'player') {
          header("Location: player_dashboard.php");
          exit();}
          else{
            header("Location: team_dashboard.php");
            exit();
          }
        // header("Location: dashboard.php");
        // exit();
    } else {
        $error = "Invalid username or password.";
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Sports League</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      background-image: url('https://images.pexels.com/photos/46798/the-ball-stadion-football-the-pitch-46798.jpeg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      background-repeat: no-repeat;
    }
    .content-wrapper {
      background-color: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
    }
  </style>
</head>
<body>
  <header class="bg-gray-800 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
      <div class="flex items-center">
        <i class="fas fa-trophy text-yellow-400 text-3xl mr-3"></i>
        <h1 class="text-2xl font-bold"><span class="text-blue-400">Sports</span> League</h1>
      </div>
      <nav>
        <ul class="flex space-x-4">
          <li><a href="index.php" class="hover:text-gray-300"><i class="fas fa-home text-2xl"></i></a></li>
        </ul>
      </nav>
    </div>
  </header>

  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="content-wrapper max-w-lg w-full space-y-8 p-10 rounded-xl shadow-2xl">
      <div class="text-center">
        <h2 class="mt-6 text-4xl font-extrabold text-gray-900 bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
          <i class="fas fa-user-circle text-gray-700 mr-2"></i>Welcome Back!
        </h2>
        <p class="mt-3 text-md text-gray-600 italic">
          Sign in to continue your journey.
        </p>
        <div class="h-1 w-24 bg-gray-500 mx-auto mt-4 rounded-full"></div>
      </div>
      <div class="p-6">
        <?php if(isset($error)) echo "<p class='text-red-600 text-center mb-4'>$error</p>"; ?>
        <form method="POST" action="" class="space-y-6">
          <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2">
              <i class="fas fa-user mr-2"></i>Username
            </label>
            <input type="text" name="username" required placeholder="Enter your username"
                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 transition duration-200">
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2">
              <i class="fas fa-lock mr-2"></i>Password
            </label>
            <input type="password" name="password" required placeholder="Enter your password"
                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 transition duration-200">
          </div>
          
          <button type="submit" 
                  class="w-full py-4 rounded-lg bg-gray-500 hover:bg-gray-600 text-white text-lg font-semibold transition duration-200 transform hover:scale-[1.02]">
            Sign In
          </button>
        </form>
        <p class="mt-8 text-center text-gray-600">
          Don't have an account? 
          <a href="register.php" class="text-gray-800 hover:text-gray-600 font-medium">Sign up</a>
        </p>
        <p class="text-center text-gray-600 mt-4">or Sign in with</p>
        <div class="flex justify-center space-x-6 mt-4">
          <a href="#" class="text-2xl text-red-500 hover:text-red-600">
            <i class="fab fa-google"></i>
          </a>
          <a href="#" class="text-2xl text-blue-600 hover:text-blue-700">
            <i class="fab fa-facebook"></i>
          </a>
          <a href="#" class="text-2xl text-blue-400 hover:text-blue-500">
            <i class="fab fa-twitter"></i>
          </a>
        </div>
      </div>
    </div>
  </div>

  <footer class="bg-gray-800 text-white text-center py-6">
    <div class="container mx-auto">
      <p>© 2025 Sports League. All rights reserved.</p>
    </div>
  </footer>
</body>
</html>
