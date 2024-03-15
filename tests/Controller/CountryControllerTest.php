<?php

namespace App\Test\Controller;

use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CountryControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/country/';

    /**
     * Ejecución antes de cada prueba
     * Configura el entorno
     *  Crea un cliente, obtiene la entidad y el repositorio de la entidad Country,
     *  y elimina todos los registros de países existentes en la base de datos
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Country::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    /**
     * Verifica que la pagina principal
     * cargue bien los datos y devuelva 200
     */
    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        //Aqui podemos verificar que el titulo de la pagina se el correcto
        //self::assertPageTitleContains('Country index');
    }

     /**
     * Se verifica que se pueden enviar datos y
     * crear nuevos paises.
     */
    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'country[name]' => 'Testing',
            'country[currencies]' => 'Testing',
            'country[capital]' => 'Testing',
            'country[region]' => 'Testing',
            'country[subregion]' => 'Testing',
            'country[languages]' => 'Testing',
            'country[latencia]' => 'Testing',
            'country[area]' => 'Testing',
            'country[population]' => 'Testing',
            'country[timezone]' => 'Testing',
            'country[continente]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    /**
     * Comprobación de que se pueda acceder a 
     * la página de un pais existente y que
     * se muestra los detalles del pais con 
     * titulo
     */
    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Country();
        $fixture->setName('My Title');
        $fixture->setCurrencies('My Title');
        $fixture->setCapital('My Title');
        $fixture->setRegion('My Title');
        $fixture->setSubregion('My Title');
        $fixture->setLanguages('My Title');
        $fixture->setLatencia('My Title');
        $fixture->setArea('My Title');
        $fixture->setPopulation('My Title');
        $fixture->setTimezone('My Title');
        $fixture->setContinente('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Country');

        
    }

    /**
     * Se comprueba que los cambios de edicion
     * se hagan correctamente y verifica
     * que se muestren esos cambios en los detalles del país
     *
     */
    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Country();
        $fixture->setName('Value');
        $fixture->setCurrencies('Value');
        $fixture->setCapital('Value');
        $fixture->setRegion('Value');
        $fixture->setSubregion('Value');
        $fixture->setLanguages('Value');
        $fixture->setLatencia('Value');
        $fixture->setArea('Value');
        $fixture->setPopulation('Value');
        $fixture->setTimezone('Value');
        $fixture->setContinente('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'country[name]' => 'Something New',
            'country[currencies]' => 'Something New',
            'country[capital]' => 'Something New',
            'country[region]' => 'Something New',
            'country[subregion]' => 'Something New',
            'country[languages]' => 'Something New',
            'country[latencia]' => 'Something New',
            'country[area]' => 'Something New',
            'country[population]' => 'Something New',
            'country[timezone]' => 'Something New',
            'country[continente]' => 'Something New',
        ]);

        //verificación de buena redireccion 
        self::assertResponseRedirects('/country/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getCurrencies());
        self::assertSame('Something New', $fixture[0]->getCapital());
        self::assertSame('Something New', $fixture[0]->getRegion());
        self::assertSame('Something New', $fixture[0]->getSubregion());
        self::assertSame('Something New', $fixture[0]->getLanguages());
        self::assertSame('Something New', $fixture[0]->getLatencia());
        self::assertSame('Something New', $fixture[0]->getArea());
        self::assertSame('Something New', $fixture[0]->getPopulation());
        self::assertSame('Something New', $fixture[0]->getTimezone());
        self::assertSame('Something New', $fixture[0]->getContinente());
    }

    /**
     * Verifica que se pueda eliminar un país existente
     */
    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Country();
        $fixture->setName('Value');
        $fixture->setCurrencies('Value');
        $fixture->setCapital('Value');
        $fixture->setRegion('Value');
        $fixture->setSubregion('Value');
        $fixture->setLanguages('Value');
        $fixture->setLatencia('Value');
        $fixture->setArea('Value');
        $fixture->setPopulation('Value');
        $fixture->setTimezone('Value');
        $fixture->setContinente('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/country/');
        self::assertSame(0, $this->repository->count([]));
    }
}
