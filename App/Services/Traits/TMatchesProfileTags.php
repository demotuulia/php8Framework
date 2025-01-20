<?php

namespace App\Services\Traits;


use App\Enums\EMatchType;
use App\Factory\FModel;
use App\Models\Matches;
use App\Models\MatchesOptions;
use App\Traits\TString;

/**
 * Create the tags from the content of the profile. These tags are used in wilfd card search.
 */
trait TMatchesProfileTags
{
    use TString;

    /**
     * Set tags
     *
     * @param array $profile        
     * @return string           Genrated tags as a string
     */
    private function setTags(array $profile): string
    {
        $profile = current($this->show($profile['id']));
        $tags = [];
        $tags = $this->setProfileTags($profile, $tags);
        $tags = $this->setUserFieldsTags($profile['userFields'], $tags);
        $tags = $this->setMatchesTags($profile, $tags);

        // use only tags having letters
        for ($i = 0; $i < count($tags); $i++) {
            if (!preg_match("/[a-z]/i", $tags[$i])) {
                unset($tags[$i]);
            }
        }

        $tags = array_unique($tags);
        sort($tags);
        return implode(',', $tags);
    }


    /**
     * Set  tags from the profile data
     *
     * @param array $profile
     * @param array $tags
     * @return array
     */
    private function setProfileTags(array $profile, array $tags): array
    {
        foreach (['name', 'description'] as $column) {
            if (isset($profile[$column])) {
                $tags = array_merge(
                    $tags,
                    $this->stringToArray($profile[$column])
                );
            }
        }
        return $tags;
    }

    /**
     * USet tags from the profile user fields
     *
     * @param array $userFields
     * @param array $tags
     * @return array
     */
    private function setUserFieldsTags(array $userFields, array $tags): array
    {
        foreach ($userFields as $value) {
            if ($value && is_string($value)) {
                $tags = array_merge(
                    $tags,
                    $this->stringToArray($value)
                );
            }
        }
        return $tags;
    }

    /**
     * Set tags from the matches data
     *
     * @param array $profile
     * @param array $tags
     * @return array
     */
    private function setMatchesTags(array $profile, array $tags): array
    {
        /** @var Matches $mMatches */
        $mMatches = FModel::build('Matches');
        $matches = $mMatches->all('db_code');

        /** @var MatchesOptions $mMatchesOptions */
        $mMatchesOptions = FModel::build('MatchesOptions');

        foreach ($matches as $key => $match) {
            if (isset($profile[$key])) {
                $profileItem = $profile[$key];
                if ($profileItem) {
                    switch ($match['match_type']) {
                        case EMatchType::$MULTIPLE_CHOOSE :
                        case EMatchType::$MULTIPLE_CHOOSE_OR :
                            $tags[] = $profile['matches'][$key]['valuesStr'];
                            break;
                        case EMatchType::$MENU:
                        case EMatchType::$RADIO_BUTTON:
                            $newTag = (!is_null($profileItem))
                                ? $mMatchesOptions->get($profileItem)
                                : null;
                            if (!is_null($newTag) && !empty($newTag)) {
                                $newTag = $newTag[0]['value'];
                                $tags[] = strtolower($newTag);
                            }
                            break;
                        case EMatchType::$DESCRIPTION:
                            $tags = array_merge(
                                $tags,
                                $this->stringToArray($profile[$key])
                            );
                            break;
                        default:
                            break;
                    }

                    if (isset($profile['matchesComments'])) {
                        foreach ($profile['matchesComments'] as $comment) {
                            if($comment['comment']) {
                                $tags = array_merge(
                                    $tags,
                                    $this->stringToArray($comment['comment'])
                                );
                            }
                        }
                    }

                }
            }


        }
        return $tags;
    }
}