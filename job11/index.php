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

    // Getters and setters for Clothing-specific properties
    // ...
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

    // Getters and setters for Electronic-specific properties
    // ...
}

// Connection to the database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=draft-shop', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Example usage
$clothing = new Clothing(0, 'T-shirt', ['https://via.placeholder.com/150'], 1000, 'A beautiful T-shirt', 10, new DateTime(), new DateTime(), 1, 'M', 'Red', 'Casual', 50);
$electronic = new Electronic(0, 'Smartphone', ['https://via.placeholder.com/150'], 50000, 'Latest model smartphone', 5, new DateTime(), new DateTime(), 2, 'BrandX', 200);

var_dump($clothing);
var_dump($electronic);