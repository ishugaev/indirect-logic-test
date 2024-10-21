<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class UserAnswerCollection implements \IteratorAggregate
{
    /** @var UserAnswer[] */
    private ArrayCollection $userAnswers;

    public function __construct()
    {
        $this->userAnswers = new ArrayCollection();
    }

    public function addUserAnswer(UserAnswer $userAnswer): void
    {
        $this->userAnswers->add($userAnswer);
    }

    public function getIterator(): \Traversable
    {
        return $this->userAnswers;
    }
}
