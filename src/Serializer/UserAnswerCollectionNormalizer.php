<?php declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Question;
use App\Entity\UserAnswer;
use App\Entity\UserAnswerCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UserAnswerCollectionNormalizer implements DenormalizerInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function denormalize(mixed $data, string $class, string $format = null, array $context = []): mixed
    {
        $questionIds = array_keys($data);
        $dictionaryQuestions = $this->getQuestionsByIds($questionIds);

        $userAnswers = new UserAnswerCollection();
        foreach ($data as $questionId => $answerIds) {
            if (!isset($dictionaryQuestions[$questionId])) {
                throw new \InvalidArgumentException('Wrong question data input!');
            }

            $dictionaryQuestion = $dictionaryQuestions[$questionId];

            foreach ($answerIds as $answerId) {
                $userAnswer = new UserAnswer();
                $dictionaryAnswer = $dictionaryQuestion->getAnswerById((int) $answerId);
                if (null === $dictionaryAnswer) {
                    throw new \InvalidArgumentException('Wrong answer data input!');
                }

                $userAnswer->setQuestion($dictionaryQuestion);
                $userAnswer->setAnswer($dictionaryAnswer);
                $userAnswer->setIsCorrect($dictionaryAnswer->isCorrect());

                $userAnswers->addUserAnswer($userAnswer);
            }
        }

        return $userAnswers;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return UserAnswerCollection::class === $type;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            UserAnswerCollection::class => true,
        ];
    }

    private function getQuestionsByIds(array $questionIds): array
    {
        $questions = $this->entityManager->getRepository(Question::class)->findBy(['id' => $questionIds]);
        $result = [];
        foreach ($questions as $question) {
            $result[$question->getId()] = $question;
        }
        return $result;
    }
}
