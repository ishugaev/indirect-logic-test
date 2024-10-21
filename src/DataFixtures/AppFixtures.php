<?php declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Question;
use App\Entity\Answer;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $questionsData = [
            ['1+1=', ['3', '2', '0'], [1]],
            ['2+2=', ['4', '3+1', '10'], [0, 1]],
            ['3+3=', ['1+5', '1', '6', '2+4'], [0, 2, 3]],
            ['4+4=', ['8', '4', '0', '0+8'], [0, 3]],
            ['5+5=', ['6', '18', '10', '9', '0'], [2]],
            ['6+6=', ['3', '9', '0', '12', '5+7'], [3, 4]],
            ['7+7=', ['5', '14'], [1]],
            ['8+8=', ['16', '12', '9', '5'], [0]],
            ['9+9=', ['18', '9', '17+1', '2+16'], [0, 2, 3]],
            ['10+10=', ['0', '2', '8', '20'], [3]],
        ];

        foreach ($questionsData as $data) {
            $question = new Question();
            $question->setText($data[0]);

            foreach ($data[1] as $index => $text) {
                $answer = new Answer();
                $answer->setText($text);
                $answer->setIsCorrect(in_array($index, $data[2]));
                $answer->setQuestion($question);
                $question->getAnswers()->add($answer);
                $manager->persist($answer);
            }

            $manager->persist($question);
        }

        $manager->flush();
    }
}
