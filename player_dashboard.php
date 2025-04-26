<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'player') {
    header("Location: login.php");
    exit();
}

include 'db_config.php';

$user_id = $_SESSION['user_id'];
$msg = isset($_GET['msg']) ? $_GET['msg'] : "";

$stmt = $conn->prepare("SELECT team_id, join_status FROM players WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

$hasRequest = false;
$approvedTeamId = null;
$joinStatus = "";
if ($stmt->num_rows > 0) {
    $stmt->bind_result($team_id, $join_status);
    $stmt->fetch();
    $hasRequest = true;
    $joinStatus = $join_status;
    if ($join_status == 'approved') {
        $approvedTeamId = $team_id;
    }
}
$stmt->close();

$team_details = null;
if ($approvedTeamId) {
    $stmt = $conn->prepare("SELECT team_name, sport, min_players, max_players FROM teams WHERE id = ?");
    $stmt->bind_param("i", $approvedTeamId);
    $stmt->execute();
    $result = $stmt->get_result();
    $team_details = $result->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Player Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-image: url('https://images.pexels.com/photos/46798/the-ball-stadion-football-the-pitch-46798.jpeg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      color: #333;
      padding-top: 4rem;
    }
    .glass-effect {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 16px;
    }
    .dashboard-btn {
      transition: all 0.3s ease;
    }
    .dashboard-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    .team-details {
      background: rgba(30, 58, 138, 0.1);
      border-left: 4px solid #3b82f6;
    }
  </style>
</head>
<body class="p-6">
  <nav class="bg-gray-800 text-white py-4 shadow-lg fixed top-0 left-0 right-0 z-50">
    <div class="container mx-auto px-6 flex justify-between items-center">
      <a href="index.php" class="text-xl font-bold flex items-center">
        <i class="fas fa-trophy mr-2 text-yellow-400"></i>
        <span class="text-blue-400">Sports</span>&nbsp;&nbsp;<span class="text-white">League</span>
      </a>
    </div>
  </nav>

  <div class="max-w-5xl mx-auto glass-effect p-8 rounded-lg shadow-lg mt-16 mb-8">
    <h1 class="text-4xl font-bold mb-6 text-center text-blue-600">Player Dashboard</h1>
    <?php if($msg) echo "<p class='mb-6 text-green-600 text-center font-medium'>" . htmlspecialchars($msg) . "</p>"; ?>
    
    <div class="mb-8">
      <?php if ($approvedTeamId && $team_details): ?>
        <div class="team-details p-4 rounded-lg mb-6">
          <h2 class="text-2xl font-semibold mb-3 text-blue-700">Your Team Details</h2>
          <p class="text-lg mb-2"><strong class="text-gray-700">Team Name:</strong> <span class="text-gray-900"><?php echo htmlspecialchars($team_details['team_name']); ?></span></p>
          <p class="text-lg"><strong class="text-gray-700">Sport:</strong> <span class="text-gray-900"><?php echo htmlspecialchars($team_details['sport']); ?></span></p>
        </div>
      <?php else: ?>
        <div class="text-center mb-6">
          <?php if ($hasRequest): ?>
            <p class="text-amber-600 mb-4 text-lg font-medium">Your join request is currently <strong><?php echo ucfirst(htmlspecialchars($joinStatus)); ?></strong>.</p>
          <?php else: ?>
            <a href="join_team.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg font-medium hover:bg-blue-700 transition-all inline-block">Request to Join a Team</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
      <div>
        <a href="performance.php" class="dashboard-btn bg-gradient-to-r from-blue-700 to-blue-500 text-white p-4 rounded-lg flex items-center justify-center transition-all duration-200 hover:from-blue-600 hover:to-blue-400 shadow-md">
          <i class="fas fa-chart-line mr-3 text-xl"></i> 
          <span class="text-lg font-medium">View Performance</span>
        </a>
      </div>
      <div>
        <a href="messaging.php" class="dashboard-btn bg-gradient-to-r from-green-700 to-green-500 text-white p-4 rounded-lg flex items-center justify-center transition-all duration-200 hover:from-green-600 hover:to-green-400 shadow-md">
          <i class="fas fa-comments mr-3 text-xl"></i> 
          <span class="text-lg font-medium">Messaging</span>
        </a>
      </div>
      <div>
        <a href="match_schedule.php" class="dashboard-btn bg-gradient-to-r from-purple-700 to-purple-500 text-white p-4 rounded-lg flex items-center justify-center transition-all duration-200 hover:from-purple-600 hover:to-purple-400 shadow-md">
          <i class="fas fa-calendar-alt mr-3 text-xl"></i> 
          <span class="text-lg font-medium">Scheduled Matches</span>
        </a>
      </div>
      <div>
        <a href="logout.php" class="dashboard-btn bg-gradient-to-r from-red-700 to-red-500 text-white p-4 rounded-lg flex items-center justify-center transition-all duration-200 hover:from-red-600 hover:to-red-400 shadow-md">
          <i class="fas fa-sign-out-alt mr-3 text-xl"></i> 
          <span class="text-lg font-medium">Logout</span>
        </a>
      </div>
    </div>
  </div>
</body>
</html>
