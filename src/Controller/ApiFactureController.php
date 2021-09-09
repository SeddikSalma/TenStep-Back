<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Entity\Reservation;
use App\Entity\Facture;

use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class ApiFactureController extends AbstractController
{
    /**
     * @Route("/Facture", name="api_D", methods={"GET"})
     */
    public function index(FactureRepository $forRep, ReservationRepository $resrep, NormalizerInterface $norm)
    {
        $Facture = $forRep->findAll();
        //dd($Facture);
        /*//dd($Facture[0]->getReservation()->getId());
        // $Facture[0]->setReservation($Facture[0]->getReservation()->getId());
        $FactureNorm = $norm->normalize($Facture, null, ['groups' => 'form:reads']);
        $json1 = json_encode($FactureNorm, JSON_FORCE_OBJECT);
        $obj = json_decode($json1);
        $Facture[0]->setReservation($resrep->findOneBy(['id' => $Facture[0]->getReservation()->getId()]));
        // $obj[0]->reservation = $resrep->findOneBy(['id' => $Facture[0]->getReservation()->getId()]);
        $json = json_encode($obj);
        dd($FactureNorm);
        $resp = new Response($json, 200, ["Content-Type" => "application/json"]);
        return $resp;*/
        return $this->json($Facture, 200, [], ['groups' => 'form:reads']);

        //return $Facture;
    }
    /**
     * @Route("/facture/{id}", name="oneFac", methods={"GET"})
     */
    public function OneFac(FactureRepository $resRep, Request $request, $id, EntityManagerInterface $em): Response
    {
        $res = $resRep->findOneBy(['id' => $id]);

        return $this->json($res, 202, [], ['groups' => 'form:reads']);
    }

    /**
     * @Route("/Facture", name="api_addfacture", methods={"POST"})
     */
    public function Add(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $jsonResp = $request->getContent();
        try {

            $form = $serializer->deserialize($jsonResp, Facture::class, 'json');
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
     * @Route("/Facture/{id}", name="api_addfacture", methods={"POST"})
     */
    public function AddFac($id, Request $request, ReservationRepository $rsRep, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $jsonResp = $request->getContent();
        $data = json_decode($request->getContent());
        try {

            $form = $serializer->deserialize($jsonResp, Facture::class, 'json');
            $form->setReservation($rsRep->findOneBy(['id' => $id]));
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
}
