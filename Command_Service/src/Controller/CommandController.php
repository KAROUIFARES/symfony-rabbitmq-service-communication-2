<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\MessageHandler\SendMessageToProductService;

class CommandController extends AbstractController
{
    private SendMessageToProductService $RabbitMq;
    public function __construct(SendMessageToProductService $RabbitMq)
    {
        $this->RabbitMq=$RabbitMq;
    }
    
    #[Route('/command', name: 'app_command')]
    public function index(Request $req): JsonResponse
    {
        $data=json_decode($req->getContent(), true);
        $this->RabbitMq->execute($data['id']);
        return $this->json(['message' => "Message sended successfully"]);
    }
}
