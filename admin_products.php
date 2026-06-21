<?php

require_once 'src/config/connection.php';
require_once 'src/helpers/helpers.php'; 


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}


$conn = getConnection();

$message = '';
$error = '';



$current_category = isset($_GET['cat']) ? trim($_GET['cat']) : 'chair';


function uploadProductImage($file, $category) {

    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {

        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
           
            $uploadFolder = "src/assets/products/" . $category . "/";
            
            if ($category === 'chair') $uploadFolder .= "chair_home/";
            elseif ($category === 'desk') $uploadFolder .= "desk_home/";
            elseif ($category === 'controller') $uploadFolder .= "controllers_home/";
            elseif ($category === 'playstation') $uploadFolder .= "playStation_home/";
            elseif ($category === 'mouse') $uploadFolder .= "mous_home/";
            elseif ($category === 'ecran') $uploadFolder .= "ecran_home/";
            
            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder, 0755, true);
            }
            
           
            $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $dest_path = $uploadFolder . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                return $newFileName; 
            }
        }
    }
    return null;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {

    $name = trim($_POST['name']);
    $brand = trim($_POST['brand'] ?? 'Generic');
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    
    
    $main_image = uploadProductImage($_FILES['main_image'], $category) ?? '';
    $hover_image = uploadProductImage($_FILES['hover_image'], $category) ?? '';
    
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))) . '-' . time();

    if (!empty($name) && !empty($price) && !empty($main_image)) {
        $stmt = $conn->prepare("INSERT INTO products (category, brand, name, slug, price, main_image, hover_image, description, is_active) VALUES (:category, :brand, :name, :slug, :price, :main_image, :hover_image, :description, 1)");
        
        $success = $stmt->execute([
            ':category' => $category,
            ':brand' => $brand,
            ':name' => $name,
            ':slug' => $slug,
            ':price' => $price,
            ':main_image' => $main_image,
            ':hover_image' => $hover_image,
            ':description' => $description
        ]);

        if ($success) {
            header("Location: admin_products.php?cat=$category&success=Product Added Successfully");
            exit;
        } else {
            $error = "Something went wrong while adding product.";
        }
    } else {
        $error = "Please fill all required fields and upload the Main Image.";
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {

    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $brand = trim($_POST['brand'] ?? 'Generic');
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
   

    $img_stmt = $conn->prepare("SELECT main_image, hover_image FROM products WHERE id = :id");
    $img_stmt->execute([':id' => $id]);
    $old_images = $img_stmt->fetch();
    
    $main_image = uploadProductImage($_FILES['main_image'], $category) ?: $old_images['main_image'];
    $hover_image = uploadProductImage($_FILES['hover_image'], $category) ?: $old_images['hover_image'];

    if ($id > 0 && !empty($name) && !empty($price)) {
        $stmt = $conn->prepare("UPDATE products SET name = :name, brand = :brand, description = :description, price = :price, category = :category, main_image = :main_image, hover_image = :hover_image WHERE id = :id");
        
        $success = $stmt->execute([
            ':name' => $name,
            ':brand' => $brand,
            ':description' => $description,
            ':price' => $price,
            ':category' => $category,
            ':main_image' => $main_image,
            ':hover_image' => $hover_image,
            ':id' => $id
        ]);

        if ($success) {
            header("Location: admin_products.php?cat=$category&success=Product Updated Successfully");
            exit;
        } else {
            $error = "Failed to update product.";
        }
    }
}



if (isset($_GET['delete'])) {

    $id_to_delete = intval($_GET['delete']);

    if ($id_to_delete > 0) {
        $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
        if ($stmt->execute([':id' => $id_to_delete])) {
            header("Location: admin_products.php?cat=$current_category&success=Deleted successfully");
            exit;
        }
    }
}

if (isset($_GET['success'])) {
    $message = htmlspecialchars($_GET['success']);
}


$products_stmt = $conn->prepare("
    SELECT p.*, COALESCE(p.hover_image, g.image_path) as calculated_hover 
    FROM products p 
    LEFT JOIN product_gallery g ON g.product_id = p.id AND g.image_type = 'home'
    WHERE p.category = :category 
    GROUP BY p.id
    ORDER BY p.id DESC
");
$products_stmt->execute([':category' => $current_category]);
$products = $products_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Tech Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <style>
        :root {
            --bg-color: #0d0e12;
            --sidebar-color: #13151b;
            --card-color: #1f222a;
            --neon-green: #8bfb02;
            --text-color: #ffffff;
            --text-muted: #aaa;
            --border-color: #2a2e3d;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-color); display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar { width: 260px; background-color: var(--sidebar-color); border-right: 1px solid var(--border-color); display: flex; flex-direction: column; padding: 20px; }
        .sidebar .logo { font-size: 20px; font-weight: 900; font-style: italic; text-transform: uppercase; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; }
        .sidebar .logo span { color: var(--neon-green); }
        .sidebar-menu { list-style: none; display: flex; flex-direction: column; gap: 10px; height: 100%; }
        .sidebar-menu a { display: flex; align-items: center; gap: 15px; color: var(--text-muted); text-decoration: none; padding: 12px 15px; border-radius: 8px; font-size: 15px; font-weight: 600; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(139, 251, 2, 0.08); color: var(--neon-green); }
        .sidebar-menu a.logout { margin-top: auto; color: #ff4d4d; }

        /* Main Content */
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .header-dash { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px; }

        /* Category Filter Tabs Navigation */
        .category-tabs { display: flex; gap: 10px; margin-bottom: 25px; background: var(--sidebar-color); padding: 8px; border-radius: 8px; border: 1px solid var(--border-color); overflow-x: auto; }
        .tab-btn { padding: 10px 20px; color: var(--text-muted); text-decoration: none; font-weight: bold; border-radius: 6px; font-size: 14px; transition: 0.2s; text-transform: uppercase; white-space: nowrap; }
        .tab-btn:hover { color: white; background: rgba(255,255,255,0.02); }
        .tab-btn.active { background: var(--neon-green); color: black; }

        /* Form Controls styling */
        .form-box { background: var(--card-color); border: 1px solid var(--border-color); padding: 25px; border-radius: 12px; margin-bottom: 40px; }
        .form-box h2 { font-size: 18px; margin-bottom: 20px; color: var(--neon-green); display: flex; align-items: center; gap: 10px; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-group.full-width { grid-column: span 2; }
        .form-group label { font-size: 13px; color: var(--text-muted); }
        .form-group input, .form-group textarea, .form-group select { background: #13151b; border: 1px solid var(--border-color); padding: 10px; border-radius: 6px; color: white; outline: none; }
        .form-group input[type="file"] { padding: 6px; }
        .btn-submit { background: var(--neon-green); color: black; border: none; padding: 12px; font-weight: bold; border-radius: 6px; cursor: pointer; transition: 0.2s; margin-top: 15px; }
        .btn-submit:hover { background: #76d402; }

        /* Interactive Table List */
        .table-box { background: var(--card-color); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 15px; border-bottom: 1px solid var(--border-color); font-size: 14px; }
        th { background: #13151b; color: var(--neon-green); font-weight: 600; }
        
        /* Containers to show image previews with a neat hover effect */
        .img-container { display: flex; gap: 8px; }
        .prod-img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; background: #13151b; border: 1px solid var(--border-color); transition: transform 0.2s ease; }
        .prod-img:hover { transform: scale(1.2); border-color: var(--neon-green); z-index: 10; }
        
        .actions-btn { display: flex; gap: 15px; align-items: center; }
        .btn-edit { color: #facc15; background: none; border: none; cursor: pointer; font-size: 16px; }
        .btn-delete { color: #ff4d4d; text-decoration: none; font-size: 16px; }
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; font-size: 14px; }
        .alert-success { background: rgba(139, 251, 2, 0.1); color: var(--neon-green); border: 1px solid var(--neon-green); }
        .alert-danger { background: rgba(255, 77, 77, 0.1); color: #ff4d4d; border: 1px solid #ff4d4d; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="logo"><i class="fas fa-gamepad"></i> Tech<span>Shop</span></div>
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php"><i class="fas fa-chart-pie"></i> Overview</a></li>
            <li><a href="admin_products.php" class="active"><i class="fas fa-box"></i> Products (CRUD)</a></li>
            <li><a href="index.php"><i class="fas fa-eye"></i> View Website</a></li>
            <li style="margin-top: auto;"><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="header-dash">
            <h1>Manage Products</h1>
        </div>

        <div class="category-tabs">
            <a href="admin_products.php?cat=chair" class="tab-btn <?= $current_category === 'chair' ? 'active' : '' ?>">Chairs</a>
            <a href="admin_products.php?cat=mouse" class="tab-btn <?= $current_category === 'mouse' ? 'active' : '' ?>">Mouse</a>
            <a href="admin_products.php?cat=keyboard" class="tab-btn <?= $current_category === 'keyboard' ? 'active' : '' ?>">Keyboard</a>
            <a href="admin_products.php?cat=headset" class="tab-btn <?= $current_category === 'headset' ? 'active' : '' ?>">Headset</a>
            <a href="admin_products.php?cat=desk" class="tab-btn <?= $current_category === 'desk' ? 'active' : '' ?>">Desks</a>
            <a href="admin_products.php?cat=controller" class="tab-btn <?= $current_category === 'controller' ? 'active' : '' ?>">Controllers</a>
            <a href="admin_products.php?cat=playstation" class="tab-btn <?= $current_category === 'playstation' ? 'active' : '' ?>">PlayStation</a>
            <a href="admin_products.php?cat=ecran" class="tab-btn <?= $current_category === 'ecran' ? 'active' : '' ?>">Écran</a>
        </div>

        <?php if(!empty($message)): ?> <div class="alert alert-success"><?= $message ?></div> <?php endif; ?>
        <?php if(!empty($error)): ?> <div class="alert alert-danger"><?= $error ?></div> <?php endif; ?>

        <div class="form-box">
            <h2 id="form-title"><i class="fas fa-plus-circle"></i> Add Product to <?= strtoupper($current_category) ?></h2>
            <form id="product-form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="prod-id">
                <input type="hidden" name="category" value="<?= htmlspecialchars($current_category) ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Product Name *</label>
                        <input type="text" name="name" id="prod-name" placeholder="e.g. Razer Iskur" required>
                    </div>
                    <div class="form-group">
                        <label>Brand Name</label>
                        <input type="text" name="brand" id="prod-brand" placeholder="e.g. Logitech, Razer">
                    </div>
                    <div class="form-group">
                        <label>Price (DH) *</label>
                        <input type="number" step="0.01" name="price" id="prod-price" placeholder="2999.00" required>
                    </div>
                    <div class="form-group">
                        <label>Main Image (Choose file from PC) *</label>
                        <input type="file" name="main_image" id="prod-main-img">
                        <small id="main-img-hint" style="color:var(--neon-green); font-size:11px;"></small>
                    </div>
                    <div class="form-group">
                        <label>Hover Image (Choose file from PC)</label>
                        <input type="file" name="hover_image" id="prod-hover-img">
                        <small id="hover-img-hint" style="color:var(--neon-green); font-size:11px;"></small>
                    </div>
                    <div class="form-group full-width">
                        <label>Description</label>
                        <textarea name="description" id="prod-description" rows="2" placeholder="Describe specifications..."></textarea>
                    </div>
                </div>
                <button type="submit" name="add_product" id="btn-submit-form" class="btn-submit">Add Product</button>
                <button type="button" id="btn-cancel" class="btn-submit" style="background:#444; color:white; display:none; margin-left:10px;">Cancel Edit</button>
            </form>
        </div>

        <div class="table-box">
            <h3 style="padding: 15px; background: #161920; font-size: 15px; border-bottom:1px solid var(--border-color);">
                Active <?= strtoupper($current_category) ?> List (<?= count($products) ?> items found)
            </h3>
            <table>
                <thead>
                    <tr>
                        <th>Images (Main / Hover)</th>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($products) > 0): ?>
                        <?php foreach($products as $p): 
                            // جلب الصور عن طريق دالة asset_url الأصلية المتواجدة بملف helpers.php
                            $main_src = asset_url($p['category'], 'main', $p['main_image']);
                            $hover_src = asset_url($p['category'], 'hover', $p['calculated_hover']);
                            
                            // fallback في حالة غياب مسارات الصورة
                            if(empty($main_src)) $main_src = 'src/assets/banners_hero_section/login_woman 1.jpg';
                            if(empty($hover_src)) $hover_src = 'src/assets/banners_hero_section/login_woman 1.jpg';
                        ?>
                            <tr>
                                <td>
                                    <div class="img-container">
                                        <img class="prod-img" src="<?= $main_src ?>" alt="main" title="Main Image View">
                                        <img class="prod-img" src="<?= $hover_src ?>" alt="hover" title="Hover Image View" style="border-color: #facc15;">
                                    </div>
                                </td>
                                <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                                <td><?= htmlspecialchars($p['brand']) ?></td>
                                <td style="color:var(--neon-green); font-weight:bold;"><?= number_format($p['price'], 2) ?> DH</td>
                                <td class="actions-btn">
                                    <button class="btn-edit" onclick='editProduct(<?= json_encode($p) ?>)'><i class="fas fa-edit"></i></button>
                                    <a class="btn-delete" href="admin_products.php?cat=<?= $current_category ?>&delete=<?= $p['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?')"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">Empty category. Click on choose file button above to upload new items!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function editProduct(product) {
            document.getElementById('form-title').innerHTML = '<i class="fas fa-edit"></i> Edit Product: ' + product.name;
            document.getElementById('btn-submit-form').name = 'update_product';
            document.getElementById('btn-submit-form').innerText = 'Save Changes';
            document.getElementById('btn-cancel').style.display = 'inline-block';

            document.getElementById('prod-id').value = product.id;
            document.getElementById('prod-name').value = product.name;
            document.getElementById('prod-brand').value = product.brand;
            document.getElementById('prod-price').value = product.price;
            document.getElementById('prod-description').value = product.description;

            document.getElementById('main-img-hint').innerText = "Current: " + (product.main_image ? product.main_image : 'None');
            document.getElementById('hover-img-hint').innerText = "Current: " + (product.hover_image ? product.hover_image : 'None');

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        document.getElementById('btn-cancel').addEventListener('click', function(){
            document.getElementById('form-title').innerHTML = '<i class="fas fa-plus-circle"></i> Add Product to <?= strtoupper($current_category) ?>';
            document.getElementById('btn-submit-form').name = 'add_product';
            document.getElementById('btn-submit-form').innerText = 'Add Product';
            document.getElementById('btn-cancel').style.display = 'none';
            document.getElementById('product-form').reset();
            document.getElementById('prod-id').value = '';
            document.getElementById('main-img-hint').innerText = "";
            document.getElementById('hover-img-hint').innerText = "";
        });
    </script>
</body>
</html>