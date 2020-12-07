<?php

namespace App\Controller;

use App\Entity\ShopCategories;
use App\Entity\User;
use App\Entity\UserSearch;
use App\Form\EditRoleUserType;
use App\Form\ShopCategoriesType;
use App\Form\UserSearchType;
use App\Repository\ShopCategoriesRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * ADMINISTRATION HOME 
     * @Route("/home", name="home")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'current_menu' => 'admin'
        ]);
    }

    /************************************** USERS MANAGER *****************************/
    /**
     * LIST AND SEARCH USERS
     * @Route("/listuser", name="listuser")
     *
     * @param UserRepository $userRepository
     */
    public function userList(Request $request, UserRepository $userRepository, PaginatorInterface $paginator)
    {
        $search = new UserSearch();
        $form = $this->createForm(UserSearchType::class,$search);
        $form->handleRequest($request);
        $usersList = $userRepository->findUserBySearch($search);
        $numberResultUser = count($usersList);
        $users = $paginator->paginate(
            $usersList,
            $request->query->getInt('page',1),
            5
        );
        return $this->render('admin/listuser.html.twig', [
            'users' => $users,
            'formSearch' => $form->createView(),
            'numberResultUser' => $numberResultUser
        ]);
    }
    /**
     * ROLE USER MANAGER
     * @Route("/editroleuser/{id}", name="editroleuser")
     *
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function editRoleUser(Request $request, User $user): Response
    {
        $form = $this->createForm(EditRoleUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();
            return $this->redirect($request->headers->get('referer'));
        }
        return $this->render('admin/editroleuser.html.twig', [
            'editRoleForm' => $form->createView(),
            'user' => $user
        ]);
    }

    /*********************************** CATEGORIES MANAGER ****************************/
    /**
     * LIST AND ADD CATEGORIES
     * @Route("/listcategories", name="listcategories")
     *
     * @param ShopCategoriesRepository $category
     * @return void
     */
    public function editCategories(Request $request, ShopCategoriesRepository $category){
        $newCategory = new ShopCategories();
        $categories = $category->findAll();
        $form = $this->createForm(ShopCategoriesType::class,$newCategory);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($newCategory);
            $manager->flush();
            return $this->redirectToRoute('admin_listcategories');
        }
        return $this->render('admin/listcategories.html.twig',[
            'categories' => $categories,
            'categoryForm' => $form->createView()
        ]);
    }

    /**
     * UPDATE CATEGORY
     * @Route("/editcategory/{id}", name="editcategory")
     *
     * @param ShopCategories $category
     * @return void
     */
    public function editCategory(Request $request, ShopCategories $category){
        $form = $this->createForm(ShopCategoriesType::class,$category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('admin_listcategories');
        }
        return $this->render('admin/editcategory.html.twig',[
            'categoryForm' => $form->createView()
        ]);
    }

    /**
     * DELETE CATEGORY
     * @Route("/deletecategory/{id}", name="deletecategory")
     *
     * @param ShopCategories $category
     * @return void
     */
    public function deleteCategory(ShopCategories $category){
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($category);
        $manager->flush();
        return $this->redirectToRoute('admin_listcategories');
    }
}
