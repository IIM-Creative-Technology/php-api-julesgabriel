<?php

namespace App\Controller;

use App\Entity\Promotion;
use App\Entity\Student;
use App\Entity\User;
use App\Form\PromotionType;
use App\Repository\ClassroomRepository;
use App\Repository\LessonRepository;
use App\Repository\MarkRepository;
use App\Repository\PromotionRepository;
use App\Repository\StudentRepository;
use App\Repository\TeacherRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    private $objectManager;

    public function __construct(EntityManagerInterface $objectManager, RequestStack $request)
    {
        $apiToken = $request->getCurrentRequest()->headers->get('api_token');
        $user = $objectManager->getRepository(User::class)->findOneBy([
            'api_token' => $apiToken,
        ]);

        if (!$user instanceof User) {
            throw new HttpException(401, 'Unauthorized');
        }
    }

    public function findAllElement($arg)
    {
        $elements = $arg->findAll();
        return $this->json($elements);
    }

    /**
     * @Route("/promotion", name="promotion_findAll", methods={"GET"})
     * @param PromotionRepository $promotionRepository
     * @return Response
     */

    public function findAllPromotion(PromotionRepository $promotionRepository): Response
    {
        return $this->findAllElement($promotionRepository);
    }

    /**
     * @Route("/mark", name="mark_findAll", methods={"GET"})
     * @param MarkRepository $markRepository
     * @return Response
     */

    public function findAllMarks(MarkRepository $markRepository): Response
    {
        return $this->findAllElement($markRepository);
    }

    /**
     * @Route("/student", name="student_findAll", methods={"GET"})
     * @param StudentRepository $studentRepository
     * @return Response
     */

    public function findAllStudent(StudentRepository $studentRepository): Response
    {
        return $this->findAllElement($studentRepository);
    }

    /**
     * @Route("/teacher", name="teacher_findAll", methods={"GET"})
     * @param TeacherRepository $teacherRepository
     * @return Response
     */

    public function findAllTeacher(TeacherRepository $teacherRepository): Response
    {
        return $this->findAllElement($teacherRepository);
    }

    /**
     * @Route("/lesson", name="lesson_findAll", methods={"GET"})
     * @param LessonRepository $lessonRepository
     * @return Response
     */

    public function findAllLesson(LessonRepository $lessonRepository): Response
    {
        return $this->findAllElement($lessonRepository);
    }

    /**
     * @Route("/classroom", name="classroom_findAll", methods={"GET"})
     * @param ClassroomRepository $classroomRepository
     * @return Response
     */

    public function findAllClassroom(ClassroomRepository $classroomRepository): Response
    {
        return $this->findAllElement($classroomRepository);
    }

    /**
     * @Route("/user", name="user_findAll", methods={"GET"})
     * @param UserRepository $userRepository
     * @return Response
     */

    public function findAllUser(UserRepository $userRepository): Response
    {
        return $this->findAllElement($userRepository);
    }

    /**
     * @Route("/promotion/new", name="promotion_new", methods={"GET","POST"})
     */
    public function newPromotion(Request $request): Response
    {
        $start = $request->get('start');
        $exit = $request->get('date_exit');
        $promotion = new Promotion();
        $promotion->setStart(new \DateTime($start));
        $promotion->setDateExit(new \DateTime($exit));
        $em = $this->getDoctrine()->getManager();
        $em->persist($promotion);
        $em->flush();
        return $this->json([
            'success' => "YES",
            'start' => $promotion->getStart(),
            'exit' => $promotion->getDateExit()
        ]);
    }

    /**
     * @Route("/student/new", name="student_new", methods={"GET","POST"})
     */
    public function newStudent(Request $request): Response
    {
        $firstName = $request->get('first_name');
        $lastName = $request->get('last_name');
        $age = $request->get('age');
        $date = $request->get('arrived_date');
        $student = new Student();
        $student->setFirstName($firstName);
        $student->setLastName($lastName);
        $student->setAge($age);
        $student->setArrivedDate(new \DateTime($date));
        $em = $this->getDoctrine()->getManager();
        $em->persist($student);
        $em->flush();
        return $this->json([
            'success' => "YES",
            'first_name' => $student->getFirstName(),
            'last_name' => $student->getLastName()
        ]);
    }

    public function findOneByElement($arg, $id)
    {
        $element = $arg->find($id);
        return $this->json($element);
    }

    /**
     * @Route("/promotion/{id}", name="promotion_show", methods={"GET"})
     */
    public function promotionFindOneBy(PromotionRepository $promotionRepository, $id): Response
    {
        return $this->findOneByElement($promotionRepository, $id);
    }

    /**
     * @Route("/user/{id}", name="user_show", methods={"GET"})
     */
    public function userFindOneBy(UserRepository $userRepository, $id): Response
    {
        return $this->findOneByElement($userRepository, $id);
    }

    /**
     * @Route("/teacher/{id}", name="teacher_show", methods={"GET"})
     */
    public function teacherFindOneBy(TeacherRepository $teacherRepository, $id): Response
    {
        return $this->findOneByElement($teacherRepository, $id);
    }

    /**
     * @Route("/student/{id}", name="student_show", methods={"GET"})
     */
    public function studentFindOneBy(StudentRepository $studentRepository, $id): Response
    {
        return $this->findOneByElement($studentRepository, $id);
    }

    /**
     * @Route("/mark/{id}", name="mark_show", methods={"GET"})
     */
    public function markFindOneBy(MarkRepository $markRepository, $id): Response
    {
        return $this->findOneByElement($markRepository, $id);
    }

    /**
     * @Route("/lesson/{id}", name="lesson_show", methods={"GET"})
     */
    public function lessonFindOneBy(LessonRepository $lessonRepository, $id): Response
    {
        return $this->findOneByElement($lessonRepository, $id);
    }

    /**
     * @Route("/classroom/{id}", name="lesson_show", methods={"GET"})
     */
    public function classroomFindOneBy(ClassroomRepository $classroomRepository, $id): Response
    {
        return $this->findOneByElement($classroomRepository, $id);
    }
}

