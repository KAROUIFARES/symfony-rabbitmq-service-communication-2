<?php

namespace App\Repository;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;


class ProductRepository extends ServiceEntityRepository
{
    private $doctrine;
    private $logger;
    public function __construct(ManagerRegistry $doctrine,LoggerInterface $logger)
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
    }
    public function connect()
    {
        try {
            /** @var Connection $connection */
            $connection = $this->doctrine->getConnection();
            return $connection;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function getProducts()
    {
        $conn=$this->connect();
        if($conn){
            $query="SELECT * FROM product";
            try{
                $products=$conn->executeQuery($query)->fetchAllAssociative();
                return $products;
            }catch(\Exception $e){
                throw $e;
            }
        }
    }
    public function getProductById(string $id)
    {
        $conn = $this->connect();
        if ($conn) {
            $query = "SELECT * FROM product WHERE id = :id";
            $params = ['id' => $id];
            try {
                $products = $conn->executeQuery($query, $params)->fetchAllAssociative();
                return $products;
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }
    public function CreateProduct(Product $product)
    {
        $conn =$this->connect();
        if ($conn) {
            $query = "insert into product(id,product_name,description,price,quantity)values(:id, :Name, :description, :price, :quantity)";
            $params = [
                'id' => $product->getId(),
                'Name' => $product->getProductName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'quantity' => $product->getQuantity()
            ];
            try {
                $conn->executeStatement($query, $params);
                $this->logger->info("Product created successfully");
                return ['response'=>'data inserted succeffly'];
            } catch (\Exception $e) {
                $this->logger->error("Error creating product: " . $e->getMessage());
                return ['response' => 'Data insert failed'];
            }
        } else {
            $this->logger->error("Database connection failed");
            return ['response' => 'Database connection failed'];
        }
    }
}