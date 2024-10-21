<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\TestResultRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestResultRepository::class)]
class TestResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\OneToMany(mappedBy: 'testResult', targetEntity: UserAnswer::class, cascade: ['persist', 'remove'])]
    private Collection $userAnswers;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->userAnswers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUserAnswers(): Collection
    {
        return $this->userAnswers;
    }

    public function addUserAnswer(UserAnswer $userAnswer): void
    {
        if (!$this->userAnswers->contains($userAnswer)) {
            $this->userAnswers->add($userAnswer);
        }
    }

    public function getCorrectlyAnsweredQuestions(): array
    {
        $answeredQuestions = [];

        foreach ($this->userAnswers as $userAnswer) {
            $question = $userAnswer->getQuestion();
            $questionId = $question->getId();

            if (!isset($answeredQuestions[$questionId])) {
                $answeredQuestions[$questionId] = $question;
            }
        }

        foreach ($this->userAnswers as $userAnswer) {
            if (!$userAnswer->isCorrect()) {
                // If any answer is incorrect, remove the question from correctly answered list
                unset($answeredQuestions[$userAnswer->getQuestion()->getId()]);
            }
        }

        return array_values($answeredQuestions);
    }

    public function getIncorrectlyAnsweredQuestions(): array
    {
        $res = [];

        /** @var UserAnswer $userAnswer */
        foreach ($this->userAnswers as $userAnswer) {
            if (!$userAnswer->isCorrect()) {
                $question = $userAnswer->getQuestion();
                $res[$question->getId()] = $userAnswer->getQuestion();
            }
        }

        return array_values($res);
    }
}
