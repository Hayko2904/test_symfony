<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CountryRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    private $productRepository;
    private $countryRepository;

    public function __construct(
        ProductRepository $productRepository,
        CountryRepository $countryRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->countryRepository = $countryRepository;
    }

    #[Route('/', name: 'app_product')]
    public function index(): Response
    {
        $product = new Product();

        $form = $this->createFormBuilder($product)
            ->setAction($this->generateUrl('get_price'))
            ->add('name', ChoiceType::class, [
                'choices'  => array_combine(
                    array_map(
                        'current',
                        $this->productRepository->getToArray('name')),
                        array_map(
                            'current',
                            $this->productRepository->getToArray('id'))
                            ),
            ])
            ->add('Submit', SubmitType::class, ['label' => 'Get Price'])
            ->getForm();

        return $this->render('product/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/get-price', name: 'get_price')]
    public function getPrice(Request $request): Response
    {
        $requestData = $request->request->all();

        $countryCode = strtoupper(substr($requestData['tax_code'], 0, 2));

        $percentage = $this->countryRepository->findByTaxCodeField($countryCode);

        if (!$percentage) {
            return new Response(
                'Country not found', 
                 Response::HTTP_OK
            );
        }

        $price = $this->productRepository->findOneByIdField((int)$requestData['form']['name'])->getPrice();

        return new Response(
                $price + $percentage['tax_percentage'], 
                Response::HTTP_OK
            );

    }
}
