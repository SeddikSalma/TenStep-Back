<?php

namespace App\Controller;

use DateTimeInterface;
use App\Entity\Reservation;
use App\Entity\Devis;
use App\Entity\Facture;
use App\Entity\Formateur;
use App\Entity\Participant;
use App\Entity\Formation;
use App\Repository\DevisRepository;
use App\Repository\FactureRepository;
use App\Repository\FormateurRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ParticipantRepository;
use App\Repository\ReservationRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;

class ApiReservationController extends AbstractController
{
    /**
     * @Route("/reservation", name="api_reservation", methods={"GET"})
     */
    public function lister(ReservationRepository $resRep, NormalizerInterface $norm): Response
    {

        // dd($res);
        /*$resNorm = $norm->normalize($res, 'json', ['groups' => 'read']);
        dd($resNorm);$classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new PropertyNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);
        $res = $resRep->findAll();
        $resNorm =  $serializer->normalize($res, 'json', ['groups' => 'read']);
        dd($resNorm);
        $json = json_encode($resNorm);
        $data = json_decode($json, true);
        dd($data['id']);
        $resp = new Response($json, 200, ["Content-Type" => "application/json"]);
        return $resp;*/

        $res = $resRep->findAll();
        //dd($res);

        return $this->json($res, 200, [], ['groups' => ['form:reads']]);
    }

