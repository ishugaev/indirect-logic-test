<?php declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Question;
use App\Entity\Answer;
use Symfony\Component\DomCrawler\Crawler;

class TestControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', '/test');

        $this->assertResponseIsSuccessful();

        $questionCount = $this->client->getContainer()->get('doctrine')->getRepository(Question::class)->count([]);
        $this->assertEquals($questionCount, $crawler->filter('.question')->count());

        $answersCount = $this->client->getContainer()->get('doctrine')->getRepository(Answer::class)->count([]);
        $this->assertEquals($answersCount, $crawler->filter('input.form-check-input')->count());

        $submitAction = $this->client->getContainer()->get('router')->generate('test_submit');
        $this->assertSelectorExists('form[action="'.$submitAction.'"][method="post"]');
        $this->assertSelectorExists('button[type="submit"].btn.btn-primary');
    }

    public function testSubmitAllCorrect(): void
    {
        $questions = $this->client->getContainer()->get('doctrine')->getRepository(Question::class)->findAll();

        $submitData = [];
        foreach ($questions as $question) {
            $answers = $question->getAnswers();
            $answerData = [];
            foreach ($answers as $answer) {
                if ($answer->isCorrect()) {
                    $answerData[] = $answer->getId();
                }
            }
            $questionId = $question->getId();
            $submitData[$questionId] = $answerData;
        }

        $this->client->request('POST', '/test/submit', $submitData);

        // Get the response and create a crawler from it
        $response = $this->client->getResponse();

        $crawler = new Crawler($response->getContent());

        $this->assertResponseIsSuccessful();

        $this->assertEquals(count($questions), $crawler->filter('.correct')->count());
        $this->assertEquals(0, $crawler->filter('.incorrect')->count());
    }

    public function testSubmitAllIncorrect(): void
    {
        $questions = $this->client->getContainer()->get('doctrine')->getRepository(Question::class)->findAll();

        $submitData = [];
        foreach ($questions as $question) {
            $answers = $question->getAnswers();
            $answerData = [];
            foreach ($answers as $answer) {
                if (!$answer->isCorrect()) {
                    $answerData[] = $answer->getId();
                }
            }
            $questionId = $question->getId();
            $submitData[$questionId] = $answerData;
        }

        $this->client->request('POST', '/test/submit', $submitData);

        // Get the response and create a crawler from it
        $response = $this->client->getResponse();

        $crawler = new Crawler($response->getContent());

        $this->assertResponseIsSuccessful();

        $this->assertEquals(0, $crawler->filter('.correct')->count());
        $this->assertEquals(count($questions), $crawler->filter('.incorrect')->count());
    }

    public function testSubmitSomeCorrectSomeAreNot(): void
    {
        $questions = $this->client->getContainer()->get('doctrine')->getRepository(Question::class)->findAll();

        $submitData = [];
        $i = 0;
        foreach ($questions as $question) {
            $answers = $question->getAnswers();
            $answerData = [];
            foreach ($answers as $answer) {
                if ($answer->isCorrect()) {
                    $answerData[] = $answer->getId();
                }

                if ($i%2 == 0 && !$answer->isCorrect()) {
                    $answerData[] = $answer->getId();
                }
            }
            $i++;
            $questionId = $question->getId();
            $submitData[$questionId] = $answerData;
        }

        $this->client->request('POST', '/test/submit', $submitData);

        // Get the response and create a crawler from it
        $response = $this->client->getResponse();

        $crawler = new Crawler($response->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertEquals(count($questions)/2, $crawler->filter('.correct')->count());
        $this->assertEquals(count($questions)/2, $crawler->filter('.incorrect')->count());
    }
}
