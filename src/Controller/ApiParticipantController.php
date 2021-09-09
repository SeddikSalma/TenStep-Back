<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ParticipantRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiParticipantController extends AbstractController
{
    /**
     * @Route("/participant", name="api_participant", methods={"GET"})
     */
    public function index(ParticipantRepository $pRep, NormalizerInterface $norm): Response
    {
        $participant = $pRep->findAll();
        $participantNorm = $norm->normalize($participant, null, ['groups' => 'form:read']);
        $json = json_encode($participantNorm);
        $resp = new Response($json, 200, ["Content-Type" => "application/json"]);
        return $resp;
    }


    /**
     * @Route("/participant", name="api_addparticipant", methods={"POST"})
     */
    public function Add(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $jsonResp = $request->getContent();
        try {

            $form = $serializer->deserialize($jsonResp, Participant::class, 'json');
            $error = $validator->validate($form);
            /*  if (count($error) > 0) {
                return $this->json($error, 401);
            }*/
            $em->persist($form);
            $em->flush();
            return $this->json($form, 201, [], ['groups' => 'form:read']);
        } catch (NotEncodableValueException $e) {
            return $this->json(['status' => 400, 'message' => $e->getMessage()], 400);
        }
    }


    /**
     * @Route("/participant/{id}", name="mateur", methods={"PUT"})
     */
    public function update(ParticipantRepository $forRep, Request $request, $id, EntityManagerInterface $em): JsonResponse
    {
        $format = $forRep->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);
        empty($data['nom']) ? true : $format->setNom($data['nom']);
        empty($data['nbr']) ? true : $format->setNbr($data['nbr']);

        $em->persist($format);
        $em->flush();

        return new JsonResponse(json_encode($format), Response::HTTP_OK);
        //return $resp;
    }
    /**
     * @Route("/participant/{id}", name="deletefor", methods={"DELETE"})
     */
    public function delete(ParticipantRepository $forRep, Request $request, $id, EntityManagerInterface $em): Response
    {
        $format = $forRep->findOneBy(['id' => $id]);
        $em->remove($format);
        $em->flush();
        $json = [
            'Deleted' => $format,
            'message' => 'sucess'
        ];
        return $this->json($json, 202, [], ['groups' => 'Formation']);
    }
    /**
     * @Route("/participant/{id}", name="onepart", methods={"GET"})
     */
    public function Onepart(ParticipantRepository $resRep, Request $request, $id, EntityManagerInterface $em): Response
    {
        $res = $resRep->findOneBy(['id' => $id]);

        return $this->json($res, 202, [], ['groups' => 'form:read']);
    }
}
