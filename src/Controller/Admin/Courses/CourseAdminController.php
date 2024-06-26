<?php

namespace App\Controller\Admin\Courses;

use App\Entity\Course;
use App\Form\course\CourseType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route("/admin/course", name: 'admin.course.')]
#[IsGranted('ROLE_ADMIN')]
class CourseAdminController extends AbstractController
{
    public function __construct(
        private readonly CourseRepository $courseRepository
    )
    {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render("/admin/course/course.html.twig", [
            'courses' => $this->courseRepository->findCourseByRecipe()
        ]);
    }

    #[Route('[/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Course $course, Request $request, EntityManagerInterface $em): RedirectResponse|Response
    {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The course has been edited');
            return $this->redirectToRoute('admin.course.index');
        }
        return $this->render('admin/course/edit.html.twig', [
            'course' => $course,
            'form' => $form
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($course);
            $em->flush();
            $this->addFlash('success', 'New course created');
            return $this->redirectToRoute('admin.course.index');
        }
        return $this->render('admin/course/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Course $course, EntityManagerInterface $em): RedirectResponse
    {
        $em->remove($course);
        $em->flush();
        $this->addFlash('success', 'Course deleted');
        return $this->redirectToRoute('admin.course.index');
    }
}