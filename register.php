<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - Sports League</title>
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
    .role-btn:hover {
      transform: translateY(-5px);
      transition: transform 0.3s ease;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .success-message {
      animation: fadeInUp 0.5s ease-out;
    }
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>
 
<?php
include 'db_config.php';

$msg = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'] ?? '';

    if(empty($role)) {
        $error = "Please select a role";
    }
    else {
        $check_stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
        if ($check_stmt) {
            $check_stmt->bind_param("s", $username);
            $check_stmt->execute();
            $check_stmt->store_result();
            
            if($check_stmt->num_rows > 0) {
                $error = "Username already exists. Please choose another.";
            }
            $check_stmt->close();
        } else {
            $error = "Database error occurred";
        }
    }

    if (empty($error) && $role === 'admin') {
        $adminCodeInput = trim($_POST['admin_code'] ?? '');
        $secureAdminCode = "admin@321"; 
        if (!$secureAdminCode || $adminCodeInput !== $secureAdminCode) {
            $error = "Invalid admin code. You are not authorized to register as an admin.";
        }
    }
    
    if (empty($error)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("sss", $username, $hashedPassword, $role);
            if ($stmt->execute()) {
                $msg = "Successfully registered. Please Login.";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Database error occurred";
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - Sports League</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <script>
    function toggleAdminCode(role) {
      var adminField = document.getElementById("adminCodeField");
      var roleInput = document.getElementById("roleInput");
      roleInput.value = role;
      
      document.querySelectorAll('.role-btn').forEach(btn => {
        btn.classList.remove('bg-gray-600');
        btn.classList.add('bg-gray-500');
      });
      
      event.target.classList.remove('bg-gray-500');
      event.target.classList.add('bg-gray-600');
      
      if (role === "admin") {
        adminField.style.display = "block";
      } else {
        adminField.style.display = "none";
      }
    }
  </script>
</head>
<body class="flex flex-col min-h-screen">
  <header class="w-full bg-gray-800 bg-opacity-90 text-white py-4 px-6 flex justify-between items-center">
    <div class="flex items-center">
      <i class="fas fa-trophy text-yellow-400 text-3xl mr-3"></i>
      <h1 class="text-2xl font-bold"><span class="text-blue-400">Sports</span> League</h1>
    </div>
    <a href="index.php" class="flex items-center hover:text-gray-300 transition duration-200">
      <i class="fas fa-home text-2xl"></i>
    </a>
  </header>

  <div class="flex justify-center items-center flex-grow py-8 mb-16">
    <div class="max-w-xl w-full mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden content-wrapper">
      <div class="px-8 py-8 bg-gray-200 bg-opacity-90">
        <h2 class="text-4xl font-bold text-center text-gray-800">Create Your Account</h2>
        <p class="text-center mt-4 text-xl text-gray-600 font-medium italic">
          <i class="fas fa-fire text-orange-500 mr-2"></i>
          Sign in to access your account
        </p>
      </div>
      <div class="p-10">
        <?php 
          if(!empty($msg)) {
            echo "<div class='success-message bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-md'>
                    <div class='flex items-center'>
                      <i class='fas fa-check-circle text-green-500 text-2xl mr-3'></i>
                      <p class='font-medium'>$msg</p>
                    </div>
                    <p class='mt-2 ml-9'>You can now <a href='login.php' class='text-blue-600 hover:underline font-semibold'>login here</a>.</p>
                  </div>";
          }
          if(!empty($error)) echo "<p class='text-red-600 text-center mb-4 bg-red-100 p-3 rounded-lg border-l-4 border-red-500'>$error</p>"; 
        ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-8">
          <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2">
              <i class="fas fa-user mr-2"></i>Username
            </label>
            <input type="text" name="username" required placeholder="Enter your name"
                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 transition duration-200">
          </div>
          
          <div>
            <label class="block text-gray-700 text-sm font-semibold mb-2">
              <i class="fas fa-lock mr-2"></i>Password
            </label>
            <input type="password" name="password" required placeholder="Enter a password"
                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 transition duration-200">
          </div>
          
          <div>
            <label class="block text-gray-700 text-lg font-semibold mb-4">
              <i class="fas fa-users mr-2"></i>Choose Your Role
            </label>
            <input type="hidden" name="role" id="roleInput" required>
            <div class="grid grid-cols-3 gap-6">
              <div class="flex flex-col items-center">
                <button type="button" onclick="toggleAdminCode('player')" 
                        class="role-btn w-full bg-gray-500 text-white py-4 px-6 rounded-lg transition duration-200 text-lg hover:bg-gray-600">
                  <i class="fas fa-running text-blue-300 mr-2"></i>Player
                </button>
                <!-- <span class="mt-2 text-sm text-gray-700 font-semibold">Join as Player</span> -->
              </div>
              <div class="flex flex-col items-center">
                <button type="button" onclick="toggleAdminCode('team')"
                        class="role-btn w-full bg-gray-500 text-white py-4 px-6 rounded-lg transition duration-200 text-lg hover:bg-gray-600">
                  <i class="fas fa-users-rectangle text-green-400 mr-2"></i><i class="fas fa-trophy text-yellow-400 mr-2"></i>Team
                </button>
                <!-- <span class="mt-2 text-sm text-gray-700 font-semibold">Unite & Conquer</span> -->
              </div>
              <div class="flex flex-col items-center">
                <button type="button" onclick="toggleAdminCode('admin')"
                        class="role-btn w-full bg-gray-500 text-white py-4 px-6 rounded-lg transition duration-200 text-lg hover:bg-gray-600">
                  <i class="fas fa-user-shield text-purple-400 mr-2"></i>Admin
                </button>
                <!-- <span class="mt-2 text-sm text-gray-700 font-semibold">Administration Access</span> -->
              </div>
            </div>
          </div>
          
          <div id="adminCodeField" style="display:none;">
            <label class="block text-gray-700 text-sm font-semibold mb-2">
              <i class="fas fa-key mr-2"></i>Admin Code
            </label>
            <input type="password" name="admin_code" 
                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 transition duration-200">
          </div>
          
          <button type="submit" 
                  class="w-full py-4 rounded-lg bg-gray-500 hover:bg-gray-600 text-white text-lg font-semibold transition duration-200 transform hover:scale-[1.02]">
            Join Now
          </button>
        </form>
        <p class="mt-8 text-center text-gray-600">
          Already have an account? 
          <a href="login.php" class="text-gray-800 hover:text-gray-600 font-medium">Sign in</a>
        </p>
        <p class="text-center text-gray-600 mt-4">or Sign up with</p>
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

  <footer class="bg-gray-800 bg-opacity-90 text-white text-center py-4 w-full">
    <p>© 2025 Sports League. All rights reserved.</p>
  </footer>

</body>
</html>
