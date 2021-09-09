<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Repository\DevisRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
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

class ApiDevisController extends AbstractController
{
    /**
     * @Route("/Devis", name="api_Devis", methods={"GET"})
     */
    public function listerdevis(DevisRepository $forRep, NormalizerInterface $norm): Response
    {
        $Devis = $forRep->findAll();
        // dd($Devis[0]->getReservation());
        /* $DevisNorm = $norm->normalize($Devis, null, ['groups' => 'form:list']);
        $json = json_encode($DevisNorm);
        $j = json_decode($json);
        //dd($json['reservation']);
        // dd($Devis);
        $resp = new Response($json, 200, ["Content-Type" => "application/json"]);
        return $resp;*/

        return $this->json($Devis, 200, [], ['groups' => 'form:reads']);
    }

    /**
     * @Route("/Devis/{id}", name="oneDevis", methods={"GET"})
     */
    public function oneDevis(DevisRepository $forRep, $id, NormalizerInterface $norm): Response
    {
        $Devis = $forRep->findOneBy(['id' => $id]);
        dd($Devis);
        return $this->json($Devis, 200, [], ['groups' => ['form:reads']]);
    }


    /**
     * @Route("/Devis/{id}", name="api_addDevis", methods={"POST"})
     */
    public function Add($id, Request $request, ReservationRepository $rsRep, DevisRepository $resRep, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $jsonResp = $request->getContent();
        $data = json_decode($request->getContent());

        try {
            $form = $serializer->deserialize($jsonResp, Devis::class, 'json');;
            $form->setReservation($rsRep->findOneBy(['id' => $id]));

            $error = $validator->validate($form);
            if (count($error) > 0) {
                return $this->json($error, 400);
            }
            /*$res = $data['reservation'];
            $form->setReservation($resRep->findOneBy(['id' => $res]));*/
            $em->persist($form);
            $em->flush();
            return $this->json($form, 201, [], ['groups' => 'form:read']);
        } catch (NotEncodableValueException $e) {
            return $this->json(['status' => 400, 'message' => $e->getMessage()], 400);
        }
    }


    /**
     * @Route("/Devis/{id}", name="updateDevis", methods={"PUT"})
     */
    public function update(DevisRepository $forRep, ReservationRepository $resRep, Request $request, $id, EntityManagerInterface $em): JsonResponse
    {
        $Devis = $forRep->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);
        empty($data['nbrParticipant']) ? true : $Devis->setNbrParticipant($data['nbrParticipant']);
        empty($data['somme']) ? true : $Devis->setSomme($data['somme']);
        empty($data['reservation']) ? true : $Devis->setReservation($resRep->findOneBy(['id' => $data['reservation']]));
        $em->persist($Devis);
        $em->flush();

        return new JsonResponse(json_encode($Devis), Response::HTTP_OK);
        //return $resp;
    }
    /**
     * @Route("/Devis/{id}", name="for", methods={"DELETE"})
     */
    public function delete(DevisRepository $forRep, Request $request, $id, EntityManagerInterface $em): Response
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
}
