<?php

namespace App\Controller;

use App\Entity\Country;
use App\Form\CountryType;
use App\Repository\CountryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpClient\HttpClient;



#[Route('/')]
class CountryController extends AbstractController
{
    //Mostramos los datos en la página principal
    #[Route('/', name: 'app_country_index', methods: ['GET'])]
    public function index(CountryRepository $countryRepository): Response
    {
        return $this->render('country/index.html.twig', [
            'countries' => $countryRepository->findAll(),
        ]);
    }

    //Creamos un nuevo pais
    #[Route('/new', name: 'app_country_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $country = new Country();
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($country);
            $entityManager->flush();

            return $this->redirectToRoute('app_country_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('country/new.html.twig', [
            'country' => $country,
            'form' => $form,
        ]);
    }

    //Mostramos los datos de un pais en concreto a traves de su id
    #[Route('/{id}', name: 'app_country_show', methods: ['GET'])]
    public function show(Country $country): Response
    {
        return $this->render('country/show.html.twig', [
            'country' => $country,
        ]);
    }

    //Editamos los registros a traves del id
    #[Route('/{id}/edit', name: 'app_country_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Country $country, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_country_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('country/edit.html.twig', [
            'country' => $country,
            'form' => $form,
        ]);
    }

    //Eliminamos registros de paises a través de su id
    #[Route('/{id}', name: 'app_country_delete', methods: ['POST'])]
    public function delete(Request $request, Country $country, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$country->getId(), $request->request->get('_token'))) {
            $entityManager->remove($country);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_country_index', [], Response::HTTP_SEE_OTHER);
    }

    //Sincronizamos datos con la api
    #[Route('/sync/{id}', name: 'app_country_sync')]
    public function sync(int $id, EntityManagerInterface $entityManager): Response
    {

        
        // Obtener el país por su ID
        $country = $entityManager->getRepository(Country::class)->find($id);
        
        if (!$country) {
            throw $this->createNotFoundException('No se encontró el país con el ID: '.$id);
        }

        // Realizar una solicitud a la API pública
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'https://restcountries.com/v3.1/all');
       
        // Decodificar la respuesta JSON
        $data = $response->toArray();
        foreach ($data as $countryData) {
            // Comparar los datos devueltos con los datos del país
            // Si hay diferencias, actualizar los datos del país
            // Puedes hacer esto comparando cada campo individualmente y actualizando el país si es necesario

            // Por ejemplo, para actualizar el nombre del país si es diferente en la API
            if ($countryData["name"]["common"] !== $country->getName()) {
                $country->setName($countryData["name"]["common"] );
            }
        }


       

        // Repite este proceso para los demás campos que deseas sincronizar

        // Guardar los cambios en la base de datos
        $entityManager->flush();

        // Redirigir a la página de índice de países o a donde desees
        return $this->redirectToRoute('app_country_index');
    }
}
