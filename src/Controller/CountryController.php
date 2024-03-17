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
        //Renderizamos la template
        return $this->render('country/index.html.twig', [
            'countries' => $countryRepository->findAll(),
        ]);
    }

    //Creamos un nuevo pais
    #[Route('/new', name: 'app_country_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        //Creamos la nueva entidad de Country
        $country = new Country();
        //Desde aqui creamos y manejamos el formulario
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        //Verifica si el formulario ha sido enviado
        if ($form->isSubmitted() && $form->isValid()) {
            //Confirmamos las operaciones pendientes en base de datos
            $entityManager->persist($country);
            $entityManager->flush();
            //reedirigimos al usuario a la ruta que deseamos
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
        // Este método se utiliza para mostrar los detalles de un país específico.
    
        // Renderiza la plantilla 'show.html.twig' y pasa el objeto $country como contexto.
        // En la plantilla, $country contendrá los datos del país que se mostrarán
        return $this->render('country/show.html.twig', [
            'country' => $country,
        ]);
    }

    //Editamos los registros a traves del id
    #[Route('/{id}/edit', name: 'app_country_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Country $country, EntityManagerInterface $entityManager): Response
    {
        // Este método se utiliza para editar  un país.

        // Crea un formulario de edición utilizando el tipo de formulario 'CountryType' y pasa la entidad $country.
        $form = $this->createForm(CountryType::class, $country);
        //Manejamos la solicitud actual del formulario
        $form->handleRequest($request);
        //Verifica si el formulario ha sido enviado y los datos son validos
        if ($form->isSubmitted() && $form->isValid()) {
             // Si el formulario es válido, actualiza el objeto $country en la base de datos.
            $entityManager->flush();
             //reedirigimos al usuario a la ruta que deseamos
            return $this->redirectToRoute('app_country_index', [], Response::HTTP_SEE_OTHER);
        }

        // Si el formulario no se ha enviado o si los datos no son válidos, renderiza la plantilla de edición.
   
        return $this->render('country/edit.html.twig', [
            'country' => $country,
            'form' => $form,
        ]);
    }

    //Eliminamos registros de paises a través de su id
    #[Route('/{id}', name: 'app_country_delete', methods: ['POST'])]
    public function delete(Request $request, Country $country, EntityManagerInterface $entityManager): Response
    {

       

        // Verifica si el token CSRF es válido para proteger la acción de eliminación contra ataques CSRF.
        if ($this->isCsrfTokenValid('delete'.$country->getId(), $request->request->get('_token'))) {
            $entityManager->remove($country);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_country_index', [], Response::HTTP_SEE_OTHER);
    }

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
        $apiCountriesData = $response->toArray();
        
        // Variables para almacenar la mejor coincidencia encontrada
        $bestMatch = null;
        $bestMatchSimilarity = 0;

        // Comparar los datos devueltos con los datos del país y encontrar la mejor coincidencia
        foreach ($apiCountriesData as $apiCountryDataItem) {
            // Calcular la similitud entre los nombres de los países
            similar_text($country->getName(), $apiCountryDataItem["name"]["common"], $similarity);
            if ($similarity > $bestMatchSimilarity) {
                $bestMatchSimilarity = $similarity;
                $bestMatch = $apiCountryDataItem;
            }
        }

        // Actualizar los campos del país con los datos de la mejor coincidencia encontrada
        if ($bestMatch) {
            $country->setName($bestMatch["name"]["common"]);
            $country->setCurrencies($bestMatch["currencies"]);
            $country->setCapital($bestMatch["capital"][0]);
            $country->setRegion($bestMatch["region"]);
            $country->setSubregion($bestMatch["subregion"]);
            $country->setLanguages($bestMatch["languages"]);
            $country->setLatencia($bestMatch["latlng"]);
            $country->setArea($bestMatch["area"]);
            $country->setPopulation($bestMatch["population"]);

            /**
             * Manejamos el dato timezone a través del metodo convertTimezoneFormat
             */
            if (isset($bestMatch["timezones"]) && !empty($bestMatch["timezones"][0])) {
                $timezone = $this->convertTimezoneFormat($bestMatch["timezones"][0]);
                $country->setTimezone($timezone);
            }

            /**
             * Manejar el campo continente ya que en la api es un array y en mi entidad country es un string (Convertimos el dato)
             */
            if (isset($bestMatch["continents"]) && !empty($bestMatch["continents"][0])) {
                // Concatenar los valores del array en una sola cadena
                $continente = implode(', ', $bestMatch["continents"]);
                $country->setContinente($continente);
            }

            // Guardar los cambios en la base de datos
            $entityManager->flush();
        }

        // Redirigir a la página de índice de países o a donde desees
        return $this->redirectToRoute('app_country_index');
    }
        /**
         * Convertimos el dato de la api en un objeto datetime tal y como
         * lo tenemos en la entidad country
         */
        private function convertTimezoneFormat(string $apiTimezone): \DateTimeInterface
        {
            // Convertir el formato de la zona horaria de la API a "UTC+02:00"
            $timezoneOffset = intval($apiTimezone); // Convertir a entero
            $timezoneId = timezone_name_from_abbr(null, $timezoneOffset * 3600, false); // Multiplicar por 3600
            $timezone = new \DateTimeZone($timezoneId);

            // Crear un objeto DateTime con la zona horaria
            $dateTime = new \DateTime('now', $timezone);

            return $dateTime;
        }

    }