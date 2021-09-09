<?php

namespace App\Controller;

use App\Entity\Formation;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Lcobucci\JWT\Validation\Validator;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiFormationController extends AbstractController
{
    /**
     * @Route("/formation", name="api_formation", methods={"GET"})
     */
    public function lister(FormationRepository $forRep, NormalizerInterface $norm): Response
    {
        $formation = $forRep->findAll();
        $formationNorm = $norm->normalize($formation, null, ['groups' => 'form:read']);
        $json = json_encode($formationNorm);
        $resp = new Response($json, 200, ["Content-Type" => "application/json"]);
        return $resp;
    }
    /**
     * @Route("/formation/{id}", name="oneforma", methods={"GET"})
     */
    public function Oneforma(FormationRepository $resRep, Request $request, $id, EntityManagerInterface $em): Response
    {
        $res = $resRep->findOneBy(['id' => $id]);

        return $this->json($res, 202, [], ['groups' => 'form:read']);
    }


    /**
     * @Route("/formations", name="api_store", methods={"POST"})
     */
    public function Add(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $jsonResp = $request->getContent();
        try {

            $form = $serializer->deserialize($jsonResp, Formation::class, 'json');
            $error = $validator->validate($form);
            if (count($error) > 0) {
                return $this->json($error, 400);
            }
            $em->persist($form);
            $em->flush();
            return $this->json($form, 201, [], ['groups' => 'form:read']);
        } catch (NotEncodableValueException $e) {
            return $this->json(['status' => 400, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/formation/{id}", name="api_storee", methods={"PUT"})
     */
    public function update(FormationRepository $forRep, Request $request, $id, EntityManagerInterface $em): JsonResponse
    {
        $formation = $forRep->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);
        empty($data['nom']) ? true : $formation->setNom($data['nom']);
        empty($data['nbr_jours']) ? true : $formation->setNbrJours($data['nbr_jours']);
        empty($data['prix']) ? true : $formation->setPrix($data['prix']);
        $updatedFormation = $forRep->updateFormation($formation, $em);

        //$resp = new Response(json_encode($updatedFormation), 200, ["Content-Type" => "application/json"]);
        return new JsonResponse(json_encode($updatedFormation), Response::HTTP_OK);
        //return $resp;
    }
    /**
     * @Route("/formation/{id}", name="api_storeee", methods={"DELETE"})
     */
    public function delete(FormationRepository $forRep, Request $request, $id, EntityManagerInterface $em): Response
    {
        $formation = $forRep->findOneBy(['id' => $id]);
        $em->remove($formation);
        $em->flush();
        $json = [
            'Deleted' => $formation,
            'message' => 'sucess'
        ];
        return $this->json($json, 202, [], ['groups' => 'Formation']);
    }
}
