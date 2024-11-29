<?php

abstract class AbstractProduct {
    // Protected properties
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

    // Abstract methods
    abstract public function create(PDO $pdo): ?self;
    abstract public function update(PDO $pdo): bool;
    abstract public static function findOneById(PDO $pdo, int $id): ?self;
    abstract public static function findAll(PDO $pdo): array;

    // Getters and setters
    // ... existing getters and setters ...
}

class Clothing extends AbstractProduct {
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

    public function create(PDO $pdo): ?self {
        // Implémentation spécifique à Clothing
    }

    public function update(PDO $pdo): bool {
        // Implémentation spécifique à Clothing
    }

    public static function findOneById(PDO $pdo, int $id): ?self {
        // Implémentation spécifique à Clothing
    }

    public static function findAll(PDO $pdo): array {
        // Implémentation spécifique à Clothing
    }
}

class Electronic extends AbstractProduct {
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

    public function create(PDO $pdo): ?self {
        // Implémentation spécifique à Electronic
    }

    public function update(PDO $pdo): bool {
        // Implémentation spécifique à Electronic
    }

    public static function findOneById(PDO $pdo, int $id): ?self {
        // Implémentation spécifique à Electronic
    }

    public static function findAll(PDO $pdo): array {
        // Implémentation spécifique à Electronic
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