<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Country;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CountrySyncService
{

    public function __construct(private readonly EntityManagerInterface $em, private readonly CountryFetcherService $fetcher)
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function syncCountries(): void
    {
        $countries = $this->fetcher->fetchCountries();
        foreach ($countries as $data) {
            $country = $this->em->getRepository(Country::class)->findOneBy(['cca3' => $data['cca3']]);
            $country =  $country ?? new Country();
            $country->setName($data['name']['common']);
            $country->setRegion($data['region']);
            $country->setCca3($data['cca3'] ?? '');
            $country->setSubRegion($data['subregion'] ?? '');
            $country->setDemonym(array_key_exists('demonyms',$data) ? $data['demonyms']['eng']['f'] : '');
            $country->setPopulation($data['population']);
            $country->setIndependent($data['independent'] ?? false);
            $country->setFlag($data['flags']['png']);
            $country->setCurrency(array_key_exists('currencies',$data) ? array_values($data['currencies']) : []);

            $this->em->persist($country);
        }

        $this->em->flush();
    }
}
