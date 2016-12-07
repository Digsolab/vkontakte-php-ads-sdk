<?php

namespace DSL\Client\Tests;


use DSL\Client\Vkontakte\PreparationCriteriaData;

class PreparationCriteriaDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testAssert($settings, $expected)
    {
        $this->assertEquals($expected, (new PreparationCriteriaData($settings))->getPreparedSettings());
    }

    public function additionProvider()
    {
        $birthday = 'birthday';
        $ageFrom = 'age_from';
        $ageTo = 'age_to';
        $otherPropertyFirst = 'otherPropertyFirst';

        $ageFromDefault = 12;
        $ageToDefault = 65;

        return [
            //only birtday
            [[$birthday => [1]], [$birthday => 1]],
            [[$birthday => [2]], [$birthday => 2]],
            [[$birthday => [4]], [$birthday => 4]],
            [[$birthday => [1, 2]], [$birthday => 3]],
            [[$birthday => [2, 4]], [$birthday => 6]],
            [[$birthday => [1, 4]], [$birthday => 5]],
            [[$birthday => [1, 2, 4]], [$birthday => 7]],
            [[$birthday => 7], [$birthday => 7]],
            [[$birthday => 6], [$birthday => 6]],

            //only ages
            [[$ageFrom => 20], [$ageFrom => 20, $ageTo => $ageToDefault]],
            [[$ageTo => 20], [$ageFrom => $ageFromDefault, $ageTo => 20]],
            [[$ageFrom => 20, $ageTo => 40], [$ageFrom => 20, $ageTo => 40]],

            //other value
            [[$otherPropertyFirst => $otherPropertyFirst], [$otherPropertyFirst => $otherPropertyFirst]],
            [[$otherPropertyFirst => 0], [$otherPropertyFirst => 0]],

            //mixed data
            [[$birthday => [1, 2], $ageFrom => 20], [$birthday => 3, $ageFrom => 20, $ageTo => $ageToDefault]],
            [[$birthday => [1, 2, 4], $ageFrom => 20], [$birthday => 7, $ageFrom => 20, $ageTo => $ageToDefault]],
            [[$birthday => 7, $ageFrom => 20], [$birthday => 7, $ageFrom => 20, $ageTo => $ageToDefault]],
            [
                [$birthday => [1, 2, 4], $ageTo => 50, $otherPropertyFirst => 0],
                [$birthday => 7, $ageFrom => $ageFromDefault, $ageTo => 50, $otherPropertyFirst => 0],
            ],

            [[], []],
        ];
    }
}
