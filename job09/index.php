<?php

class Product {
    // Private roperties
    private int $id;
    private string $name;
    private array $photos;
    private int $price;
    private string $description;
    private int $quantity;
    private DateTime $created_at;
    private DateTime $updated_at;
    private int $category_id;

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
}

// Creation of a new product
$product1 = new Product(1, 'T-shirt', ['https://via.placeholder.com/150'], 1000, 'A beautiful T-shirt', 10, new DateTime(), new DateTime());
$product2 = new Product();

// Verify the initials values with the getters
var_dump($product1->getId());
var_dump($product1->getName());
var_dump($product1->getPhotos());
var_dump($product1->getPrice());
var_dump($product1->getDescription());
var_dump($product1->getQuantity());
var_dump($product1->getCreatedAt());
var_dump($product1->getUpdatedAt());
var_dump($product1->getCategoryId());
// Modify the product
$product1->setName("New T-shirt Name");
$product1->setPrice(1200);
$product1->setQuantity(20);
$product1->setUpdatedAt(new DateTime());

// Verify the modified values with the getters
var_dump($product1->getName());
var_dump($product1->getPrice());
var_dump($product1->getQuantity());
var_dump($product1->getUpdatedAt());

class Category {
    private int $id;
    private string $name;
    private string $description;
    private DateTime $created_at;
    private DateTime $updated_at;

    // Constructor
    public function __construct(int $id, string $name, string $description, DateTime $created_at, DateTime $updated_at) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getCreatedAt(): DateTime {
        return $this->created_at;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updated_at;
    }

    // Setters
    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setCreatedAt(DateTime $created_at): void {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt(DateTime $updated_at): void {
        $this->updated_at = $updated_at;
    }

    // Method to get all products in this category
    public function getProducts(PDO $pdo): array {
        $query = $pdo->prepare("SELECT * FROM product WHERE category_id = :category_id");
        $query->execute(['category_id' => $this->id]);
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
}

// Connection to the database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=draft-shop', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Exemple d'utilisation de la méthode getProducts()
$category = new Category(1, 'Clothing', 'All types of clothing', new DateTime(), new DateTime());
$products = $category->getProducts($pdo);

if (!empty($products)) {
    foreach ($products as $product) {
        var_dump($product);
    }
} else {
    echo "Aucun produit trouvé pour cette catégorie.";
}

// Example usage of findOneById
$product = new Product();
if ($product->findOneById($pdo, 7)) {
    var_dump($product);
} else {
    echo "Aucun produit trouvé avec l'ID 7.";
}

// Example usage of findAll
$products = Product::findAll($pdo);

if (!empty($products)) {
    foreach ($products as $product) {
        var_dump($product);
    }
} else {
    echo "Aucun produit trouvé.";
}

// Example usage of create
$newProduct = new Product(
    0,
    'New Product',
    ['https://via.placeholder.com/150'],
    1500,
    'A new product description',
    5,
    new DateTime(),
    new DateTime(),
    1
);

$createdProduct = $newProduct->create($pdo);

if ($createdProduct) {
    var_dump($createdProduct);
} else {
    echo "Échec de l'insertion du produit.";
}