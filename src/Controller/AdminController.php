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

// ***********************************************************************************************
// **                                      INDEX                                                **
// **                                                                                           **
// **                                CLASS MAIN ROUTE                                           **
// **                           Route("/admin", name="admin_")                                  **
// **                                                                                           **
// **                                  USERS MANAGER                                            **
// **                         Route("/listuser", name="listuser")                               **
// **                   Route("/editroleuser/{id}", name="editroleuser")                        **
// **                                                                                           **
// **                               CATEGORIES MANAGER                                          **
// **                 Route("/listcategories", name="listcategories")                           **
// **                 Route("/editcategory/{id}", name="editcategory")                          **
// **                Route("/deletecategory/{id}", name="deletecategory")                       **
// **                                                                                           **
// ***********************************************************************************************



/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * administration home 
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
     * list and search users
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
     * role user manager
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
     * list and add categories
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
     * update category
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
     * delete category
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
