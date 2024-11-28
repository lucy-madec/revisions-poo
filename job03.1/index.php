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
}