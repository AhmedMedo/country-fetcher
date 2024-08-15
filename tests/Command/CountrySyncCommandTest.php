<?php

namespace App\Tests\Command;

use App\Command\CountrySyncCommand;
use App\Entity\Country;
use App\Service\CountryFetcherService;
use App\Service\CountrySyncService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CountrySyncCommandTest extends TestCase
{
    private $em;
    private $httpClient;
    private $countryRepository;

    private $countrySyncServie;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);

        $this->httpClient = $this->createMock(HttpClientInterface::class);

        $this->countryRepository = $this->createMock(EntityRepository::class);

        $this->countrySyncServie = new CountrySyncService($this->em, new CountryFetcherService($this->httpClient));

        $this->em->method('getRepository')->willReturn($this->countryRepository);
    }

    public function testAddNewCountry()
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')->willReturn([
            [
                'name' => ['common' => 'Testland'],
                'region' => 'Test Region',
                'subregion' => 'Test SubRegion',
                'demonyms' => ['eng' => ['f' => 'Testlandian']],
                'population' => 1000,
                'independent' => true,
                'cca3' => 'TEST',
                'flags' => ['png' => 'https://example.com/test.png'],
                'currencies' => ['TEST' => ['name' => 'Test Currency', 'symbol' => 'T']]
            ]
        ]);

        $this->httpClient->method('request')->willReturn($response);

        $this->countryRepository->method('findOneBy')->willReturn(null);

        $this->em->expects($this->once())->method('persist')->with($this->isInstanceOf(Country::class));

        $this->em->expects($this->once())->method('flush');

        $command = new CountrySyncCommand($this->countrySyncServie);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Country data synchronized successfully.', $output);
    }

    public function testUpdateExistingCountry()
    {
        $existingCountry = new Country();
        $existingCountry->setName('Testland');
        $existingCountry->setRegion('Old Region');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')->willReturn([
            [
                'name' => ['common' => 'Testland'],
                'region' => 'New Region',
                'subregion' => 'Test SubRegion',
                'demonyms' => ['eng' => ['f' => 'Testlandian']],
                'population' => 1000,
                'independent' => true,
                'cca3' => 'TEST',
                'flags' => ['png' => 'https://example.com/test.png'],
                'currencies' => ['TEST' => ['name' => 'Test Currency', 'symbol' => 'T']]
            ]
        ]);

        $this->httpClient->method('request')->willReturn($response);

        $this->countryRepository->method('findOneBy')->willReturn($existingCountry);

        $this->em->expects($this->once())->method('flush');

        $command = new CountrySyncCommand($this->countrySyncServie);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertEquals('New Region', $existingCountry->getRegion());
    }

    public function testRemoveNonExistingCountry()
    {
        $existingCountry = new Country();
        $existingCountry->setName('Oldland');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')->willReturn([
            [
                'name' => ['common' => 'Newland'],
                'region' => 'New Region',
                'subregion' => 'Test SubRegion',
                'demonyms' => ['eng' => ['f' => 'Newlandian']],
                'population' => 1000,
                'independent' => true,
                'cca3' => 'TEST',
                'flags' => ['png' => 'https://example.com/test.png'],
                'currencies' => ['NEW' => ['name' => 'New Currency', 'symbol' => 'N']]
            ]
        ]);

        $this->httpClient->method('request')->willReturn($response);

        $this->countryRepository->method('findAll')->willReturn([$existingCountry]);

        $this->em->expects($this->once())->method('remove')->with($existingCountry);

        $this->em->expects($this->once())->method('flush');

        $command = new CountrySyncCommand($this->countrySyncServie);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Country data synchronized successfully.', $output);
    }
}

