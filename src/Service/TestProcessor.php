<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\TestResult;
use App\Entity\UserAnswerCollection;
use Doctrine\ORM\EntityManagerInterface;

class TestProcessor
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function processTest(UserAnswerCollection $userAnswerCollection): TestResult
    {
        $testResult = new TestResult();

        foreach ($userAnswerCollection as $userAnswer) {
            $userAnswer->setTestResult($testResult);
            $testResult->addUserAnswer($userAnswer);
            $this->entityManager->persist($userAnswer);
        }

        $this->entityManager->persist($testResult);
        $this->entityManager->flush();

        return $testResult;
    }
}
