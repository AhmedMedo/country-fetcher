<?php
declare(strict_types=1);

namespace App\Controller\V1;

use App\Entity\Country;
use App\Form\CountryForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('countries')]
class CountryController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em
    )
    {
    }

    /**
     * Get details of a country by UUID.
     *
     * @OA\Get(
     *     path="/countries/{uuid}/show",
     *     summary="Get details of a country",
         *     @OA\Parameter(
         *         name="uuid",
         *         in="path",
         *         description="UUID of the country",
         *         required=true,
         *         @OA\Schema(type="string")
         *     ),
         *     @OA\Response(
         *         response=200,
         *         description="Country details",
         *         @Model(type=Country::class,groups={"non_sensitive_data"})
         *     ),
         *     @OA\Response(
         *         response=404,
         *         description="Country not found"
         *     )
     * )
     */
    #[Route('/{uuid}/show', methods: ['GET'])]
    public function getCountry(string $uuid): JsonResponse
    {
        $country = $this->em->getRepository(Country::class)->findOneBy(['uuid' => $uuid]);
        if (!$country) {
            return $this->json(['error' => 'Country not found!'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($country);
    }

    /**
     * Get a list of all countries.
     *
     * @OA\Get(
     *     path="/countries/list",
     *     summary="Get list of all countries",
     *     @OA\Response(
     *         response=200,
     *         description="List of countries",
     *         @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=Country::class)))
     *     )
     * )
     */
    #[Route('/list', methods: ['GET'])]
    public function getCountries(): JsonResponse
    {
        $countries = $this->em->getRepository(Country::class)->findAll();

        return $this->json($countries);
    }

    /**
     * Add a new country.
     *
     * @OA\Post(
     *     path="/countries",
     *     summary="Add a new country",
     *     @OA\RequestBody(
     *         description="Country data",
     *         required=true,
     *         @OA\JsonContent(ref=@Model(type=CountryForm::class))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Country created",
     *         @OA\JsonContent(ref=@Model(type=Country::class))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="errors", type="object", additionalProperties={"type"="string"})
     *         )
     *     )
     * )
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/', methods: ['POST'])]
    public function addCountry(Request $request): JsonResponse
    {
        $form = $this->createForm(CountryForm::class, null, [
            'allow_extra_fields' => true
        ]);

        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true, true) as $error) {
                $field = $error->getOrigin()->getName();
                $errors[$field] = $error->getMessage();
            }

            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $country = new Country();
        $country->setName($data['name']);
        $country->setRegion($data['region']);
        $country->setSubRegion($data['subRegion']);
        $country->setDemonym($data['demonym']);
        $country->setPopulation($data['population']);
        $country->setIndependent($data['independant']);
        $country->setFlag($data['flag']);
        $country->setCurrency($data['currency']);

        $this->em->persist($country);
        $this->em->flush();
        return $this->json($country, Response::HTTP_CREATED);
    }

    /**
     * Update a country by UUID.
     *
     * @OA\Patch(
     *     path="/countries/{uuid}",
     *     summary="Update a country",
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID of the country",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         description="Updated country data",
     *         required=true,
     *         @OA\JsonContent(ref=@Model(type=CountryForm::class))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Country updated",
     *         @OA\JsonContent(type="object", @OA\Property(property="status", type="string"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="errors", type="object", additionalProperties={"type"="string"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Country not found",
     *         @OA\JsonContent(type="object", @OA\Property(property="error", type="string"))
     *     )
     * )
     */
    #[Route('/{uuid}', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateCountry(string $uuid, Request $request): JsonResponse
    {
        $country = $this->em->getRepository(Country::class)->findOneBy(['uuid' => $uuid]);

        if (!$country) {
            return $this->json(['error' => 'Country not found!'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(CountryForm::class, null, [
            'allow_extra_fields' => true
        ]);

        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true, true) as $error) {
                $field = $error->getOrigin()->getName();
                $errors[$field] = $error->getMessage();
            }

            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['name'])) {
            $country->setName($data['name']);
        }
        if (isset($data['region'])) {
            $country->setRegion($data['region']);
        }
        if (isset($data['subRegion'])) {
            $country->setSubRegion($data['subRegion']);
        }
        if (isset($data['demonym'])) {
            $country->setDemonym($data['demonym']);
        }
        if (isset($data['population'])) {
            $country->setPopulation($data['population']);
        }
        if (isset($data['independent'])) {
            $country->setIndependent($data['independant']);
        }
        if (isset($data['flag'])) {
            $country->setFlag($data['flag']);
        }
        if (isset($data['currency'])) {
            $country->setCurrency($data['currency']);
        }

        $this->em->flush();

        return $this->json(['status' => 'Country updated!']);
    }

    /**
     * Delete a country by UUID.
     *
     * @OA\Delete(
     *     path="/countries/{uuid}",
     *     summary="Delete a country",
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID of the country",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Country deleted",
     *         @OA\JsonContent(type="object", @OA\Property(property="status", type="string"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Country not found",
     *         @OA\JsonContent(type="object", @OA\Property(property="error", type="string"))
     *     )
     * )
     */
    #[Route('/{uuid}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteCountry(string $uuid): JsonResponse
    {
        $country = $this->em->getRepository(Country::class)->findOneBy(['uuid' => $uuid]);
        if (!$country) {
            return $this->json(['error' => 'Country not found!'], Response::HTTP_NOT_FOUND);
        }
        $this->em->remove($country);
        $this->em->flush();

        return $this->json(['status' => 'Country deleted!']);
    }
}
