<?php

class Product {
    // Private properties
    protected int $id;
    protected string $name;
    protected array $photos;
    protected int $price;
    protected string $description;
    protected int $quantity;
    protected DateTime $created_at;
    protected DateTime $updated_at;
    protected int $category_id;

    // Constructor with optional parameters
    public function __construct(
        int $id = 0,
        string $name = '',
        array $photos = [],
        int $price = 0,
        string $description = '',
        int $quantity = 0,
        DateTime $created_at = null,
        DateTime $updated_at = null,
        int $category_id = 0
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->photos = $photos;
        $this->price = $price;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->created_at = $created_at ?? new DateTime();
        $this->updated_at = $updated_at ?? new DateTime();
        $this->category_id = $category_id;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getPhotos(): array {
        return $this->photos;
    }

    public function getPrice(): int {
        return $this->price;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function getCreatedAt(): DateTime {
        return $this->created_at;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updated_at;
    }

    public function getCategoryId(): int {
        return $this->category_id;
    }

    // Setters
    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setPhotos(array $photos): void {
        $this->photos = $photos;
    }

    public function setPrice(int $price): void {
        if ($price < 0) {
            throw new InvalidArgumentException("Price cannot be negative");
        }
        $this->price = $price;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setQuantity(int $quantity): void {
        if ($quantity < 0) {
            throw new InvalidArgumentException("Quantity cannot be negative");
        }
        $this->quantity = $quantity;
    }

    public function setCreatedAt(DateTime $created_at): void {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt(DateTime $updated_at): void {
        $this->updated_at = $updated_at;
    }

    public function setCategoryId(int $category_id): void {
        $this->category_id = $category_id;
    }

    // Method to get the associated category
    public function getCategory(PDO $pdo): ?Category {
        $query = $pdo->prepare("SELECT * FROM category WHERE id = :id");
        $query->execute(['id' => $this->category_id]);
        $categoryData = $query->fetch(PDO::FETCH_ASSOC);

        if ($categoryData) {
            return new Category(
                $categoryData['id'],
                $categoryData['name'],
                $categoryData['description'],
                new DateTime($categoryData['created_at']),
                new DateTime($categoryData['updated_at'])
            );
        }

        return null; // Return null if no category is found
    }

    // Method to find a product by ID and hydrate the current instance
    public function findOneById(PDO $pdo, int $id): bool {
        $query = $pdo->prepare("SELECT * FROM product WHERE id = :id");
        $query->execute(['id' => $id]);
        $productData = $query->fetch(PDO::FETCH_ASSOC);

        if ($productData) {
            $this->id = $productData['id'];
            $this->name = $productData['name'];
            $this->photos = json_decode($productData['photos'], true);
            $this->price = $productData['price'];
            $this->description = $productData['description'];
            $this->quantity = $productData['quantity'];
            $this->created_at = new DateTime($productData['created_at']);
            $this->updated_at = new DateTime($productData['updated_at']);
            $this->category_id = $productData['category_id'];
            return true;
        }

        return false;
    }

    // Method to find all products
    public static function findAll(PDO $pdo): array {
        $query = $pdo->query("SELECT * FROM product");
        $productsData = $query->fetchAll(PDO::FETCH_ASSOC);

        $products = [];
        foreach ($productsData as $productData) {
            $products[] = new Product(
                $productData['id'],
                $productData['name'],
                json_decode($productData['photos'], true),
                $productData['price'],
                $productData['description'],
                $productData['quantity'],
                new DateTime($productData['created_at']),
                new DateTime($productData['updated_at']),
                $productData['category_id']
            );
        }

        return $products;
    }

    // Method to create a new product in the database
    public function create(PDO $pdo): ?self {
        $query = $pdo->prepare("
            INSERT INTO product (name, photos, price, description, quantity, created_at, updated_at, category_id)
            VALUES (:name, :photos, :price, :description, :quantity, :created_at, :updated_at, :category_id)
        ");

        $success = $query->execute([
            'name' => $this->name,
            'photos' => json_encode($this->photos),
            'price' => $this->price,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'category_id' => $this->category_id
        ]);

        if ($success) {
            $this->id = (int)$pdo->lastInsertId();
            return $this;
        }

        return null;
    }

    // Method to update the current product in the database
    public function update(PDO $pdo): bool {
        if ($this->id === 0) {
            return false; // Cannot update a product without a valid ID
        }

        $query = $pdo->prepare("
            UPDATE product SET
                name = :name,
                photos = :photos,
                price = :price,
                description = :description,
                quantity = :quantity,
                created_at = :created_at,
                updated_at = :updated_at,
                category_id = :category_id
            WHERE id = :id
        ");

        $success = $query->execute([
            'id' => $this->id,
            'name' => $this->name,
            'photos' => json_encode($this->photos),
            'price' => $this->price,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'category_id' => $this->category_id
        ]);

        return $success;
    }
}

class Clothing extends Product {
    private string $size;
    private string $color;
    private string $type;
    private int $material_fee;

    public function __construct(
        int $id = 0,
        string $name = '',
        array $photos = [],
        int $price = 0,
        string $description = '',
        int $quantity = 0,
        DateTime $created_at = null,
        DateTime $updated_at = null,
        int $category_id = 0,
        string $size = '',
        string $color = '',
        string $type = '',
        int $material_fee = 0
    ) {
        parent::__construct($id, $name, $photos, $price, $description, $quantity, $created_at, $updated_at, $category_id);
        $this->size = $size;
        $this->color = $color;
        $this->type = $type;
        $this->material_fee = $material_fee;
    }

    public function findOneById(PDO $pdo, int $id): bool {
        $query = $pdo->prepare("SELECT p.*, c.size, c.color, c.type, c.material_fee FROM product p JOIN clothing c ON p.id = c.product_id WHERE p.id = :id");
        $query->execute(['id' => $id]);
        $productData = $query->fetch(PDO::FETCH_ASSOC);

        if ($productData) {
            $this->id = $productData['id'];
            $this->name = $productData['name'];
            $this->photos = json_decode($productData['photos'], true);
            $this->price = $productData['price'];
            $this->description = $productData['description'];
            $this->quantity = $productData['quantity'];
            $this->created_at = new DateTime($productData['created_at']);
            $this->updated_at = new DateTime($productData['updated_at']);
            $this->category_id = $productData['category_id'];
            $this->size = $productData['size'];
            $this->color = $productData['color'];
            $this->type = $productData['type'];
            $this->material_fee = $productData['material_fee'];
            return true;
        }

        return false;
    }

    public static function findAll(PDO $pdo): array {
        $query = $pdo->query("SELECT p.*, c.size, c.color, c.type, c.material_fee FROM product p JOIN clothing c ON p.id = c.product_id");
        $productsData = $query->fetchAll(PDO::FETCH_ASSOC);

        $products = [];
        foreach ($productsData as $productData) {
            $products[] = new self(
                $productData['id'],
                $productData['name'],
                json_decode($productData['photos'], true),
                $productData['price'],
                $productData['description'],
                $productData['quantity'],
                new DateTime($productData['created_at']),
                new DateTime($productData['updated_at']),
                $productData['category_id'],
                $productData['size'],
                $productData['color'],
                $productData['type'],
                $productData['material_fee']
            );
        }

        return $products;
    }

    public function create(PDO $pdo): ?self {
        $pdo->beginTransaction();
        try {
            $productCreated = parent::create($pdo);
            if (!$productCreated) {
                $pdo->rollBack();
                return null;
            }

            $query = $pdo->prepare("
                INSERT INTO clothing (product_id, size, color, type, material_fee)
                VALUES (:product_id, :size, :color, :type, :material_fee)
            ");

            $success = $query->execute([
                'product_id' => $this->id,
                'size' => $this->size,
                'color' => $this->color,
                'type' => $this->type,
                'material_fee' => $this->material_fee
            ]);

            if ($success) {
                $pdo->commit();
                return $this;
            } else {
                $pdo->rollBack();
                return null;
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            return null;
        }
    }

    public function update(PDO $pdo): bool {
        $pdo->beginTransaction();
        try {
            $productUpdated = parent::update($pdo);
            if (!$productUpdated) {
                $pdo->rollBack();
                return false;
            }

            $query = $pdo->prepare("
                UPDATE clothing SET
                    size = :size,
                    color = :color,
                    type = :type,
                    material_fee = :material_fee
                WHERE product_id = :product_id
            ");

            $success = $query->execute([
                'product_id' => $this->id,
                'size' => $this->size,
                'color' => $this->color,
                'type' => $this->type,
                'material_fee' => $this->material_fee
            ]);

            if ($success) {
                $pdo->commit();
                return true;
            } else {
                $pdo->rollBack();
                return false;
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }
}

class Electronic extends Product {
    private string $brand;
    private int $waranty_fee;

    public function __construct(
        int $id = 0,
        string $name = '',
        array $photos = [],
        int $price = 0,
        string $description = '',
        int $quantity = 0,
        DateTime $created_at = null,
        DateTime $updated_at = null,
        int $category_id = 0,
        string $brand = '',
        int $waranty_fee = 0
    ) {
        parent::__construct($id, $name, $photos, $price, $description, $quantity, $created_at, $updated_at, $category_id);
        $this->brand = $brand;
        $this->waranty_fee = $waranty_fee;
    }

    public function findOneById(PDO $pdo, int $id): bool {
        $query = $pdo->prepare("SELECT p.*, e.brand, e.waranty_fee FROM product p JOIN electronic e ON p.id = e.product_id WHERE p.id = :id");
        $query->execute(['id' => $id]);
        $productData = $query->fetch(PDO::FETCH_ASSOC);

        if ($productData) {
            $this->id = $productData['id'];
            $this->name = $productData['name'];
            $this->photos = json_decode($productData['photos'], true);
            $this->price = $productData['price'];
            $this->description = $productData['description'];
            $this->quantity = $productData['quantity'];
            $this->created_at = new DateTime($productData['created_at']);
            $this->updated_at = new DateTime($productData['updated_at']);
            $this->category_id = $productData['category_id'];
            $this->brand = $productData['brand'];
            $this->waranty_fee = $productData['waranty_fee'];
            return true;
        }

        return false;
    }

    public static function findAll(PDO $pdo): array {
        $query = $pdo->query("SELECT p.*, e.brand, e.waranty_fee FROM product p JOIN electronic e ON p.id = e.product_id");
        $productsData = $query->fetchAll(PDO::FETCH_ASSOC);

        $products = [];
        foreach ($productsData as $productData) {
            $products[] = new self(
                $productData['id'],
                $productData['name'],
                json_decode($productData['photos'], true),
                $productData['price'],
                $productData['description'],
                $productData['quantity'],
                new DateTime($productData['created_at']),
                new DateTime($productData['updated_at']),
                $productData['category_id'],
                $productData['brand'],
                $productData['waranty_fee']
            );
        }

        return $products;
    }

    public function create(PDO $pdo): ?self {
        $pdo->beginTransaction();
        try {
            $productCreated = parent::create($pdo);
            if (!$productCreated) {
                $pdo->rollBack();
                return null;
            }

            $query = $pdo->prepare("
                INSERT INTO electronic (product_id, brand, waranty_fee)
                VALUES (:product_id, :brand, :waranty_fee)
            ");

            $success = $query->execute([
                'product_id' => $this->id,
                'brand' => $this->brand,
                'waranty_fee' => $this->waranty_fee
            ]);

            if ($success) {
                $pdo->commit();
                return $this;
            } else {
                $pdo->rollBack();
                return null;
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            return null;
        }
    }

    public function update(PDO $pdo): bool {
        $pdo->beginTransaction();
        try {
            $productUpdated = parent::update($pdo);
            if (!$productUpdated) {
                $pdo->rollBack();
                return false;
            }

            $query = $pdo->prepare("
                UPDATE electronic SET
                    brand = :brand,
                    waranty_fee = :waranty_fee
                WHERE product_id = :product_id
            ");

            $success = $query->execute([
                'product_id' => $this->id,
                'brand' => $this->brand,
                'waranty_fee' => $this->waranty_fee
            ]);

            if ($success) {
                $pdo->commit();
                return true;
            } else {
                $pdo->rollBack();
                return false;
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }
}

// Connection to the database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=draft-shop', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Example usage
$clothing = Clothing::findOneById($pdo, 1);
if ($clothing) {
    var_dump($clothing);
} else {
    echo "Aucun vêtement trouvé avec l'ID 1.";
}

$electronics = Electronic::findAll($pdo);
foreach ($electronics as $electronic) {
    var_dump($electronic);
}