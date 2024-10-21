<?php declare(strict_types=1);

namespace App\Controller;

use App\Repository\QuestionRepository;
use App\Service\TestProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\UserAnswerCollection;

class TestController extends AbstractController
{
    #[Route('/test', name: 'test')]
    public function index(QuestionRepository $questionRepository): Response
    {
        $questions = $questionRepository->findAllRandomOrder();

        foreach ($questions as $question) {
            $question->reshuffleAnswers();
        }

        return $this->render('test/index.html.twig', [
            'questions' => $questions,
        ]);
    }

    #[Route('/test/submit', name: 'test_submit', methods: ['POST'])]
    public function submit(Request $request, SerializerInterface $serializer, TestProcessor $testProcessor): Response {
        $data = $request->request->all();

        $userAnswerCollection = $serializer->deserialize(
            json_encode($data),
            UserAnswerCollection::class,
            'json'
        );

        $testResult = $testProcessor->processTest($userAnswerCollection);

        return $this->render('test/result.html.twig', ['testResult' => $testResult]);
    }
}
