<?php

namespace App\DataFixtures;

use App\Entity\Classroom;
use App\Entity\Lesson;
use App\Entity\Mark;
use App\Entity\Promotion;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Repository\PromotionRepository;
use App\Repository\StudentRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Firebase\JWT\JWT;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $params;
    private $promotionRepository;
    private $studentRepository;
    public function __construct(ContainerBagInterface $params , PromotionRepository $promotionRepository, StudentRepository $studentRepository){
        $this->params = $params;
        $this->promotionRepository = $promotionRepository;
        $this->studentRepository = $studentRepository;
    }

    public function MarksGeneration($manager){
        for ($i = 0; $i <= 20; $i++) {
            $mark = new Mark();
            $mark->setValue($i);
            $manager->persist($mark);
        }
    }

    public function TeacherLessonGenerator($manager){
        for ($i = 0; $i <= 3; $i++) {
            $year = 2017 + $i;

            $lesson = new Lesson();
            $lesson->setTitle("Cours numéro: ". $i);
            $lesson->setStartDate(new \DateTime($year.'-04-11'));
            $lesson->setEndDate(new \DateTime($year.'-04-16'));
            $manager->persist($lesson);

            $teacher = new Teacher();
            $teacher->setFirstName("First Name Teacher number: ". $i);
            $teacher->setLastName("Last Name Teacher number: ". $i);
            $teacher->setArrivedDate(new \DateTime("2018-04-16"));
            $teacher->addLesson($lesson);
            $manager->persist($teacher);
        }
    }

    public function StudentGeneration($manager)
    {
        for ($i = 0; $i < 5; $i++) {
            $year = 2017 + $i;
            $year_end = $year + 5;

            $promotion = new Promotion();
            $promotion->setStart(new \DateTime($year . '-03-11'));
            $promotion->setDateExit(new \DateTime($year_end . '-03-11'));
            $manager->persist($promotion);

            $student = new Student();
            $student->setFirstName("First Name student number " . $i);
            $student->setLastName("Last Name student number " . $i);
            $student->setArrivedDate(new \DateTime($year . '-03-11'));
            $student->setPromotion($promotion);
            $student->setAge(23);
            $manager->persist($student);

            $classroom = new Classroom();
            $classroom->setLabel("Promo 2023");
            $classroom->setDateEnd(new \DateTime($year_end . "-04-11"));
            $classroom->addStudent($student);
            $manager->persist($classroom);
        }
    }

    public function load(ObjectManager $manager)
    {
        $this->MarksGeneration($manager);
        $this->TeacherLessonGenerator($manager);
        $this->StudentGeneration($manager);
        $user = new User();
        $user->setEmail("karine.mousdik@devinci.fr");
        $user->setPassword("password");
        $payload = [
            "user" => $user->getUsername(),
            "exp" => (new \DateTime())->modify("+5 minutes")->getTimestamp(),
        ];
        $user->setApiToken(JWT::encode($payload, $this->params->get('jwt_secret'), 'HS256'));
        $user->setRoles(["ROLE_ADMIN"]);
        $manager->persist($user);
        $manager->flush();
    }
}
