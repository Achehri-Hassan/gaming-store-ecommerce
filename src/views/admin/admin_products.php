<?php

require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../helpers/helpers.php';
require_once __DIR__ . '/../../models/ProductModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// require_admin() also exists in helpers.php; kept as an explicit check
// here so a missing session/role fails closed with the same behaviour.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = '';
$error = '';

$requested_category = isset($_GET['cat']) ? trim($_GET['cat']) : 'chair';
$current_category   = is_valid_category($requested_category) ? $requested_category : 'chair';

// ── Handling ADD PRODUCT ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    verify_csrf();

    $name        = trim($_POST['name'] ?? '');
    $brand       = trim($_POST['brand'] ?? 'Generic');
    $description = trim($_POST['description'] ?? '');
    $price       = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);
    $category    = trim($_POST['category'] ?? '');

    if (!is_valid_category($category)) {
        $error = "Invalid product category.";
    } elseif (empty($name) || $price === false || $price <= 0) {
        $error = "Please fill all required fields with valid values.";
    } else {
        $main_image  = secure_image_upload($_FILES['main_image']  ?? [], upload_folder($category))      ?? '';
        $hover_image = secure_image_upload($_FILES['hover_image'] ?? [], upload_folder($category))      ?? '';
        $shop_image  = secure_image_upload($_FILES['shop_image']  ?? [], shop_upload_folder($category));

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))) . '-' . time();

        if (empty($main_image)) {
            $error = "Please upload a valid Main Image (jpg, jpeg, png or webp, max 2MB).";
        } else {
            $new_id = createProduct([
                'category'    => $category,
                'brand'       => $brand,
                'name'        => $name,
                'slug'        => $slug,
                'price'       => $price,
                'main_image'  => $main_image,
                'hover_image' => $hover_image,
                'description' => $description
            ]);

            if ($new_id !== false && $new_id > 0) {
                if ($shop_image) {
                    addGalleryImage($new_id, $shop_image);
                }
                header("Location: admin_products.php?cat=" . urlencode($category));
                exit;
            } else {
                $error = "Something went wrong while adding product.";
            }
        }
    }
}

// ── Handling UPDATE PRODUCT ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    verify_csrf();

    $id          = (int) ($_POST['id'] ?? 0);
    $name        = trim($_POST['name'] ?? '');
    $brand       = trim($_POST['brand'] ?? 'Generic');
    $description = trim($_POST['description'] ?? '');
    $price       = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);
    $category    = trim($_POST['category'] ?? '');

    $old_product = $id > 0 ? selectById($id) : null;

    if (!$old_product) {
        $error = "Product not found.";
    } elseif (!is_valid_category($category)) {
        $error = "Invalid product category.";
    } elseif (empty($name) || $price === false || $price <= 0) {
        $error = "Please fill all required fields with valid values.";
    } else {
        $new_main  = secure_image_upload($_FILES['main_image']  ?? [], upload_folder($category));
        $new_hover = secure_image_upload($_FILES['hover_image'] ?? [], upload_folder($category));
        $new_shop  = secure_image_upload($_FILES['shop_image']  ?? [], shop_upload_folder($category));

        $main_image  = $new_main  ?: $old_product['main_image'];
        $hover_image = $new_hover ?: $old_product['hover_image'];

        $success = updateProduct([
            'name'        => $name,
            'brand'       => $brand,
            'description' => $description,
            'price'       => $price,
            'category'    => $category,
            'main_image'  => $main_image,
            'hover_image' => $hover_image,
            'id'          => $id
        ]);

        if ($success) {
            if ($new_shop) {
                addGalleryImage($id, $new_shop);
            }

            // Only remove the OLD file once the new one has been safely
            // written and the DB row updated, and only when the category
            // (and therefore folder) hasn't changed.
            $folder = upload_folder($old_product['category']);
            if ($folder && $old_product['category'] === $category) {
                if ($new_main && !empty($old_product['main_image'])) {
                    delete_image($folder, $old_product['main_image']);
                }
                if ($new_hover && !empty($old_product['hover_image'])) {
                    delete_image($folder, $old_product['hover_image']);
                }
            }

            header("Location: admin_products.php?cat=" . urlencode($category));
            exit;
        } else {
            $error = "Failed to update product.";
        }
    }
}

