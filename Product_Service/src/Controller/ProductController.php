<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private ProductRepository $repository;
    public function __construct(ProductRepository $repository)
    {
        $this->repository=$repository;
    }

    #[Route('/product', name: 'app_product')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ProductController.php',
        ]);
    }
    #[Route('/GetProducts',name:'app_product_get',methods:'GET')]
    public function GetProducts()
    {
        try {
            $response = $this->repository->getProducts();
            return $this->json($response);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Database connection error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route('/GetProduct/{id}',name:'app_product_getProduct',methods:'GET')]
    public function GetProduct(Request $req)
    {
        $routeAttributes = $req->attributes->all();
        $id = $routeAttributes['_route_params']['id'];
        try{
            $response=$this->repository->getProductById($id);
            return $this->json($response);
        }catch(\Exception $e){
            return $this->json(['error' => 'Database connection error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }   
    }
    
    #[Route('/Createproduct',name:'app_product_create',methods:'POST')]
    public function CreateProduct(Request $req)
    {
        $requestData = json_decode($req->getContent(), true);
        if ($requestData === null) {
            return new JsonResponse(['error' => 'Invalid JSON data'], 400);
        }
        $product=new Product();
        $uuid=Uuid::uuid4();
        $product->setId($uuid->toString());
        $product->setProductName($requestData['productName']);
        $product->setDescription($requestData['productDescription']);
        $product->setPrice($requestData['productPrice']);
        $product->setQuantity($requestData['productQuantity']);
        $response=$this->repository->CreateProduct($product);
        if($response['response']=="data inserted succeffly"){   return $this->json($response, 200); }
        else if($response['response']=="Data insert failed"){   return $this->json($response, 400); }
        return $this->json($response, 500); 
        
    }
}