<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'db_config.php';

// Get total teams count
$teams_query = "SELECT COUNT(*) as total_teams FROM teams";
$teams_result = $conn->query($teams_query);
$teams_data = $teams_result->fetch_assoc();
$total_teams = $teams_data['total_teams'];

// Get upcoming matches count
$current_time = date('Y-m-d H:i:s');
$matches_query = "SELECT COUNT(*) as upcoming_matches, MIN(match_date) as next_match FROM matches WHERE match_date > '$current_time'";
$matches_result = $conn->query($matches_query);
$matches_data = $matches_result->fetch_assoc();
$upcoming_matches = $matches_data['upcoming_matches'];
$next_match_date = $matches_data['next_match'];

// Calculate days until next match
$days_until_next = "No upcoming matches";
if ($next_match_date) {
    $next_match_timestamp = strtotime($next_match_date);
    $current_timestamp = time();
    $days_diff = ceil(($next_match_timestamp - $current_timestamp) / (60 * 60 * 24));
    $days_until_next = "Next match in $days_diff days";
}

// Get active players count
$players_query = "SELECT COUNT(*) as active_players FROM players WHERE join_status = 'approved'";
$players_result = $conn->query($players_query);
$players_data = $players_result->fetch_assoc();
$active_players = $players_data['active_players'];

// Get recent activity
$activity_query = "SELECT t.team_name, 'team_registration' as activity_type, t.created_at as activity_time 
                  FROM teams t 
                  UNION 
                  SELECT CONCAT(t1.team_name, ' vs. ', t2.team_name) as team_name, 'match_scheduled' as activity_type, m.created_at as activity_time 
                  FROM matches m 
                  JOIN teams t1 ON m.team1_id = t1.id 
                  JOIN teams t2 ON m.team2_id = t2.id 
                  ORDER BY activity_time DESC LIMIT 5";
