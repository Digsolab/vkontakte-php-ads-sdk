<?php

namespace DSL\Client\Vkontakte;

/**
 * Class PreparationCriteriaData
 * @package DSL\Client\Vkontakte
 */
class PreparationCriteriaData
{
    const DEFAULT_CRITERIA_AGE_FROM = 12;
    const DEFAULT_CRITERIA_AGE_TO   = 65;

    const AGE_FROM     = 'age_from';
    const AGE_TO       = 'age_to';
    const BIRTHDAY     = 'birthday';

    private $preparedSettings;

    /**
     * PreparationCriteriaData constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->preparedSettings = $this->exec($settings);
    }

    /**
     * Returns the data already prepared (ready to send to api VK)
     *
     * @return array
     */
    public function getPreparedSettings()
    {
        return $this->preparedSettings;
    }

    /**
     * A method of preparing an array of "criteria"
     *
     * @param array $settings
     *
     * @return array
     */
    private function exec(array $settings)
    {
        $settingsNotEmpty = count($settings) > 0;
        if ($settingsNotEmpty) {
            $settings = $this->checkAndSetDefaultValueAge($settings);
            $settings = $this->checkAndSumBirthdays($settings);
        }

        return $settings;
    }

    /**
     * The method checks and if required sets the initial value for age.
     * If you specify lower or upper age limit, you need to set default values for the second parameter of age
     *
     * @param array $settings
     *
     * @return array
     */
    private function checkAndSetDefaultValueAge(array $settings)
    {
        $ageFromNotEmpty = isset($settings[self::AGE_FROM]) && $settings[self::AGE_FROM] > 0;
        $ageToNotEmpty   = isset($settings[self::AGE_TO]) && $settings[self::AGE_TO] > 0;

        if ($ageFromNotEmpty && ! $ageToNotEmpty) {
            $settings[self::AGE_TO] = self::DEFAULT_CRITERIA_AGE_TO;
        } elseif ( ! $ageFromNotEmpty && $ageToNotEmpty) {
            $settings[self::AGE_FROM] = self::DEFAULT_CRITERIA_AGE_FROM;
        }

        return $settings;
    }

    /**
     * The method of inspection and if necessary (if the values are not empty birthday and if it is array)
     * birthday of the summing array and sets the result the parameter of birthday
     *
     * @param array $settings
     *
     * @return array
     */
    private function checkAndSumBirthdays(array $settings)
    {
        $resetBirthday = isset($settings[self::BIRTHDAY]) && is_array($settings[self::BIRTHDAY])
            && count($settings[self::BIRTHDAY]) > 0;

        if ($resetBirthday) {
            $settings[self::BIRTHDAY] = array_sum($settings[self::BIRTHDAY]);
        }

        return $settings;
    }
}
