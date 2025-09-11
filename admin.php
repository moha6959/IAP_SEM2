<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users - DEKS HOSTELS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #2c3e50;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        
        header {
            background: linear-gradient(90deg, #3498db, #2980b9);
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .content {
            padding: 30px;
        }
        
        .user-list {
            margin-top: 20px;
        }
        
        .user-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eaeaea;
            transition: background 0.3s;
        }
        
        .user-item:hover {
            background: #f8f9fa;
        }
        
        .user-number {
            background: #3498db;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .user-info {
            flex-grow: 1;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .user-email {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .user-date {
            color: #95a5a6;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .search-box {
            position: relative;
            margin-bottom: 20px;
        }
        
        .search-input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #eaeaea;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .search-input:focus {
            border-color: #3498db;
            outline: none;
        }
        
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
        }
        
        .no-users {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
        }
        
        .admin-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .action-btn {
            background: #ecf0f1;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background 0.3s;
        }
        
        .action-btn:hover {
            background: #dfe6e9;
        }
        
        .action-btn.primary {
            background: #3498db;
            color: white;
        }
        
        .action-btn.primary:hover {
            background: #2980b9;
        }
        
        @media (max-width: 768px) {
            .user-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .user-number {
                margin-bottom: 10px;
            }
            
            .admin-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>DEKS HOSTELS - Registered Users</h1>
            <p>All users in alphabetical order by name</p>
        </header>
        
        <div class="content">
            <div class="admin-actions">
                <button class="action-btn primary" onclick="window.location.href='forms.php'">
                    <i class="fas fa-arrow-left"></i> Back to Main Site
                </button>
                <button class="action-btn" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt"></i> Refresh List
                </button>
            </div>
            
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search users by name or email..." id="searchInput" onkeyup="filterUsers()">
            </div>
            
            <div class="user-list" id="userList">
                <?php
                // Include database connection
                require_once 'db_connect.php';

                // Query to get users from database in alphabetical order by name
                $sql = "SELECT id, name, email, created_at FROM users ORDER BY name ASC";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($users) > 0) {
                    $count = 1;
                    foreach ($users as $user) {
                        $signupDate = date('F j, Y', strtotime($user['created_at']));
                        
                        echo '
                        <div class="user-item">
                            <div class="user-number">' . $count . '</div>
                            <div class="user-info">
                                <div class="user-name">' . htmlspecialchars($user['name']) . '</div>
                                <div class="user-email">' . htmlspecialchars($user['email']) . '</div>
                                <div class="user-date">Joined: ' . $signupDate . '</div>
                            </div>
                        </div>';
                        
                        $count++;
                    }
                } else {
                    echo '
                    <div class="no-users">
                        <i class="fas fa-user-slash fa-3x" style="margin-bottom: 15px;"></i>
                        <h3>No users found</h3>
                        <p>No users have signed up yet</p>
                    </div>';
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        // Filter users based on search input
        function filterUsers() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const userItems = document.querySelectorAll('.user-item');
            
            userItems.forEach(item => {
                const userName = item.querySelector('.user-name').textContent.toLowerCase();
                const userEmail = item.querySelector('.user-email').textContent.toLowerCase();
                
                if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>