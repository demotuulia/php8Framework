<?php
/**
 * SearchTest
 *
 */

namespace Feature;

require_once __DIR__ . '/../BaseTest.php';


use App\Services\TranslationsService;
use Tests\BaseTest;
use App\Enums\ELocales;

class TRanslationsTest extends BaseTest
{
    public function testTranslate(): void
    {
        // Check by default language (Dutch)
        // Test 'default' translatons
        $translation = TranslationsService::_('test', ["@name" => "Nick", "@weather" => "Sunny"]);
        $this->assertEquals("Heer weer is Nick. Het weer is Sunny.", $translation);
        // Test 'errors' translatons
        $translation = TranslationsService::_('test', ["@name" => "Nick", "@weather" => "Sunny"], 'errors');
        $this->assertEquals("Hallo Nick. Uw fout is  Sunny.", $translation);

         // English translations
        $translation::setlocale(ELocales::en_US);
        // Test 'default' translatons
        $translation = TranslationsService::_('test', ["@name" => "Nick", "@weather" => "Sunny"]);
        $this->assertEquals("Hi Nick. The weather is Sunny.", $translation);
        // Test 'errors' translatons
        $translation = TranslationsService::_('test', ["@name" => "Nick", "@weather" => "Sunny"], 'errors');
        $this->assertEquals("Hi Nick. Your error  is Sunny.", $translation);


     
    }

}