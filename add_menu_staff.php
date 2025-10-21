<?php
session_start();
include 'db.php';
include 'navbar.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Handle file upload
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $target = "uploads/" . $imageName;

        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }

        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    $sql = "INSERT INTO menu_items (name, price, description, image) 
            VALUES ('$name', '$price', '$description', " . 
            ($imageName ? "'$imageName'" : "NULL") . ")";

    if (mysqli_query($conn, $sql)) {
        header("Location: menu_list_staff.php?msg=added");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Menu | Arjuna n Co-ffee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Elegant Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=Cormorant+Garamond:wght@300;400;700&family=Parisienne&family=Lora&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #1a1a1a;
      --accent-color: #c19a6b;
      --light-color: #f8f5f2;
      --transition: all 0.3s ease;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Lora', serif;
      color: var(--primary-color);
      background-color: var(--light-color);
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Background with subtle pattern */
    .bg-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -2;
      background-color: var(--light-color);
      background-image: url('images/coffee1.jpg');
      background-size: cover;
      background-position: center;
    }
    
    .bg-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(248, 245, 242, 0.85);
      z-index: -1;
    }

    /* Header styling */
    .page-header {
      padding: 2rem 0;
      text-align: center;
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      margin-bottom: 2rem;
    }
    
    .page-title {
      font-family: 'Playfair Display', serif;
      font-size: 2.5rem;
      font-weight: 500;
      color: var(--primary-color);
      letter-spacing: 1px;
      margin-bottom: 0.5rem;
    }
    
    .page-subtitle {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.2rem;
      color: #666;
      font-weight: 300;
    }

    /* Form container */
    .form-container {
      max-width: 600px;
      margin: 0 auto 3rem;
      padding: 0 1.5rem;
      flex-grow: 1;
    }

    .form-box {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      padding: 2.5rem;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .form-box h2 {
      font-family: 'Playfair Display', serif;
      text-align: center;
      margin-bottom: 2rem;
      font-weight: 500;
      font-size: 1.8rem;
      color: var(--primary-color);
      position: relative;
      padding-bottom: 1rem;
    }
    
    .form-box h2:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 2px;
      background: var(--accent-color);
    }

    /* Form elements */
    .form-label {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 0.5rem;
      color: var(--primary-color);
    }
    
    .form-control {
      border: 1px solid rgba(0, 0, 0, 0.1);
      border-radius: 6px;
      padding: 0.75rem 1rem;
      font-family: 'Lora', serif;
      font-size: 1rem;
      transition: var(--transition);
      background: rgba(255, 255, 255, 0.8);
    }
    
    .form-control:focus {
      border-color: var(--accent-color);
      box-shadow: 0 0 0 0.2rem rgba(193, 154, 107, 0.2);
    }
    
    textarea.form-control {
      min-height: 120px;
      resize: vertical;
    }

    /* Buttons */
    .btn-container {
      display: flex;
      gap: 1rem;
      justify-content: center;
      margin-top: 2rem;
    }
    
    .elegant-btn {
      background-color: var(--primary-color);
      color: white;
      border: none;
      border-radius: 6px;
      padding: 0.75rem 2rem;
      font-family: 'Cormorant Garamond', serif;
      font-weight: 600;
      font-size: 1.1rem;
      letter-spacing: 0.5px;
      transition: var(--transition);
      text-decoration: none;
      display: inline-block;
      text-align: center;
      cursor: pointer;
    }
    
    .elegant-btn:hover {
      background-color: #333;
      color: white;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .elegant-btn.accent {
      background-color: var(--accent-color);
    }
    
    .elegant-btn.accent:hover {
      background-color: #b08a5d;
    }

    /* Footer */
    .page-footer {
      text-align: center;
      padding: 1.5rem;
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(10px);
      border-top: 1px solid rgba(0, 0, 0, 0.05);
      font-family: 'Cormorant Garamond', serif;
      color: #666;
      font-size: 0.9rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .form-box {
        padding: 2rem 1.5rem;
      }
      
      .page-title {
        font-size: 2rem;
      }
      
      .btn-container {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
 
  
  <div class="bg-container"></div>
  <div class="bg-overlay"></div>

 

 
    <div class="form-box">
      <h2>Add Menu Item</h2>
      <form method="POST" enctype="multipart/form-data">
        <div class="mb-4">
          <label class="form-label">Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-4">
          <label class="form-label">Price (RM)</label>
          <input type="number" step="0.01" name="price" class="form-control" required>
        </div>
        <div class="mb-4">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="mb-4">
          <label class="form-label">Upload Image</label>
          <input type="file" name="image" class="form-control">
        </div>
        <div class="btn-container">
          <button type="submit" class="elegant-btn accent">Add Item</button>
          <a href="menu_list_staff.php" class="elegant-btn">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  <footer class="page-footer">
    <p>&copy; <?php echo date('Y'); ?> Arjuna n Co-ffee. All rights reserved.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>