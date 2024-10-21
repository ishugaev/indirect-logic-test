<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\TestResult;
use App\Repository\TestResultRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestResultController extends AbstractController
{
    #[Route('/testresult', name: 'test_result_list', methods: ['GET'])]
    public function list(TestResultRepository $testResultRepository): Response
    {
        $testResults = $testResultRepository->findAll();

        return $this->render('test_result/list.html.twig', [
            'testResults' => $testResults,
        ]);
    }

    #[Route('/testresult/{id}', name: 'test_result_detail', methods: ['GET'])]
    public function detail(TestResult $testResult): Response
    {
        return $this->render('test_result/detail.html.twig', [
            'testResult' => $testResult
        ]);
    }
}
