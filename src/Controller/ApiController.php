<?php

namespace App\Controller;

use App\Entity\Classroom;
use App\Entity\Lesson;
use App\Entity\Mark;
use App\Entity\Promotion;
use App\Entity\Student;
use App\Entity\Teacher;
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

    public function findOneByElement($arg, $id)
    {
        $element = $arg->find($id);
        return $this->json($element);
    }

    public function deleteElement($arg, $id){
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($arg);
        $entityManager->flush();
        return $this->json([
            'success' => "Promotion with id: " . $id . " successfully deleted",
        ]);
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
     * @Route("/promotion/{id}", name="promotion_show", methods={"GET"})
     */
    public function promotionFindOneBy(PromotionRepository $promotionRepository, $id): Response
    {
        return $this->findOneByElement($promotionRepository, $id);
    }

    /**
     * @Route("/promotion/new", name="promotion_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function newPromotion(Request $request): Response
    {
        $start = $request->get('date_start');
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
     * @Route("/promotion/{id}", name="promotion_delete", methods={"DELETE"})
     * @param Promotion $promotion
     * @param $id
     * @return Response
     */

    public function deletePromotion(Promotion $promotion, $id): Response
    {
        return $this->deleteElement($promotion, $id);
    }

    /**
     * @Route("/promotion/edit/{id}", name="promotion_edit", methods={"GET","PUT"})
     * @param Request $request
     * @param Promotion $promotion
     * @param $id
     * @param PromotionRepository $promotionRepository
     * @return Response
     */

    public function editPromotion(Request $request, Promotion $promotion, $id, PromotionRepository $promotionRepository): Response
    {
        $element = $promotionRepository->find($id);
        $element->setStart(new \DateTime($request->get('date_start')));
        $element->setDateExit(new \DateTime($request->get('date_exit')));
        $em = $this->getDoctrine()->getManager();
        $em->persist($element);
        $em->flush();
        return $this->json([
            'success' => "Success !"
        ]);
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
     * @Route("/mark/{id}", name="mark_show", methods={"GET"})
     */

    public function markFindOneBy(MarkRepository $markRepository, $id): Response
    {
        return $this->findOneByElement($markRepository, $id);
    }

    /**
     * @Route("/mark/edit/{id}", name="promotion_edit", methods={"GET","PUT"})
     * @param Request $request
     * @param $id
     * @param MarkRepository $markRepository
     * @return Response
     */

    public function editMark(Request $request, $id, MarkRepository $markRepository): Response
    {
        $element = $markRepository->find($id);
        $element->setValue($request->get('value'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($element);
        $em->flush();
        return $this->json([
            'success' => "Success !"
        ]);
    }

    /**
     * @Route("/mark/{id}", name="mark_delete", methods={"DELETE"})
     * @param Mark $mark
     * @param $id
     * @return Response
     */

    public function deleteMark(Mark $mark, $id): Response
    {
        return $this->deleteElement($mark, $id);
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
     * @Route("/student/{id}", name="student_show", methods={"GET"})
     */
    public function studentFindOneBy(StudentRepository $studentRepository, $id): Response
    {
        return $this->findOneByElement($studentRepository, $id);
    }

    /**
     * @Route("/student/new", name="student_new", methods={"GET","POST"})
     * @param Request $request
     * @param PromotionRepository $promotionRepository
     * @return Response
     * @throws \Exception
     */
    public function newStudent(Request $request, PromotionRepository $promotionRepository): Response
    {
        $firstName = $request->get('first_name');
        $lastName = $request->get('last_name');
        $age = $request->get('age');
        $date = $request->get('arrived_date');
        $idPromotion = $request->get('promotion');
        $student = new Student();
        $student->setFirstName($firstName);
        $student->setLastName($lastName);
        $student->setAge($age);
        $student->setPromotion($promotionRepository->find($idPromotion));
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

    /**
     * @Route("/student/edit/{id}", name="student_edit", methods={"GET","PUT"})
     * @param Request $request
     * @param $id
     * @param MarkRepository $markRepository
     * @return Response
     */

    public function editStudent(Request $request, $id, StudentRepository $studentRepository): Response
    {
        $element = $studentRepository->find($id);
        $element->setFirstName($request->get('first_name'));
        $element->setLastName($request->get('last_name'));
        $element->setClassroom($request->get('classroom'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($element);
        $em->flush();
        return $this->json([
            'success' => "Success !"
        ]);
    }

    /**
     * @Route("/student/{id}", name="student_delete", methods={"DELETE"})
     * @param Mark $mark
     * @param $id
     * @return Response
     */

    public function deleteStudent(Student $student, $id): Response
    {
        return $this->deleteElement($student, $id);
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
     * @Route("/teacher/{id}", name="teacher_show", methods={"GET"})
     */
    public function teacherFindOneBy(TeacherRepository $teacherRepository, $id): Response
    {
        return $this->findOneByElement($teacherRepository, $id);
    }

    /**
     * @Route("/teacher/new", name="teacher_new", methods={"GET","POST"})
     * @param Request $request
     * @param LessonRepository $lessonRepository
     * @return Response
     * @throws \Exception
     */
    public function newTeacher(Request $request, LessonRepository $lessonRepository): Response
    {
        $firstName = $request->get('first_name');
        $lastName = $request->get('last_name');
        $lesson = $request->get('lesson');
        $date = $request->get('arrived_date');
        $teacher = new Teacher();
        $teacher->setFirstName($firstName);
        $teacher->setLastName($lastName);
        $teacher->setArrivedDate(new \DateTime($date));
        $teacher->addLesson($lessonRepository->find($lesson));
        $em = $this->getDoctrine()->getManager();
        $em->persist($teacher);
        $em->flush();
        return $this->json([
            'success' => "YES",
            'first_name' => $teacher->getFirstName(),
            'last_name' => $teacher->getLastName()
        ]);
    }

    /**
     * @Route("/teacher/edit/{id}", name="student_edit", methods={"GET","PUT"})
     * @param Request $request
     * @param $id
     * @param TeacherRepository $teacherRepository
     * @return Response
     */

    public function editTeacher(Request $request, $id, TeacherRepository $teacherRepository): Response
    {
        $element = $teacherRepository->find($id);
        $element->setFirstName($request->get('first_name'));
        $element->setLastName($request->get('last_name'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($element);
        $em->flush();
        return $this->json([
            'success' => "Success !"
        ]);
    }

    /**
     * @Route("/teacher/{id}", name="student_delete", methods={"DELETE"})
     * @param Teacher $teacher
     * @param $id
     * @return Response
     */

    public function deleteTeacher(Teacher $teacher, $id): Response
    {
        return $this->deleteElement($teacher, $id);
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
     * @Route("/user/{id}", name="user_show", methods={"GET"})
     * @param UserRepository $userRepository
     * @param $id
     * @return Response
     */
    public function userFindOneBy(UserRepository $userRepository, $id): Response
    {
        return $this->findOneByElement($userRepository, $id);
    }

    /**
     * @Route("/user/edit/{id}", name="user_edit", methods={"GET","PUT"})
     * @param Request $request
     * @param $id
     * @param TeacherRepository $teacherRepository
     * @return Response
     */

    public function editUser(Request $request, $id, UserRepository $userRepository): Response
    {
        $element = $userRepository->find($id);
        $element->setEmail($request->get('first_name'));
        $element->setPassword($request->get('last_name'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($element);
        $em->flush();
        return $this->json([
            'success' => "Success !"
        ]);
    }

    /**
     * @Route("/user/{id}", name="user_delete", methods={"DELETE"})
     * @param User $user
     * @param $id
     * @return Response
     */

    public function deleteUser(User $user, $id): Response
    {
        return $this->deleteElement($user, $id);
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
     * @Route("/lesson/{id}", name="lesson_show", methods={"GET"})
     */
    public function lessonFindOneBy(LessonRepository $lessonRepository, $id): Response
    {
        return $this->findOneByElement($lessonRepository, $id);
    }

    /**
     * @Route("/lesson/new", name="lesson_new", methods={"GET","POST"})
     * @param Request $request
     * @param LessonRepository $lessonRepository
     * @param TeacherRepository $teacherRepository
     * @param ClassroomRepository $classroomRepository
     * @return Response
     * @throws \Exception
     */
    public function newLesson(Request $request, LessonRepository $lessonRepository, TeacherRepository $teacherRepository, ClassroomRepository $classroomRepository): Response
    {
        $lesson = new Lesson();
        $lesson->setTitle($request->get('title'));
        $lesson->setTeacher($teacherRepository->find($request->get('teacher')));
        $lesson->setClassroom($classroomRepository->find($request->get('classroom')));
        $lesson->setStartDate(new \DateTime($request->get('start_date')));
        $lesson->setEndDate(new \DateTime($request->get('end_date')));
        $em = $this->getDoctrine()->getManager();
        $em->persist($lesson);
        $em->flush();
        return $this->json([
            'success' => "YES",
            'title' => $lesson->getTitle(),
            'last_name' => $lesson->getClassroom()
        ]);
    }

    /**
     * @Route("/lesson/edit/{id}", name="student_edit", methods={"GET","PUT"})
     * @param Request $request
     * @param $id
     * @param LessonRepository $lessonRepository
     * @return Response
     */

    public function editLesson(Request $request, $id, LessonRepository $lessonRepository, TeacherRepository $teacherRepository, ClassroomRepository $classroomRepository): Response
    {
        $element = $lessonRepository->find($id);
        $element->setTitle($request->get('title'));
        $element->setTeacher($teacherRepository->find($request->get('teacher')));
        $element->setClassroom($classroomRepository->find($request->get('classroom')));
        $element->setStartDate(new \DateTime($request->get('start_date')));
        $element->setEndDate(new \DateTime($request->get('end_date')));
        $em = $this->getDoctrine()->getManager();
        $em->persist($element);
        $em->flush();
        return $this->json([
            'success' => "Success !"
        ]);
    }

    /**
     * @Route("/lesson/{id}", name="student_delete", methods={"DELETE"})
     * @param Lesson $lesson
     * @param $id
     * @return Response
     */

    public function deleteLesson(Lesson $lesson, $id): Response
    {
        return $this->deleteElement($lesson, $id);
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
     * @Route("/classroom/{id}", name="lesson_show", methods={"GET"})
     */
    public function classroomFindOneBy(ClassroomRepository $classroomRepository, $id): Response
    {
        return $this->findOneByElement($classroomRepository, $id);
    }

    /**
     * @Route("/classroom/new", name="lesson_new", methods={"GET","POST"})
     * @param Request $request
     * @param LessonRepository $lessonRepository
     * @param StudentRepository $studentRepository
     * @param ClassroomRepository $classroomRepository
     * @return Response
     * @throws \Exception
     */
    public function newClassroom(Request $request, LessonRepository $lessonRepository, StudentRepository $studentRepository): Response
    {
        $classroom = new Classroom();
        $classroom->setLabel($request->get('title'));
        $classroom->setDateEnd(new \DateTime($request->get('date_end')));
        $classroom->addLesson($lessonRepository->find($request->get('lesson')));
        $classroom->addStudent($studentRepository->find($request->get('student')));
        $em = $this->getDoctrine()->getManager();
        $em->persist($classroom);
        $em->flush();
        return $this->json([
            'success' => "YES",
            'title' => $classroom->getLabel(),
        ]);
    }

    /**
     * @Route("/classroom/edit/{id}", name="student_edit", methods={"GET","PUT"})
     * @param Request $request
     * @param $id
     * @param LessonRepository $lessonRepository
     * @param StudentRepository $studentRepository
     * @param ClassroomRepository $classroomRepository
     * @return Response
     * @throws \Exception
     */

    public function editClassroom(Request $request, $id, LessonRepository $lessonRepository, StudentRepository $studentRepository, ClassroomRepository $classroomRepository): Response
    {
        $element = $classroomRepository->find($id);
        $element->setLabel($request->get('title'));
        $element->setDateEnd(new \DateTime($request->get('date_end')));
        $element->addLesson($lessonRepository->find($request->get('lesson')));
        $element->addStudent($studentRepository->find($request->get('student')));
        $em = $this->getDoctrine()->getManager();
        $em->persist($element);
        $em->flush();
        return $this->json([
            'success' => "Success !"
        ]);
    }

    /**
     * @Route("/classroom/{id}", name="student_delete", methods={"DELETE"})
     * @param Classroom $classroom
     * @param $id
     * @return Response
     */

    public function deleteClassroom(Classroom $classroom, $id): Response
    {
        return $this->deleteElement($classroom, $id);
    }
}

