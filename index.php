<?php
// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'core/dbconfig.php'; // include database connection
require 'core/model.php'; // include the CRUD model

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$crud = new CRUD($pdo);

$success = '';
$error = '';
$search = $_GET['search'] ?? ''; // Define $search variable
$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;

// Handle edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_application'])) {
    $data = [
        'id' => $_POST['id'],
        'name' => $_POST['name'],
        'birthday' => $_POST['birthday'],
        'location_of_birth' => $_POST['location_of_birth'],
        'gender' => $_POST['gender'],
        'marital_status' => $_POST['marital_status'],
        'education' => $_POST['education'],
        'description' => $_POST['description'],
    ];
    $crud->updateApplication($data);
    header("Location: index.php?success=Application updated successfully");
    exit;
}

// Handle new application submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_application'])) {
    $data = [
        'name' => $_POST['name'],
        'birthday' => $_POST['birthday'],
        'location_of_birth' => $_POST['location_of_birth'],
        'gender' => $_POST['gender'],
        'marital_status' => $_POST['marital_status'],
        'education' => $_POST['education'],
        'description' => $_POST['description']
    ];

    // Insert the new application and redirect
    if ($crud->insertApplication($data)) {
        header("Location: index.php?success=Application submitted successfully");
        exit;
    } else {
        $error = "Failed to submit application.";
    }
}

// Handle delete request
if ($action === 'delete' && $id) {
    $crud->deleteApplication($id);
    header("Location: index.php?success=Application deleted successfully");
    exit;
}

// Fetch all applications
$applications = $crud->getAllApplications($search);

// Fetch application for editing
$applicationToEdit = null;
if ($action === 'edit' && $id) {
    $applicationToEdit = $crud->getApplicationById($id);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Medical Job Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: lightblue;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #0066cc;
            color: white;
            text-align: center;
            padding: 15px;
        }

        main {
            padding: 20px;
        }

        h2 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        input, select, textarea {
            padding: 8px;
            margin: 10px 0;
            width: 100%;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .success, .error {
            font-weight: bold;
            margin-top: 20px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        a {
            color: #0066cc;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>Medical Job Application System</h1>
    </header>

    <main>
        <p>Logged in as: <strong><?php echo $_SESSION['username']; ?></strong> (<a href="logout.php">Logout</a>)</p>

        <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

        <?php if ($applicationToEdit) : ?>
            <h2>Edit Application</h2>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $applicationToEdit['id']; ?>">
                <input type="text" name="name" value="<?php echo htmlspecialchars($applicationToEdit['name']); ?>" required>
                <input type="date" name="birthday" value="<?php echo htmlspecialchars($applicationToEdit['birthday']); ?>" required>
                <input type="text" name="location_of_birth" value="<?php echo htmlspecialchars($applicationToEdit['location_of_birth']); ?>" required>
                <select name="gender" required>
                    <option value="Male" <?php echo $applicationToEdit['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo $applicationToEdit['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo $applicationToEdit['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
                <select name="marital_status" required>
                    <option value="Single" <?php echo $applicationToEdit['marital_status'] === 'Single' ? 'selected' : ''; ?>>Single</option>
                    <option value="Married" <?php echo $applicationToEdit['marital_status'] === 'Married' ? 'selected' : ''; ?>>Married</option>
                    <option value="Widowed" <?php echo $applicationToEdit['marital_status'] === 'Widowed' ? 'selected' : ''; ?>>Widowed</option>
                    <option value="Divorced" <?php echo $applicationToEdit['marital_status'] === 'Divorced' ? 'selected' : ''; ?>>Divorced</option>
                </select>
                <input type="text" name="education" value="<?php echo htmlspecialchars($applicationToEdit['education']); ?>" required>
                <textarea name="description" required><?php echo htmlspecialchars($applicationToEdit['description']); ?></textarea>
                <button type="submit" name="update_application">Update Application</button>
            </form>
        <?php else : ?>
            <h2>Submit a New Application</h2>
            <form method="POST">
                <input type="text" name="name" placeholder="Name" required>
                <input type="date" name="birthday" required>
                <input type="text" name="location_of_birth" placeholder="Location of Birth" required>
                <select name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
                <select name="marital_status" required>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Widowed">Widowed</option>
                    <option value="Divorced">Divorced</option>
                </select>
                <input type="text" name="education" placeholder="Education" required>
                <textarea name="description" placeholder="Describe yourself" required></textarea>
                <button type="submit" name="submit_application">Submit Application</button>
            </form>
        <?php endif; ?>

        <h2>Search Applications</h2>
        <form method="GET">
            <input type="text" name="search" placeholder="Search by Name or Education" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <h2>Registered Applications</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Birthday</th>
                    <th>Location of Birth</th>
                    <th>Gender</th>
                    <th>Marital Status</th>
                    <th>Education</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($applications)) : ?>
                    <?php foreach ($applications as $app) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($app['name']); ?></td>
                            <td><?php echo htmlspecialchars($app['birthday']); ?></td>
                            <td><?php echo htmlspecialchars($app['location_of_birth']); ?></td>
                            <td><?php echo htmlspecialchars($app['gender']); ?></td>
                            <td><?php echo htmlspecialchars($app['marital_status']); ?></td>
                            <td><?php echo htmlspecialchars($app['education']); ?></td>
                            <td><?php echo htmlspecialchars($app['description']); ?></td>
                            <td>
                                <a href="?action=edit&id=<?php echo $app['id']; ?>">Edit</a> | 
                                <a href="?action=delete&id=<?php echo $app['id']; ?>" onclick="return confirm('Are you sure you want to delete this application?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="8">No applications found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
