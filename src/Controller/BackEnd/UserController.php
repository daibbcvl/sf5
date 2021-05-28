<?php

namespace App\Controller\BackEnd;

use App\Entity\User;
use App\Form\Type\UserCreateType;
use App\Form\Type\UserEditType;
use App\Form\Type\UserSearchType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/users")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="user_index", methods={"GET"})
     *
     * @param Request        $request
     * @param UserRepository $userRepository
     *
     * @return Response
     */
    public function index(Request $request, UserRepository $userRepository): Response
    {


        $form = $this->createForm(UserSearchType::class);


        $form->handleRequest($request);
        $criteria = $form->getData() ?: [];
        $page = $request->get('page', 1);
        $size = $request->get('size', 10);
        $sort = $request->get('sort', ['id' => 'desc']);


        return $this->render('backend/user/index.html.twig', [
            'form' => $form->createView(),
            'pager' => $userRepository->search($criteria, $sort)->setMaxPerPage($size)->setCurrentPage($page),
        ]);
    }

    /**
     * @Route("/create", name="user_create", methods={"GET", "POST"})
     *
     * @param Request                      $request
     * @param EntityManagerInterface       $entityManager
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserCreateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, $user->getPlainPassword()));
            $entityManager->persist($user);
            $entityManager->flush();
            $user->eraseCredentials();

            $this->addFlash('success', 'Create user successfully.');
            if ($url = $request->get('redirect_url')) {
                return $this->redirect($url);
            }

            return $this->redirectToRoute('user_create');
        }

        return $this->render('backend/user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="user_edit", methods={"GET", "PUT"})
     *
     * @param Request                $request
     * @param User                   $user
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Edit user successfully.');

            if ($url = $request->get('redirect_url')) {
                return $this->redirect($url);
            }
        }

        $warning = null;
        if ($user === $this->getUser()) {
            $warning = 'This is your account.';
        }

        return $this->render('backend/user/edit.html.twig', [
            'form' => $form->createView(),
            'warning' => $warning,
        ]);
    }
}
