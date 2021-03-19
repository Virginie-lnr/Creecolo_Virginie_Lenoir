<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Swift_Mailer;
use Swift_Message;

class ContactController extends AbstractController
{
    /**
     * Form to send a message to creecolo 
     * 
     * @Route("/contact", name="app_contact")
     */
    public function contactForm(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(ContactType::class); 
        $form->handleRequest($request); 
        
        if($form->isSubmitted() && $form->isValid()){
            $contactFormData = $form->getData();
            // dd($contactFormData['email']);
            $message = (new \Swift_Message)
                ->setSubject('New email from' . ' ' . $contactFormData['first_name'] . ' ' . $contactFormData['name'])
                ->setFrom('creecolo@gmail.com')
                ->setReplyTo($contactFormData['email'])
                ->setTo('creecolo@gmail.com')
                ->setBody($contactFormData['message'])
            ;

            $mailer->send($message);

            $this->addFlash('success', 
                'Your message has been sent successfully, we will get back to you as soon as possible 😊'
            );
        }

        return $this->render('contact/contact-form.html.twig', [
            'my_form' => $form->createView()
        ]);
    }
}