// ── Handling DELETE PRODUCT (POST-only, CSRF-protected) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    verify_csrf();

    $id_to_delete = (int) ($_POST['id'] ?? 0);

    if ($id_to_delete > 0) {
        $product_to_delete = selectById($id_to_delete);

        if ($product_to_delete) {
            $cat = $product_to_delete['category'];

            $shop_images = selectProductImages($id_to_delete);

            if (deleteProduct($id_to_delete)) {
                $folder = upload_folder($cat);
                if ($folder) {
                    if (!empty($product_to_delete['main_image'])) {
                        delete_image($folder, $product_to_delete['main_image']);
                    }
                    if (!empty($product_to_delete['hover_image'])) {
                        delete_image($folder, $product_to_delete['hover_image']);
                    }
                }

                $shopFolder = shop_upload_folder($cat);
                if ($shopFolder && !empty($shop_images)) {
                    foreach ($shop_images as $img_name) {
                        delete_image($shopFolder, $img_name);
                    }
                }
            } else {
                $error = "Failed to delete product.";
            }
        }
    }

    header("Location: admin_products.php?cat=" . urlencode($current_category));
    exit;
}

if (isset($_GET['success'])) {
    $message = h($_GET['success']);
}

$products = selectByCategoryForAdmin($current_category);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Tech Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <link rel="stylesheet" href="/../../css/admin_products.css">
</head>

<body>

    <aside class="sidebar">
        <div class="logo"><i class="fas fa-gamepad"></i> Tech<span>Shop</span></div>
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php"><i class="fas fa-chart-pie"></i> Overview</a></li>
            <li><a href="admin_products.php" class="active"><i class="fas fa-box"></i> Products (CRUD)</a></li>
            <li><a href="/index.php"><i class="fas fa-eye"></i> View Website</a></li>
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

        <?php if (!empty($message)): ?> <div class="alert alert-success"><?= $message ?></div> <?php endif; ?>
        <?php if (!empty($error)): ?> <div class="alert alert-danger"><?= $error ?></div> <?php endif; ?>

        <div class="form-box">
            <h2 id="form-title"><i class="fas fa-plus-circle"></i> Add Product to <?= strtoupper($current_category) ?></h2>
            <form id="product-form" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
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
                        <label>Main Image</label>
                        <input type="file" name="main_image" id="prod-main-img">
                        <small id="main-img-hint" style="color:var(--neon-green); font-size:11px;"></small>
                    </div>
                    <div class="form-group">
                        <label>Hover Image</label>
                        <input type="file" name="hover_image" id="prod-hover-img">
                        <small id="hover-img-hint" style="color:var(--neon-green); font-size:11px;"></small>
                    </div>
                    <div class="form-group">
                        <label>Shop Detail Image </label>
                        <input type="file" name="shop_image" id="prod-shop-img">
                        <small id="shop-img-hint" style="color:var(--neon-green); font-size:11px;"></small>
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
            <h3 class="item_pro">
                Active <?= strtoupper($current_category) ?> List (<?= count($products) ?> items found)
            </h3>
            <table>
                <thead>
                    <tr>
                        <th>Images</th>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $p):
                            $main_src = asset_url($p['category'], 'main', $p['main_image']);
                            $hover_src = asset_url($p['category'], 'hover', $p['calculated_hover']);

                            if (empty($main_src)) $main_src = 'src/assets/banners_hero_section/login_woman 1.jpg';
                            if (empty($hover_src)) $hover_src = 'src/assets/banners_hero_section/login_woman 1.jpg';
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
                                    <button
                                        class="btn-edit js-edit-product"
                                        type="button"
                                        data-id="<?= (int) $p['id'] ?>"
                                        data-name="<?= h($p['name']) ?>"
                                        data-brand="<?= h($p['brand']) ?>"
                                        data-price="<?= h((string) $p['price']) ?>"
                                        data-description="<?= h($p['description'] ?? '') ?>"
                                        data-main-image="<?= h($p['main_image'] ?? '') ?>"
                                        data-hover-image="<?= h($p['hover_image'] ?? '') ?>"
                                    ><i class="fas fa-edit"></i></button>

                                    <form method="POST" action="admin_products.php" class="delete-form"
                                          onsubmit="return confirm('Are you sure you want to delete this product?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                                        <input type="hidden" name="delete_product" value="1">
                                        <button type="submit" class="btn-delete"><i class="fas fa-trash-alt"></i></button>
                                    </form>
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


    <script src="../../../js/handelAdminProducts.js"></script>
    <script>
        // Wire up Edit buttons using their data-* attributes (safer than
        // embedding json_encode() output inside an HTML onclick attribute).
        document.querySelectorAll('.js-edit-product').forEach(function (btn) {
            btn.addEventListener('click', function () {
                editProduct({
                    id: btn.dataset.id,
                    name: btn.dataset.name,
                    brand: btn.dataset.brand,
                    price: btn.dataset.price,
                    description: btn.dataset.description,
                    main_image: btn.dataset.mainImage,
                    hover_image: btn.dataset.hoverImage
                });
            });
        });
    </script>
</body>

</html>