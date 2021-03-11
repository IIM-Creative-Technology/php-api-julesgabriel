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
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Firebase\JWT\JWT;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $params;
    private $promotionRepository;
    public function __construct(ContainerBagInterface $params , PromotionRepository $promotionRepository){
        $this->params = $params;
        $this->promotionRepository = $promotionRepository;
    }

    public function load(ObjectManager $manager)
    {

        for ($i = 0; $i <= 20; $i++) {
            $mark = new Mark();
            $mark->setValue($i);
            $manager->persist($mark);
        }

        for ($i = 0; $i <= 3; $i++) {
            $teacher = new Teacher();
            $teacher->setFirstName("Pierre");
            $teacher->setLastName("Grimaud");
            $teacher->setArrivedDate(new \DateTime("2018-04-16"));
            $manager->persist($teacher);

            $lesson = new Lesson();
            $lesson->setTitle("Cours de PHP");
            $lesson->setStartDate(new \DateTime('2018-04-11'));
            $lesson->setEndDate(new \DateTime('2018-04-16'));
            $manager->persist($lesson);

            $classroom = new Classroom();
            $classroom->setLabel("Promo 2023");
            $classroom->setDateEnd(new \DateTime("2023-04-11"));
            $manager->persist($classroom);
        }

        $promotion = new Promotion();
        $promotion->setStart(new \DateTime('2018-03-11'));
        $promotion->setDateExit(new \DateTime('2023-03-11'));
        $manager->persist($promotion);

        $student = new Student();
        $student->setFirstName("Jules");
        $student->setLastName("DAYAUX");
        $student->setArrivedDate(new \DateTime('2018-03-11'));
        $student->setPromotion($this->promotionRepository->find(1));
        $student->setAge(23);
        $manager->persist($student);

        $user = new User();
        $user->setEmail("jules.dayaux@mail.com");
        $user->setPassword("password");
        $payload = [
            "user" => $user->getUsername(),
            "exp" => (new \DateTime())->modify("+5 minutes")->getTimestamp(),
        ];
        $user->setApiToken(JWT::encode($payload, $this->params->get('jwt_secret'), 'HS256'));
        $user->setRoles(["ROLE_USER"]);
        $manager->persist($user);

        $manager->flush();
    }
}
