<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Formateur;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Lcobucci\JWT\Validation\Validator;
use App\Repository\FormateurRepository;
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

class ApiFormateurController extends AbstractController
{
    /**
     * @Route("/formateur", name="api_formateur", methods={"GET"})
     */
    public function index(FormateurRepository $forRep, NormalizerInterface $norm): Response
    {
        $formateur = $forRep->findAll();
        $formateurNorm = $norm->normalize($formateur, null, ['groups' => 'form:read']);
        $json = json_encode($formateurNorm);
        $resp = new Response($json, 200, ["Content-Type" => "application/json"]);
        return $resp;
    }


    /**
     * @Route("/formateur", name="api_addformateur", methods={"POST"})
     */
    public function Add(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $jsonResp = $request->getContent();
        try {

            $form = $serializer->deserialize($jsonResp, Formateur::class, 'json');
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
     * @Route("/formateur/{id}", name="mateur", methods={"PUT"})
     */
    public function update(FormateurRepository $forRep, Request $request, $id, EntityManagerInterface $em): JsonResponse
    {
        $format = $forRep->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);
        empty($data['nom']) ? true : $format->setNom($data['nom']);
        empty($data['type']) ? true : $format->setType($data['type']);

        $em->persist($format);
        $em->flush();

        return new JsonResponse(json_encode($format), Response::HTTP_OK);
        //return $resp;
    }
    /**
     * @Route("/formateur/{id}", name="deletefor", methods={"DELETE"})
     */
    public function delete(FormateurRepository $forRep, Request $request, $id, EntityManagerInterface $em): Response
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
     * @Route("/formateur/{id}", name="onefor", methods={"GET"})
     */
    public function Oneform(FormateurRepository $resRep, Request $request, $id, EntityManagerInterface $em): Response
    {
        $res = $resRep->findOneBy(['id' => $id]);

        return $this->json($res, 202, [], ['groups' => 'form:read']);
    }
}