$activity_result = $conn->query($activity_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
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
      padding-top: 4rem;
    }
    .glass-effect {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .admin-btn {
      transition: all 0.3s ease;
    }
    .admin-btn:hover {
      transform: translateY(-3px);
      opacity: 0.9;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    .stat-card {
      border-left: 4px solid;
      transition: all 0.3s ease;
    }
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }
    .activity-item {
      border-bottom: 1px solid #e5e7eb;
      padding: 12px 0;
    }
    .activity-item:last-child {
      border-bottom: none;
    }
  </style>
</head>
<body class="bg-gray-100 p-6">
  <nav class="bg-gray-900 bg-opacity-80 text-white py-4 shadow-lg fixed top-0 left-0 right-0 z-10">
    <div class="container mx-auto px-6 flex justify-between items-center">
      <a href="index.php" class="text-xl font-bold flex items-center">
        <i class="fas fa-trophy mr-2 text-yellow-400"></i>
        <span class="text-blue-400">Sports</span> &nbsp;&nbsp; <span class="text-white">League</span>
      </a>
      <a href="logout.php" class="hover:text-blue-400 transition-colors">
        <i class="fas fa-sign-out-alt mr-1"></i> Logout
      </a>
    </div>
  </nav>

  <div class="max-w-5xl mx-auto glass-effect p-8 rounded-lg shadow-lg mt-16 mb-8">
    <h1 class="text-4xl font-bold mb-8 text-center text-blue-600">Admin Dashboard</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="stat-card bg-white p-4 rounded-lg shadow border-blue-500">
        <div class="flex justify-between items-center">
          <div>
            <p class="text-gray-500 text-sm">Total Teams</p>
            <h3 class="text-3xl font-bold text-gray-800"><?php echo $total_teams; ?></h3>
          </div>
          <div class="bg-blue-100 p-3 rounded-full">
            <i class="fas fa-users text-blue-500 text-xl"></i>
          </div>
        </div>
        <p class="text-green-500 text-sm mt-2"><i class="fas fa-arrow-up"></i> 12% increase</p>
      </div>
      
      <div class="stat-card bg-white p-4 rounded-lg shadow border-green-500">
        <div class="flex justify-between items-center">
          <div>
            <p class="text-gray-500 text-sm">Upcoming Matches</p>
            <h3 class="text-3xl font-bold text-gray-800"><?php echo $upcoming_matches; ?></h3>
          </div>
          <div class="bg-green-100 p-3 rounded-full">
            <i class="fas fa-calendar-alt text-green-500 text-xl"></i>
          </div>
        </div>
        <p class="text-gray-500 text-sm mt-2"><?php echo $days_until_next; ?></p>
      </div>
      
      <div class="stat-card bg-white p-4 rounded-lg shadow border-purple-500">
        <div class="flex justify-between items-center">
          <div>
            <p class="text-gray-500 text-sm">Active Players</p>
            <h3 class="text-3xl font-bold text-gray-800"><?php echo $active_players; ?></h3>
          </div>
          <div class="bg-purple-100 p-3 rounded-full">
            <i class="fas fa-running text-purple-500 text-xl"></i>
          </div>
        </div>
        <p class="text-green-500 text-sm mt-2"><i class="fas fa-arrow-up"></i> 8% increase</p>
      </div>
    </div>
    
    <div class="bg-white p-5 rounded-lg shadow-md mb-8">
      <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">Recent Activity</h2>
      <?php if ($activity_result && $activity_result->num_rows > 0): ?>
        <?php while ($activity = $activity_result->fetch_assoc()): ?>
          <div class="activity-item flex items-center">
            <div class="<?php echo $activity['activity_type'] == 'team_registration' ? 'bg-blue-100' : 'bg-green-100'; ?> p-2 rounded-full mr-3">
              <i class="<?php echo $activity['activity_type'] == 'team_registration' ? 'fas fa-user-plus text-blue-500' : 'fas fa-check-circle text-green-500'; ?>"></i>
            </div>
            <div>
              <p class="font-medium">
                <?php if ($activity['activity_type'] == 'team_registration'): ?>
                  New team registration: <span class="text-blue-600"><?php echo htmlspecialchars($activity['team_name']); ?></span>
                <?php else: ?>
                  Match scheduled: <span class="text-blue-600"><?php echo htmlspecialchars($activity['team_name']); ?></span>
                <?php endif; ?>
              </p>
              <p class="text-sm text-gray-500">
                <?php 
                  $activity_time = strtotime($activity['activity_time']);
                  $current_time = time();
                  $diff = $current_time - $activity_time;
                  
                  if ($diff < 3600) {
                    echo round($diff / 60) . " minutes ago";
                  } elseif ($diff < 86400) {
                    echo round($diff / 3600) . " hours ago";
                  } elseif ($diff < 172800) {
                    echo "Yesterday at " . date('g:i A', $activity_time);
                  } else {
                    echo date('M j, Y', $activity_time);
                  }
                ?>
              </p>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="activity-item flex items-center">
          <div class="bg-blue-100 p-2 rounded-full mr-3">
            <i class="fas fa-user-plus text-blue-500"></i>
          </div>
          <div>
            <p class="font-medium">New team registration: <span class="text-blue-600">Supa Strikers</span></p>
            <p class="text-sm text-gray-500">2 hours ago</p>
          </div>
        </div>
        <div class="activity-item flex items-center">
          <div class="bg-green-100 p-2 rounded-full mr-3">
            <i class="fas fa-check-circle text-green-500"></i>
          </div>
          <div>
            <p class="font-medium">Match scheduled: <span class="text-blue-600">Supa Strikers vs. Blue Lock</span></p>
            <p class="text-sm text-gray-500">Yesterday at 3:45 PM</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
      <div>
        <a href="performance.php" class="admin-btn bg-gradient-to-r from-purple-600 to-purple-400 text-white p-5 rounded-lg flex items-center justify-center">
          <i class="fas fa-chart-line mr-3 text-2xl text-pink-200"></i> 
          <span class="text-lg font-medium">Performance Reports</span>
        </a>
      </div>
      
      <div>
        <a href="messaging.php" class="admin-btn bg-gradient-to-r from-green-600 to-green-400 text-white p-5 rounded-lg flex items-center justify-center">
          <i class="fas fa-comments mr-3 text-2xl text-yellow-200"></i> 
          <span class="text-lg font-medium">Messaging</span>
        </a>
      </div>
      
      <div>
        <a href="admin_matches.php" class="admin-btn bg-gradient-to-r from-blue-600 to-blue-400 text-white p-5 rounded-lg flex items-center justify-center">
          <i class="fas fa-futbol mr-3 text-2xl text-yellow-300"></i> 
          <span class="text-lg font-medium">Manage Matches</span>
        </a>
      </div>
    </div>
  </div>
</body>
</html>
