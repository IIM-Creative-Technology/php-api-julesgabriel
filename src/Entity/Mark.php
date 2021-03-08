<?php

namespace App\Entity;

use App\Repository\MarkRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MarkRepository::class)
 */
class Mark
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Lesson::class, inversedBy="marks")
     */
    private $course;

    /**
     * @ORM\ManyToOne(targetEntity=Student::class, inversedBy="marks")
     */
    private $student;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourse(): ?Lesson
    {
        return $this->course;
    }

    public function setCourse(?Lesson $course): self
    {
        $this->course = $course;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }
}