    /**
     * @Route("/reservations", name="apie", methods={"POST"})
     */
    public function Add(Request $request, FormateurRepository $fRep, ParticipantRepository $pRep, FactureRepository $facRep, DevisRepository $dRep, FormationRepository $forRep, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $jsonResp = $request->getContent();
        $data = json_decode($jsonResp, true);


        //dd($facRep->findOneBy(['id' => $fac]), $dRep->findOneBy(['id' => $d]), $forRep->findOneBy(['id' => $for]));
        try {
            //dd($data);
            $for = $data['formation'];
            //  $d = $data['devis'];
            // $fac = $data['facture'];
            // $fo = $data['formateur'];
            $part = $data['participant'];
            $form = $serializer->deserialize($jsonResp, Reservation::class, 'json');
            //dd(gettype($form->getDateDeb()));
            $error = $validator->validate($form);
            if (count($error) > 0) {
                return $this->json($error, 400);
            }
            // $form->setDevis($dRep->findOneBy(['id' => $d]));
            // $form->setFacture($facRep->findOneBy(['id' => $fac]));
            $form->setFormation($forRep->findOneBy(['id' => $for]));
            // $form->setFacture($facRep->findOneBy(['id' => $fac]));
            // $form->setDevis($dRep->findOneBy(['id' => $d]));
            // $form->setFormateur($fRep->findOneBy(['id' => $fo]));
            $form->setParticipant($pRep->findOneBy(['id' => $part]));
            // $form->setFormateur(null);
            $em->persist($form);
            $em->flush();
            return $this->json($form, 201, [], ['groups' => 'form:reads']);
        } catch (NotEncodableValueException $e) {
            return $this->json(['status' => 400, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/reservation/{id}", name="resUpdate", methods={"PUT"})
     */
    public function update(ReservationRepository $resRep, FactureRepository $facRep, ParticipantRepository $pRep, DevisRepository $dRep, FormationRepository $forRep, Request $request, $id, EntityManagerInterface $em): JsonResponse
    {;
        $Resrvation = $resRep->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);
        //dd(gettype($data['dateDeb']));
        //$time = strtotime($data['dateDeb']);
        // $newformat = date('Y-m-d\TH:i:sO', $time);
        //dd(new \DateTime($data['dateDeb']));
        // dd(dateType::date('Y-m-d h:i:s', strtotime($data['dateDeb'])));
        //DateTime::createFromFormat('Y-m-d', $data['dateDeb']);
        // dd($resRep->findOneBy(['id' => $id]));
        empty($data['dateDeb']) ? true : $Resrvation->setDateDeb(new \DateTime($data['dateDeb']));
        empty($data['dateFin']) ? true : $Resrvation->setDateFin(new \DateTime($data['dateFin']));
        empty($data['devis']) ? true : $Resrvation->setDevis($dRep->findOneBy(['id' => $data['devis']]));
        empty($data['facture']) ? true : $Resrvation->setFacture($facRep->findOneBy(['id' => $data['facture']]));
        empty($data['formation']) ? true : $Resrvation->setFormation($forRep->findOneBy(['id' => $data['formation']]));
        empty($data['formateur']) ? true : $Resrvation->setFormation($forRep->findOneBy(['id' => $data['formateur']]));
        empty($data['participant']) ? true : $Resrvation->setParticipant($pRep->findOneBy(['id' => $data['participant']]));
        $em->persist($Resrvation);
        $em->flush();

        //$resp = new Response(json_encode($updatedResrvation), 200, ["Content-Type" => "application/json"]);
        return new JsonResponse(json_encode($Resrvation), Response::HTTP_OK);
        //return $resp;
    }


    /**
     * @Route("/reservation/Formateur/{id}", name="api_stor", methods={"PUT"})
     */
    public function UpdateFormateur(ReservationRepository $resRep, FormateurRepository $forRep, Request $request, $id, EntityManagerInterface $em)
    {
        $Resrvation = $resRep->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);
        empty($data['formateur']) ? true : $Resrvation->setFormateur($forRep->findOneBy(['id' => $data['formateur']]));
        $em->persist($Resrvation);
        $em->flush();
        $Resrvation = $resRep->findOneBy(['id' => $id]);

        return $this->json($Resrvation, 200, [], ['groups' => ['form:reads']]);
    }

    /**
     * @Route("/reservation/{id}", name="api_sto", methods={"DELETE"})
     */
    public function delete(ReservationRepository $resRep, Request $request, $id, EntityManagerInterface $em): Response
    {
        $res = $resRep->findOneBy(['id' => $id]);
        $em->remove($res);
        $em->flush();
        $json = [
            'Deleted' => $res,
            'message' => 'sucess'
        ];
        return $this->json($json, 202, [], ['groups' => 'res']);
    }
    /**
     * @Route("/reservation/{id}", name="oneRes", methods={"GET"})
     */
    public function OneRes(ReservationRepository $resRep, Request $request, $id, EntityManagerInterface $em): Response
    {
        $res = $resRep->findOneBy(['id' => $id]);

        return $this->json($res, 202, [], ['groups' => 'form:reads']);
    }

    /**
     * @Route("reservation/Devis/{id}", name="ResDevis", methods={"GET"})
     */
    public function ResDevis(DevisRepository $forRep, ReservationRepository $resRep, $id, NormalizerInterface $norm): Response
    {
        $res = $resRep->findOneBy(['id' => $id]);
        //dd($res, $res->getDevis()->getId());
        $Devis = $forRep->findOneBy(['id' => $res->getDevis()->getId()]);
        //dd($Devis);
        return $this->json($Devis, 200, [], ['groups' => ['form:reads']]);
    }
    /**
     * @Route("reservation/facture/{id}", name="ResFac", methods={"GET"})
     */
    public function ResFac(FactureRepository $forRep, ReservationRepository $resRep, $id, NormalizerInterface $norm): Response
    {
        $res = $resRep->findOneBy(['id' => $id]);
        //dd($res, $res->getDevis()->getId());
        $Devis = $forRep->findOneBy(['id' => $res->getDevis()->getId()]);
        //dd($Devis);
        return $this->json($Devis, 200, [], ['groups' => ['form:reads']]);
    }
    /**
     * @Route("reservation/formation/{id}", name="ResFormation", methods={"GET"})
     */
    public function ResFormation(FormationRepository $forRep, ReservationRepository $resRep, $id, NormalizerInterface $norm): Response
    {
        $res = $resRep->findOneBy(['id' => $id]);
        //dd($res, $res->getDevis()->getId());
        $Devis = $forRep->findOneBy(['id' => $res->getDevis()->getId()]);
        //dd($Devis);
        return $this->json($Devis, 200, [], ['groups' => ['form:reads']]);
    }
    /**
     * @Route("reservation/formateur/{id}", name="ResFormateur", methods={"GET"})
     */
    public function ResFormateur(FormateurRepository $forRep, ReservationRepository $resRep, $id, NormalizerInterface $norm): Response
    {
        $res = $resRep->findOneBy(['id' => $id]);
        //dd($res, $res->getDevis()->getId());
        $Devis = $forRep->findOneBy(['id' => $res->getDevis()->getId()]);
        //dd($Devis);
        return $this->json($Devis, 200, [], ['groups' => ['form:reads']]);
    }

    /**
     * @Route("reservation/Devis/{id}", name="AddResDevis", methods={"POST"})
     */
    public function AddResDevis(Request $request, DevisRepository $DRep, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator, ReservationRepository $resRep, $id, NormalizerInterface $norm): Response
    {
        $jsonResp = $request->getContent();
        $data = json_decode($request->getContent(), true);

        $res = $resRep->findOneBy(['id' => $id]);
        //dd($res, $res->getDevis()->getId());
        $Devis = $DRep->findOneBy(['id' => $res->getDevis()->getId()]);
        try {
            $form = $serializer->deserialize($jsonResp, Devis::class, 'json');
            dd($form);
        } catch (NotEncodableValueException $e) {
            return $this->json(['status' => 400, 'message' => $e->getMessage()], 400);
        }
        // dd($Devis);
        return $this->json($Devis, 200, [], ['groups' => ['form:list']]);
    }
}
