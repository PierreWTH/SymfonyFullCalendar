<?php

namespace App\Controller;

use App\Entity\Calendar;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    #[Route('/api/{id}/edit', name: 'api_event_edit', methods:["PUT"])] // Put met a jour un enregistrement ou le crée si il n'existe pas
    public function majEvent(?Calendar $calendar, Request $resquest): Response // le ? permet de passer un Id qui n'existe potentiellement pas
    {
        // On récupere les données
        $donnees = json_decode($resquest->getContent());

        // On verifie toutes les données (obligé pour une requete PUT)
        if(
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->description) && !empty($donnees->description) &&
            isset($donnees->backgroundColor) && !empty($donnees->backgroundColor) &&
            isset($donnees->borderColor) && !empty($donnees->borderColor) &&
            isset($donnees->textColor) && !empty($donnees->textColor)
        ){
            // Les données sont complétées
            // On initialise un code

            $code = 200; // 200 corresponds a une mise a jour

            // On vérifie si l'id existe
            if(!$calendar){
                // On instancie un rendez vous 
                $calendar = new Calendar;
                // On change le code
                $code = 201; // Corresponds a une création
            }

            // On hydrate l'objet avec les données 
            $calendar->setTitle($donnees->title);
            $calendar->setDescription($donnees->description);
            $calendar->setStart(new DateTime($donnees->start));
            if($donnees->allDay){
                $calendar->setEnd(new DateTime($donnees->start));
            }
            else{
                $calendar->setEnd(new DateTime($donnees->end));
            }
            $calendar->setAllDay($donnees->allDay);
            $calendar->setBackgroundColor($donnees->backgroundColor);
            $calendar->setBorderColor($donnees->borderColor);
            $calendar->setTextColor($donnees->textColor);

            $em = $this->getDoctrine()->getManager();
            $em->persist($calendar);
            $em->flush();

            // On retourne le code 

            return new Response('OK', $code);
            
        }else{
            // Les données ne sont pas complètes
            return new Response('Données incomplètes', 404);
        }



        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
}


