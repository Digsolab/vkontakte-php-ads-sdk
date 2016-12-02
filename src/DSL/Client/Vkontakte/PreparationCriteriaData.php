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
     * Возвращает уже подготовленные данные (готовые к отправки в api вк)
     * @return array
     */
    public function getPreparedSettings()
    {
        return $this->preparedSettings;
    }

    /**
     * Метод подготовливающий массив "criteria"
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
            $settings = $this->checkAndSummBirthdays($settings);
        }

        return $settings;
    }

    /**
     * Метод проверяет и если необходимо выставляет начальные значения для возраста
     * если указана нижняя или верхняя граница возраста необходимо выставить дефолтные значения для второго
     * параметра возраста
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
     * Метод проверяющий и при необходимости (если значения birthday не пустое) сумирующий массив birthday
     * и устанавливающий результат сложения праметру birthday
     *
     * @param array $settings
     *
     * @return array
     */
    private function checkAndSummBirthdays(array $settings)
    {
        $resetBirthday = isset($settings[self::BIRTHDAY]) && count($settings[self::BIRTHDAY]) > 0;

        if ($resetBirthday) {
            $summBirthdays = 0;
            foreach ($settings[self::BIRTHDAY] as $birthday) {
                $summBirthdays += $birthday;
            }
            $settings[self::BIRTHDAY] = $summBirthdays;
        }

        return $settings;
    }
}
