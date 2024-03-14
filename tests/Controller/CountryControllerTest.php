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

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Country index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

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

        // Use assertions to check that the properties are properly displayed.
    }

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
