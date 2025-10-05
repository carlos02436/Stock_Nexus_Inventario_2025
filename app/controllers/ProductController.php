<?php
namespace App\Controllers;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;

class ProductController {
    protected $pdo;

    public function __construct($pdo) { 
        $this->pdo = $pdo; 
    }

    // 📦 Mostrar lista de productos
    public function index() {
        $model = new Product($this->pdo);
        $items = $model->all();
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/products/index.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    // 🆕 Mostrar formulario de creación
    public function create() {
        $catModel = new Category($this->pdo);
        $supModel = new Supplier($this->pdo);
        $categories = $catModel->all();
        $suppliers = $supModel->all();
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/products/create.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    // 💾 Guardar nuevo producto
    public function store() {
        $data = [
            ':sku' => $_POST['sku'] ?? '',
            ':nombre' => $_POST['nombre'] ?? '',
            ':descripcion' => $_POST['descripcion'] ?? '',
            ':category_id' => $_POST['category_id'] ?? null,
            ':supplier_id' => $_POST['supplier_id'] ?? null,
            ':costo_unitario' => $_POST['costo_unitario'] ?? 0,
            ':precio_venta' => $_POST['precio_venta'] ?? 0,
            ':stock_minimo' => $_POST['stock_minimo'] ?? 0,
            ':stock_actual' => $_POST['stock_actual'] ?? 0,
            ':unidad_medida' => $_POST['unidad_medida'] ?? 'unidad'
        ];
        $model = new Product($this->pdo);
        $model->create($data);
        header('Location: /public/index.php?module=products&action=index');
        exit;
    }

    // ✏️ Mostrar formulario de edición
    public function edit($id = null) {
        if (!$id) { 
            header('Location: /public/index.php?module=products&action=index');
            exit;
        }

        $model = new Product($this->pdo);
        $product = $model->find($id);

        if (!$product) {
            header('Location: /public/index.php?module=products&action=index');
            exit;
        }

        $catModel = new Category($this->pdo);
        $supModel = new Supplier($this->pdo);
        $categories = $catModel->all();
        $suppliers = $supModel->all();

        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/products/edit.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    // 🔁 Actualizar producto existente
    public function update($id = null) {
        if (!$id) { 
            header('Location: /public/index.php?module=products&action=index');
            exit;
        }

        $data = [
            ':sku' => $_POST['sku'] ?? '',
            ':nombre' => $_POST['nombre'] ?? '',
            ':descripcion' => $_POST['descripcion'] ?? '',
            ':category_id' => $_POST['category_id'] ?? null,
            ':supplier_id' => $_POST['supplier_id'] ?? null,
            ':costo_unitario' => $_POST['costo_unitario'] ?? 0,
            ':precio_venta' => $_POST['precio_venta'] ?? 0,
            ':stock_minimo' => $_POST['stock_minimo'] ?? 0,
            ':stock_actual' => $_POST['stock_actual'] ?? 0,
            ':unidad_medida' => $_POST['unidad_medida'] ?? 'unidad'
        ];

        $model = new Product($this->pdo);
        $model->update($id, $data);

        header('Location: /public/index.php?module=products&action=index');
        exit;
    }

    // 🚫 Desactivar producto (eliminación lógica)
    public function deactivate($id = null) {
        if ($id) {
            $model = new Product($this->pdo);
            $model->deactivate($id);
        }
        header('Location: /public/index.php?module=products&action=index');
        exit;
    }
}