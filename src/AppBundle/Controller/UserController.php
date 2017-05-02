<?php
/**
 * Created by PhpStorm.
 * User: manoj
 * Date: 28/4/17
 * Time: 5:24 PM
 */

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\User;
use AppBundle\Form\Type\Userform;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Form\Type;

class UserController extends Controller
{

    /**
     * @Route("/user/registeruser", name="registeruser")
     */
    public function registeruserAction(Request $request)
    {
       $user = new User();

       $user->setPassword('PASC123');
       $number = mt_rand(0, 100);
       $defaulUsername = "ABC".$number;
       $user->setUsername($defaulUsername);
       $user->setConfirmpass('PASC123');
       $user->setDateofbirth(new \DateTime('1988-05-05 16:35:25'));


        $form = $this->createFormBuilder($user)
            ->add('password', TextType::class)
            ->add('username', TextType::class)
            ->add('confirmpass', TextType::class)
            ->add('dateofbirth', DateType::class)
            ->add('save', SubmitType::class, array('label' => 'SAVE USER'))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $user = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            // $em = $this->getDoctrine()->getManager();
            // $em->persist($task);
            // $em->flush();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('usersuccess');
        }

       //$em = $this->getDoctrine()->getManager();

        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        //$em->persist($user);

        // actually executes the queries (i.e. the INSERT query)
       // $em->flush();

        //return new Response('Saved new User with id '.$user->getId());
       // return $this->render('user/registeruser.html.twig',['userid' => $user->getId()]);
        return $this->render('user/registeruser.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/user/usersuccess", name="usersuccess")
     */

    public  function usersuccessAction(Request $request)
    {




        $usersdetails = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAll();



        return $this->render('user/usersucess.html.twig', array(
            'users' => $usersdetails,
        ));
    }

    /**
     * @Route("/user/edit/{id}", name="useredit")
     * @Method({"GET", "POST"})
     */
    public  function usereditAction($id,Request $request)
    {


        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');


        $userdetails = $userRepository->find($id);

        if (!$userdetails) {
            throw $this->createNotFoundException('Unable to find Profile entity.');
        }
        $form = $this->createForm(Userform::class, $userdetails);
        $form->handleRequest($request);


        if ($form->isValid()) {
            $userRepository->persist($userdetails);


            return $this->redirect($this->generateUrl('usersuccess', array('id' => $id)));
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/delete/{id}", name="userdelete")
     */
    public  function userdeleteAction(Request $request)
    {
        return $this->render('user/edit.html.twig', array(

        ));
    }

}