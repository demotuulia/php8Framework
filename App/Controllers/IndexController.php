<?php

namespace App\Controllers;

use App\Models\Matches;
use App\Plugins\Http\Response as Status;
use App\Plugins\Http\Exceptions;
use App\Factory\FModel;
class IndexController extends BaseController
{
    /**
     * Controller function used to test whether the project was set up properly.
     *
     * @return void
     */
    public function test()
    {
        /** @var Matches $test */
        $test = FModel::build('Matches' );
        $matches = $test->get();
        // Respond with 200 (OK):
        (new Status\Ok(['message' => 'Hello world!']))->send();
    }
    protected function getValidationRules(): array
    {
        return [];
    }
}
